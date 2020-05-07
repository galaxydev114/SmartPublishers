<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Http\Controllers\Controller;
use DLW\Models\Report;
use DLW\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use DLW\Models\Plan;
use Stripe\PaymentMethod;
use Stripe\Plan as StripePlan;
use Stripe\Subscription;
use Stripe\Stripe;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Carbon\Carbon;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Plan $plan)
    {
        $user = Auth::guard('admin')->user();

        $paymentMethod = json_decode($request->stripeToken, true);

        //dd(Cashier::findBillable($paymentMethod['id']));
        $secretKey = \Config::get('services.stripe.secret');
        $currency = \Config::get('services.stripe.currency');
        if($user->stripe_id == '')
        {
            $stripe = new \Stripe\StripeClient(
                $secretKey
            );

            $newCustomer = $stripe->customers->create([
                'description' => $user->name.'['.$user->email.']',
            ]);

            $user->stripe_id = $newCustomer->id;
            $user->save();
        }

        $result = $user->newSubscription('default', $plan->slug)
            //->trialUntil(Carbon::now()->addDays(30))
            ->create($paymentMethod['id']);
        $trial_end = Carbon::now()->addDays(30)->format('Y-m-d');
        if($plan->id > 4)
            $trial_end = Carbon::now()->addYear(1)->format('Y-m-d');

        if(isset($result->id)){
            $user->agreement_id = $result->id;
            $user->is_subscribed = 1;
            $user->subscribe_id = $plan->id;
            $user->plan_id = $plan->slug;
            $user->trial_start = substr($result->created_at, 0, 10);
            $user->trial_end = $trial_end;
            $user->trial_ends_at = $trial_end;
            $user->save();
            return redirect()->route('subscription.index')->with('success', __('globals.msg.new_subscription_success'));
        }
        return redirect()->route('subscription.index')->with("fail", __('globals.msg.new_subscription_failed'));
    }

    /**
     * Stripe subscribe cancel
     *
     * @param \Illuminate\Http\Request $request
     */
    public function cancel(Request $request)
    {
        if(Auth::guard('admin')->user()->subscription('default')->cancelNow())
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
            return redirect()->route('subscription.index')->with('success', __('globals.msg.subscription_cancel_success'));
        }
        return redirect()->route('subscription.index')->with("fail", __('globals.msg.subscription_cancel_failed'));
    }
}
