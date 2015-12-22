<?php
namespace resque\test\job;

use resque\BaseJob;

class FailingJob extends BaseJob
{
    public function perform()
    {
        throw new FailingException('Message!');
    }
}
