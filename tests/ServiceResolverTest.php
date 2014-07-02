<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\ServiceResolver;

class ServiceResolverTest extends ProphecyTestCase
{
	public function testRegisterAndResolution()
	{
		$resolver = new ServiceResolver();
		$resolver->register("blah", function () { });

		$this->assertInstanceOf('Rawebone\Injector\Func', $resolver->resolve("blah"));
	}

	public function testRegisterInstance()
	{
		$resolver = new ServiceResolver();
		$resolver->register("blah", new \stdClass());

		$this->assertInstanceOf('Rawebone\Injector\Func', $resolver->resolve("blah"));
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
 