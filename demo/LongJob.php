<?php
class LongJob extends \resque\Job
{
    public function perform()
    {
        sleep(600);
    }
}
