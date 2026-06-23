<?php

use App\Jobs\ProcessOrderTest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-msg', function () {
    ProcessOrderTest::dispatch("Order created at " . now());
    return "Message sent to RabbitMQ!";
});
