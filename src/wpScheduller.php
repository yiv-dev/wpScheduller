<?php

namespace YIVDEV\WPSCHEDULLER;

use YIVDEV\WPSCHEDULLER\TaskInterface;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * wpScheduller class
 */
class wpScheduller
{
    /**
     * slug of task variable
     *
     * @var String
     */
    private $slug;

    /**
     * Task period variable
     *
     * @var Integer
     */
    private $period;

    /**
     * Class name for task run variable.
     *
     * @var String
     */
    private $taskClass;

    /**
     * classParameters variable if it needed for task working
     *
     * @var Array
     */
    private $classParameters;

    /**
     * construct function
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * Set the value of period
     *
     * @return  self
     */
    public function setPeriod(int $period): wpScheduller
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Set class slug for task run variable. Must be implemented TaskInterface
     *
     * @param  TaskInterface $taskClass  Class slug for task run variable. Must be implemented TaskInterface
     *
     * @return  self
     */
    public function setTaskClass(String $taskClass)
    {
        $this->taskClass = $taskClass;

        return $this;
    }

    /**
     * Set the value of classParameters
     *
     * @return  self
     */
    public function setTaskClassParameters(array $classParameters): wpScheduller
    {
        $this->classParameters = $classParameters;

        return $this;
    }

    /**
     * Get cron jobs function
     * @return array
     */
    public function get_wpcron_jobs(): array
    {
        $times = [];
        $cron_jobs = _get_cron_array();
        foreach ($cron_jobs as $key => $task) {
            if (isset($task[$this->slug])) {
                $times[] = ['datetime' => get_date_from_gmt(date('Y-m-d H:i:s', $key), 'F j, Y H:i:s'), 'timestamp' => $key, 'job' => $task];
            }
        }
        return $times;
    }

    /**
     * Set the wp hook to add wpcron task
     *
     */
    public function set_cron_task(): void
    {
        try {
            add_filter('cron_request', [$this, 'change_cron_scheme'], 10, 1);
            add_filter('cron_schedules', [$this, 'create_interval'], 50, 1);
            add_action('init', [$this, 'add_new_task'], 50);

            if (defined('DOING_CRON') && DOING_CRON) {
                add_action($this->slug, [$this, 'run_task'], 50);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the wp hook to add wpcron task if not exist cron task
     *
     */
    public function set_cron_task_if_not_exist(): void
    {
        try {
            add_filter('cron_request', [$this, 'change_cron_scheme'], 10, 1);
            add_filter('cron_schedules', [$this, 'create_interval'], 50, 1);
            add_action('init', [$this, 'add_new_task_if_not_exist'], 50);

            if (defined('DOING_CRON') && DOING_CRON) {
                add_action($this->slug, [$this, 'run_task'], 50);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set thewp  hook to remove wpcron task
     *
     */
    public function remove_cron_task(): void
    {
        try {
            add_action('init', [$this, 'remove_task'], 49);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add wpcron task if not exist in cron list.
     * public because of wp hook
     *
     */
    public function add_new_task_if_not_exist(): Bool
    {
        try {
            $result = null;
            if (!$this->is_task_in_cron()) {
                $result = wp_schedule_event(time(), $this->slug, $this->slug);
            }

            return null === $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * Add wpcron task.
     * public because of wp hook
     *
     */
    public function add_new_task(): Bool
    {
        try {
            $this->remove_task();
            $result = wp_schedule_event(time(), $this->slug, $this->slug);

            return null === $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the wpcron task
     * public because of wp hook
     */
    public function remove_task(): Bool
    {
        try {
            if ($this->is_task_in_cron()) {
                $result = wp_unschedule_hook($this->slug);

                if (false !== $result) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Run the task
     */
    public function run_task(): void
    {
        try {
            $taskObject = new $this->taskClass();
            if ($this->classParameters) {
                $taskObject->setParameters($this->classParameters);
            }
            $taskObject->run();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Register the wpcron timeout.
     * public because of wp hook
     *
     */
    public function change_cron_scheme(array $args): array
    {
        try {
            if ($args['args']['timeout'] !== 3) {
                $args['args']['timeout'] = 3;
            }

            return $args;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Register the scheduller interval for wp hook function.
     * public because of wp hook
     */
    public function create_interval(array $schedules): array
    {
        try {
            $schedules[$this->slug] = [
                'interval' => $this->period,
                'display' => $this->slug . ' (' . $this->period . ') sec'
            ];

            return $schedules;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function is_task_in_cron(): Bool
    {
        try {
            $cron_tasks = array_filter(\_get_cron_array(), function ($task) {
                return isset($task[$this->slug]);
            });

            return count($cron_tasks) > 0;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
