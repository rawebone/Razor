<?php

namespace Razor\Tests\Extensions\Json;


use Razor\Extensions\Json\Deserializer;
use Symfony\Component\HttpFoundation\Request;

class DeserializerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testThrowsErrorIfNonObjectPassed()
	{
		$deserialiser = new Deserializer($this->getRequest(""));
		$deserialiser->decodeToObject("");
	}

	/**
	 * @expectedException \ErrorException
	 */
	public function testThrowsErrorJsonMalformed()
	{
		$deserialiser = new Deserializer($this->getRequest("{"));
		$deserialiser->decodeToObject(new \stdClass());
	}

	public function testMapsToObject()
	{
		$obj = new \stdClass();
		$obj->key = "";

		$deserialiser = new Deserializer($this->getRequest("{\"key\":\"value\"}"));
		$deserialiser->decodeToObject($obj);

		$this->assertEquals("value", $obj->key);
	}

	/**
	 * @param string $content
	 * @return Request
	 */
	protected function getRequest($content)
	{
		return Request::create("/blah.php", "GET", array(), array(), array(), array(), $content);
	}
}
