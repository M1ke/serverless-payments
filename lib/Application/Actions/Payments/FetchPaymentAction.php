<?php
namespace App\Application\Actions\Payments;

use App\Domain\Payment\PaymentNotFoundException;
use Psr\Http\Message\ResponseInterface;

class FetchPaymentAction extends PaymentAction {

	protected function action(): ResponseInterface{
		$id = $this->request->getQueryParams()['id'];

		if (!$id){
			return $this->respondWithError('You must supply a payment ID in the query string');
		}

		try {
			$payment = $this->payments->findPaymentIntent($id);
		}
		catch (PaymentNotFoundException $e) {
			return $this->respondWithError("The payment with ID $id was not found", 404);
		}

		return $this->respondWithData($payment);
	}
}
