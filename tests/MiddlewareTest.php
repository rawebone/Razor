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

		$middleware->letInjectorBe($injector->reveal());

		$this->assertEquals(true, $middleware());
	}

	public function testLetInjectorInstanceIsPassedOnToOtherMiddleware()
	{
		$injector = $this->prophesize('Rawebone\Injector\Injector');
		$delegate = $this->prophesize('Razor\Middleware');
		$delegate->letInjectorBe($injector->reveal())->shouldBeCalled();

		$middleware = new Middleware($delegate->reveal());
		$middleware->letInjectorBe($injector->reveal());
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
 