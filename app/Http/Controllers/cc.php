<?php

namespace App\Http\Controllers;

use App\Classes\FacebookDownloader;
use App\Models\User;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Telegram\Bot\Api;


class cc extends Controller
{

    private $context = [

        'http' => [

            'method' => 'GET',

            'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36',

        ],

    ];

    function sendToAll(Request $request){
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

    function getAll(Request $request){

        $users = User::all();
       return response()->json($users);

    }

    function recive(Request $request)
    {

        $telegram = new Api(env('tokenApi'));
        $data = $request->all();
        $update_id =isset($data['update_id']) ? $data['update_id'] : '';
        $message = isset($data['message']) ? $data['message'] : '';
        $from = isset($message['from']) ? $message['from'] : '';
        $user_id = isset($from['id']) ? $from['id'] : '190861649';
        $user_first_name = isset($from['first_name']) ? $from['first_name'] : '';
        //$user_last_name = $from['last_name'];
       // $user_username = $from['username'];
       // $user_language_code = $from['language_code'];
        $text = isset($message['text']) ? $message['text'] : '/start';

        $recev_msg_id = isset($message['message_id']) ?$message['message_id'] : '9312';

        $path = public_path('files');
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

      /*  $user = User::where('user_id','=',$user_id)->get();
        if (count($user) == 0){
            User::create([
                'first_name'=>$user_first_name,
                'last_name'=>$user_last_name,
                'username'=>$user_username,
                'language_code'=>$user_language_code,
                'user_id'=>$user_id,
            ]);
        }*/

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
                    $messageText = str_replace("m.", "ar-ar.", $messageText);

                    try {

                        $context = stream_context_create($this->context);
                        $data_from_msg = file_get_contents($messageText, false, $context);

                        $downloader = new FacebookDownloader();
                        $videoData = $downloader->getVideoInfo($messageText);
                     //   if($videoData != false){

                        $this->sendSD($update_id , $videoData['sd_download_url'] , $data_from_msg , $telegram  , $user_id ,$request ,$recev_msg_id);
                            $response = $telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => $videoData['title'],
                                'parse_mode' => 'HTML',
                            ]);
//
                     //   }
                        if ($hdLink = $this->hdLink($data_from_msg)){



                            $vid_title = urlencode($this->getTitle($data_from_msg) .
                                "\n\n<b>HD</b>\n\n<b>Downloaded by Syrian Addicted bot</b> \n\n @syrianaddicted \n\n @FVD_SA_bot");


                            set_time_limit(0);
                            $before = memory_get_usage();
                            $vid_data = $this->file_get_contents_curl($hdLink);
                            $after = memory_get_usage();

                            $tot = $after- $before;

                            if($tot > 20971520){
                                $sdLink = $this->getSDLink($data_from_msg);
                                $this->sendSD($update_id , $sdLink , $data_from_msg , $telegram  , $user_id ,$request ,$recev_msg_id);
                            }else{

                                $this->sendHD($update_id ,$vid_data , $telegram ,$user_id ,$request ,$vid_title , $recev_msg_id );

                            }



                        }else  if ($sdLink = $this->sdLink($data_from_msg)) {

                            $this->sendSD($update_id , $sdLink , $data_from_msg , $telegram  , $user_id ,$request ,$recev_msg_id);


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

    function sendSD($update_id , $sdLink , $data_from_msg , $telegram  , $user_id ,$request ,$recev_msg_id){
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
    }

    function sendHD($update_id ,$vid_data , $telegram ,$user_id ,$request ,$vid_title , $recev_msg_id ){
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
