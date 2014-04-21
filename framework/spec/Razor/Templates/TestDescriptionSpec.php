<?php

namespace spec\Razor\Templates;

use Prophecy\Argument;
use spec\Razor\ResourceRendererSpec;
use Razor\Resource;
use Razor\TemplateRenderer;

class TestDescriptionSpec extends ResourceRendererSpec
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\Templates\TestDescription');
    }

    function it_should_render(Resource $resource, TemplateRenderer $renderer)
    {
        $renderer->render($resource, array("description" => "1", "described" => "2"))
            ->shouldBeCalled()
            ->willReturn("abc");

        $this->render("1", "2")->shouldReturn("abc");
    }
}
