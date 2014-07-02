<?php

namespace Razor;

use Rawebone\Injector\Injector;

/**
 * Middleware provides the ability for users to intercept
 * the current request/response cycle.
 *
 * @package Razor
 */
class Middleware
{
	protected $delegate;

	public function __construct(callable $delegate)
	{
		$this->delegate = $delegate;
	}

	/**
	 * Invokes the wrapped delegate, children should
	 * override this method to add behaviour.
	 *
	 * @param Injector $injector
	 * @return mixed|object
	 */
	public function __invoke(Injector $injector)
	{
		return $injector->inject($this->delegate);
	}

	/**
	 * Provides us with access to the underlying delegate for
	 * direct testing.
	 *
	 * @return callable
	 */
	public function unwrap()
	{
		if ($this->delegate instanceof Middleware) {
			return $this->delegate->unwrap();
		} else {
			return $this->delegate;
		}
	}
}
