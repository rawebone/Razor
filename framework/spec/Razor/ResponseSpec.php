<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\Response');
    }

    function it_should_return_a_json_response()
    {
        $this->json(array(), 200, array())
             ->shouldReturnAnInstanceOf('Symfony\Component\HttpFoundation\JsonResponse');
    }

    function it_should_return_a_general_response()
    {
        $this->general("", 200, array())
             ->shouldReturnAnInstanceOf('Symfony\Component\HttpFoundation\Response');
    }
}
