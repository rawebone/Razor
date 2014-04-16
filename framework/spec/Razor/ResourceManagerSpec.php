<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Razor\Filesystem;

class ResourceManagerSpec extends ObjectBehavior
{
    protected $framework = "razor/";

    function let(Filesystem $filesystem)
    {
        $this->beConstructedWith($filesystem, $this->framework);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\ResourceManager');
    }

    function it_should_return_a_resource_for_the_framework()
    {
        $this->framework("templates/test_results.html")
             ->shouldReturnAnInstanceOf('Razor\Resource');
    }
}
