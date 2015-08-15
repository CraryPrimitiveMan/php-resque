<?php
namespace resque\test\job;

class Job
{
    public static $called = false;

    public function perform()
    {
        self::$called = true;
    }
}
