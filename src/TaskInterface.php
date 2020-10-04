<?php

namespace PROJECT;

if (!defined('ABSPATH')) exit;

/**
 * TaskInterface interface
 */
interface TaskInterface
{
    /**
     * Set parameters function
     *
     * @param array $parameters
     * @return void
     */
    public function setParameters(array $parameters): void;

    /**
     * Run function. do any what you want on schedule
     *
     * @return void
     */
    public function run(): void;
}
