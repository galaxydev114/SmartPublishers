<?php

namespace DLW\Http\Controllers\Admin;

use DLW\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
//use Illuminate\Support\Facades\Storage;


class StripeWebHookController extends CashierController
{
    /**
     * Handle a cancelled customer from a Stripe subscription.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        //Storage::disk('local')->put('example.txt', json_encode($payload));
        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            $user->subscriptions->filter(function ($subscription) use ($payload) {
                return $subscription->stripe_id === $payload['data']['object']['id'];
            })->each(function ($subscription) {
                $subscription->markAsCancelled();
            });
            $user->is_subscribed = 0;
            $user->subscribe_id = 0;
            $user->agreement_id = "";
            $user->plan_id = "";
            $user->card_brand = "";
            $user->card_last_four = "";
            $user->save();
        }
        return $this->successMethod();
    }

    /**
     * Handle a subscription payment fail customer from a Stripe subscription.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleInvoicePaymentFailed(array $payload)
    {
        //Storage::disk('local')->put('example.json', json_encode($payload));
        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            $user->subscriptions->filter(function ($subscription) use ($payload) {
                return $subscription->stripe_id === $payload['data']['object']['id'];
            })->each(function ($subscription) {
                $subscription->markAsCancelled();
            });
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
        }
        return $this->successMethod();
    }
}
