<?php
namespace resque\test\job;

class FailingJob extends \resque\Job
{
    public function perform()
    {
        throw new FailingException('Message!');
    }
}
