<?php

namespace App\Http\Controllers;

use App\Post;
use App\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class MainController extends Controller
{

    /**
     *  Display the page we can feed anonymous post
     * @return \Illuminate\View\View
     */
    public function index() {
        return view('post');
    }

    /**
     *  Publish post
     * @param LaravelFacebookSdk $fb
     * @return \Illuminate\View\View
     */
    public function post(LaravelFacebookSdk $fb) {

        $pageToken = Setting::get('page_token');
        $pageId = Setting::get('page_id');

        // Get page token and set it as default token first
        $fb->setDefaultAccessToken($pageToken);

        // Check token is not empty and is not expired , otherwise , reequest new page token
        if($pageToken == '' || $fb->getDefaultAccessToken()->isExpired()) {

            // Change back to the facebook user
            $fbToken = Setting::get('facebook_token');
            $fb->setDefaultAccessToken($fbToken);

            // Get all pages data
            $pages = $fb->get('/me/accounts')->getBody();
            $pages = json_decode($pages, true)['data'];

            // Find the page that we set in '/settings' page
            foreach($pages as $page) {
                if($pageId == $page['id'])
                    $pageToken = $page['access_token'];
            }
            // Change to page user
            $fb->setDefaultAccessToken($pageToken);
        }


        $content = Request::input('content');

        // Add Post data to database
        // ( This is useless now , but we use it linking the job in queue )
        $post = Post::addPost($content);

        // format the publish content ( hashtag .. or some information )
        $hashTag = Setting::get('hash_tag');

        $content = '#'.$hashTag.'_'.$post->id."\n\n".$content;

        // publish it
        // ##### We will use async task in the future #####
        $res = $fb->post('/'.$pageId.'/feed', [
            'message' => $content
        ]);

        $decodeBody = $res->getDecodedBody();

        // update the post_id in database
        Post::updatePostId($post->id, $decodeBody['id']);


        return view('redirect', [
            'url' => 'http://fb.com/'.$decodeBody['id'],
            'seconds' => 5,
        ]);

    }
}
