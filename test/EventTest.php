<?php
namespace resque\test;

require_once dirname(__FILE__) . '/bootstrap.php';

use resque\Resque;
use resque\core\Worker;
use resque\core\Job;
use resque\core\Event;
use resque\core\job\DontPerform;
use resque\test\job\Job as TestJob;

/**
 * resque\core\Event tests.
 *
 * @package     resque\test
 * @author      Chris Boulton <chris@bigcommerce.com>
 * @license     http://www.opensource.org/licenses/mit-license.php
 */
class EventTest extends TestCase
{
    private $callbacksHit = array();

    public function setUp()
    {
        TestJob::$called = false;

        // Register a worker to test with
        $this->worker = new Worker('jobs');
        $this->worker->registerWorker();
    }

    public function tearDown()
    {
        Event::clearListeners();
        $this->callbacksHit = array();
    }

    public function getEventTestJob()
    {
        $payload = array(
            'class' => TestJob::className(),
            'args' => array(
                'somevar',
            ),
        );
        $job = new Job('jobs', $payload);
        $job->worker = $this->worker;
        return $job;
    }

    public function eventCallbackProvider()
    {
        return array(
            array('beforePerform', 'beforePerformEventCallback'),
            array('afterPerform', 'afterPerformEventCallback'),
            array('afterFork', 'afterForkEventCallback'),
        );
    }

    /**
     * @dataProvider eventCallbackProvider
     */
    public function testEventCallbacksFire($event, $callback)
    {
        Event::listen($event, array($this, $callback));

        $job = $this->getEventTestJob();
        $this->worker->perform($job);
        $this->worker->work(0);

        $this->assertContains($callback, $this->callbacksHit, $event . ' callback (' . $callback .') was not called');
    }

    public function testBeforeForkEventCallbackFires()
    {
        $event = 'beforeFork';
        $callback = 'beforeForkEventCallback';

        Event::listen($event, array($this, $callback));
        Resque::enqueue('jobs', TestJob::className(), array(
            'somevar'
        ));
        $this->worker->work(0);
        $this->assertContains($callback, $this->callbacksHit, $event . ' callback (' . $callback .') was not called');
    }

    public function testBeforePerformEventCanStopWork()
    {
        $callback = 'beforePerformEventDontPerformCallback';
        Event::listen('beforePerform', array($this, $callback));

        $job = $this->getEventTestJob();

        $this->assertFalse($job->perform());
        $this->assertContains($callback, $this->callbacksHit, $callback . ' callback was not called');
        $this->assertFalse(TestJob::$called, 'Job was still performed though DontPerform was thrown');
    }

    public function testAfterEnqueueEventCallbackFires()
    {
        $callback = 'afterEnqueueEventCallback';
        $event = 'afterEnqueue';

        Event::listen($event, array($this, $callback));
        Resque::enqueue('jobs', TestJob::className(), array(
            'somevar'
        ));
        $this->assertContains($callback, $this->callbacksHit, $event . ' callback (' . $callback .') was not called');
    }

    public function testStopListeningRemovesListener()
    {
        $callback = 'beforePerformEventCallback';
        $event = 'beforePerform';

        Event::listen($event, array($this, $callback));
        Event::stopListening($event, array($this, $callback));

        $job = $this->getEventTestJob();
        $this->worker->perform($job);
        $this->worker->work(0);

        $this->assertNotContains($callback, $this->callbacksHit,
            $event . ' callback (' . $callback .') was called though Event::stopListening was called'
        );
    }


    public function beforePerformEventDontPerformCallback($instance)
    {
        $this->callbacksHit[] = __FUNCTION__;
        throw new DontPerform;
    }

    public function assertValidEventCallback($function, $job)
    {
        $this->callbacksHit[] = $function;
        if (!$job instanceof Job) {
            $this->fail('Callback job argument is not an instance of \resque\resque\Job');
        }
        $args = $job->getArguments();
        $this->assertEquals($args[0], 'somevar');
    }

    public function afterEnqueueEventCallback($class, $args)
    {
        $this->callbacksHit[] = __FUNCTION__;
        $this->assertEquals(TestJob::className(), $class);
        $this->assertEquals(array(
            'somevar',
        ), $args);
    }

    public function beforePerformEventCallback($job)
    {
        $this->assertValidEventCallback(__FUNCTION__, $job);
    }

    public function afterPerformEventCallback($job)
    {
        $this->assertValidEventCallback(__FUNCTION__, $job);
    }

    public function beforeForkEventCallback($job)
    {
        $this->assertValidEventCallback(__FUNCTION__, $job);
    }

    public function afterForkEventCallback($job)
    {
        $this->assertValidEventCallback(__FUNCTION__, $job);
    }
}
