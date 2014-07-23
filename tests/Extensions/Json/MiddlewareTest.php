<?php

namespace Razor\Tests\Extensions\Json;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\Extensions\Json\Middleware;
use Razor\HttpResponse;
use Razor\Services\Http;
use Symfony\Component\HttpFoundation\Request;

class MiddlewareTest extends ProphecyTestCase
{
	public function testReturn415IfBadContentType()
	{
		$req = Request::create("/blah.php");
		$req->headers->set("Content-Type", "text/html");

		$http = new Http($req, new HttpResponse());

		$middleware = $this->getMiddleware();
		$resp = $middleware($http);

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $resp);

		$this->assertEquals(415, $resp->getStatusCode());
	}

	public function testReturn406IfBadAcceptHeader()
	{
		$req = Request::create("/blah.php");
		$req->headers->set("Content-Type", "application/json");
		$req->headers->set("Accept", "text/html,text/xhtml+xml");

		$http = new Http($req, new HttpResponse());

		$middleware = $this->getMiddleware();
		$resp = $middleware($http);

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $resp);

		$this->assertEquals(406, $resp->getStatusCode());
	}

	public function testReturnResponseFromDelegatePassesThrough()
	{
		$req = Request::create("/blah.php");
		$req->headers->set("Content-Type", "application/json");
		$req->headers->set("Accept", "application/json");

		$resp = new HttpResponse();
		$http = new Http($req, $resp);

		$standard = $resp->standard();

		$injector = $this->prophesize('Rawebone\Injector\Injector');
		$injector->inject(Argument::any())->willReturn($standard);

		$middleware = $this->getMiddleware();
		$middleware->letInjectorBe($injector->reveal());
		$return = $middleware($http);

		$this->assertEquals($standard, $return);
	}

	public function testReturnDataYieldsJsonResponse()
	{
		$req = Request::create("/blah.php");
		$req->headers->set("Content-Type", "application/json");
		$req->headers->set("Accept", "application/json");

		$resp = new HttpResponse();
		$http = new Http($req, $resp);

		$object = new \stdClass();

		$injector = $this->prophesize('Rawebone\Injector\Injector');
		$injector->inject(Argument::any())->willReturn($object);

		$middleware = $this->getMiddleware();
		$middleware->letInjectorBe($injector->reveal());
		$return = $middleware($http);

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $return);
	}

	/**
	 * @return Middleware
	 */
	protected function getMiddleware()
	{
		return new Middleware(function () { throw new \Exception(); });
	}
}
