<?php
declare(strict_types=1);

namespace App\Domain\Payment;

use App\Domain\DomainException\DomainRecordNotFoundException;

class PaymentNotFoundException extends DomainRecordNotFoundException {
	public $message = 'The payment you requested does not exist.';
}
