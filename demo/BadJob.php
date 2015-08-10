<?php
class BadJob
{
    public function perform()
    {
        throw new Exception('Unable to run this job!');
    }
}