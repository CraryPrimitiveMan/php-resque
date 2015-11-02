<?php
namespace resque\test\job;

class JobWithSetUp extends \resque\Job
{
    public static $called = false;
    public $args = false;

    public function setUp()
    {
        self::$called = true;
    }

    public function perform()
    {

    }
}
