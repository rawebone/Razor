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
	/**
	 * The Middleware/Delegate function to be invoked
	 * after this Middleware is invoked.
	 *
	 * @var callable
	 */
	protected $delegate;

	/**
	 * The Injector instance to be used for calling the delegate.
	 *
	 * @var Injector
	 */
	protected $injector;

	public function __construct(callable $delegate)
	{
		$this->delegate = $delegate;
	}

	/**
	 * Invokes the wrapped delegate, children should
	 * override this method to add behaviour. It can
	 * have any services required added to its signature
	 * for injection.
	 *
	 * @return mixed|object
	 */
	public function __invoke()
	{
		return $this->invokeDelegate();
	}

	/**
	 * Helper to call the injection system on the next
	 * delegate.
	 *
	 * @return mixed|object
	 */
	public function invokeDelegate()
	{
		return $this->injector->inject($this->delegate);
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

	/**
	 * Sets the instance of the Injector to be used during
	 * dispatch.
	 *
	 * @param Injector $injector
	 */
	public function letInjectorBe(Injector $injector)
	{
		if ($this->delegate instanceof Middleware) {
			$this->delegate->letInjectorBe($injector);
		}

		$this->injector = $injector;
	}
}
