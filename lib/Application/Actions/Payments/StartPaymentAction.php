<?php

namespace App\Application\Actions\Payments;

use App\Application\Settings\Env;
use App\Domain\Payment\PaymentNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StartPaymentAction extends PaymentAction {

	protected function action(): Response{
		$id = $this->request->getQueryParams()['id'];

		if (!$id){
			return $this->respondWithError('You must supply a payment ID in the query string');
		}

		try {
			$payment = $this->payments->findPayment($id);
		}
		catch (PaymentNotFoundException $e) {
			return $this->respondWithError("The payment with ID $id was not found", 404);
		}

		// This is your real test secret API key.
		Stripe::setApiKey(Env::getStripeKey());

		$payment_intent = PaymentIntent::create([
			'amount' => $payment->getAmount(),
			'currency' => $payment->getCurrency(),
			'description' => $payment->getDescription(),
			'metadata' => [
				'id' => $payment->getId()
			]
		]);

		// replace this with just the Payment entity return
		$output = array_merge($payment->jsonSerialize(), [
			'clientSecret' => $payment_intent->client_secret,
		]);

		return $this->respondWithData($output);
	}
}
