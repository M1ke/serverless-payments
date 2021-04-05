<?php

namespace App\Application\Actions\Payments;

use Psr\Http\Message\ResponseInterface as Response;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CreatePaymentAction extends PaymentAction {

	private static function calculateOrderAmount(array $items): int{
		// Replace this constant with a calculation of the order's amount
		// Calculate the order total on the server to prevent
		// customers from directly manipulating the amount on the client
		return 1400;
	}

	protected function action(): Response{
		// This is your real test secret API key.
		Stripe::setApiKey($_ENV['STRIPE_PRIVATE']);

		// retrieve JSON from POST body
		$data = $this->getFormData();

		$paymentIntent = PaymentIntent::create([
			'amount' => self::calculateOrderAmount($data->items),
			'currency' => 'usd',
		]);

		$output = [
			'clientSecret' => $paymentIntent->client_secret,
		];

		return $this->respondWithData($output);
	}
}
