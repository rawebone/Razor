<?php

namespace Razor;

use Razor\Services\Http;
use Symfony\Component\HttpFoundation\Request;
use Rawebone\Injector\RegisterResolver;

class Environment
{
	public $development = true;
	public $testing = false;

	protected $services;

	public function __construct()
	{
		$this->services = new RegisterResolver();

		$this->setupServices();
	}

	/**
	 * The environments service container.
	 *
	 * @return \Rawebone\Injector\RegisterResolver
	 */
	public function services()
	{
		return $this->services;
	}

	protected function setupServices()
	{
		// Save the environment instance for use by
		// some of the framework services.
		$this->services->registerObject("razor", $this);

		$this->services->register("request", function ()
		{
			return Request::createFromGlobals();
		});

		$this->services->register("response", function ()
		{
			return new HttpResponse();
		});

		$this->services->register("http", function (HttpResponse $response, Request $request)
		{
			return new Http($request, $response);
		});
	}
}