<?php

namespace App\Application\Actions\Payments;

use App\Application\Actions\Payments\PaymentAction;
use Psr\Http\Message\ResponseInterface as Response;
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
		Stripe::setApiKey('sk_test_WT39umPjMrogJiaz6uuEsNmo');

		// retrieve JSON from POST body
		$data = $this->getFormData();

		$paymentIntent = \Stripe\PaymentIntent::create([
			'amount' => self::calculateOrderAmount($data->items),
			'currency' => 'usd',
		]);

		$output = [
			'clientSecret' => $paymentIntent->client_secret,
		];

		return $this->respondWithData($output);
	}
}
