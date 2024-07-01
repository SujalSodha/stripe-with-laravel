<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        try {
            $stripe = new StripeClient(config('app_custom.STRIPE_SECRET'));

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => 1099,
                'currency' => 'usd',
                'payment_method' => $request->payment_method,
                'automatic_payment_methods' => ['enabled' => true],
                'customer' => auth()->user()->stripe_customer_id,
                'confirm' => true,
                'return_url' => route('dashboard')
            ]);

            return response()->json(['status' => true, 'message' => 'Payment successful']);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function handlePayment(Request $request)
    {
        try {

            $stripe = new StripeClient(config('app_custom.STRIPE_SECRET'));

            $token = $request->stripeToken;

            $paymentMethod = $stripe->paymentMethods->create([
                'type' => 'card',
                "billing_details" => [
                    "email" => auth()->user()->email,
                    "name" => $request->cardholderName,
                ],
                'card' => [
                    'token' => $token,
                ],
            ]);


            $paymentMethod = $stripe->paymentMethods->attach(
                $paymentMethod->id,
                ['customer' => auth()->user()->stripe_customer_id]
            );

            $stripe->customers->update(auth()->user()->stripe_customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethod->id,
                ],
            ]);

            return response()->json(['status' => true, 'message' => 'Card saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
