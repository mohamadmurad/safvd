<?php

namespace App\Http\Controllers;

use App\Classes\FacebookDownloader;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;


class cc extends Controller
{

    private $context = [

        'http' => [

            'method' => 'GET',

            'header' => 'User-Agent: PostmanRuntime/7.28.0',

        ],

    ];

    function sendToAll(Request $request)
    {
        $message = "<b>هذه الرسالة من مطور البوت</b> \n";
        $message .= $request->get('message');
        //    $telegram = new Api(env('tokenApi'));
        /* $users = User::all();
         foreach ($users as $user){
             $response = $telegram->sendMessage([
                 'chat_id' => $user->user_id,
                 'text' => $message,
                 'parse_mode' => 'HTML',
             ]);
         }*/

    }

    function getAll(Request $request)
    {

        $users = User::all();
        return response()->json($users);

    }

    function log()
    {
        $logFile = 'laravel-2021-06-12.log';
        $lines = file($logFile); // Get each line of the file and store it as an array

// The regular expression to break lines
        $rgx = '/(\d+-\d+-\d+) (\d+:\d+:\d+) (\d+\.\d+\.\d+\.\d+) (.*? \(.*\)) (.*? .*?) (\d*) (.* email: .*@.*?) (.*$)/';


        foreach ($lines as $line) {

            $output = preg_split($rgx, $line);

            echo "<td>$output[0]</td>\n";


        }

    }

    function recive(Request $request)
    {


        $telegram = new Api(env('tokenApi'));
        $data = $request->all();
        $update_id = isset($data['update_id']) ? $data['update_id'] : '';
        $message = isset($data['message']) ? $data['message'] : '';
        $callback_query = isset($data['callback_query']) ? $data['callback_query'] : '';
        $callback_query_data = isset($callback_query['data']) ? $callback_query['data'] : '';

        $from = isset($message['from']) ? $message['from'] : '';
        $user_id = isset($from['id']) ? $from['id'] : '190861649';
        $user_first_name = isset($from['first_name']) ? $from['first_name'] : '';
        //$user_last_name = $from['last_name'];
        // $user_username = $from['username'];
        // $user_language_code = $from['language_code'];
        $text = isset($message['text']) ? $message['text'] : '/start';

        $recev_msg_id = isset($message['message_id']) ? $message['message_id'] : '9312';


        $keyboard = [
            'inline_keyboard' => []
        ];
//        $encodedKeyboard = json_encode($keyboard);
//
//        $response = $telegram->sendMessage([
//            'chat_id' => $user_id,
//            'text' => 'هذا البوت مخصص لتحميل فديوهات الفيسبوك فقط ولا يدعم الدردشة',
//            'parse_mode' => 'HTML',
//            'reply_markup' => $encodedKeyboard,
//        ]);

        $path = public_path('files');
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }


        if ($callback_query) {
            $response = $telegram->sendMessage([
                'chat_id' => $user_id,
                'text' => $callback_query_data,
                'parse_mode' => 'HTML'
            ]);

            $this->sendHD($update_id, $callback_query_data, $telegram, $user_id, $request, $callback_query['message']['message_id']);
            $this->sendSD($update_id,$callback_query_data,$telegram,$user_id,$request,$callback_query['message']['message_id']);
            return 0;
        }

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
                    $messageText = str_replace("m.", "ar-ar.", $messageText);



                    try {

                        $context = stream_context_create($this->context);
                       // $data_from_msg = file_get_contents($messageText, false, $context);
                      /*  $response =  Http::post('https://www.getfvid.com/nl/downloader',[
                            'url' => $messageText,
                        ]);*/
                     //   Log::info($response);

                      //  $link = $this->getSDLink($response);
                      //  Log::error($link);
                      //  $this->sendSD($update_id,$link,$telegram,$user_id,$request,$recev_msg_id);

                      //  $downloader = new FacebookDownloader();
                        $downloader = new downloader();
                       // $videoData = $downloader->getVideoInfo($text);
                        $videoData = $downloader->getVideoInfo($text);

                    Log::error($videoData);

                    return null;
                        if($videoData == false){

                            $response = $telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => 'خطأ',
                                'parse_mode' => 'HTML'
                            ]);
                            return 0;
                        }
                        if ($videoData['hd_download_url']){
                            $keyboard['inline_keyboard'] = [
                                ['text' => 'HD Link', 'callback_data' => $videoData['hd_download_url']]
                            ];
                        }
                        if ($videoData['sd_download_url']){
                            $keyboard['inline_keyboard'] = [
                                ['text' => 'HD Link', 'callback_data' => $videoData['sd_download_url']]
                            ];
                        }


                        $encodedKeyboard = json_encode($keyboard);

                        $response = $telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => 'الرجاء اختيار الدقة',
                            'parse_mode' => 'HTML',
                            'reply_markup' => $encodedKeyboard,
                        ]);

                        return 0;

                        if ($hdLink = $this->hdLink($data_from_msg)) {

                            $keyboard['inline_keyboard'] = [
                                ['text' => 'HD Link', 'callback_data' => $hdLink]
                            ];

//                            $vid_title = urlencode($this->getTitle($data_from_msg) .
//                                "\n\n<b>HD</b>\n\n<b>Downloaded by Syrian Addicted bot</b> \n\n @syrianaddicted \n\n @FVD_SA_bot");
//
//
//                            set_time_limit(0);
//                            $before = memory_get_usage();
//                            $vid_data = $this->file_get_contents_curl($hdLink);
//                            $after = memory_get_usage();
//
//                            $tot = $after - $before;
//
//                            if ($tot > 20971520) {
//                                $sdLink = $this->getSDLink($data_from_msg);
//                                $this->sendSD($update_id, $sdLink, $data_from_msg, $telegram, $user_id, $request, $recev_msg_id);
//                            } else {
//
//                                $this->sendHD($update_id, $vid_data, $telegram, $user_id, $request, $vid_title, $recev_msg_id);
//
//                            }


                        }
                        else if ($sdLink = $this->sdLink($data_from_msg)) {

                            $keyboard['inline_keyboard'] = [
                                ['text' => 'SD Link', 'callback_data' => $sdLink]
                            ];

//                            if ($callback_query_data && $callback_query){
//                                $this->sendSD($update_id, $callback_query_data, '', $telegram, $user_id, $request, $recev_msg_id);
//
//                            }


                        } else {
                            $this->sendMessage($user_id, "هذا العنوان غير صحيح");
                            return 0;
                        }

                        $encodedKeyboard = json_encode($keyboard);

                        $response = $telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => 'الرجاء اختيار الدقة',
                            'parse_mode' => 'HTML',
                            'reply_markup' => $encodedKeyboard,
                        ]);

                        return 0;

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


        return true;

    }

    public function sendSendingMessage($telegram, $user_id)
    {
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
    }

    function sendSD($update_id, $sdLink, $telegram, $user_id, $request, $recev_msg_id)
    {
        $this->sendSendingMessage($telegram, $user_id);


        set_time_limit(0);
        $vid_data = $this->file_get_contents_curl($sdLink);
        $vid_name = $update_id . rand() . ".mp4";

        $vid_title = urlencode( "\n\n<b>SD</b>\n\n<b>Downloaded by Syrian Addicted bot</b> \n\n @syrianaddicted \n\n @FVD_SA_bot");

        file_put_contents("files/" . $vid_name, $vid_data);


        $this->sendVido($user_id, $request->getSchemeAndHttpHost() . '/files/' . $vid_name, $vid_title, $recev_msg_id);


        File::delete(public_path("files/" . $vid_name));
    }

    function sendHD($update_id, $hdLink, $telegram, $user_id, $request, $recev_msg_id)
    {

        $this->sendSendingMessage($telegram, $user_id);
        $vid_data = $this->file_get_contents_curl($hdLink);
        $vid_name = $update_id . rand() . ".mp4";
        $vid_title = urlencode( "\n\n<b>HD</b>\n\n<b>Downloaded by Syrian Addicted bot</b> \n\n @syrianaddicted \n\n @FVD_SA_bot");


        file_put_contents("files/" . $vid_name, $vid_data);


        $this->sendVido($user_id, $request->getSchemeAndHttpHost() . '/files/' . $vid_name, $vid_title, $recev_msg_id);


        File::delete(public_path("files/" . $vid_name));
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
        $regexSrc = '/source src="([^"]+)"/';
        $fpos = strpos($curl_content,'<source src="');

        $ss1 =  substr($curl_content,$fpos+13);

        $tpos = strpos($ss1,'" type="video/mp4"');

        $ss2 =  substr($ss1,0,$tpos-1);

        return $ss2;
        if (preg_match($regexRateLimit, $curl_content, $match)) {

            return $match[1];

        } elseif (preg_match($regexSrc, $curl_content, $match)) {

            return $match[1];

        } else {

            return false;

        }

    }

    function sdLink($curl_content)
    {
        $regex = '/sd_src:"([^"]+)"/';
        if (preg_match($regex, $curl_content, $match1)) {
            return $match1[1];
        } else {
            return;
        }
    }

    function hdLink($curl_content)
    {
        $regex = '/hd_src:"([^"]+)"/';
        if (preg_match($regex, $curl_content, $match)) {
            return $match[1];
        } else {
            return;
        }
    }

    public function _parse($HTML) {
        $_values = [];
        $old_libxml_error = libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML($HTML);

        libxml_use_internal_errors($old_libxml_error);

        $tags = $doc->getElementsByTagName('meta');
        if (!$tags || $tags->length === 0) {
            return false;
        }

        $page = new self();

        $nonOgDescription = null;

        foreach ($tags AS $tag) {
            if ($tag->hasAttribute('property') &&
                strpos($tag->getAttribute('property'), 'og:') === 0) {
                $key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
                $_values[$key] = $tag->getAttribute('content');
            }

            //Added this if loop to retrieve description values from sites like the New York Times who have malformed it.
            if ($tag ->hasAttribute('value') && $tag->hasAttribute('property') &&
                strpos($tag->getAttribute('property'), 'og:') === 0) {
                $key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
                $_values[$key] = $tag->getAttribute('value');
            }
            //Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
            if ($tag->hasAttribute('name') && $tag->getAttribute('name') === 'description') {
                $nonOgDescription = $tag->getAttribute('content');
            }

        }
        //Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
        if (!isset($page->_values['title'])) {
            $titles = $doc->getElementsByTagName('title');
            if ($titles->length > 0) {
               $_values['title'] = $titles->item(0)->textContent;
            }
        }
        if (!isset($page->_values['description']) && $nonOgDescription) {
            $_values['description'] = $nonOgDescription;
        }

        //Fallback to use image_src if ogp::image isn't set.
        if (!isset($page->values['image'])) {
            $domxpath = new DOMXPath($doc);
            $elements = $domxpath->query("//link[@rel='image_src']");

            if ($elements->length > 0) {
                $domattr = $elements->item(0)->attributes->getNamedItem('href');
                if ($domattr) {
                    $_values['image'] = $domattr->value;
                    $_values['image_src'] = $domattr->value;
                }
            }
        }

        if (empty($_values)) { return false; }

        return $_values;
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


        $send_url = "https://api.telegram.org/bot" . env('tokenApi') . "/sendVideo?chat_id=" . $chat_id . "&video=" . $url . "&caption=" . $caption
            . "&parse_mode=html" . "&reply_to_message_id=" . $msg_url;


        file_get_contents($send_url);


    }

    function sendMessage($chat_id, $msg)
    {


        $send_url = "https://api.telegram.org/bot" . env('tokenApi') . "/sendMessage?chat_id=" . $chat_id
            . "&text=" . urlencode($msg) . "&parse_mode=html ";


        $result = file_get_contents($send_url);


        return $result;


    }

    function Edit_sending_MSG($chat_id, $msg_id, $txt)
    {


        $send_url = "https://api.telegram.org/bot" . env('tokenApi') . "/editMessageText?chat_id=" . $chat_id .
            "&message_id=" . $msg_id . "&parse_mode=HTML" . "&text=" . $txt;


        $result = file_get_contents($send_url);


    }

    function deleteMSG($chat_id, $msg_id)
    {


        $send_url = "https://api.telegram.org/bot" . env('tokenApi') . "/deleteMessage?chat_id=" . $chat_id . "&message_id=" . $msg_id . "&parse_mode=HTML";


        $result = file_get_contents($send_url);

    }

}
