<?php

namespace App\Http\Controllers;

use App\Classes\FacebookDownloader;
use Illuminate\Http\Request;

class MainController extends Controller
{

    public function index(){
        return view('home');
    }

    public function download(Request $request){
        $request->validate([
            "videourl" => 'required'
        ]);
        $url = $request->input('videourl');
        $url = 'https://www.facebook.com/133584260669221/posts/737568603604114/?app=fbl';
        $data = [
            'url' => $url
        ];
        switch ($this->detectWebsite($url)){
            case 'youtube':
                if($this->detectPlaylist($url) == false){
                    $yt = new \YouTubeDownloader();
                    $links = $yt->getDownloadLinks($url);
                    $id = $yt->extractId($url);
                    $content = file_get_contents("http://youtube.com/get_video_info?video_id=" . $id);
                    parse_str($content, $ytarr);
                    $img = "https://img.youtube.com/vi/".$id."/0.jpg";
                    $data['links'] = $links;
                    $data['thumbnail'] = $img;
                    $data['title'] = @$ytarr['title'];
                    return view('download.youtube-video',$data);
                }else{
                    $playlist_id = $this->detectPlaylist($url);
                    $url = "https://www.youtube.com/list_ajax?style=json&action_get_list=1&list=PLvah45Gv0-CfNA0zfS4Sr6ov0pAL96Fr1";
                    $playlistData = json_decode(file_get_contents($url),true);
                    $data['playlist'] = $playlistData;
                    return view('download.youtube-playlist',$data);
                }
                break;
            case 'facebook':
                $downloader = new FacebookDownloader();
                $videoData = $downloader->getVideoInfo($url);

                if($videoData == false){
                    return redirect('/')->withErrors(["Can't download private videos"]);
                }
                $data['videoData'] = $videoData;
                return view('download.facebook',$data);
                break;
            case 'vimeo':
                break;
            case 'unknown':
                return redirect()->back()->withErrors(["Invalid Url"]);
                break;
        }
    }

    public function detectWebsite($url){
        if (strpos($url, 'youtube') > 0) {
            return 'youtube';
        } elseif (strpos($url, 'vimeo') > 0) {
            return 'vimeo';
        }elseif (strpos($url, 'facebook') > 0) {
            return 'facebook';
        } else {
            return 'unknown';
        }
    }


}

