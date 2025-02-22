#!/usr/bin/env php
<?php

use Symfony\Component\Console\Input\ArgvInput;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Bootstrap Laravel and handle the command...
$status = $app->handleCommand(new ArgvInput);
    
$artisan = app('Illuminate\Support\Facades\Artisan');
$artisan::call('schedule:work');

exit($status);