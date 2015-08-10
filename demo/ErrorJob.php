<?php
class ErrorJob
{
    public function perform()
    {
        callToUndefinedFunction();
    }
}