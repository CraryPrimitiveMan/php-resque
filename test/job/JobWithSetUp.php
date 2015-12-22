<?php
namespace resque\test\job;

use resque\BaseJob;

class JobWithSetUp extends BaseJob
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
