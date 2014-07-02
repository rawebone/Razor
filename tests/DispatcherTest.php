<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\Dispatcher;
use Razor\HttpAbortException;
use Razor\ServiceResolver;
use Symfony\Component\HttpFoundation\Request;

class DispatcherTest extends ProphecyTestCase
{
	public function testDispatchAbortsWhenTesting()
	{
		$environment = $this->prophesize('Razor\Environment');
		$environment->testing = true;
		$environment->services()->shouldNotBeCalled();

		$endPoint = $this->prophesize('Razor\EndPoint');

		(new Dispatcher($environment->reveal(), $endPoint->reveal()));
	}

	public function testDispatchCallsEndPointMethod()
	{
		$resolver = new ServiceResolver();
		$resolver->register("request", Request::create("/blah.php"));

		$environment = $this->prophesize('Razor\Environment');
		$environment->testing = false;
		$environment->services()->willReturn($resolver);

		$response = $this->prophesize('Symfony\Component\HttpFoundation\Response');
		$response->send()->shouldBeCalled();

		$func = function () use ($response) { return $response->reveal(); };

		$endPoint = $this->prophesize('Razor\EndPoint');
		$endPoint->get()->willReturn($func);

		(new Dispatcher($environment->reveal(), $endPoint->reveal()));
	}

	public function testDispatchCallsNotFound()
	{
		$resolver = new ServiceResolver();
		$resolver->register("request", Request::create("/blah.php", "MKCOL"));

		$environment = $this->prophesize('Razor\Environment');
		$environment->testing = false;
		$environment->services()->willReturn($resolver);

		$response = $this->prophesize('Symfony\Component\HttpFoundation\Response');
		$response->send()->shouldBeCalled();

		$func = function () use ($response) { return $response->reveal(); };

		$endPoint = $this->prophesize('Razor\EndPoint');
		$endPoint->onNotFound()->willReturn($func);

		(new Dispatcher($environment->reveal(), $endPoint->reveal()));
	}

	public function testDispatchCallsOnError()
	{
		$resolver = new ServiceResolver();
		$resolver->register("request", Request::create("/blah.php"));

		$environment = $this->prophesize('Razor\Environment');
		$environment->testing = false;
		$environment->development = false;
		$environment->services()->willReturn($resolver);

		$response = $this->prophesize('Symfony\Component\HttpFoundation\Response');
		$response->send()->shouldBeCalled();

		$func = function () use ($response) { return $response->reveal(); };

		$endPoint = $this->prophesize('Razor\EndPoint');
		$endPoint->get()->willReturn(function () use ($response) { throw new \Exception(); });
		$endPoint->onError()->willReturn($func);

		(new Dispatcher($environment->reveal(), $endPoint->reveal()));
	}

	/**
	 * @expectedException \Exception
	 */
	public function testDispatchThrowsException()
	{
		$resolver = new ServiceResolver();
		$resolver->register("request", Request::create("/blah.php"));

		$environment = $this->prophesize('Razor\Environment');
		$environment->testing = false;
		$environment->development = true;
		$environment->services()->willReturn($resolver);

		$endPoint = $this->prophesize('Razor\EndPoint');
		$endPoint->get()->willReturn(function () { throw new \Exception(); });

		(new Dispatcher($environment->reveal(), $endPoint->reveal()));
	}

	public function testDispatchAllowsForAbort()
	{
		$resolver = new ServiceResolver();
		$resolver->register("request", Request::create("/blah.php"));

		$environment = $this->prophesize('Razor\Environment');
		$environment->testing = false;
		$environment->services()->willReturn($resolver);

		$func = function () { throw new HttpAbortException(); };

		$endPoint = $this->prophesize('Razor\EndPoint');
		$endPoint->get()->willReturn($func);

		(new Dispatcher($environment->reveal(), $endPoint->reveal()));
	}
}
 