<?php

namespace Razor;

use Rawebone\Jasmini\ListenerInterface;
use Razor\Templates\TestItem;
use Razor\Templates\TestDescription;
use Razor\Templates\TestResult;

class HtmlTestListener implements ListenerInterface
{
    protected $outputFile;
    protected $filesystem;
    protected $testItem;
    protected $testDesc;
    protected $testResult;
    protected $recorded;

    public function __construct($outputFile, Filesystem $filesystem, TestItem $item, TestDescription $desc, TestResult $result)
    {
        $this->outputFile = $outputFile;
        $this->filesystem = $filesystem;
        $this->testItem = $item;
        $this->testDesc = $desc;
        $this->testResult = $result;
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
        $described = "";

        foreach ($this->recorded as $description => $items) {

            $itemString = "";
            foreach ($items as $item) {
                $itemString .= $this->testItem->render($item["title"], $item["status"]);
            }

            $described .= $this->testDesc->render($description, $itemString);
        }

        $this->filesystem->write($this->outputFile, $this->testResult->render($described));
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
        if (!isset($this->recorded[$description])) {
            $this->recorded[$description] = array();
        }

        $this->recorded[$description][] = compact("title", "status", "ex");
    }
}
