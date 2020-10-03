<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Traits\Telegram;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::post('/939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/webhook', function () {
    $updates = Telegram::getWebhookUpdates();

    return $updates;
});
