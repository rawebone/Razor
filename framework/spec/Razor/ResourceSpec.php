<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Razor\Filesystem;

class ResourceSpec extends ObjectBehavior
{
    protected $file;

    function let(Filesystem $filesystem)
    {
        $this->file = __FILE__ . "_";

        $this->beConstructedWith($this->file, $filesystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\Resource');
    }

    function it_should_return_whether_it_exists(Filesystem $filesystem)
    {
        $filesystem->isFile($this->file)
                   ->willReturn(true)
                   ->shouldBeCalled();

        $this->exists()->shouldReturn(true);
    }

    function it_should_return_its_contents(Filesystem $filesystem)
    {
        $filesystem->read($this->file)
                   ->willReturn("ABC")
                   ->shouldBeCalled();

        $this->contents()->shouldReturn("ABC");
    }
}
