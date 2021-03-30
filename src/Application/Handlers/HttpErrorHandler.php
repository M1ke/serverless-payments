<?php
declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;
use Throwable;

class HttpErrorHandler extends ErrorHandler {
	/**
	 * @inheritdoc
	 */
	protected function respond(): ResponseInterface{
		$exception = $this->exception;
		$statusCode = 500;
		$error = new ActionError(
			ActionError::SERVER_ERROR,
			'An internal error has occurred while processing your request.'
		);

		if ($exception instanceof HttpException){
			$statusCode = (int) $exception->getCode();
			$error->setDescription($exception->getMessage());

			if ($exception instanceof HttpNotFoundException){
				$error->setType(ActionError::RESOURCE_NOT_FOUND);
			}
			elseif ($exception instanceof HttpMethodNotAllowedException) {
				$error->setType(ActionError::NOT_ALLOWED);
			}
			elseif ($exception instanceof HttpUnauthorizedException) {
				$error->setType(ActionError::UNAUTHENTICATED);
			}
			elseif ($exception instanceof HttpForbiddenException) {
				$error->setType(ActionError::INSUFFICIENT_PRIVILEGES);
			}
			elseif ($exception instanceof HttpBadRequestException) {
				$error->setType(ActionError::BAD_REQUEST);
			}
			elseif ($exception instanceof HttpNotImplementedException) {
				$error->setType(ActionError::NOT_IMPLEMENTED);
			}
		}

		if (
			!($exception instanceof HttpException)
			&& $this->displayErrorDetails
		){
			$error->setDescription($exception->getMessage());
		}

		$payload = new ActionPayload($statusCode, null, $error);
		$encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

		$response = $this->responseFactory->createResponse($statusCode);
		$response->getBody()->write($encodedPayload);

		return $response->withHeader('Content-Type', 'application/json');
	}
}
