<?php

namespace Razor\Extensions\Json;

use Braincrafted\Json\Json;
use Braincrafted\Json\JsonDecodeException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Deserializer provides a simple mechanism for working with received JSON data.
 * Based on experience with writing Web Services in the framework, a pattern of
 * usage emerges whereby we might do something like:
 *
 * <code>
 * $endPoint->post(function (Http $http) {
 *     $decoded = json_decode($http->request->getContent(), true);
 *     if (JSON_ERROR_NONE !== json_last_error()) {
 *         // handle errors
 *     }
 *
 * 	   $object = new DomainObject();
 *     // Map decoded data onto the $object
 * });
 * </code>
 *
 * We look to provide the ability to make this process simpler by handling the
 * decoding, plus the basic object mapping:
 *
 * <code>
 * $endPoint->post(function (Http $http, Deserializer $jsonDeserializer) {
 *     try {
 *         $object = $jsonDeserializer->decodeToObject(new DomainObject());
 *     catch (Exception $exception) {
 *         // Handle exception
 *     }
 * });
 * </code>
 *
 * @package Razor\Extensions\Json
 */
class Deserializer
{
	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Provides a basic method of decoding JSON data into an object. This
	 * does not provide validation or sanitization of the input- that needs
	 * to be handled in your domain logic and with the libraries you want.
	 *
	 * @param object $object
	 * @return object
	 * @throws \InvalidArgumentException
	 * @throws \ErrorException
	 */
	public function decodeToObject($object)
	{
		if (!is_object($object)) {
			throw new \InvalidArgumentException("Cannot decoded to non-object");
		}

		// For performance, assume that we are using in conjunction
		// with the Middleware to filter out bad requests.
		try {
			$decoded = Json::decode($this->request->getContent(), true);

		} catch (JsonDecodeException $ex) {
			throw new \ErrorException($ex->getMessage());
		}

		foreach ($object as $key => &$value) {
			if (isset($decoded[$key])) {
				$value = $decoded[$key];
			}
		}

		return $object;
	}
}
