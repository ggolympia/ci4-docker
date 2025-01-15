<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends BaseController
{
    public function __construct()
    {
        // Set the Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    // Endpoint to create a Payment Intent
    public function createPaymentIntent()
    {
        // Get the amount from the request (in cents)
        $amount = $this->request->getVar('amount');  // Example: 5000 (for $50)

        try {
            // Create a PaymentIntent on Stripe
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',  // Set the currency
            ]);

            return $this->response->setJSON([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => $e->getMessage(),
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
