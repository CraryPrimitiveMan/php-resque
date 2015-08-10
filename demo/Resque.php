<?php
date_default_timezone_set('GMT');
require __DIR__ . '/../src/Init.php';
require __DIR__ . '/Job.php';
require __DIR__ . '/BadJob.php';
require __DIR__ . '/LongJob.php';
require __DIR__ . '/ErrorJob.php';
require __DIR__ . '/ResquePlugin.php';

use resque\Init;

$init = new Init();
$init->run();