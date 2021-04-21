<?php
declare(strict_types=1);

namespace Tests\Application\Actions;

use App\Application\Actions\Action;
use App\Application\Actions\ActionPayload;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Tests\AppTestCase;

class ActionAppTest extends AppTestCase {
	public function testActionSetsHttpCodeInRespond(): void{
		$app = $this->getAppInstance();
		$container = $app->getContainer();
		$logger = $container->get(LoggerInterface::class);

		$testAction = new class($logger) extends Action {
			public function __construct(
				LoggerInterface $loggerInterface
			){
				parent::__construct($loggerInterface);
			}

			public function action(): Response{
				return $this->respond(
					new ActionPayload(
						202,
						[
							'willBeDoneAt' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM),
						]
					)
				);
			}
		};

		$app->get('/test-action-response-code', $testAction);
		$request = $this->createRequest('GET', '/test-action-response-code');
		$response = $app->handle($request);

		Assert::assertEquals(202, $response->getStatusCode());
	}

	public function testActionSetsHttpCodeRespondData(): void{
		$app = $this->getAppInstance();
		$container = $app->getContainer();
		$logger = $container->get(LoggerInterface::class);

		$testAction = new class($logger) extends Action {
			public function __construct(
				LoggerInterface $loggerInterface
			){
				parent::__construct($loggerInterface);
			}

			public function action(): Response{
				return $this->respondWithData(
					[
						'willBeDoneAt' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM),
					],
					202
				);
			}
		};

		$app->get('/test-action-response-code', $testAction);
		$request = $this->createRequest('GET', '/test-action-response-code');
		$response = $app->handle($request);

		Assert::assertEquals(202, $response->getStatusCode());
	}
}
