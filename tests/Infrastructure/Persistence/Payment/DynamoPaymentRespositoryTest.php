<?php

namespace Tests\Infrastructure\Persistence\Payment;

use App\Application\Settings\Env;
use App\Domain\Payment\Payment;
use App\Infrastructure\Persistence\Payment\DynamoPaymentRepository;
use App\Infrastructure\Time;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Result;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Guid\Guid;

class DynamoPaymentRespositoryTest extends TestCase {

	public function testFindPayment(): void{
		$id = Guid::uuid4()->toString();
		$description = 'Some description';
		$amount = 123;
		$currency = 'gbp';
		$created = time();
		$result = new Result(['Items' => [
			[
				'id' => ['S' => $id],
				'description' => ['S' => $description],
				'amount' => ['N' => $amount],
				'currency' => ['S' => $currency],
				'created' => ['N' => $created],
			],
		]]);

		$client = $this->getDynamoMock();
		$client->expects(self::once())
			->method('__call')
			->with('query')
			->willReturn($result);

		$repository = new DynamoPaymentRepository($client);

		$payment = $repository->findPayment($id);

		Assert::assertSame($id, $payment->getId());
		Assert::assertSame($description, $payment->getDescription());
		Assert::assertSame($amount, $payment->getAmount());
		Assert::assertSame($currency, $payment->getCurrency());
		Assert::assertSame($created, $payment->getCreated());
	}

	public function testPutPayment(): void{
		$amount_pounds = 123.45;
		$description = 'Test';
		$time = time();
		Time::test($time);

		$payment = Payment::create($amount_pounds, $description);

		$client = $this->getDynamoMock();
		$client->expects(self::once())
			->method('__call')
			->with('putItem', [[
				'TableName' => 'test',
				'Item' => [
					'id' => ['S' => FIXED_UUID_V4],
					'description' => ['S' => $description],
					'amount' => ['N' => $amount_pounds * 100],
					'currency' => ['S' => Env::getCurrency()],
					'created' => ['N' => $time],
					'paid' => ['N' => 0],
					'last_updated' => ['N' => $time],
				],
			]]);

		$repository = new DynamoPaymentRepository($client);

		$repository->putPayment($payment);
	}

	/**
	 * @return DynamoDbClient|MockObject
	 */
	private function getDynamoMock(){
		return $this->getMockBuilder(DynamoDbClient::class)
			->disableOriginalConstructor()
			->getMock();
	}
}
