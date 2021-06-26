<?php
namespace App\Application\Settings;

class Env {
	public const STRIPE_PRIVATE = 'STRIPE_PRIVATE';
	public const ORIGIN = 'ORIGIN';
	public const PROD = 'PROD';
	public const AWS_REGION = 'AWS_REGION';
	public const DYNAMO_ENDPOINT = 'DYNAMO_ENDPOINT';
	public const CURRENCY = 'REACT_APP_CURRENCY';
	public const DYNAMO_TABLE = 'DYNAMO_TABLE';

	public static function isProd() :bool {
		return (bool) ($_ENV[self::PROD] ?? false);
	}

	public static function getOrigin() :string {
		return $_ENV[self::ORIGIN];
	}

	public static function getStripeKey() :string{
		return $_ENV[self::STRIPE_PRIVATE];
	}

	public static function getAwsRegion() :string{
		return $_ENV[self::AWS_REGION];
	}

	public static function getDynamoEndpoint() :?string{
		return $_ENV[self::DYNAMO_ENDPOINT] ?? null;
	}

	public static function getCurrency() :string{
		return $_ENV[self::CURRENCY];
	}

	public static function getDynamoTable() :string{
		return $_ENV[self::DYNAMO_TABLE].'-'.(self::isProd() ? 'production' : 'development');
	}

	public static function tests(array $test_env) :void{
		$_ENV = $test_env;
	}
}
