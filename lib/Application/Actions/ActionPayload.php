<?php
declare(strict_types=1);

namespace App\Application\Actions;

use JsonSerializable;

class ActionPayload implements JsonSerializable {

	private int $statusCode;

	/**
	 * @var array|object|null
	 */
	private $data;

	private ?ActionError $error;

	/**
	 * @param array|object|null $data
	 */
	public function __construct(
		int $statusCode = 200,
		$data = null,
		?ActionError $error = null
	){
		$this->statusCode = $statusCode;
		$this->data = $data;
		$this->error = $error;
	}

	public function getStatusCode(): int{
		return $this->statusCode;
	}

	/**
	 * @return array|null|object
	 */
	public function getData(){
		return $this->data;
	}

	public function getError(): ?ActionError{
		return $this->error;
	}

	public function jsonSerialize(): array{
		$status = $this->statusCode;
		$payload = [
			'statusCode' => $status,
			// Inspired by JS Fetch command that always returns
			// a bool "ok" that's true for any code other than
			// 4xx or 5xx. Makes it easy for a consumer to always
			// check a simple bool value rather than existence of
			// an error field or otherwise
			'ok' => !($status>=400 && $status<=599),
		];

		if ($this->data!==null){
			$payload['data'] = $this->data;
		}
		elseif ($this->error!==null) {
			$payload['error'] = $this->error;
		}

		return $payload;
	}
}
