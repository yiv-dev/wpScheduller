<?php

namespace YIVDEV\WPSCHEDULLER;

if (!defined('ABSPATH')) exit;

use YIVDEV\WPSCHEDULLER\TaskInterface;

/**
 * TestTask class as samble of realization
 */
class TestTask implements TaskInterface
{
    /**
     * id variable/ We have it in the parameters for test case
     *
     * @var Int
     */
    private $id;

    /**
     * Run function. do any what you want on schedule
     *
     * @return void
     */
    public function run(): void
    {
        try {
            $file = \uniqid() . '_' . $this->id . '_test.txt';
            $content = 'TEST CONTENT';
            file_put_contents($file, $content);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Set parameters function
     *
     * @param array $parameters
     * @return void
     */
    public function setParameters(array $parameters): void
    {
        try {
            $this->id = $parameters['id'];
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
