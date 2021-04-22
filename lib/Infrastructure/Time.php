<?php

namespace App\Infrastructure;

class Time {
	private static ?int $time;

	public static function time() :int{
		return self::$time ?? time();
	}

	public static function test(int $time) :void{
		self::$time = $time;
	}
}
