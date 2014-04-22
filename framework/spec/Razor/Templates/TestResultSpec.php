<?php

namespace spec\Razor\Templates;

use Prophecy\Argument;
use spec\Razor\ResourceRendererSpec;
use Razor\Resource;
use Razor\TemplateRenderer;

class TestResultSpec extends ResourceRendererSpec
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\Templates\TestResult');
    }

    function it_should_render(Resource $resource, TemplateRenderer $renderer)
    {
        $renderer->render($resource, array("described" => "1"))
            ->shouldBeCalled()
            ->willReturn("abc");

        $this->render("1")->shouldReturn("abc");
    }
}
