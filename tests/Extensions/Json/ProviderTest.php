<?php

namespace Razor\Tests\Extensions\Json;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\Extensions\Json\Provider;

class ProviderTest extends ProphecyTestCase
{
	public function testRegistrations()
	{
		$resolver = $this->prophesize('Rawebone\Injector\RegisterResolver');
		$resolver->register("jsonDeserializer", Argument::type("Closure"))->shouldBeCalled();

		$provider = new Provider();
		$provider->letResolverBe($resolver->reveal());
		$provider->register();
	}
}
