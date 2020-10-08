<?php

namespace App\Http\Controllers;

use App\Models\User;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class cc extends Controller
{


    private $context = [

        'http' => [

            'method' => 'GET',

            'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36',

        ],

    ];

    function sendToAll(Request $request){
        $message = $request->get('message');
        $telegram = new Api('939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU');
        $users = User::all();
        foreach ($users as $user){
            $response = $telegram->sendMessage([
                'chat_id' => $user->user_id,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
        }

    }
    function recive(Request $request)
    {

        $telegram = new Api('939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU');
        $data = $request->all();
        $update_id = $data['update_id'];
        $message = $data['message'];
        $from = $message['from'];
        $user_id = $from['id'];
        $user_first_name = $from['first_name'];
        $user_last_name = $from['last_name'];
        $user_username = $from['username'];
        $user_language_code = $from['language_code'];
        $text = $message['text'];
        $recev_msg_id =$message['message_id'];

        $path = public_path('files');
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        $user = User::where('user_id','=',$user_id)->get();
        if (count($user) == 0){
            User::create([
                'first_name'=>$user_first_name,
                'last_name'=>$user_last_name,
                'username'=>$user_username,
                'language_code'=>$user_language_code,
                'user_id'=>$user_id,
            ]);
        }

        $messageToSend = "Hello <b>" . $user_first_name . '</b> we are coming soon';
        if (!empty($text)) {
            if ($text !== "/start") {
                if (!filter_var($text, FILTER_VALIDATE_URL)) {

                    $response = $telegram->sendMessage([
                        'chat_id' => $user_id,
                        'text' => 'هذا البوت مخصص لتحميل فديوهات الفيسبوك فقط ولا يدعم الدردشة',
                        'parse_mode' => 'HTML',
                    ]);

                } else {
                    $messageText = str_replace("m.", "www.", $text);
                    $messageText = str_replace("m.", "ar-ar.", $text);

                    try {

                        $context = stream_context_create($this->context);
                        $data_from_msg = file_get_contents($messageText, false, $context);



                        if ($hdLink = $this->getHDLink($data_from_msg)){

                            $vid_title = urlencode($this->getTitle($data_from_msg) . "\n\n<b>HD</b>\n\n<b>Downloaded by Syrian Addicted bot</b> \n\n @syrianaddicted \n\n @FVD_SA_bot");


                            set_time_limit(0);

                            $vid_data = $this->file_get_contents_curl($hdLink);
                            $vid_name = $update_id.  rand() . ".mp4";
                            file_put_contents( "files/".$vid_name, $vid_data );

                            $response = $telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => 'جارِ ارسال الفديو...',
                                'parse_mode' => 'HTML',
                            ]);


                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو..',
                                'parse_mode' => 'HTML',
                            ]);

                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو.',
                                'parse_mode' => 'HTML',
                            ]);

                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو',
                                'parse_mode' => 'HTML',
                            ]);


                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو...',
                                'parse_mode' => 'HTML',
                            ]);


                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو..',
                                'parse_mode' => 'HTML',
                            ]);

                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو.',
                                'parse_mode' => 'HTML',
                            ]);

                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو',
                                'parse_mode' => 'HTML',
                            ]);








                            $response = $telegram->deleteMessage([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                            ]);


                            $this->sendVido($user_id,$request->getSchemeAndHttpHost() . '/files/'. $vid_name,  $vid_title,$recev_msg_id);


                            $response = $telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => 'We are back to work now',
                                'parse_mode' => 'HTML',
                            ]);
                            File::delete(public_path("files/".$vid_name));


                        }else  if ($sdLink = $this->getSDLink($data_from_msg)) {

                            set_time_limit(0);
                            $vid_data = $this->file_get_contents_curl($sdLink);
                            $vid_name = $update_id.  rand() . ".mp4";

                            $vid_title = urlencode($this->getTitle($data_from_msg) . "\n\n<b>SD</b>\n\n<b>Downloaded by Syrian Addicted bot</b> \n\n @syrianaddicted \n\n @FVD_SA_bot");


                            //dd($path);
                            file_put_contents( "files/".$vid_name, $vid_data );

                            $response = $telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => 'جارِ ارسال الفديو...',
                                'parse_mode' => 'HTML',
                            ]);


                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو..',
                                'parse_mode' => 'HTML',
                            ]);

                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو.',
                                'parse_mode' => 'HTML',
                            ]);

                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو',
                                'parse_mode' => 'HTML',
                            ]);


                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو...',
                                'parse_mode' => 'HTML',
                            ]);


                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو..',
                                'parse_mode' => 'HTML',
                            ]);

                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو.',
                                'parse_mode' => 'HTML',
                            ]);

                            $response = $telegram->editMessageText([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                                'text' => 'جارِ ارسال الفديو',
                                'parse_mode' => 'HTML',
                            ]);







                            $response = $telegram->deleteMessage([
                                'chat_id' => $user_id,
                                'message_id' => $response->getMessageId(),
                            ]);

                            $this->sendVido($user_id,$request->getSchemeAndHttpHost() . '/files/'. $vid_name,  $vid_title,$recev_msg_id);

                            $response = $telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => 'We are back to work now',
                                'parse_mode' => 'HTML',
                            ]);
                            File::delete(public_path("files/".$vid_name));

                        }else{
                            $this->sendMessage($user_id,"هذا العنوان غير صحيح");
                        }

                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }


                }

            } else {

                $response = $telegram->sendMessage([
                    'chat_id' => $user_id,
                    'text' => 'اهلا ' . $user_first_name . 'بك في بوت مدمن سوري لتحميل فديوهات فيسبوك',
                    'parse_mode' => 'HTML',
                ]);

            }
        } else {
            $response = $telegram->sendMessage([
                'chat_id' => $user_id,
                'text' => 'هذا البوت مخصص لتحميل فديوهات الفيسبوك فقط ولا يدعم الدردشة',
                'parse_mode' => 'HTML',
            ]);
        }

//        $response = $telegram->sendMessage([
//            'chat_id' => $user_id,
//            'text' => $messageToSend,
//            'parse_mode' => 'HTML',
//        ]);

        return true;

    }

    function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }


    function generateId($url)

    {

        $id = '';

        if (is_int($url)) {

            $id = $url;

        } elseif (preg_match('#(\d+)/?$#', $url, $matches)) {

            $id = $matches[1];

        }


        return $id;

    }


    function cleanStr($str)

    {

        return html_entity_decode(strip_tags($str), ENT_QUOTES, 'UTF-8');

    }


    function getSDLink($curl_content)

    {

        $regexRateLimit = '/sd_src_no_ratelimit:"([^"]+)"/';

        $regexSrc = '/sd_src:"([^"]+)"/';


        if (preg_match($regexRateLimit, $curl_content, $match)) {

            return $match[1];

        } elseif (preg_match($regexSrc, $curl_content, $match)) {

            return $match[1];

        } else {

            return false;

        }

    }


    function getHDLink($curl_content)

    {

        $regexRateLimit = '/hd_src_no_ratelimit:"([^"]+)"/';

        $regexSrc = '/hd_src:"([^"]+)"/';


        if (preg_match($regexRateLimit, $curl_content, $match)) {

            return $match[1];

        } elseif (preg_match($regexSrc, $curl_content, $match)) {

            return $match[1];

        } else {

            return false;

        }

    }


    function getTitle($curl_content)

    {

        $title = null;

        if (preg_match('/h2 class="uiHeaderTitle"?[^>]+>(.+?)<\/h2>/', $curl_content, $matches)) {

            $title = $matches[1];

        } elseif (preg_match('/title id="pageTitle">(.+?)<\/title>/', $curl_content, $matches)) {

            $title = $matches[1];

        }


        return $this->cleanStr($title);

    }


    function getDescription($curl_content)

    {

        if (preg_match('/span class="hasCaption">(.+?)<\/span>/', $curl_content, $matches)) {

            return cleanStr($matches[1]);

        }


        return false;

    }


    function sendVido($chat_id, $url, $caption, $msg_url)
    {


        $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/sendVideo?chat_id=" . $chat_id . "&video=" . $url . "&caption=" . $caption
            . "&parse_mode=html" . "&reply_to_message_id=" . $msg_url;


        file_get_contents($send_url);


    }


    function sendMessage($chat_id, $msg)
    {


        $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/sendMessage?chat_id=" . $chat_id
            . "&text=" . urlencode($msg) . "&parse_mode=html ";


        $result = file_get_contents($send_url);


        return $result;


    }


    function Edit_sending_MSG($chat_id, $msg_id, $txt)
    {


        $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/editMessageText?chat_id=" . $chat_id .
            "&message_id=" . $msg_id . "&parse_mode=HTML" . "&text=" . $txt;


        $result = file_get_contents($send_url);


    }

    function deleteMSG($chat_id, $msg_id)
    {


        $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/deleteMessage?chat_id=" . $chat_id . "&message_id=" . $msg_id . "&parse_mode=HTML";


        $result = file_get_contents($send_url);

    }
}
