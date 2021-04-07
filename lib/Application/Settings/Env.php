<?php
namespace App\Application\Settings;

class Env {
	public const STRIPE_PRIVATE = 'STRIPE_PRIVATE';
	public const ORIGIN = 'ORIGIN';
	public const PROD = 'PROD';

	public static function isProd() :bool {
		return (bool) ($_ENV[self::PROD] ?? false);
	}

	public static function getOrigin() :string {
		return $_ENV[self::ORIGIN];
	}

	public static function getStripeKey() :string{
		return $_ENV[self::STRIPE_PRIVATE];
	}
}
