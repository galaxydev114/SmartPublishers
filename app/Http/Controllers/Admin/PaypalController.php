<?php

namespace DLW\Http\Controllers\Admin;

use Carbon\Carbon;
use DLW\Http\Controllers\Controller;
use DLW\Models\Report;
use Illuminate\Http\Request;
// Used to process plans
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Common\PayPalModel;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;


// use to process billing agreements
use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\ShippingAddress;

use DLW\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;

class PaypalController extends Controller
{
    private $apiContext;
    private $mode;
    private $client_id;
    private $secret;

    // Create a new instance with our paypal credentials
    public function __construct()
    {
        $this->mode =config('paypal.settings.mode');
        if($this->mode == 'live'){
            $this->client_id = config('paypal.live_client_id');
            $this->secret = config('paypal.live_secret');
        } else {
            $this->client_id = config('paypal.sandbox_client_id');
            $this->secret = config('paypal.sandbox_secret');
        }

        // Set the Paypal API Context/Credentials
        $this->apiContext = new ApiContext(new OAuthTokenCredential($this->client_id, $this->secret));
        $this->apiContext->setConfig(config('paypal.settings'));
    }

    public function subscriptions(){
        $trial_version_msg = "";
        $user = Auth::guard('admin')->user();
        if ($user->trial_end < date('Y-m-d') && $user->trial_end != "0000-00-00" && $user->trial_end != null && $user->is_subscribed != 1)
        {
            $trial_version_msg = "Your trial version has finished, check the subscription plans here.";
        } else if ($user->trial_end >= date('Y-m-d') && $user->is_subscribed != 1)
        {
            $remain = (strtotime($user->trial_end) - strtotime(date('Y-m-d')))/60/60/24;
            $trial_version_msg = "Your trial version will be expired after " . $remain . " days.";
        } else {
            $trial_version_msg = "Your trial version is started now. it will be expire after 30 days.";
            $user->trial_start = date('Y-m-d');
            $user->trial_end = date('Y-m-d', strtotime(date('Y-m-d'). ' + 30 days'));
            $user->save();
        }
        return view("user.subscriptions", compact('trial_version_msg'));
    }

    public function create_plan(SubscriptionPlan $subscribePlan){
        if(Auth::guard('admin')->user()->is_super == false) {
            abort('404');
        }
        // Create a new billing plan
        $baseUrl = url('/');
        $plan = new Plan();

//        try {
//            $allowedParams = array(
//                'status' => 'ALL',
//                'page_size' => 8,
//                'total_required'=>'yes',
//            );
//            $plan1 = Plan::all($allowedParams, $this->apiContext);
//
//            dd($plan1);
//        } catch (Exception $ex) {
//            die($ex);
//        }

        $plan->setName($subscribePlan->plan_name)
            ->setDescription($subscribePlan->plan_description)
            ->setType('INFINITE');

        // Set billing plan definitions
//        $paymentDefinitionTrial = new PaymentDefinition();
//        $paymentDefinitionTrial->setName($subscribePlan->trial_payment_name)
//            ->setType('TRIAL')
//            ->setFrequency($subscribePlan->trial_frequency)
//            ->setFrequencyInterval($subscribePlan->trial_frequency_interval)
//            ->setCycles($subscribePlan->trial_cycle)
//            ->setAmount(new Currency(['value' => $subscribePlan->trial_amount, 'currency' => $subscribePlan->trial_currency ]));

        $paymentDefinition = new PaymentDefinition();
        $paymentDefinition->setName($subscribePlan->regular_payment_name)
            ->setType('REGULAR')
            ->setFrequency($subscribePlan->regular_frequency)
            ->setFrequencyInterval($subscribePlan->regular_frequency_interval)
            ->setCycles($subscribePlan->regular_cycle)
            ->setAmount(new Currency( ['value' => $subscribePlan->regular_amount, 'currency' => $subscribePlan->regular_currency]));

        // Set merchant preferences
        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl($baseUrl.'/'.$subscribePlan->return_url)
            ->setCancelUrl($baseUrl.'/'.$subscribePlan->cancel_url)
            ->setAutoBillAmount('yes')
            ->setInitialFailAmountAction('CONTINUE')
            ->setMaxFailAttempts('0');
//            ->setSetupFee(new Currency(array('value' => 11.99, 'currency' => 'USD')));

//        $plan->addPaymentDefinition($paymentDefinitionTrial);
        $plan->addPaymentDefinition($paymentDefinition);
        $plan->setMerchantPreferences($merchantPreferences);

        //dd($plan);
        //create the plan
        try {
            $createdPlan = $plan->create($this->apiContext);

            try {
                $patch = new Patch();
                $value = new PayPalModel('{"state":"ACTIVE"}');
                $patch->setOp('replace')
                    ->setPath('/')
                    ->setValue($value);
                $patchRequest = new PatchRequest();
                $patchRequest->addPatch($patch);
                $createdPlan->update($patchRequest, $this->apiContext);
                $plan = Plan::get($createdPlan->getId(), $this->apiContext);
                $subscribePlan->plan_id = $createdPlan->getId();
                $subscribePlan->save();
                // Output plan id
                echo 'Plan ID:' . $plan->getId();
            } catch (PayPal\Exception\PayPalConnectionException $ex) {
                echo $ex->getCode();
                echo $ex->getData();
                die($ex);
            } catch (Exception $ex) {
                die($ex);
            }
        } catch (PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        } catch (Exception $ex) {
            die($ex);
        }
    }
    public function paypalRedirect(SubscriptionPlan $subscribePlan){
        // Create new agreement
        $agreement = new Agreement();
        $agreement->setName($subscribePlan->plan_name . ' Agreement')
            ->setDescription($subscribePlan->plan_name)
            ->setStartDate(\Carbon\Carbon::now()->addMinutes(5)->toIso8601String());

        // Set plan id
        $plan = new Plan();
        $plan->setId($subscribePlan->plan_id);
        $agreement->setPlan($plan);

        // Add payer type
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $agreement->setPayer($payer);

        try {
            // Create agreement
            $agreement = $agreement->create($this->apiContext);

            // Extract approval URL to redirect user
            $approvalUrl = $agreement->getApprovalLink();
            $user = Auth::guard('admin')->user();
            $user->subscribe_id = $subscribePlan->id;
            $user->plan_id = $subscribePlan->plan_id;
            $user->save();
            return redirect($approvalUrl);
        } catch (PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        } catch (Exception $ex) {
            die($ex);
        }
    }
    public function paypalReturn(Request $request){
        $token = $request->token;
        $agreement = new \PayPal\Api\Agreement();
        try {
            // Execute agreement
            $result = $agreement->execute($token, $this->apiContext);
            $user = Auth::guard('admin')->user();
            if(isset($result->id)){
                $trial_end = Carbon::now()->addDays(30)->format('Y-m-d');
                if($user->subscribe_id > 4)
                    $trial_end = Carbon::now()->addYear(1)->format('Y-m-d');
                $user->is_subscribed = 1;
                $user->trial_start = date('Y-m-d');
                $user->trial_end = $trial_end;
                $user->trial_ends_at = $trial_end;
                $user->agreement_id = $result->id;
                $user->save();
                return redirect()->route('subscription.index')->with('success', __('globals.msg.new_subscription_success'));
            }
            return redirect()->route('subscription.index')->with("fail", __('globals.msg.new_subscription_failed'));

        } catch (\PayPal\Exception\PayPalConnectionException $ex) {

            $user = Auth::guard('admin')->user();
            $user->is_subscribed = 0;
            $user->subscribe_id = 0;
            $user->agreement_id = "";
            $user->plan_id = "";
            $user->save();
            return redirect()->route('subscription.index')->with('success', __('globals.msg.subscription_cancel_success'));
        }
    }
    public function paypalCancel(){
        $agreement = Agreement::get(Auth::guard('admin')->user()->agreement_id, $this->apiContext);
        $agreementStateDescriptor = new AgreementStateDescriptor();
        $agreementStateDescriptor->setNote("Cancel the agreement");
        try {
            // Execute agreement
            $result = $agreement->cancel($agreementStateDescriptor, $this->apiContext);
            $user = Auth::guard('admin')->user();
            $user->is_subscribed = 0;
            $user->subscribe_id = 0;
            $user->agreement_id = "";
            $user->plan_id = "";
            $user->card_brand = "";
            $user->card_last_four = "";
            $user->save();
            Report::allCmpPause();
            return redirect()->route('subscription.index')->with('success', __('globals.msg.subscription_cancel_success'));

        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            //echo 'You have either cancelled the request or your session has expired';
            if(mb_strpos($ex->getData(), 'Invalid profile status for cancel action') > -1)
            {
                $user = Auth::guard('admin')->user();
                $user->is_subscribed = 0;
                $user->subscribe_id = 0;
                $user->agreement_id = "";
                $user->plan_id = "";
                $user->card_brand = "";
                $user->card_last_four = "";
                $user->save();
                Report::allCmpPause();
                return redirect()->route('subscription.index')->with('success', __('globals.msg.subscription_cancel_failed'));
            }
            return redirect()->route('subscription.index')->with('error', __('globals.msg.subscription_cancel_failed'));
        }
    }
}