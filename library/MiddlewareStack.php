<?php

namespace Razor;

use Razor\Injection\Injector;

class MiddlewareStack
{
	protected $injector;
	protected $middleware;
	protected $pos;

	public function __construct(array $middleware, Injector $injector)
	{
		$this->injector = $injector->instance("next", $this);
		$this->middleware = $middleware;
		$this->pos = 0;
	}

	public function __invoke()
	{
		if (!isset($this->middleware[$this->pos])) {
			return null;
		}

		return $this->injector->inject($this->middleware[$this->pos++]);
	}
}
