<?php

namespace Razor;

use Razor\Services\Http;

/**
 * EndPoint objects represent an HTTP end point in the application
 * which can be passed around the framework.
 *
 * @package Razor
 */
class EndPoint
{
	protected $verbs = [];

	public function __construct()
	{
		$this->onError(function (Http $http, \Exception $exception)
		{
			return $http->response->standard($exception, 500);
		});

		$this->onNotFound(function (Http $http)
		{
			return $http->response->standard("", 405);
		});
	}

	public function get(callable $delegate = null)
	{
		return $this->assignOrReturn(__FUNCTION__, $delegate);
	}

	public function post(callable $delegate = null)
	{
		return $this->assignOrReturn(__FUNCTION__, $delegate);
	}

	public function delete(callable $delegate = null)
	{
		return $this->assignOrReturn(__FUNCTION__, $delegate);
	}

	public function patch(callable $delegate = null)
	{
		return $this->assignOrReturn(__FUNCTION__, $delegate);
	}

	public function head(callable $delegate = null)
	{
		return $this->assignOrReturn(__FUNCTION__, $delegate);
	}

	public function options(callable $delegate = null)
	{
		return $this->assignOrReturn(__FUNCTION__, $delegate);
	}

	public function onError(callable $delegate = null)
	{
		return $this->assignOrReturn(__FUNCTION__, $delegate);
	}

	public function onNotFound(callable $delegate = null)
	{
		return $this->assignOrReturn(__FUNCTION__, $delegate);
	}

	/**
	 * Basic handling for assigning delegates to HTTP Verbs.
	 *
	 * @param $verb
	 * @param callable $delegate
	 * @return $this|null
	 */
	protected function assignOrReturn($verb, callable $delegate = null)
	{
		if (is_null($delegate)) {
			return (isset($this->verbs[$verb]) ? $this->verbs[$verb] : null);

		} else {
			$this->verbs[$verb] = $delegate;
			return $this;
		}
	}
}
