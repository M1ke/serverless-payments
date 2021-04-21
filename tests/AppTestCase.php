<?php
declare(strict_types=1);

namespace Tests;

use DI\ContainerBuilder;
use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;
use function DI\autowire;

class AppTestCase extends PHPUnit_TestCase {
	protected function getAppInstance(array $definitions = []): App{
		// Instantiate PHP-DI ContainerBuilder
		$containerBuilder = new ContainerBuilder();

		// Container intentionally not compiled for tests.

		$containerBuilder->addDefinitions(array_merge([
			LoggerInterface::class => autowire(NullLogger::class),
		], $definitions));

		// Build PHP-DI Container instance
		try {
			$container = $containerBuilder->build();
		}
		catch (Exception $e) {
			Assert::fail("Failed to create the application: {$e->getMessage()}");
		}

		// Instantiate the app
		AppFactory::setContainer($container);
		$app = AppFactory::create();

		// Register middleware
		$middleware = require __DIR__.'/../app/middleware.php';
		$middleware($app);

		// Register routes
		$routes = require __DIR__.'/../app/routes.php';
		$routes($app);

		return $app;
	}

	protected function createRequest(
		string $method,
		string $path,
		?StreamInterface $stream = null
	): ServerRequestInterface{
		$uri = new Uri('', '', 80, $path);
		if (!$stream){
			$handle = fopen('php://temp', 'wb+');
			$stream = (new StreamFactory())->createStreamFromResource($handle);
		}

		$h = new Headers();

		return new SlimRequest($method, $uri, $h, [], [], $stream);
	}
}
