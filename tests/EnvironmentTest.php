<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\Environment;

class EnvironmentTest extends ProphecyTestCase
{
	public function testDefaults()
	{
		$env = new Environment();

		$this->assertEquals(true, $env->development, "Environment::\$development default has changed");
		$this->assertEquals(false, $env->testing, "Environment::\$testing default has changed");
	}

	public function testServiceContainer()
	{
		$env = new Environment();

		$this->assertInstanceOf('Rawebone\Injector\RegisterResolver', $env->services());
	}

	public function testProviderRegistration()
	{
		$env = new Environment();

		$provider = $this->prophesize('Razor\Provider');
		$provider->letResolverBe($env->services())->shouldBeCalled();
		$provider->register()->shouldBeCalled();

		$env->registerProvider($provider->reveal());
	}
}
 