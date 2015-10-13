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
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class SettingsController extends Controller
{

    public function index()
    {

        $pageId = Setting::get('page_id');
        $fbToken = Setting::get('facebook_token');
        $autoInc = Setting::get('auto_inc', '0');

        if($autoInc == '0')
            Setting::set('auto_inc', '1');

        $autoInc = Setting::get('auto_inc', '0');
        $hashTag = Setting::get('hash_tag');

        return view('settings', [
            'pageId' => $pageId,
            'fbToken' => $fbToken,
            'autoInc' => $autoInc,
            'hashTag' => $hashTag
        ]);
    }

    public function save(Request $request)
    {

        if(Session::get('login') === true) {
            $pageId = $request->input('page_id');
            $fbToken = $request->input('facebook_token');
            $autoInc = $request->input('auto_inc');
            $hashTag = $request->input('hash_tag');

            Setting::set('page_id', $pageId);
            Setting::set('facebook_token', $fbToken);
            return view('settings', [
                'message' => '儲存成功',
                'pageId' => $pageId,
                'fbToken' => $fbToken,
                'autoInc' => $autoInc,
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

        if (! $token->isLongLived()) {

            $oauth_client = $fb->getOAuth2Client();

            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (FacebookSDKException $e) {
                return redirect('/');
            }
        }

        Session::put('login', true);
        Setting::set('facebook_token', $token->getValue());
        $fb->setDefaultAccessToken($token);

        return redirect('/settings');
    }

}
