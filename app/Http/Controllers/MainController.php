<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class MainController extends Controller
{

    public function index() {

    }

    public function test(LaravelFacebookSdk $fb) {

        $fbToken = Setting::get('facebook_token');
        $pageId = Setting::get('page_id');

        $fb->setDefaultAccessToken($fbToken);


        $pages = $fb->get('/me/accounts')->getBody();
        $pages = json_decode($pages, true)['data'];

        $page_token = '';

        foreach($pages as $page) {
            if($pageId == $page['id'])
                $page_token = $page['access_token'];
        }

        $fb->setDefaultAccessToken($page_token);

        $res = $fb->post('/'.$pageId.'/feed', [
           'message' => 'test'
        ]);

        dd($res);
    }
}
