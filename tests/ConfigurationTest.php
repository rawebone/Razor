<?php

namespace Razor\Tests;

use Razor\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaultConfigurationOptions()
	{
		$configuration = new Configuration();

		$this->assertEquals(false, $configuration->testing);
		$this->assertEquals(false, $configuration->development);
	}
}
 