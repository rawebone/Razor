<?php

namespace Razor\Tests;

use Prophecy\PhpUnit\ProphecyTestCase;
use Razor\Events;

class EventsTest extends ProphecyTestCase
{
	public function testEventRegistrationAndDispatch()
	{
		$delegate = function () { };

		/** @var \Rawebone\Injector\Injector|\Prophecy\Prophecy\ObjectProphecy $injector */
		$injector = $this->prophesize('Rawebone\Injector\Injector');
		$injector->inject($delegate)->willReturn(true);

		$events = new Events($injector->reveal());
		$events->register("name", $delegate);
		$events->fire("name");
	}
}
 