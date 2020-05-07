<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Component\HttpFoundation\Response;
use DLW\Models\Admin;


class PaypalWebHookController extends Controller
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
    /**
     * Handle a registe paypal webhook
     *
     * @return boolean
     */
    protected function registerPaypalWebHook()
    {
        if(Auth::guard('admin')->user()->is_super == false) {
            abort('404');
        }
        // Create a new instance of Webhook object
        $webhook = new \PayPal\Api\Webhook();
        // # Basic Information
        //     {
        //         "url":"https://requestb.in/10ujt3c1",
        //         "event_types":[
        //            {
        //                "name":"PAYMENT.AUTHORIZATION.CREATED"
        //            },
        //            {
        //                "name":"PAYMENT.AUTHORIZATION.VOIDED"
        //            }
        //         ]
        //      }
        // Fill up the basic information that is required for the webhook
        // The URL should be actually accessible over the internet. Having a localhost here would not work.
        // #### There is an open source tool http://requestb.in/ that allows you to receive any web requests to a url given there.
        // #### NOTE: Please note that you need an https url for paypal webhooks. You can however override the url with https, and accept
        // any warnings your browser might show you. Also, please note that this is entirely for demo purposes, and you should not
        // be using this in production
        $baseUrl = url('/');
        //$baseUrl = "https://c274d729db0e.ngrok.io/paypal/webhook";

        $webhook->setUrl("$baseUrl/paypal/");

        // # Event Types
        // Event types correspond to what kind of notifications you want to receive on the given URL.
        $webhookEventTypes = array();
        $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
        '{
                 "name":"PAYMENT.SALE.REVERSED"
            }'
        );
        $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
            '{
                 "name":"PAYMENT.SALE.REFUNDED"
            }'
        );
        $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
        '{
                "name":"BILLING.SUBSCRIPTION.ACTIVATED"
            }'
        );
        $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
            '{
                "name":"BILLING.SUBSCRIPTION.CANCELLED"
            }'
        );
        $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
            '{
                "name":"BILLING.SUBSCRIPTION.SUSPENDED"
            }'
        );
        $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
            '{
                "name":"BILLING.SUBSCRIPTION.PAYMENT.FAILED"
            }'
        );

        $webhook->setEventTypes($webhookEventTypes);

        // For Sample Purposes Only.
        $request = clone $webhook;

        // ### Create Webhook
        try {
            $output = $webhook->create($this->apiContext);
        } catch (Exception $ex) {
            // ^ Ignore workflow code segment
            if ($ex instanceof \PayPal\Exception\PayPalConnectionException) {
                $data = $ex->getData();
                // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                ResultPrinter::printError("Created Webhook Failed. Checking if it is Webhook Number Limit Exceeded. Trying to delete all existing webhooks", "Webhook", "Please Use <a style='color: red;' href='DeleteAllWebhooks.php' >Delete All Webhooks</a> Sample to delete all existing webhooks in sample", $request, $ex);
                if (strpos($data, 'WEBHOOK_NUMBER_LIMIT_EXCEEDED') !== false) {
                    try {
                        $output = $webhook->create($this->apiContext);
                    } catch (Exception $ex) {
                        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                        echo("Webhook Creation Error: ". $output->getId());
                        exit(1);
                    }
                } else {
                    // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                    echo("Webhook Creation Error: ". $output->getId());
                    exit(1);
                }
            } else {
                // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                echo("Webhook Creation Error: ". $output->getId());
                exit(1);
            }
            // Print Success Result
        }
        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        echo("Created Webhook: ". $output->getId());
        return true;
    }

    /**
     * Handle a Paypal webhook processor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        //https://cliente0001.smartpublishers.co/paypal/webhook
        //\Illuminate\Support\Facades\Storage::disk('local')->put('example__0.json', print_r($payload, true));
        $eventType = trim(strval($payload['event_type']));
        $argId = trim(strval($payload['resource']['id']));
        $user = Admin::where([['agreement_id', 'like', '%'.trim(strval($argId)).'%']])->first();
        if($user)
        {
            if($eventType == 'BILLING.SUBSCRIPTION.PAYMENT.FAILED' || $eventType == 'BILLING.SUBSCRIPTION.SUSPENDED'
                || $eventType == 'PAYMENT.SALE.REFUNDED' || $eventType == 'PAYMENT.SALE.REVERSED')
            {

                $user->is_subscribed = 0;
                $user->subscribe_id = 0;
                $user->agreement_id = "";
                $user->plan_id = "";
                $user->card_brand = "";
                $user->card_last_four = "";

                if($user->trial_end != '' && $user->trial_end != null && $user->trial_end != "0000-00-00")
                {
                    if ($user->trial_end < date('Y-m-d'))
                    {
                        $user->trial_start = date('Y-m-d');
                        $user->trial_end = date('Y-m-d');
                        $user->trial_end_at = null;
                    }
                }
                $user->save();
            } else if( $eventType == 'BILLING.SUBSCRIPTION.CANCELLED') {

                $user->is_subscribed = 0;
                $user->subscribe_id = 0;
                $user->agreement_id = "";
                $user->plan_id = "";
                $user->card_brand = "";
                $user->card_last_four = "";
                $user->save();
            }

        }
        return new Response('Webhook Handled', 200);
    }
}
