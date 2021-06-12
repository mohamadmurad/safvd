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

Route::post('webhook',[\App\Http\Controllers\cc::class,'recive']);
Route::post('/939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/webhook',[\App\Http\Controllers\cc::class,'recive']);

Route::post('/sendToAll',[\App\Http\Controllers\cc::class,'sendToAll']);

Route::get('/getAll',[\App\Http\Controllers\cc::class,'getAll']);
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('/939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/webhook', function (Request $request) {
  //  $updates = Telegram::getWebhookUpdates();
    $telegram = new Api('939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU');
   // $update = $request->get('update_id');

    //$result = $update['result'];
    dump('dsdsssssssss');

   // $image = file_get_contents("https://raw.githubusercontent.com/TelegramBots/book/master/src/docs/video-countdown.mp4");


  //  file_put_contents("files/1.mp4",$image);


   // $files = \Telegram\Bot\FileUpload\InputFile::create("/files/1.mp4");

//    $response = $telegram->sendVideo([
//        'chat_id' => '190861649',
//        'video' => $files,
//    ]);

//    $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/sendVideo?chat_id=" . '190861649' . "&video=" . 'https://raw.githubusercontent.com/TelegramBots/book/master/src/docs/video-countdown.mp4' . "&caption=" . 'fs'
//        . "&parse_mode=html&supports_streaming=true";
//
//
//    file_get_contents($send_url);
    $response = $telegram->sendMessage([
        'chat_id' => '190861649',
        'text' => 'Hello m'
    ]);
    return true;
});
