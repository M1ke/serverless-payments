<?php
declare(strict_types=1);

use App\Application\Actions\Payments\CreatePaymentAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return static function (App $app){
	$app->options('/{routes:.*}', function (ServerRequestInterface $request, ResponseInterface $response){
		// CORS Pre-Flight OPTIONS Request Handler
		return $response;
	});

	$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response){
		$response->getBody()->write('Hello world!');

		return $response;
	});

	$app->post('/create', CreatePaymentAction::class);

	$app->group('/users', function (Group $group){
		$group->get('', ListUsersAction::class);
		$group->get('/{id}', ViewUserAction::class);
	});
};
