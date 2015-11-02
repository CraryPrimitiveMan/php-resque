<?php
namespace resque\test;

require_once dirname(__FILE__) . '/bootstrap.php';

use stdClass;
use resque\Resque;
use resque\core\Worker;
use resque\core\Stat;
use resque\core\Job;
use resque\core\Redis;
use resque\test\job\Job as TestJob;
use resque\test\job\FailingJob;
use resque\test\job\JobWithSetUp;
use resque\test\job\JobWithTearDown;
use resque\test\job\JobWithoutPerformMethod;

/**
 * resque\core\Job tests.
 *
 * @package     resque\test
 * @author      Chris Boulton <chris@bigcommerce.com>
 * @license     http://www.opensource.org/licenses/mit-license.php
 */
class JobTest extends TestCase
{
    protected $worker;

    public function setUp()
    {
        parent::setUp();

        // Register a worker to test with
        $this->worker = new Worker('jobs');
        $this->worker->registerWorker();
    }

    public function testJobCanBeQueued()
    {
        $this->assertTrue((bool)Resque::enqueue('jobs', TestJob::className()));
    }

    public function testQeueuedJobCanBeReserved()
    {
        Resque::enqueue('jobs', TestJob::className());

        $job = Job::reserve('jobs');
        if($job == false) {
            $this->fail('Job could not be reserved.');
        }
        $this->assertEquals('jobs', $job->queue);
        $this->assertEquals(TestJob::className(), $job->payload['class']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testObjectArgumentsCannotBePassedToJob()
    {
        $args = new stdClass;
        $args->test = 'somevalue';
        Resque::enqueue('jobs', TestJob::className(), $args);
    }

    public function testQueuedJobReturnsExactSamePassedInArguments()
    {
        $args = array(
            'int' => 123,
            'numArray' => array(
                1,
                2,
            ),
            'assocArray' => array(
                'key1' => 'value1',
                'key2' => 'value2'
            ),
        );
        Resque::enqueue('jobs', TestJob::className(), $args);
        $job = Job::reserve('jobs');

        $this->assertEquals($args, $job->getArguments());
    }

    public function testAfterJobIsReservedItIsRemoved()
    {
        Resque::enqueue('jobs', TestJob::className());
        Job::reserve('jobs');
        $this->assertFalse(Job::reserve('jobs'));
    }

    public function testRecreatedJobMatchesExistingJob()
    {
        $args = array(
            'int' => 123,
            'numArray' => array(
                1,
                2,
            ),
            'assocArray' => array(
                'key1' => 'value1',
                'key2' => 'value2'
            ),
        );

        Resque::enqueue('jobs', TestJob::className(), $args);
        $job = Job::reserve('jobs');

        // Now recreate it
        $job->recreate();

        $newJob = Job::reserve('jobs');
        $this->assertEquals($job->payload['class'], $newJob->payload['class']);
        $this->assertEquals($job->payload['args'], $newJob->getArguments());
    }


    public function testFailedJobExceptionsAreCaught()
    {
        $payload = array(
            'class' => FailingJob::className(),
            'args' => null
        );
        $job = new Job('jobs', $payload);
        $job->worker = $this->worker;

        $this->worker->perform($job);

        $this->assertEquals(1, Stat::get('failed'));
        $this->assertEquals(1, Stat::get('failed:'.$this->worker));
    }

    /**
     * @expectedException \resque\core\Exception
     */
    public function testJobWithoutPerformMethodThrowsException()
    {
        Resque::enqueue('jobs', JobWithoutPerformMethod::className());
        $job = $this->worker->reserve();
        $job->worker = $this->worker;
        $job->perform();
    }

    /**
     * @expectedException \resque\core\Exception
     */
    public function testInvalidJobThrowsException()
    {
        Resque::enqueue('jobs', '\resque\test\job\InvalidJob');
        $job = $this->worker->reserve();
        $job->worker = $this->worker;
        $job->perform();
    }

    public function testJobWithSetUpCallbackFiresSetUp()
    {
        $payload = array(
            'class' => JobWithSetUp::className(),
            'args' => array(
                'somevar',
                'somevar2',
            ),
        );
        $job = new Job('jobs', $payload);
        $job->perform();

        $this->assertTrue(JobWithSetUp::$called);
    }

    public function testJobWithTearDownCallbackFiresTearDown()
    {
        $payload = array(
            'class' => JobWithTearDown::className(),
            'args' => array(
                'somevar',
                'somevar2',
            ),
        );
        $job = new Job('jobs', $payload);
        $job->perform();

        $this->assertTrue(JobWithTearDown::$called);
    }

    public function testJobWithNamespace()
    {
        Redis::prefix('php');
        $queue = 'jobs';
        $payload = array('another_value');
        Resque::enqueue($queue, JobWithTearDown::className(), $payload);

        $this->assertEquals(Resque::queues(), array('jobs'));
        $this->assertEquals(Resque::size($queue), 1);

        Redis::prefix('resque');
        $this->assertEquals(Resque::size($queue), 0);
    }
}
