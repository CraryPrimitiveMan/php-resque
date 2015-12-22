<?php
use resque\BaseJob;

class ErrorJob extends BaseJob
{
    public function perform()
    {
        callToUndefinedFunction();
    }
}
