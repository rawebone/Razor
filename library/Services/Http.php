<?php

namespace Razor\Services;

use Razor\HttpResponse;
use Razor\HttpAbortException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Http Service for consumption in the front end.
 *
 * @package Razor
 * @property \Symfony\Component\HttpFoundation\Request $request
 * @property \Razor\HttpResponse $response
 */
class Http
{
	protected $request;
	protected $response;

	public function __construct(Request $request, HttpResponse $response)
	{
		$this->request = $request;
		$this->response = $response;
	}

	public function __get($name)
	{
		if ($name === "request" || $name === "response") {
			return $this->$name;
		}

		return null;
	}

	public function abort()
	{
		throw new HttpAbortException();
	}
} 