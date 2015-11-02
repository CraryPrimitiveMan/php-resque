<?php
namespace resque\test\job;

class JobWithTearDown extends \resque\Job
{
    public static $called = false;
    public $args = false;

    public function perform()
    {

    }

    public function tearDown()
    {
        self::$called = true;
    }
}
