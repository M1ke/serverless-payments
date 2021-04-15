<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);

namespace App\Domain\Payment;

use App\Application\Settings\Env;
use App\Domain\EntityHydrate;
use App\Infrastructure\Persistence\DynamoInterface;
use JsonSerializable;
use Ramsey\Uuid\Guid\Guid;

class Payment implements DynamoInterface, JsonSerializable {
	use EntityHydrate;

	private const ID = 'id';
	private const AMOUNT = 'amount';
	private const DESCRIPTION = 'description';
	private const CURRENCY = 'currency';

	private string $id;

	private string $description;

	private int $amount;

	private string $currency;

	private int $created;

	private int $paid = 0;

	private function __construct(){
	}

	public static function create(float $amount, string $description): self{
		$self = new self;

		$self->id = Guid::uuid4()->toString();
		$self->amount = (int) round($amount * 100);
		$self->currency = Env::getCurrency();
		$self->description = $description;
		$self->created = time();

		return $self;
	}

	public function getAmount(): int{
		return $this->amount;
	}

	public function getCurrency(): string{
		return $this->currency;
	}

	public function getCreated(): int{
		return $this->created;
	}

	public function getDescription(): ?string{
		return $this->description;
	}

	public function jsonSerialize(){
		return [
			self::ID => $this->id,
			self::AMOUNT => $this->amount,
			self::DESCRIPTION => $this->description,
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
		return self::ID;
	}

	public static function rangeName(): ?string{
		return null;
	}

	public function getId() :string{
		return $this->id;
	}
}
