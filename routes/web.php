<?php

use http\Client\Request;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Traits\Telegram;
use Telegram\Bot\Api;
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

Route::get('/c', function () {
    return 'dd';
});


Route::post('/939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/webhook',[\App\Http\Controllers\cc::class,'recive']);

Route::get('/939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/webhook', function (Request $request) {
  //  $updates = Telegram::getWebhookUpdates();
    $telegram = new Api('939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU');
   // $update = $telegram->getUpdates();
    //$result = $update['result'];
    $response = $telegram->sendMessage([
        'chat_id' => '190861649',
        'text' => 'Hello ' . $request,
    ]);
    $response = $telegram->sendMessage([
        'chat_id' => '190861649',
        'text' => 'Hello m'
    ]);
    return true;
});
