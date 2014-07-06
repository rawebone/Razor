<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\HttpResponse;

class HttpResponseTest extends ProphecyTestCase
{
	public function testStandardResponse()
	{
		$httpResponse = new HttpResponse();
		$response = $httpResponse->standard();

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
	}

	public function testJsonResponse()
	{
		$httpResponse = new HttpResponse();
		$response = $httpResponse->json();

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
	}

	public function testRedirectResponse()
	{
		$httpResponse = new HttpResponse();
		$response = $httpResponse->redirect("http://abc.co.uk");

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
	}

	public function testFileResponse()
	{
		$httpResponse = new HttpResponse();
		$response = $httpResponse->file(__FILE__);

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $response);
	}

	public function testStreamResponse()
	{
		$httpResponse = new HttpResponse();
		$response = $httpResponse->stream();

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
	}
}
 