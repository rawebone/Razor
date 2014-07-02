<?php

namespace Razor\Tests;

use Razor\Middleware;
use Prophecy\PhpUnit\ProphecyTestCase;

class MiddlewareTest extends ProphecyTestCase
{
	public function testInvocation()
	{
		$func = function () { };
		$middleware = new Middleware($func);

		$injector = $this->prophesize('Rawebone\Injector\Injector');
		$injector->inject($func)->willReturn(true);

		$this->assertEquals(true, $middleware($injector->reveal()));
	}

	public function testBasicUnwrapping()
	{
		$func = function () { };
		$middleware = new Middleware($func);

		$this->assertEquals($func, $middleware->unwrap());
	}

	public function testAdvancedUnwrapping()
	{
		$func = function () { };
		$middleware = new Middleware(new Middleware($func));

		$this->assertEquals($func, $middleware->unwrap());
	}
}
 