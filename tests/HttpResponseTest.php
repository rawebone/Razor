<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\HttpResponse;

class HttpResponseTest extends ProphecyTestCase
{
	public function testStandardResponse()
	{
		$response = (new HttpResponse())->standard();

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
	}

	public function testJsonResponse()
	{
		$response = (new HttpResponse())->json();

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
	}

	public function testRedirectResponse()
	{
		$response = (new HttpResponse())->redirect("http://abc.co.uk");

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
	}

	public function testFileResponse()
	{
		$response = (new HttpResponse())->file(__FILE__);

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $response);
	}
	public function testStreamResponse()
	{
		$response = (new HttpResponse())->stream();

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
	}
}
 