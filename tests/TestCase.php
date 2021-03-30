<?php
declare(strict_types=1);

namespace Tests;

use DI\ContainerBuilder;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

class TestCase extends PHPUnit_TestCase {
	/**
	 * @throws Exception
	 */
	protected function getAppInstance(): App{
		// Instantiate PHP-DI ContainerBuilder
		$containerBuilder = new ContainerBuilder();

		// Container intentionally not compiled for tests.

		// Set up settings
		$settings = require __DIR__.'/../app/settings.php';
		$settings($containerBuilder);

		// Set up dependencies
		$dependencies = require __DIR__.'/../app/dependencies.php';
		$dependencies($containerBuilder);

		// Set up repositories
		$repositories = require __DIR__.'/../app/repositories.php';
		$repositories($containerBuilder);

		// Build PHP-DI Container instance
		$container = $containerBuilder->build();

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
		array $headers = ['HTTP_ACCEPT' => 'application/json'],
		array $cookies = [],
		array $serverParams = []
	): ServerRequestInterface{
		$uri = new Uri('', '', 80, $path);
		$handle = fopen('php://temp', 'wb+');
		$stream = (new StreamFactory())->createStreamFromResource($handle);

		$h = new Headers();
		foreach ($headers as $name => $value){
			$h->addHeader($name, $value);
		}

		return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
	}
}
