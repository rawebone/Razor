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

	public function get($delegate = null)
	{
        $this->checkCallable($delegate);

		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function post(callable $delegate = null)
	{
        $this->checkCallable($delegate);

		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function delete(callable $delegate = null)
	{
        $this->checkCallable($delegate);

		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function patch(callable $delegate = null)
	{
        $this->checkCallable($delegate);

		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function head(callable $delegate = null)
	{
        $this->checkCallable($delegate);

		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function options(callable $delegate = null)
	{
        $this->checkCallable($delegate);

		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function onError(callable $delegate = null)
	{
        $this->checkCallable($delegate);

		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function onNotFound(callable $delegate = null)
	{
        $this->checkCallable($delegate);

		return $this->setOrReturn(__FUNCTION__, $delegate);
	}

	public function run()
	{
		Razor::run($this);
	}

	public static function create()
	{
		return new static();
	}

    protected function setOrReturn($property, $value = null)
    {
        if ($value === null) {
            return $this->$property;
        } else {
            $this->$property = $value;
            return $this;
        }
    }

    protected function checkCallable($delegate)
    {
        if ($delegate !== null && !is_callable($delegate)) {
            throw new \InvalidArgumentException("Passed delegate is not callable!");
        }
    }
}
