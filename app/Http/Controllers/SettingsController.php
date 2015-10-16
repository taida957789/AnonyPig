<?php

namespace App\Http\Controllers;

use App\Setting;
use Facebook\Exceptions\FacebookSDKException;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Mockery\CountValidator\Exception;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class SettingsController extends Controller
{

    public function index()
    {

        if(Session::get('login') === true) {
            $pageId = Setting::get('page_id');
            $fbToken = Setting::get('facebook_token');
            $hashTag = Setting::get('hash_tag');

            return view('settings', [
                'pageId' => $pageId,
                'fbToken' => $fbToken,
                'hashTag' => $hashTag
            ]);
        } else {
            return redirect('/settings/facebook/login');
        }
    }

    public function save(Request $request)
    {
        if(Session::get('login') === true) {
            $pageId = $request->input('page_id');
            $fbToken = $request->input('facebook_token');
            $hashTag = $request->input('hash_tag');

            Setting::set('page_id', $pageId);
            Setting::set('facebook_token', $fbToken);
            Setting::set('hash_tag', $hashTag);
            return view('settings', [
                'message' => '儲存成功',
                'pageId' => $pageId,
                'fbToken' => $fbToken,
                'hashTag' => $hashTag
            ]);

        } else {
            return redirect('/settings/facebook/login');
        }
    }

    public function login(LaravelFacebookSdk $fb)
    {
        $login_url = $fb->getLoginUrl(['email', 'publish_pages', 'manage_pages']);

        return redirect($login_url);
    }

    public function callback(LaravelFacebookSdk $fb)
    {
        $token = null;
        try {
            $token = $fb
                ->getRedirectLoginHelper()
                ->getAccessToken();
        } catch (FacebookSDKException $e) {
            return redirect('/');
        }

        if (! $token) {

            $helper = $fb->getRedirectLoginHelper();

            if (! $helper->getError()) {
                abort(403, 'Unauthorized action.');
            }
            return redirect('/');
            /*dd(
                $helper->getError(),
                $helper->getErrorCode(),
                $helper->getErrorReason(),
                $helper->getErrorDescription()
            );*/
        }
        $appToken = $fb->getApp()->getAccessToken();
        $fb->setDefaultAccessToken($appToken);
        $res = $fb->get('/'.env('FACEBOOK_APP_ID').'/roles');

        $body = $res->getDecodedBody();

        $admins = $body['data'];

        $fb->setDefaultAccessToken($token);
        $res = $fb->get('/me');
        $body = $res->getDecodedBody();
        $uid = $body['id'];

        $check = false;

        foreach($admins as $admin) {
            if($admin['user'] == $uid)
                $check = true;
        }

        if(!$check)
            return redirect('/');


        if (! $token->isLongLived()) {

            $oauth_client = $fb->getOAuth2Client();

            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (FacebookSDKException $e) {
                return redirect('/');
            }
        }



        Setting::set('facebook_token', $token->getValue());

        $fb->setDefaultAccessToken($token);

        try {
            $pages = $fb->get('/me/accounts')->getBody();
            $pages = json_decode($pages, true)['data'];

            $page_token = '';
            $pageId = Setting::get('page_id');

            foreach ($pages as $page) {
                if ($pageId == $page['id'])
                    $page_token = $page['access_token'];
            }

        } catch (Exception $e) {
            return redirect('/');
        }

        Session::put('login', true);
        Setting::set('page_token', $page_token);
        return redirect('/settings');
    }

}
