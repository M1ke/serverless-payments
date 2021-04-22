<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use Aws\DynamoDb\Marshaler;
use ReflectionClass;

class DynamoUtils {

	public static function marshalItem(DynamoInterface $entity): array{
		$marshaler = new Marshaler();
		$item = $entity->output();

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

		/**
		 * @var DynamoInterface $entity
		 * @psalm-var class-string $entity
		 */
		$hash_name = $entity::hashName();

		$params = [
			'TableName' => $entity::tableName(),
			'KeyConditionExpression' => '#hash=:hash',
			'ExpressionAttributeNames' => ['#hash' => $hash_name],
			'ExpressionAttributeValues' => $eav,
		];

		return self::addRangeKey($entity, $params, $range_key);
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
	 * @psalm-param ?scalar $range_key
	 */
	private static function addRangeKey(string $entity, array $params, $range_key = null): array{
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
	public static function properties(string $entity, array $item): array{
		try {
			$class = new ReflectionClass($entity);
		}
		catch (\ReflectionException $e) {
			return [];
		}
		$properties = $class->getProperties();

		foreach ($properties as $param){
			$name = $param->name;
			$type = $param->getType();
			$val = reset($item[$name]);
			if ($type!==null){
				$type_name = $type->getName();
				settype($val, $type_name);
			}

			$arr[] = $val;
		}

		return $arr;
	}
}
