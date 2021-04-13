<?php
declare(strict_types=1);

namespace App\Domain\Payment;

use App\Infrastructure\Persistence\DynamoInterface;
use App\Infrastructure\Persistence\DynamoUtils;
use JsonSerializable;
use ReflectionClass;

class Payment implements DynamoInterface, JsonSerializable {
	private const PAYMENT_INTENT = 'payment_intent';
	private const AMOUNT = 'amount';
	private const CURRENCY = 'currency';
	private const PAYMENT_SECRET = 'payment_secret';

	private string $payment_intent;

	private string $payment_secret;

	private int $amount;

	private string $currency;

	private ?int $created;

	private function __construct(){
	}

	private function set(string $payment_secret, int $amount, string $currency): self{
		$this->payment_secret = $payment_secret;
		$this->payment_intent = self::idFromSecret($payment_secret);
		$this->amount = $amount;
		$this->currency = $currency;

		return $this;
	}

	public static function create(string $payment_secret, int $amount, string $currency): self{
		$self = new self();
		$self->set($payment_secret, $amount, $currency);
		$self->setCreated(time());

		return $self;
	}

	private static function idFromSecret(string $payment_secret){
		return explode('_secret_', $payment_secret)[0];
	}

	public function getPaymentSecret(): string{
		return $this->payment_secret;
	}

	public function getPaymentIntent(): string{
		return $this->payment_intent;
	}

	public function getAmount(): int{
		return $this->amount;
	}

	public function getCurrency(): string{
		return $this->currency;
	}

	public function jsonSerialize(){
		return [
			self::PAYMENT_SECRET => $this->payment_secret,
			self::AMOUNT => $this->amount,
			self::CURRENCY => $this->currency,
		];
	}

	public function output(): array{
		$arr = get_object_vars($this);

		// This can just be a database level field
		// or we can implement it on the read model in future
		$arr['last_updated'] = time();

		return $arr;
	}

	public static function tableName(): string{
		return 'serverless-payments';
	}

	public static function hashName(): string{
		return self::PAYMENT_INTENT;
	}

	public static function rangeName(): ?string{
		return null;
	}

	public static function hydrate(array $item): self{
		$class = new ReflectionClass(self::class);
		$properties = $class->getProperties();

		$self = new self;

		foreach ($properties as $param){
			$name = $param->name;
			$type = $param->getType()->getName();
			if (!isset($item[$name])){
				continue;
			}

			$val = reset($item[$name]);
			if (!$val){
				continue;
			}

			settype($val, $type);
			$self->$name = $val;
		}

		return $self;
	}

	private function setCreated(int $time): self{
		$this->created = $time;

		return $this;
	}
}
