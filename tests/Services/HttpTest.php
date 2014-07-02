<?php

namespace Razor\Tests\Services;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\Services\Http;

class HttpTest extends ProphecyTestCase
{
	/**
	 * @var \Razor\Services\Http
	 */
	protected $subject;

	protected function setUp()
	{
		parent::setUp();

		$request = $this->prophesize('Symfony\Component\HttpFoundation\Request');
		$response = $this->prophesize('Razor\HttpResponse');

		$this->subject = new Http($request->reveal(), $response->reveal());
	}

	public function testGetRequest()
	{
		$this->assertInstanceOf(
			'Symfony\Component\HttpFoundation\Request',
			$this->subject->request
		);
	}

	public function testGetResponse()
	{
		$this->assertInstanceOf(
			'Razor\HttpResponse',
			$this->subject->response
		);
	}

	/**
	 * @expectedException \Razor\HttpAbortException
	 */
	public function testAbortThrowsExceptions()
	{
		$this->subject->abort();
	}
}
 