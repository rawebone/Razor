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
	use FluentPropertySetter;

	protected $onError;
	protected $onNotFound;
	protected $get;
	protected $put;
	protected $delete;
	protected $patch;
	protected $head;
	protected $options;

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
		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function post(callable $delegate = null)
	{
		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function delete(callable $delegate = null)
	{
		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function patch(callable $delegate = null)
	{
		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function head(callable $delegate = null)
	{
		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function options(callable $delegate = null)
	{
		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function onError(callable $delegate = null)
	{
		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function onNotFound(callable $delegate = null)
	{
		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function run()
	{
		Razor::run($this);
	}
}
