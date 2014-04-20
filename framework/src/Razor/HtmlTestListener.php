<?php

namespace Razor;

use Rawebone\Jasmini\ListenerInterface;

class HtmlTestListener implements ListenerInterface
{
    protected $outputFile;
    protected $filesystem;
    protected $recorded;

    public function __construct($outputFile, Filesystem $filesystem)
    {
        $this->outputFile = $outputFile;
        $this->filesystem = $filesystem;
    }

    /**
     * Signals that the testing has started.
     *
     * @return void
     */
    function start()
    {
        // Clear down the register of recorded tests from any previous runs
        $this->recorded = array();
    }

    /**
     * Signals that the testing has completed.
     *
     * @return void
     */
    function stop()
    {
        // TODO: Implement stop() method.
    }

    /**
     * Signals that a test has been run.
     *
     * @param string $description
     * @param string $title
     * @param \Rawebone\Jasmini\TestStatus $status A TestStatus constant
     * @param \Exception $ex If applicable, the exception recorded to enable feedback to the user
     * @return void
     */
    function record($description, $title, $status, \Exception $ex = null)
    {
        // TODO: Implement record() method.
    }
}
