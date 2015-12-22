<?php
use resque\BaseJob;

class Job extends BaseJob
{
    public function perform()
    {
        // sleep(120);
        fwrite(STDOUT, 'Hello!');
    }
}
