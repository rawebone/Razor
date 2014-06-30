<?php

namespace Razor;

use Rawebone\Injector\Injector;

/**
 * Middleware provides the basic mechanism for intercepting method
 * requests as they are being dispatched. This makes adding generic
 * functionality cheap and easy.
 *
 * @package Razor
 */
class Middleware
{
	protected $delegate;

	/**
	 * @var \Rawebone\Injector\Injector
	 */
	protected $injector;

	public function __construct(callable $delegate)
	{
		$this->delegate = $delegate;
	}

	/**
	 * This method should be overridden by the child object
	 * to provide the handling required.
	 *
	 * @return mixed
	 */
	public function __invoke()
	{
		return $this->invokeTarget();
	}

	/**
	 * Helper to make sure the delegate is correctly invoked.
	 *
	 * @return mixed
	 */
	public function invokeTarget()
	{
		if ($this->delegate instanceof Middleware) {
			$this->delegate->letInjectorBe($this->injector);
		}

		return $this->injector->inject($this->delegate);
	}

	public function letInjectorBe(Injector $injector)
	{
		$this->injector = $injector;
	}
}
