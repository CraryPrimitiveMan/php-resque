<?php
namespace resque;

require_once __DIR__ . '/../autoload.php';

use resque\core\Worker;

/**
 * InitTrait, a init file for worker
 * @author Harry Sun <sunguangjun@126.com>
 * @copyright 2015 Harry Sun <sunguangjun@126.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package resque
 */

trait InitTrait
{
    /**
     * Job queues list
     * @var array
     */
    public $queues = ['default'];
    /**
     * Redis server conf
     * @var string/array
     */
    public $redisBackend = 'localhost:6379';
    /**
     * Redis server database
     * @var int
     */
    public $database = 0;
    /**
     * Log level of worker
     * @var int
     */
    public $logLevel = Worker::LOG_NONE;
    /**
     * Interval time of worker execute
     * @var int
     */
    public $interval = 5;
    /**
     * The count of workers
     * @var int
     */
    public $count = 1;
    /**
     * The file to save pid
     * @var string
     */
    public $pidFile;

    /**
     * Run to start workers
     */
    public function run()
    {
        if (empty($this->queues)) {
            die('Set queues var containing the list of queues to work.' . PHP_EOL);
        }

        Resque::setBackend($this->redisBackend, $this->database);

        if ($this->count > 1) {
            for ($i = 0; $i < $this->count; ++$i) {
                $pid = pcntl_fork();
                if ($pid == -1) {
                    die('Could not fork worker ' . $i . PHP_EOL);
                } else if (!$pid) {
                    // Child, start the worker
                    $this->_startWorker();
                    break;
                }
            }
        } else {
            if (!empty($this->pidFile)) {
                file_put_contents($this->pidFile, getmypid()) or
                    die('Could not write PID information to ' . $PIDFILE . PHP_EOL);
            }
            // Start a single worker
            $this->_startWorker();
        }
    }

    /**
     * Start a worker
     */
    protected function _startWorker()
    {
        $worker = new Worker($this->queues);
        $worker->logLevel = $this->logLevel;
        fwrite(STDOUT, '*** Starting worker ' . $worker . PHP_EOL);
        $worker->work($this->interval);
    }
}
