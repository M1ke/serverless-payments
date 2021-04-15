<?php

namespace App\Application\Actions\Payments;

use App\Domain\Payment\Payment;
use Psr\Http\Message\ResponseInterface as Response;

class SetupPaymentAction extends PaymentAction {

	protected function action(): Response{
		// retrieve JSON from POST body
		$data = $this->getFormData();

		// Amount will be in decimal e.g. £5.54
		$amount = $data->amount;
		$description = $data->description;

		$payment = Payment::create($amount, $description);

		$this->payments->putPayment($payment);

		return $this->respondWithData($payment);
	}
}
