<?php
namespace resque\test\job;

class Job extends \resque\Job
{
    public static $called = false;

    public function perform()
    {
        self::$called = true;
    }
}
