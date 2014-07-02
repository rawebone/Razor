<?php

namespace Razor;

class Razor
{
	protected static $environment;
	protected static $endPoint;

	public static function environment(Environment $environment = null)
	{
		if ($environment) {
			self::$environment = $environment;
		}

		if (!self::$environment) {
			self::$environment = new Environment();
		}

		return self::$environment;
	}

	public static function endPoint()
	{
		return static::$endPoint;
	}

	public static function run(EndPoint $endPoint)
	{
		static::$endPoint = $endPoint;

		(new Dispatcher(static::environment(), $endPoint));
	}
}
