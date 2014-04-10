<?php

namespace spec\Razor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FilesystemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Razor\Filesystem');
    }

    function it_should_return_whether_a_file_exists()
    {
        $this->isFile(__FILE__)->shouldReturn(true);
        $this->isFile(__FILE__ . "_")->shouldReturn(false);
    }

    function it_should_return_whether_a_directory_exists()
    {
        $this->isDir(__DIR__)->shouldReturn(true);
        $this->isDir(__FILE__)->shouldReturn(false);
    }

    function it_should_create_a_file()
    {
        $this->touch(__FILE__ . "_")->shouldReturn(true);

        unlink(__FILE__ . "_");
    }

    function it_should_return_the_contents_of_a_file()
    {
        $this->read(__FILE__)->shouldReturn(file_get_contents(__FILE__));
    }

    function it_should_fail_to_return_the_contents_of_a_file_if_it_does_not_exist()
    {
        $this->shouldThrow('InvalidArgumentException')->during("read", array(__FILE__ . "_"));
    }

    function it_should_write_to_a_file()
    {
        $path = __FILE__ . "_";
        $this->write($path, "output")->shouldReturn(true);
        unlink($path);
    }

    function it_should_append_to_a_file()
    {
        $path = __FILE__ . "_";
        $this->write($path, "output");
        $this->write($path, ", appended", true);
        $this->read($path)->shouldReturn("output, appended");
        unlink($path);
    }
}
