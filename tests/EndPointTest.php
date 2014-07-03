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

	public function testOnErrorYieldsInjectable()
	{
		$this->assertEquals(true, is_callable((new EndPoint())->onError()));
	}

	public function testOnNotFoundYieldsInjectable()
	{
		$this->assertEquals(true, is_callable((new EndPoint())->onNotFound()));
	}
}
 