<?php

namespace App\Http\Controllers;

use http\Exception;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

class cc extends Controller
{


    private $context = [

        'http' => [

            'method' => 'GET',

            'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36',

        ],

    ];

    function recive(Request $request)
    {

        $telegram = new Api('939919494:AAHHzgqUYKZ5STaV6nI0kFjhkO4mJw2ZvjU');
       /* $data = $request->all();
        $update_id = $data['update_id'];
        $message = $data['message'];
        $from = $message['from'];
        $user_id = $from['id'];
        $user_first_name = $from['first_name'];
        $text = $message['text'];*/
        $response = $telegram->sendMessage([
            'chat_id' => '190861649',
            'text' => $request->all(),
            'parse_mode' => 'HTML',
        ]);



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
