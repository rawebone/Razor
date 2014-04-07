<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApplicationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\Application');
    }

    function it_should_record_and_return_a_controller()
    {
        $this->controller("name")
             ->shouldReturnAnInstanceOf('Razor\Controller');
    }
}
