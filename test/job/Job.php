<?php
namespace resque\test\job;

use resque\BaseJob;

class Job extends BaseJob
{
    public static $called = false;

    public function perform()
    {
        self::$called = true;
    }
}
