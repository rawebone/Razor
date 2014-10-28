<?php

use Razor\EndPoint as EP;

EP::create()

	->provider(new Provider())

	->get(new Middleware(), new Middleware2(), function ()
	{

	})

	->run();
