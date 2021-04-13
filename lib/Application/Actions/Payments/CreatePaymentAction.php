<?php

namespace App\Application\Actions\Payments;

use App\Application\Settings\Env;
use App\Domain\Payment\Payment;
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
		Stripe::setApiKey(Env::getStripeKey());

		// retrieve JSON from POST body
		$data = $this->getFormData();

		$amount = self::calculateOrderAmount($data->items);
		$currency = 'usd';
		$paymentIntent = PaymentIntent::create([
			'amount' => $amount,
			'currency' => $currency,
		]);

		$secret = $paymentIntent->client_secret;
		$payment = Payment::create($secret, $amount, $currency);

		$this->payments->putPayment($payment);

		// replace this with just the Payment entity return
		$output = [
			'clientSecret' => $secret,
		];

		return $this->respondWithData($output);
	}
}
