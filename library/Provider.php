<?php

namespace Razor;

use Rawebone\Injector\RegisterResolver;

/**
 * Provider allows for the registration of services to be packaged, in turn
 * allowing for extensions to be easily dropped into projects.
 *
 * @package Razor
 */
abstract class Provider
{
	/**
	 * The resolver which should be used to register services.
	 *
	 * @var \Rawebone\Injector\RegisterResolver
	 */
	private $resolver;

	/**
	 * Returns the current resolver.
	 *
	 * @return RegisterResolver
	 */
	public function resolver()
	{
		return $this->resolver;
	}

	/**
	 * Sets the resolver to be used.
	 *
	 * @param RegisterResolver $resolver
	 */
	public function letResolverBe(RegisterResolver $resolver)
	{
		$this->resolver = $resolver;
	}

	/**
	 * Registers the services exposed by the provider.
	 *
	 * @return void
	 */
	abstract public function register();
}
