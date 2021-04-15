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
}
