<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Aws\DynamoDb\Marshaler;
use JsonSerializable;
use ReflectionClass;

class DynamoUtils {

	public static function marshalItem(JsonSerializable $entity): array{
		$marshaler = new Marshaler();
		$item = $entity->jsonSerialize();

		return $marshaler->marshalItem($item);
	}

	public static function insertParams(DynamoInterface $entity): array{
		$item_marshal = self::marshalItem($entity);

		return ['TableName' => $entity->tableName(), 'Item' => $item_marshal];
	}

	/**
	 * @psalm-param class-string $entity
	 */
	public static function findParams(string $entity, string $hash_key, ?string $range_key = null): array{
		$eav = self::marshallEav($hash_key, $range_key);

		/** @var DynamoInterface $entity */
		$hash_name = $entity::hashName();

		$params = [
			'TableName' => $entity::tableName(),
			'KeyConditionExpression' => '#hash=:hash',
			'ExpressionAttributeNames' => ['#hash' => $hash_name],
			'ExpressionAttributeValues' => $eav,
		];

		return self::addRangeKey($entity, $range_key, $params);
	}

	private static function marshallEav(string $hash_key, ?string $range_key): array{
		$marshaler = new Marshaler();
		$to_marshal = [':hash' => $hash_key];
		if (!empty($range_key)){
			$to_marshal[':range'] = $range_key;
		}

		return $marshaler->marshalItem($to_marshal);
	}

	/**
	 * @psalm-param class-string $entity
	 */
	private static function addRangeKey(string $entity, ?string $range_key, array $params): array{
		if (!$range_key){
			return $params;
		}

		/** @var DynamoInterface $entity */
		$range_name = $entity::rangeName();

		$params['KeyConditionExpression'] .= is_string($range_key) ? ' and begins_with(#range, :range)' : ' and #range = :range';
		$params['ExpressionAttributeNames']['#range'] = $range_name;

		return $params;
	}

	/**
	 * @psalm-param class-string $entity
	 */
	public static function construct(string $entity, array $item): array{
		try {
			$class = new ReflectionClass($entity);
		}
		catch (\ReflectionException $e) {
			return [];
		}
		$constructor = $class->getConstructor();
		if (!$constructor){
			return [];
		}

		$params = $constructor->getParameters();

		$arr = [];
		foreach ($params as $param){
			$name = $param->name;
			$type = $param->getType()->getName();
			$val = reset($item[$name]);
			settype($val, $type);
			$arr[] = $val;
		}

		return $arr;
	}
}
