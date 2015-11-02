<?php
class Job extends \resque\Job
{
    public function perform()
    {
        sleep(120);
        fwrite(STDOUT, 'Hello!');
    }
}
