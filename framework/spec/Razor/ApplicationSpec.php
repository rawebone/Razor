<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Razor\HttpDispatcher;

class ApplicationSpec extends ObjectBehavior
{
    function let(HttpDispatcher $http)
    {
        $this->beConstructedWith($http);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\Application');
    }

    function it_should_record_and_return_a_controller()
    {
        $this->controller("name")
             ->shouldReturnAnInstanceOf('Razor\Controller');
    }

    function it_should_run_an_http_dispatch(HttpDispatcher $http)
    {
        $http->dispatch(Argument::type('Razor\Controller'))
             ->shouldBeCalled();

        $this->run("name");
    }
}
