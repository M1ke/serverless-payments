<?php
declare(strict_types=1);

namespace App\Domain\Payment;

use App\Infrastructure\Persistence\DynamoInterface;
use App\Infrastructure\Persistence\DynamoUtils;

class Payment implements DynamoInterface {
	private const PAYMENT_INTENT = 'payment_intent';
	private const AMOUNT = 'amount';
	private const CURRENCY = 'currency';

	private string $payment_intent;

	private int $amount;

	private string $currency;

	public function __construct(string $payment_intent, int $amount, string $currency){
		$this->payment_intent = $payment_intent;
		$this->amount = $amount;
		$this->currency = $currency;
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
			self::PAYMENT_INTENT => $this->payment_intent,
			self::AMOUNT => $this->amount,
			self::CURRENCY => $this->currency,
			'last_updated' => time(),
		];
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
		$params = DynamoUtils::construct(self::class, $item);

		return new self(...$params);
	}
}
