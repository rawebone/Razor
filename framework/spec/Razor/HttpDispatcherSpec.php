<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Rawebone\Injector\Injector;
use Razor\Controller;
use Razor\ServiceResolver;
use Symfony\Component\HttpFoundation\Request;

class HttpDispatcherSpec extends ObjectBehavior
{
    function let(Injector $injector, ServiceResolver $resolver)
    {
        $this->beConstructedWith($injector, $resolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\HttpDispatcher');
    }

    function it_should_dispatch_to_a_handler(Injector $injector, ServiceResolver $resolver, Request $request, Controller $controller)
    {
        $injector->service("request")->willReturn($request);
        $request->getMethod()->willReturn("GET");
        $controller->get = $func = function () { };

        $injector->inject($func)->shouldBeCalled();
        $resolver->lock()->willReturn(1);
        $resolver->unlock(1)->shouldBeCalled();

        $this->dispatch($controller);
    }

    function it_should_dispatch_to_notFound_handler(Injector $injector, ServiceResolver $resolver, Request $request)
    {
        $injector->service("request")->willReturn($request);
        $request->getMethod()->willReturn("GET");

        $controller = new Controller("test");

        $injector->inject($controller->notFound)->shouldBeCalled();
        $resolver->lock()->willReturn(1);
        $resolver->unlock(1)->shouldBeCalled();

        $this->dispatch($controller);
    }

    function it_should_dispatch_to_error_handler(Injector $injector, ServiceResolver $resolver, Request $request)
    {
        $injector->service("request")->willReturn($request);
        $request->getMethod()->willReturn("GET");

        $controller = new Controller("test");
        $controller->get = function () { };

        $injector->inject($controller->get)->willThrow('Exception');
        $injector->inject($controller->error)->shouldBeCalled();

        $resolver->lock()->willReturn(1);
        $resolver->unlock(1)->shouldBeCalled();
        $resolver->registerService("exception", Argument::type("Closure"))->shouldBeCalled();

        $this->dispatch($controller);
    }
}
