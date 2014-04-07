<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ControllerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith("name");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\Controller');
    }

    function it_should_return_a_name()
    {
        $this->name->shouldReturn("name");
    }

    function it_should_register_error_handler()
    {
        $func = function () {};

        $this->error = $func;
        $this->error->shouldBe($func);
    }

    function it_should_register_a_notFound_handler()
    {
        $func = function () {};

        $this->notFound = $func;
        $this->notFound->shouldBe($func);
    }

    function it_should_register_a_http_verb()
    {
        $func = function () {};

        $this->get = $func;
        $this->get->shouldBe($func);
    }

    function it_should_have_a_default_error_and_notFound_handler()
    {
        $this->notFound->shouldBeAnInstanceOf('Closure');
        $this->error->shouldBeAnInstanceOf('Closure');
    }

    function it_should_not_allow_setting_of_a_non_closure()
    {
        $this->shouldThrow('Razor\FrameworkException')
             ->during('__set', array('notFound', null));
    }

    function it_should_throw_an_exception_when_getting_an_invalid_parameter()
    {
        $this->shouldThrow('Razor\FrameworkException')
             ->during('__get', array('invalid'));
    }
}
