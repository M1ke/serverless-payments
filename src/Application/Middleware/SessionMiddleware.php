<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface {
	/**
	 * {@inheritdoc}
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
		if (isset($_SERVER['HTTP_AUTHORIZATION'])){
			session_start();
			$request = $request->withAttribute('session', $_SESSION);
		}

		return $handler->handle($request);
	}
}
