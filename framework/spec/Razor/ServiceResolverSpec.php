<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ServiceResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\ServiceResolver');
    }

    function it_should_register_a_service()
    {
        $this->registerService("name", function () { });
        $this->resolve("name")
             ->shouldReturnAnInstanceOf('Rawebone\Injector\Func');
    }

    function it_should_lock_to_prevent_registrations()
    {
        $this->lock();
        $this->shouldThrow('Razor\FrameworkException')
             ->during('registerService', array('name', function () { }));
    }

    function it_should_not_allow_multiple_locks()
    {
        $this->lock();
        $this->shouldThrow('Razor\FrameworkException')
             ->during('lock');
    }

    function it_should_unlock()
    {
        $key = $this->lock();
        $this->unlock($key);
        $key = $this->lock();
    }
}
