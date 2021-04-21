<?php

namespace App\Infrastructure;

class Time {
	/** @var int */
	private static $time;

	public static function time() :int{
		return self::$time ?? time();
	}

	public static function test(int $time) :void{
		self::$time = $time;
	}
}
