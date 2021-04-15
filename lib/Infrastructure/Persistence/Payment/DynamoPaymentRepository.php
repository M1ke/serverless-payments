<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Payment;

use App\Domain\Payment\Payment;
use App\Domain\Payment\PaymentNotFoundException;
use App\Domain\Payment\PaymentRepository;
use App\Infrastructure\Persistence\DynamoUtils;
use Aws\DynamoDb\DynamoDbClient;

class DynamoPaymentRepository implements PaymentRepository {

	private DynamoDbClient $client;

	public function __construct(DynamoDbClient $client){
		$this->client = $client;
	}

	public function findPayment(string $id): Payment{
		$params = DynamoUtils::findParams(Payment::class, $id);

		$result = $this->client->query($params);
		$items = $result->get('Items');

		if (count($items)===0){
			throw new PaymentNotFoundException("The payment with ID $id could not be found");
		}

		$item = reset($items);

		return Payment::hydrate($item);
	}

	public function putPayment(Payment $payment): void{
		$item_params = DynamoUtils::insertParams($payment);

		$this->client->putItem($item_params);
	}
}
