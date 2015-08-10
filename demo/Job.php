<?php
class Job
{
    public function perform()
    {
        sleep(120);
        fwrite(STDOUT, 'Hello!');
    }
}