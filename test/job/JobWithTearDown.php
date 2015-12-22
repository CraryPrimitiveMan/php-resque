<?php
namespace resque\test\job;

use resque\BaseJob;

class JobWithTearDown extends BaseJob
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
