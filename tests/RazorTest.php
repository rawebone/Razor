<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\EndPoint;
use Razor\Environment;
use Razor\Razor;

class RazorTest extends ProphecyTestCase
{
	public function testEnvironmentYeildsDefault()
	{
		$this->assertInstanceOf(
			'Razor\Environment',
			Razor::environment()
		);
	}

	public function testSettingOfEnvironment()
	{
		$env = new Environment();

		Razor::environment($env);
		$this->assertEquals($env, Razor::environment());
	}

	public function testRun()
	{
		$env = $this->prophesize('Razor\Environment');
		$env->testing = true;
		$env->services()->shouldNotBeCalled();

		Razor::environment($env->reveal());

		Razor::run(new EndPoint());
	}

	public function testRunStoresLastEndPoint()
	{
		$env = $this->prophesize('Razor\Environment');
		$env->testing = true;
		$env->services()->shouldNotBeCalled();

		Razor::environment($env->reveal());

		$ep = new EndPoint();
		Razor::run($ep);

		$this->assertEquals($ep, Razor::endPoint());
	}
}
 