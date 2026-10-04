<?php

/**
 * Entry point untuk Vercel.
 *
 * Vercel tidak otomatis mengenali Laravel, jadi setiap request diarahkan ke
 * file ini sebagai "function" PHP, lalu Laravel booted secara manual di
 * bawah. Logikanya sama persis dengan yang dilakukan public/index.php.
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Mode maintenance. File ini tidak ikut ter-deploy (diabaikan .gitignore),
// jadi biasanya tidak ada dan blok di bawah dilewati.
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle($request = Request::capture())->send();

$kernel->terminate($request, $response);