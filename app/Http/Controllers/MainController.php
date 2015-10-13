<?php

namespace App\Http\Controllers;

use App\Post;
use App\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class MainController extends Controller
{

    public function index() {
        return view('post');
    }

    public function post(LaravelFacebookSdk $fb) {

        $pageToken = Setting::get('page_token');
        $pageId = Setting::get('page_id');

        $fb->setDefaultAccessToken($pageToken);

        if($pageToken == '' || $fb->getDefaultAccessToken()->isExpired()) {

            $fbToken = Setting::get('facebook_token');
            $fb->setDefaultAccessToken($fbToken);

            $pages = $fb->get('/me/accounts')->getBody();
            $pages = json_decode($pages, true)['data'];

            foreach($pages as $page) {
                if($pageId == $page['id'])
                    $pageToken = $page['access_token'];
            }
            $fb->setDefaultAccessToken($pageToken);
        }

        $content = Request::input('content');
        $post = Post::addPost($content);

        $hashTag = Setting::get('hash_tag');

        $content = '#'.$hashTag.'_'.$post->id."\n\n".$content;

        $res = $fb->post('/'.$pageId.'/feed', [
            'message' => $content
        ]);

        $decodeBody = $res->getDecodedBody();

        Post::updatePostId($post->id, $decodeBody['id']);


        return view('redirect', [
            'url' => 'http://fb.com/'.$decodeBody['id'],
            'seconds' => 5,
        ]);

    }
}
