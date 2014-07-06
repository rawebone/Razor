<?php

namespace Razor\Tests;

use Razor\EndPoint;

class EndPointTest extends \PHPUnit_Framework_TestCase
{
	public function testAssignAndRetrieve()
	{
		$func = function () { };

		$ep = new EndPoint();
		$ep->get($func);

		$this->assertEquals($func, $ep->get());
	}

	public function testCreateByStatic()
	{
		$this->assertInstanceOf('Razor\EndPoint', EndPoint::create());
	}

	public function testOnErrorYieldsInjectable()
	{
		$this->assertEquals(true, is_callable(EndPoint::create()->onError()));
	}

	public function testOnNotFoundYieldsInjectable()
	{
		$this->assertEquals(true, is_callable(EndPoint::create()->onNotFound()));
	}
}
 