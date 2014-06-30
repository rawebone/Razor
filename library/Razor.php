<?php

namespace Razor;

class Razor
{
	/**
	 * @var \Razor\Kernel
	 */
	protected static $kernel;

	public static function kernelId()
	{
		return spl_object_hash(self::$kernel);
	}

	public static function reset()
	{
		self::$kernel = new Kernel();
	}

	public static function abort()
	{
		throw new HttpAbortException();
	}

	public static function event($name, callable $delegate)
	{
		self::$kernel->events->register($name, $delegate);
	}

	public static function fire($name)
	{
		self::$kernel->events->fire($name);
	}

	public static function services(array $services)
	{
		foreach ($services as $name => $service) {
			self::$kernel->resolver->registerService($name, $service);
		}
	}

	public static function option($name, $value)
	{
		$configuration = self::$kernel->configuration;

		if (property_exists($configuration, $name)) {
			$configuration->$name = $value;
		}
	}
}
