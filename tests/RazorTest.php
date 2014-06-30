<?php

namespace Razor\Tests;

use Razor\Razor as r;
use Prophecy\PhpUnit\ProphecyTestCase;

class RazorTest extends ProphecyTestCase
{
	/**
	 * @expectedException \Razor\HttpAbortException
	 */
	public function testAbortThrowsHttpAbortException()
	{
		r::abort();
	}

	public function testResettingTheKernel()
	{
		r::reset();

		$kid = r::kernelId();

		r::reset();

		$this->assertNotEquals($kid, r::kernelId());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testEventFrontEnd()
	{
		r::reset();

		r::event("name", function () { throw new \InvalidArgumentException(); });
		r::fire("name");
	}

	public function testServiceResolutionFrontEnd()
	{
		r::reset();

		r::services([
			"a" => function () { }
		]);
	}
}
 