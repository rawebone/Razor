<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Razor\Resource;
use Razor\TemplateRenderer;

class ResourceRendererSpec extends ObjectBehavior
{
    function let(Resource $resource, TemplateRenderer $renderer)
    {
        $this->beConstructedWith($resource, $renderer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\ResourceRenderer');
    }

    function it_should_render(Resource $resource, TemplateRenderer $renderer)
    {
        $renderer->render($resource, array())
                 ->shouldBeCalled()
                 ->willReturn("abc");

        $this->render()->shouldReturn("abc");
    }
}
