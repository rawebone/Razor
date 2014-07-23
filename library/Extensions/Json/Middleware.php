<?php

namespace Razor\Extensions\Json;

use Razor\Services\Http;
use Razor\Middleware as BaseMiddleware;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware provides filtration on requests to ensure the input data
 * is JSON and, unless a Request is returned by the delegate, returns
 * a JSON encoded response.
 *
 * @package Razor\Extensions\Json
 */
class Middleware extends BaseMiddleware
{
	public function __invoke(Http $http = null)
	{
		if (($resp = $this->checkRequestIsValid($http))) {
			return $resp;
		}

		$data = $this->invokeDelegate();
		if ($data instanceof Response) {
			return $data;
		}

		return $http->response->json($data);
	}

	/**
	 * Verifies that the Content-Type and Accept headers are valid for
	 * JSON data handling.
	 *
	 * @param Http $http
	 * @return Response|void
	 */
	protected function checkRequestIsValid(Http $http)
	{
		if ($http->request->getContentType() !== "json") {
			// Return "Unsupported Media Type" response
			return $http->response->standard("", 415);
		}

		$accept = $http->request->getAcceptableContentTypes();
		foreach ($accept as $type) {
			if (strpos($type, "application/json") !== false) {
				return;
			}
		}

		// Return "Not Acceptable" response
		return $http->response->standard("", 406);
	}
}
