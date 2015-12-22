<?php
use resque\BaseJob;

class BadJob extends BaseJob
{
    public function perform()
    {
        throw new Exception('Unable to run this job!');
    }
}
