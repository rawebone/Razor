<?php

namespace Razor\Tests;

use Razor\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
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

		$this->assertInstanceOf('Razor\ServiceResolver', $env->services());
	}
}
 