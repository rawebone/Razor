<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Razor\ResourceManager;

class HtmlTestListenerSpec extends ObjectBehavior
{
    protected $file = "test.html";

    function let(ResourceManager $manager)
    {
        $this->beConstructedWith($this->file, $manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\HtmlTestListener');
    }

    function it_should_record_and_write_out()
    {

    }
}
