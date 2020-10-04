<?php

use PROJECT\TaskInterface;
use PROJECT\wpScheduller;

/**
 * Class testScheduller
 *
 * @package Simple_Plugin
 */

define('DOING_CRON', true);

/**
 * Scheduller test case.
 */
class testScheduller extends \PHPUnit\Framework\TestCase
{

    private $period;

    private $taskname;

    public function setUp(): void
    {
        parent::setUp();
        WP_Mock::setUp();
        $this->period = 10800;
        $this->taskname = 'auto_test_name';

        //fwrite(STDOUT, 'SETUP IS FINISHED' . "\n");
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
        Mockery::close();
        parent::tearDown();
        //fwrite(STDOUT, 'TEARDOWN IS FINISHED' . "\n");
    }

    /**
     * check if I can add the hokk and run schedule the task.
     */
    public function test_create_task()
    {
        \WP_Mock::userFunction(
            '_get_cron_array',
            [
                'times'  => 1,
                'return' => [],
            ]
        );

        \WP_Mock::userFunction(
            'wp_schedule_event',
            [
                'times'  => 1,
                'args'  => [
                    time(),
                    $this->taskname,
                    $this->taskname,
                ],
                'return' => null,
            ]
        );
        $scheduler = new wpScheduller($this->taskname);
        WP_Mock::expectFilterAdded('cron_request', [$scheduler, 'change_cron_scheme'], 10, 1);
        WP_Mock::expectFilterAdded('cron_schedules', [$scheduler, 'create_interval'], 50, 1);
        WP_Mock::expectActionAdded('init', [$scheduler, 'add_task'], 50);
        WP_Mock::expectActionAdded($this->taskname, [$scheduler, 'run_task'], 50);

        $scheduler
            ->setPeriod($this->period)
            ->setTaskClass('PROJECT\\TestTask')
            ->setTaskClassParameters(['id' => 999]);

        $scheduler->set_cron_task();
        $scheduler->add_task();
    }

    /**
     * check if I can remove the schedulled task.
     */
    public function test_remove_task()
    {
        //wp_unschedule_hook
        \WP_Mock::userFunction(
            'wp_unschedule_hook',
            [
                'times'  => 1,
                'args'  => [
                    $this->taskname
                ],
                'return' => 1,
            ]
        );
        \WP_Mock::userFunction(
            '_get_cron_array',
            [
                'times'  => 1,
                'return' => [[$this->taskname => true]],
            ]
        );

        $scheduler = new wpScheduller($this->taskname);

        \WP_Mock::expectActionAdded('init', [$scheduler, 'remove_task'], 50);

        $scheduler->remove_cron_task();
        $scheduler->remove_task();
    }
}
