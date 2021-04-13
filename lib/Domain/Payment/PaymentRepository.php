<?php
declare(strict_types=1);

namespace App\Domain\Payment;

interface PaymentRepository {

	/**
	 * @throws PaymentNotFoundException
	 */
	public function findPaymentIntent(string $id): Payment;
}
