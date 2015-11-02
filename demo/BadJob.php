<?php
class BadJob extends \resque\Job
{
    public function perform()
    {
        throw new Exception('Unable to run this job!');
    }
}
