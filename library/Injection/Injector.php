<?php

namespace Razor\Injection;

/**
 * Injector provides a simple Service Injection mechanism. Service Injection
 * allows us to define dependencies easily while
 *
 * @package Razor\Injection
 */
class Injector
{

	public function requires($fn)
	{
		if (!is_callable($fn)) {
			throw new \InvalidArgumentException(__CLASS__ . "::" . __FUNCTION__ . " expects parameter \$fn to be callable");
		}

		if (is_array($fn)) {
			$reflection = new \ReflectionMethod($fn[0], $fn[1]);
		} else {
			$reflection = new \ReflectionFunction($fn);
		}

		$required = array();

		foreach ($reflection->getParameters() as $parameter) {
			$required[] = $parameter->getName();
		}

		return $required;
	}
}
