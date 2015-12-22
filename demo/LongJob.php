<?php
use resque\BaseJob;

class LongJob extends BaseJob
{
    public function perform()
    {
        sleep(600);
    }
}
