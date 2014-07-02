<?php

namespace Razor;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * HttpResponse provides a wrapper over the Symfony HttpFoundation
 * Response objects for consumption in the framework Applications,
 *
 * @package Razor
 */
class HttpResponse
{
	public function standard($contents = "", $status = 200, array $headers = [])
	{
		return new Response($contents, $status, $headers);
	}

	public function json($data = null, $status = 200, array $headers = array())
	{
		return new JsonResponse($data, $status, $headers);
	}

	public function redirect($url, $status = 302, array $headers = array())
	{
		return new RedirectResponse($url, $status, $headers);
	}

	public function file($file, $status = 200, $headers = array(), $public = true, $contentDisposition = null, $autoEtag = false, $autoLastModified = true)
	{
		return new BinaryFileResponse($file, $status, $headers, $public, $contentDisposition, $autoEtag, $autoLastModified);
	}

	public function stream($callback = null, $status = 200, $headers = array())
	{
		return new StreamedResponse($callback, $status, $headers);
	}
}
