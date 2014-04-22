<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Razor\Filesystem;
use Razor\Templates\TestDescription;
use Razor\Templates\TestItem;
use Razor\Templates\TestResult;
use Rawebone\Jasmini\TestStatus;

class HtmlTestListenerSpec extends ObjectBehavior
{
    protected $file = "test.html";

    function let(Filesystem $filesystem, TestItem $item, TestDescription $desc, TestResult $result)
    {
        $this->beConstructedWith($this->file, $filesystem, $item, $desc, $result);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\HtmlTestListener');
    }

    function it_should_record_and_write_out(Filesystem $filesystem, TestItem $item, TestDescription $desc, TestResult $result)
    {
        $item->render("test #1", TestStatus::PASSED)->shouldBeCalled()->willReturn("a");
        $desc->render("my tests", "a")->shouldBeCalled()->willReturn("b");
        $result->render("b")->shouldBeCalled()->willReturn("c");

        $filesystem->write($this->file, "c")->shouldBeCalled()->willReturn(true);

        $this->start();
        $this->record("my tests", "test #1", TestStatus::PASSED);
        $this->stop();
    }
}
