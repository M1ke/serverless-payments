<?php
declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\DomainException\DomainRecordNotFoundException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Action {
	protected LoggerInterface $logger;

	protected ServerRequestInterface $request;

	protected ResponseInterface $response;

	protected array $args;

	/**
	 * @param LoggerInterface $logger
	 */
	public function __construct(LoggerInterface $logger){
		$this->logger = $logger;
	}

	/**
	 * @throws HttpNotFoundException
	 * @throws HttpBadRequestException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface{
		$this->request = $request;
		$this->response = $response;
		$this->args = $args;

		try {
			return $this->action();
		}
		catch (DomainRecordNotFoundException $e) {
			throw new HttpNotFoundException($this->request, $e->getMessage());
		}
	}

	/**
	 * @throws DomainRecordNotFoundException
	 * @throws HttpBadRequestException
	 */
	abstract protected function action(): ResponseInterface;

	/**
	 * @return array|object
	 * @throws HttpBadRequestException
	 */
	protected function getFormData(){
		try {
			$input = json_decode(file_get_contents('php://input'), false, 512, JSON_THROW_ON_ERROR);
		}
		catch (JsonException $e) {
			throw new HttpBadRequestException($this->request, 'Malformed JSON input.');
		}

		return $input;
	}

	/**
	 * @return mixed
	 * @throws HttpBadRequestException
	 */
	protected function resolveArg(string $name){
		if (!isset($this->args[$name])){
			throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
		}

		return $this->args[$name];
	}

	/**
	 * @param array|object|null $data
	 * @return ResponseInterface
	 */
	protected function respondWithData($data = null, int $statusCode = 200): ResponseInterface{
		$payload = new ActionPayload($statusCode, $data);

		return $this->respond($payload);
	}

	protected function respond(ActionPayload $payload): ResponseInterface{
		$json = json_encode($payload, JSON_PRETTY_PRINT);
		$this->response->getBody()->write($json);

		return $this->response
			->withHeader('Content-Type', 'application/json')
			->withStatus($payload->getStatusCode());
	}
}
