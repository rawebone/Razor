<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Razor\Resource;

class TemplateRendererSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\TemplateRenderer');
    }

    function it_should_render_a_template(Resource $resource)
    {
        $resource->contents()
                 ->willReturn("ABC %a% %def%")
                 ->shouldBeCalled();

        $this->render($resource, array("a" => "1", "def" => 2))
             ->shouldReturn("ABC 1 2");
    }
}
