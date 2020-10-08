<?php

use Illuminate\Http\Request;
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
Route::post('/sendToAll',[\App\Http\Controllers\cc::class,'sendToAll']);

Route::get('/getAll',[\App\Http\Controllers\cc::class,'getAll']);

Route::get('/939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/webhook', function (Request $request) {
  //  $updates = Telegram::getWebhookUpdates();
    $telegram = new Api('939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU');
   // $update = $request->get('update_id');

    //$result = $update['result'];
    $files = \Telegram\Bot\FileUpload\InputFile::create('http://safvd.herokuapp.com/files/627254004235384452.mp4');
   /* $response = $telegram->sendVideo([
        'chat_id' => '190861649',
        'video' => new CURLFile('http://safvd.herokuapp.com/files/627254004235384452.mp4'),
    ]);*/

//    $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/sendVideo?chat_id=" . '190861649' . "&video=" . 'http://safvd.herokuapp.com/files/627254004235384452.mp4' . "&caption=" . 'fs'
//        . "&parse_mode=html";
//
//
//    file_get_contents($send_url);
    $response = $telegram->sendMessage([
        'chat_id' => '190861649',
        'text' => 'Hello m'
    ]);
    return true;
});
