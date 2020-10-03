<?php

namespace App\Http\Controllers;

use http\Exception;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

class cc extends Controller
{

    function recive(Request $request){

        $telegram = new Api('939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU');

//        $message_id = $message->message_id;
//        $user = $message->from;
//        $user_id = $user->id;
//        $user_first_name = $user->first_name;
//        $text = $message->text;

        $response = $telegram->sendMessage([
            'chat_id' => '190861649',
            'text' => 'Hello ' . $request,
        ]);

     /*   $response = $telegram->sendMessage([
            'chat_id' => '190861649',
            'text' => 'Hello ' . $message,
        ]);*/
//        $result = $update['result'];
//        $response = $telegram->sendMessage([
//            'chat_id' => '190861649',
//            'text' => 'Hello ' . $result
//        ]);
//        $message = $result->message;
//
//        $response = $telegram->sendMessage([
//            'chat_id' => '190861649',
//            'text' => 'Hello ' . $message
//        ]);
//
//        $from = $result->from;
//        $user_id =  $from->id;
//        $first_name = $from->first_name;
//        $user_id = $updateArray["message"]["from"]["id"];
//        $first_name = $updateArray["message"]["from"]["first_name"];
//        $last_name = $updateArray["message"]["from"]["last_name"];
//        $username = $updateArray["message"]["from"]["username"];
//        $messageText = $updateArray["message"]["text"];
//        $recev_msg_id = $update["message"]["message_id"];
//        $response = $telegram->sendMessage([
//            'chat_id' => '190861649',
//            'text' => 'Hello ' . $first_name
//        ]);
//
//        $messageId = $response->getMessageId();
//        $response = $telegram->sendMessage([
//            'chat_id' => '190861649',
//            'text' => $user_id,
//        ]);
        return true;

    }
    function file_get_contents_curl($url) {
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



        return cleanStr($title);

    }



    function getDescription($curl_content)

    {

        if (preg_match('/span class="hasCaption">(.+?)<\/span>/', $curl_content, $matches)) {

            return cleanStr($matches[1]);

        }



        return false;

    }



    function sendVido($chat_id,$url,$caption,$msg_url){



        $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/sendVideo?chat_id=".$chat_id . "&video=".$url . "&caption=".$caption
            ."&parse_mode=html" . "&reply_to_message_id=" . $msg_url;



        file_get_contents( $send_url);



    }



    function sendMessage($chat_id,$msg){



        $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/sendMessage?chat_id=".$chat_id
            . "&text=".urlencode($msg)."&parse_mode=html ";



        $result =  file_get_contents( $send_url);



        return $result;



    }




    function Edit_sending_MSG($chat_id,$msg_id,$txt){



        $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/editMessageText?chat_id=".$chat_id .
            "&message_id=" . $msg_id . "&parse_mode=HTML" . "&text=" . $txt;




        $result =  file_get_contents( $send_url);







    }

    function deleteMSG($chat_id,$msg_id){



        $send_url = "https://api.telegram.org/bot939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU/deleteMessage?chat_id=".$chat_id . "&message_id=" . $msg_id . "&parse_mode=HTML";



        $result =  file_get_contents( $send_url);

    }
}
