<?php

namespace Razor;

use Razor\Http\Lifecycle;
use Razor\Http\Response;
use Razor\Http\Stream;
use Razor\Injection\Injector;

/**
 * CoreServices provides the basic services for the Razor framework.
 *
 * @package Razor
 */
class CoreServices implements ProviderInterface
{
	/**
	 * @param Injector $injector
	 * @return void
	 */
	public function register(Injector $injector)
	{
		$injector->service("httpLifecycle", function ()
        {
            $stream = new Stream("php://input", "r");

            return new Lifecycle($stream, $_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
        });

        $injector->service("req", function (Lifecycle $httpLifecycle)
        {
            return $httpLifecycle->makeRequest();
        });

        $injector->service("resp", function ()
        {
            $resp = new Response();
            $resp->setBody(new Stream("php://memory", "wb+"));
            return $resp;
        });
	}
}
