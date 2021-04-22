<?php


namespace App\Domain;

use ReflectionClass;

trait EntityHydrate {
	public static function hydrate(array $item): self{
		$class = new ReflectionClass(self::class);
		$properties = $class->getProperties();

		$self = new self;

		foreach ($properties as $param){
			$name = $param->name;
			if (!isset($item[$name])){
				continue;
			}

			$val = reset($item[$name]);
			if (!$val){
				continue;
			}
			$type = $param->getType();
			if ($type!==null){
				$type_name = $type->getName();
				settype($val, $type_name);
			}
			$self->$name = $val;
		}

		return $self;
	}
}
