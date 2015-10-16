<?php

namespace App\Http\Controllers;

use App\Jobs\PublishPost;
use App\Post;
use App\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
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

        $rules = [
            'check_rule' => 'required',
            'g-recaptcha-response' => 'required|recaptcha',
        ];

        $validator = Validator::make(Request::all(), $rules);

        if ($validator->fails()) {
            return redirect('/')
                ->withErrors($validator)
                ->withInput();
        } else {

            $pageToken = Setting::get('page_token');
            $pageId = Setting::get('page_id');

            // Get page token and set it as default token first
            $fb->setDefaultAccessToken($pageToken);

            // Check token is not empty and is not expired , otherwise , reequest new page token
            if ($pageToken == '' || $fb->getDefaultAccessToken()->isExpired()) {

                // Change back to the facebook user
                $fbToken = Setting::get('facebook_token');
                $fb->setDefaultAccessToken($fbToken);

                // Get all pages data
                $pages = $fb->get('/me/accounts')->getBody();
                $pages = json_decode($pages, true)['data'];

                // Find the page that we set in '/settings' page
                foreach ($pages as $page) {
                    if ($pageId == $page['id'])
                        $pageToken = $page['access_token'];
                }
                // Change to page user
                $fb->setDefaultAccessToken($pageToken);
            }


            $content = Request::input('content');

            // Add Post data to database
            // ( This is useless now , but we use it linking the job in queue )
            $post = Post::addPost($content, Request::ip());

            // format the publish content ( hashtag .. or some information )
            //$hashTag = Setting::get('hash_tag');

            //$content = '#'.$hashTag.'_'.$post->id."\n\n".$content;

            // publish it
            // ##### We will use async task in the future #####
            //$res = $fb->post('/'.$pageId.'/feed', [
            //    'message' => $content
            //]);

            //$decodeBody = $res->getDecodedBody();

            // update the post_id in database
            //Post::updatePostId($post->id, $decodeBody['id']);

            $time = mt_rand(3, 10);
            $nextTime = $time * (Redis::zcard('queues:default:delayed') + 1);

            $job = (new PublishPost($post, $pageToken))->delay($nextTime);

            $nextTime += 2;

            $job_id = $this->dispatch($job);

            Post::updateJobId($post->id, $job_id);

            return view('redirect', [
                'seconds' => $nextTime,
                'postToken' => $post->token,
            ]);
        }

    }

    public function ping() {
        $postToken = Request::input('postToken');
        $post = POST::getPostByToken($postToken);
        if( $post != null && $post->post_id != '')
            return [
                'facebook_id' => $post->post_id
            ];
        else
            return [];
    }
}
