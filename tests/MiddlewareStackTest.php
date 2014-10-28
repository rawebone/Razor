<?php

namespace Razor\Tests;

use Razor\Injection\Injector;
use Razor\MiddlewareStack;

class MiddlewareStackTest extends \PHPUnit_Framework_TestCase
{
	public function testStackExhausted()
	{
		$stack = new MiddlewareStack(array(), new Injector());
		$this->assertEquals(null, $stack());
	}

	public function testStackExecution()
	{
		$middleware = array(
			function ($next) { return $next() . "1"; },
			function ($next) { return $next() . "2"; },
			function () { return "a"; }
		);

		$stack = new MiddlewareStack($middleware, new Injector());
		$this->assertEquals("a21", $stack());
	}
}
 