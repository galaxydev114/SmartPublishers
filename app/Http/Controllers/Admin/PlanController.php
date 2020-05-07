<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DLW\Models\Plan;
use Illuminate\Support\Facades\Auth;
use \Stripe\Plan as StripePlan;

class PlanController extends Controller
{
    public function create_stripe_plan(Request $request, Plan $plan)
    {
        if(Auth::guard('admin')->user()->is_super == false) {
            abort('404');
        }
        $planName = $plan->name;
        $amount = $plan->cost * 100;
        $apiId = uniqid();
        $playType = "month";
        if($plan->id >= 5 && $plan->id <= 8)
            $playType = "year";

        $secretKey = \Config::get('services.stripe.secret');
        $currency = \Config::get('services.stripe.currency');
        \Stripe\Stripe::setApiKey($secretKey);

        $newPlan = StripePlan::create(array(
            "amount" => $amount,
            "interval" => $playType,
            "product" => array(
                "name" => $planName
            ),
            "currency" => $currency,
            "id" => $apiId
        ));
        //"trial_period_days" => 30,

        $plan->slug = $newPlan->id;;
        $plan->save();
        echo "Plan created: ". $plan->slug;
    }
}
