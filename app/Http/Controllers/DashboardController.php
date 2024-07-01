<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class DashboardController extends Controller
{
    public function index()
    {
        $stripe = new StripeClient(config('app_custom.STRIPE_SECRET'));
        $customer_id = auth()->user()->stripe_customer_id;

        $paymentMethods = $stripe->paymentMethods->all([
            'customer' => $customer_id,
            'type'     => 'card'
        ]);

        $cust_payment_methods = [];

        $customers = $stripe->customers->retrieve(
            $customer_id,
            [
                'expand' => [
                    'invoice_settings.default_payment_method',
                    'default_source',
                ],
            ]
        );

        $default_method = "";
        if ($customers->invoice_settings->default_payment_method) {
            $default_method = $customers->invoice_settings->default_payment_method->id;
        }

        foreach ($paymentMethods as $paymentMethod) {
            $is_default = false;

            $key = $paymentMethod->id;
            if (!empty($default_method) && $default_method == $key) {
                $is_default = true;
            }
            $cust_payment_methods[$key]['exp_month']  = $paymentMethod->card->exp_month;
            $cust_payment_methods[$key]['exp_year']   = $paymentMethod->card->exp_year;
            $cust_payment_methods[$key]['brand']      = $paymentMethod->card->brand;
            $cust_payment_methods[$key]['last4']      = $paymentMethod->card->last4;
            $cust_payment_methods[$key]['id']         = $paymentMethod->id;
            $cust_payment_methods[$key]['is_default'] = $is_default;
        }

        return view('dashboard', compact('cust_payment_methods','paymentMethods'));
    }
}
