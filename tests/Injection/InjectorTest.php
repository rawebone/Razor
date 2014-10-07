<?php

namespace Razor\Tests\Injection;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\Injection\Injector;

class InjectorTest extends ProphecyTestCase
{
	/**
	 * @expectedException \InvalidArgumentException
	 */
	function testRequiresFailsWithNonCallable()
	{
		$injector = new Injector();
		$injector->requires("abc");
	}

	function testRequiresForFunction()
	{
		$injector = new Injector();
		$required = $injector->requires(function ($a, $b) {});

		$this->assertEquals(array("a", "b"), $required);
	}

	function testRequiresForMethod()
	{
		$injector = new Injector();
		$required = $injector->requires(array(new TestRequiresForMethodStub(), "test"));

		$this->assertEquals(array("a", "b"), $required);
	}
}

class TestRequiresForMethodStub
{
	public function test($a, $b)
	{
	}
}
