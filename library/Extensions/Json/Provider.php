<?php

namespace Razor\Extensions\Json;

use Razor\Provider as BaseProvider;
use Razor\Services\Http;

class Provider extends BaseProvider
{
	/**
	 * Registers the services exposed by the provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$resolver = $this->resolver();

		$resolver->register("jsonDeserializer", function (Http $http) {
			return new Deserializer($http->request);
		});
	}
}
