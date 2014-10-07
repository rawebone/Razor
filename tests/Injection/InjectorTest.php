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

	function testRegisterServiceAndDefined()
	{
		$injector = new Injector();
		$injector->service("a", function () {});
		$this->assertEquals(true, $injector->defined("a"));
	}

	function testResolveService()
	{
		$injector = new Injector();
		$injector->service("a", function () { return true; });
		$this->assertEquals(true, $injector->resolve("a"));
	}

	/**
	 * @expectedException \LogicException
	 */
	function testResolveErrorsWhenCycling()
	{
		$injector = new Injector();
		$injector->service("a", function ($a) {});
		$injector->resolve("a");
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	function testResolveFailsWhenServiceNotDefined()
	{
		$injector = new Injector();
		$injector->service("a", function ($b) {});
		$injector->resolve("a");
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	function testServiceFailsDueToBadFn()
	{
		$injector = new Injector();
		$injector->service("test", null);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	function testServiceFailsDueToResolvedService()
	{
		$injector = new Injector();
		$injector->service("a", function () { return true; });
		$this->assertEquals(true, $injector->resolve("a"));
		$injector->service("a", function () {});
	}

	function testInjection()
	{
		$self = $this;
		$injector = new Injector();

		$injector->service("a", function () use ($self) { return $self; });
		$injector->inject(function ($a) use ($self)
		{
			$self->assertEquals($self, $a);
		});
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	function testInjectionFailsWithANonCallable()
	{
		$injector = new Injector();
		$injector->inject(null);
	}

	function testResolutionWithExtension()
	{
		$injector = new Injector();
		$injector->service("a", function () { return new \stdClass(); });
		$injector->extend("a", function ($a) { $a->extended = 1; });
		$injector->extend("a", function ($a) { $a->extended = 2; });

		$resolved = $injector->resolve("a");
		$this->assertEquals(true, isset($resolved->extended));
		$this->assertEquals(2, $resolved->extended);
	}
}

class TestRequiresForMethodStub
{
	public function test($a, $b)
	{
	}
}
