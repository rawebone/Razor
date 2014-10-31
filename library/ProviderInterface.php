<?php

namespace Razor;

use Razor\Injection\Injector;

/**
 * ProviderInterface instances register services with an Injector instance to expose
 * them to an End Point.
 *
 * @package Razor
 */
interface ProviderInterface
{
	/**
	 * @param Injector $injector
	 * @return void
	 */
	function register(Injector $injector);
}
