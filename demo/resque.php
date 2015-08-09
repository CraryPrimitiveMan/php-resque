<?php
date_default_timezone_set('GMT');
require __DIR__ . '/../src/Init.php';
require __DIR__ . '/job.php';

use resque\Init;

$init = new Init();
$init->run();