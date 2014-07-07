<?php

namespace Razor;

class Razor
{
	protected static $environment;
	protected static $endPoint;

	/**
	 * Returns or sets the current environment.
	 *
	 * @param Environment $environment
	 * @return Environment
	 */
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

	/**
	 * Returns the last End Point that was ran.
	 *
	 * @return EndPoint|null
	 */
	public static function endPoint()
	{
		return static::$endPoint;
	}

	/**
	 * Runs the EndPoint.
	 *
	 * @param EndPoint $endPoint
	 */
	public static function run(EndPoint $endPoint)
	{
		static::$endPoint = $endPoint;

        $dispatcher = new Dispatcher();
		$dispatcher->dispatch(static::environment(), $endPoint);
	}
}
