# wpScheduller

## add the task

```
use YIVDEV\WPSCHEDULLER\wpScheduller;

$scheduler = new wpScheduller('test_task');
$scheduler
    ->setPeriod(10800)
    ->setTaskClass('Path\\to\\Your\\Task\\Class')
    ->setTaskClassParameters(['id' => 999]);

$scheduler->set_cron_task();
```

#### You can create your task Clas:

```
use YIVDEV\WPSCHEDULLER\TaskInterface;


class TestTask implements TaskInterface
{

    private $id;

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

    public function setParameters(array $parameters): void
    {
        try {
            $this->id = $parameters['id'];
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
```

## remove the task

```
$scheduler = new wpScheduller('test_task');
$scheduler->remove_cron_task();
```

## get the jobs

```
$scheduler = new wpScheduller('test_task');
$scheduler->get_wpcron_jobs();
```
