<?php
declare(strict_types=1);

use App\Application\Actions\Payments\CreatePaymentAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return static function (App $app){
	// This prefix must match the "PathPattern" in serverless.yml CacheBehaviors
	$app->group('/api', function (Group $group){
		$group->get('/', function (ServerRequestInterface $request, ResponseInterface $response){
			// Send a no content response so people know we're here
			return $response->withStatus(204);
		});

		$group->options('/{routes:.*}', function (ServerRequestInterface $request, ResponseInterface $response){
			// CORS Pre-Flight OPTIONS Request Handler
			return $response;
		});

		$group->post('/create', CreatePaymentAction::class);
	});
};
