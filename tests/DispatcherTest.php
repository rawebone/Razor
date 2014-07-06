<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Rawebone\Injector\RegisterResolver;
use Razor\Dispatcher;
use Razor\HttpAbortException;
use Symfony\Component\HttpFoundation\Request;
use Prophecy\Argument;

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
		$resolver = new RegisterResolver();
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
		$resolver = new RegisterResolver();
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
		$resolver = new RegisterResolver();
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
		$resolver = new RegisterResolver();
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
		$resolver = new RegisterResolver();
		$resolver->register("request", Request::create("/blah.php"));

		$environment = $this->prophesize('Razor\Environment');
		$environment->testing = false;
		$environment->services()->willReturn($resolver);

		$func = function () { throw new HttpAbortException(); };

		$endPoint = $this->prophesize('Razor\EndPoint');
		$endPoint->get()->willReturn($func);

		(new Dispatcher($environment->reveal(), $endPoint->reveal()));
	}

	public function testDispatchSetsInjectorOnMiddlewarePriorToInvocation()
	{
		$resolver = new RegisterResolver();
		$resolver->register("request", Request::create("/blah.php"));

		$environment = $this->prophesize('Razor\Environment');
		$environment->testing = false;
		$environment->services()->willReturn($resolver);

		$response = $this->prophesize('Symfony\Component\HttpFoundation\Response');
		$response->send()->shouldBeCalled();

		$middleware = $this->prophesize('Razor\Middleware');
		$middleware->invokeDelegate()->willReturn($response->reveal());
		$middleware->letInjectorBe(Argument::type('Rawebone\Injector\Injector'))->shouldBeCalled();

		$endPoint = $this->prophesize('Razor\EndPoint');
		$endPoint->get()->willReturn($middleware);

		(new Dispatcher($environment->reveal(), $endPoint->reveal()));
	}

	public function testDispatchSetsInjectorOnErrorMiddlewarePriorToInvocation()
	{
		$resolver = new RegisterResolver();
		$resolver->register("request", Request::create("/blah.php"));

		$environment = $this->prophesize('Razor\Environment');
		$environment->testing = false;
		$environment->development = false;
		$environment->services()->willReturn($resolver);

		$response = $this->prophesize('Symfony\Component\HttpFoundation\Response');
		$response->send()->shouldBeCalled();

		$middleware = $this->prophesize('Razor\Middleware');
		$middleware->invokeDelegate()->willReturn($response->reveal());
		$middleware->letInjectorBe(Argument::type('Rawebone\Injector\Injector'))->shouldBeCalled();

		$endPoint = $this->prophesize('Razor\EndPoint');
		$endPoint->get()->willReturn(function () use ($response) { throw new \Exception(); });
		$endPoint->onError()->willReturn($middleware);

		(new Dispatcher($environment->reveal(), $endPoint->reveal()));
	}
}
 