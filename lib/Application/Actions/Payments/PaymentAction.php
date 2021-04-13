<?php
declare(strict_types=1);

namespace App\Application\Actions\Payments;

use App\Application\Actions\Action;
use App\Domain\Payment\PaymentRepository;
use Psr\Log\LoggerInterface;

abstract class PaymentAction extends Action {

	protected PaymentRepository $payments;

	public function __construct(LoggerInterface $logger, PaymentRepository $payments){
		parent::__construct($logger);

		$this->payments = $payments;
	}
}
