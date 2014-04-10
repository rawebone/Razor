<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Razor\Filesystem;

class AppLogSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem)
    {
        $this->beConstructedWith(__FILE__ . "_", $filesystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\AppLog');
    }

    function it_should_log(Filesystem $filesystem)
    {
        $filesystem->write(__FILE__ . "_", Argument::type("string"))
                   ->shouldBeCalled();

        $this->log("error", "message");
    }
}
