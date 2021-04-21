<?php

use App\Application\Settings\Env;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

require __DIR__.'/../vendor/autoload.php';

Env::tests([
	Env::DYNAMO_TABLE => 'test',
	Env::CURRENCY => 'gbp',
]);

const FIXED_UUID_V4 = 'abc123';

$factory = new class extends UuidFactory {
	public function uuid4(): UuidInterface{
		return new class extends Uuid {
			/** @noinspection MagicMethodsValidityInspection */
			public function __construct(){
			}

			public function toString(): string{
				return FIXED_UUID_V4;
			}
		};
	}
};
Uuid::setFactory($factory);
