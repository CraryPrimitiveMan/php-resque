<?php
require_once __DIR__ . '/../vendor/autoload.php';

use resque\core\Event;

// Somewhere in our application, we need to register:
Event::listen('afterEnqueue', array('ResquePlugin', 'afterEnqueue'));
Event::listen('beforeFirstFork', array('ResquePlugin', 'beforeFirstFork'));
Event::listen('beforeFork', array('ResquePlugin', 'beforeFork'));
Event::listen('afterFork', array('ResquePlugin', 'afterFork'));
Event::listen('beforePerform', array('ResquePlugin', 'beforePerform'));
Event::listen('afterPerform', array('ResquePlugin', 'afterPerform'));
Event::listen('onFailure', array('ResquePlugin', 'onFailure'));

class ResquePlugin
{
    public static function afterEnqueue($class, $arguments)
    {
        echo "Job was queued for " . $class . ". Arguments:";
        print_r($arguments);
    }
    
    public static function beforeFirstFork($worker)
    {
        echo "Worker started. Listening on queues: " . implode(', ', $worker->queues(false)) . "\n";
    }
    
    public static function beforeFork($job)
    {
        echo "Just about to fork to run " . $job;
    }
    
    public static function afterFork($job)
    {
        echo "Forked to run " . $job . ". This is the child process.\n";
    }
    
    public static function beforePerform($job)
    {
        echo "Cancelling " . $job . "\n";
    //  throw new Resque_Job_DontPerform;
    }
    
    public static function afterPerform($job)
    {
        echo "Just performed " . $job . "\n";
    }
    
    public static function onFailure($exception, $job)
    {
        echo $job . " threw an exception:\n" . $exception;
    }
}