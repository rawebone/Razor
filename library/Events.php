<?php

namespace Razor;

use Rawebone\Injector\Injector;

class Events
{
	protected $injector;
	protected $registered = array();

	public function __construct(Injector $injector)
	{
		$this->injector = $injector;
	}

	public function register($name, callable $delegate)
	{
		$this->registered[$name] = $delegate;
	}

	public function fire($name)
	{
		if (!isset($this->registered[$name])) {
			throw new UnknownEventException($name);
		}

		$this->injector->inject($this->registered[$name]);
	}
}
