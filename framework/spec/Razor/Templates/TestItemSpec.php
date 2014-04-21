<?php

namespace spec\Razor\Templates;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Rawebone\Jasmini\TestStatus;
use Razor\Resource;
use Razor\TemplateRenderer;
use spec\Razor\ResourceRendererSpec;

class TestItemSpec extends ResourceRendererSpec
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\Templates\TestItem');
    }

    function it_should_render(Resource $resource, TemplateRenderer $renderer)
    {
        $renderer->render($resource, array("title" => "1", "status" => "pass"))
            ->shouldBeCalled()
            ->willReturn("abc");

        $this->render("1", TestStatus::PASSED)->shouldReturn("abc");
    }

    function it_should_render_a_fail(Resource $resource, TemplateRenderer $renderer)
    {
        $renderer->render($resource, array("title" => "1", "status" => "fail"))
            ->shouldBeCalled()
            ->willReturn("abc");

        $this->render("1", TestStatus::FAILED)->shouldReturn("abc");
    }

    function it_should_render_a_pending(Resource $resource, TemplateRenderer $renderer)
    {
        $renderer->render($resource, array("title" => "1", "status" => "pending"))
            ->shouldBeCalled()
            ->willReturn("abc");

        $this->render("1", TestStatus::PENDING)->shouldReturn("abc");
    }
}
