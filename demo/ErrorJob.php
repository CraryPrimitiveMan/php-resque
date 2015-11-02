<?php
class ErrorJob extends \resque\Job
{
    public function perform()
    {
        callToUndefinedFunction();
    }
}
