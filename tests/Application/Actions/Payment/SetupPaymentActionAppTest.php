<?php

namespace Application\Actions\Payment;

use App\Application\Actions\ActionPayload;
use App\Domain\Payment\Payment;
use App\Domain\Payment\PaymentRepository;
use DI\Container;
use PHPUnit\Framework\Assert;
use Slim\Psr7\Factory\StreamFactory;
use Tests\AppTestCase;

class SetupPaymentActionAppTest extends AppTestCase {
	public function testAction(){
		$app = $this->getAppInstance();

		/** @var Container $container */
		$container = $app->getContainer();

		$amount_pounds = 123.45;
		$description = 'Test';
		$payment = Payment::create($amount_pounds, $description);

		$payment_repository = $this->prophesize(PaymentRepository::class);
		$payment_repository
			->putPayment($payment)
			->shouldBeCalledOnce();

		$container->set(PaymentRepository::class, $payment_repository->reveal());

		$body = (new StreamFactory())->createStream(json_encode([
			'amount' => $amount_pounds,
			'description' => $description,
		]));
		$request = $this->createRequest('POST', '/api/setup', $body);
		$response = $app->handle($request);

		$payload = (string)$response->getBody();
		$expectedPayload = new ActionPayload(200, []);
		$serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

		Assert::assertEquals($serializedPayload, $payload);
	}
}
