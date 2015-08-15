<?php
namespace resque\test\job;

class FailingJob
{
    public function perform()
    {
        throw new FailingException('Message!');
    }
}
