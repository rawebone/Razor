<?php

namespace Razor;

use Razor\Injection\Injector;

/**
 * Provider instances register services with an Injector instance to expose
 * them to an End Point.
 *
 * @package Razor
 */
interface Provider
{
	/**
	 * @param Injector $injector
	 * @return void
	 */
	function register(Injector $injector);
}
