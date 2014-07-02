<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\ServiceResolver;

class ServiceResolverTest extends ProphecyTestCase
{
	public function testRegisterAndResolution()
	{
		$resolver = new ServiceResolver();
		$resolver->register("blah", function () { return true; });

		$this->assertInstanceOf('Rawebone\Injector\Func', $resolver->resolve("blah"));
		$this->assertEquals(true, $resolver->resolve("blah")->invoke(array()));
	}

	public function testRegisterInstance()
	{
		$resolver = new ServiceResolver();
		$resolver->register("blah", ($cls = new \stdClass()));

		$this->assertInstanceOf('Rawebone\Injector\Func', $resolver->resolve("blah"));
		$this->assertEquals($cls, $resolver->resolve("blah")->invoke(array()));
	}

	/**
	 * @expectedException \Rawebone\Injector\ResolutionException
	 */
	public function testRegistrationFails()
	{
		$resolver = new ServiceResolver();
		$resolver->register("blah", "a");
	}

	/**
	 * @expectedException \Rawebone\Injector\ResolutionException
	 */
	public function testResolutionFails()
	{
		$resolver = new ServiceResolver();
		$resolver->resolve("blah");
	}
}
 