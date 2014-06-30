<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\Middleware;

class MiddlewareTest extends ProphecyTestCase
{
	public function testInvocationWithAClosureDelegate()
	{
		$delegate = function () { };

		/** @var \Rawebone\Injector\Injector|\Prophecy\Prophecy\ObjectProphecy $injector */
		$injector = $this->prophesize('Rawebone\Injector\Injector');
		$injector->inject($delegate)->willReturn(true);

		$middleware = new Middleware($delegate);
		$middleware->letInjectorBe($injector->reveal());

		$this->assertEquals(true, $middleware());
	}

	public function testInvocationWithAMiddlewareDelegate()
	{
		$delegate = new Middleware(function () { });

		/** @var \Rawebone\Injector\Injector|\Prophecy\Prophecy\ObjectProphecy $injector */
		$injector = $this->prophesize('Rawebone\Injector\Injector');
		$injector->inject($delegate)->willReturn(true);

		$middleware = new Middleware($delegate);
		$middleware->letInjectorBe($injector->reveal());

		$this->assertEquals(true, $middleware());
	}
}
 