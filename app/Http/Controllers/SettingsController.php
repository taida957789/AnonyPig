<?php

namespace App\Http\Controllers;

use App\Setting;
use Facebook\Exceptions\FacebookSDKException;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class SettingsController extends Controller
{

    public function index()
    {

        $pageId = Setting::get('page_id');
        $fbToken = Setting::get('facebook_token');

        return view('settings', [
            'pageId' => $pageId,
            'fbToken' => $fbToken
        ]);
    }

    public function save(Request $request) {
        $pageId = $request->input('page_id');
        $fbToken = $request->input('facebook_token');
        Setting::set('page_id', $pageId);
        Setting::set('facebook_token', $fbToken);
        return view('settings', [
            'message' => '儲存成功',
            'pageId' => $pageId,
            'fbToken' => $fbToken
        ]);
    }

    public function login(LaravelFacebookSdk $fb) {

        $login_url = $fb->getLoginUrl(['email', 'publish_pages', 'manage_pages']);

        return redirect($login_url);
    }

    public function callback(LaravelFacebookSdk $fb) {
        $token = null;
        try {
            $token = $fb
                ->getRedirectLoginHelper()
                ->getAccessToken();
        } catch (FacebookSDKException $e) {
            dd($e->getMessage());
        }

        if (! $token) {

            $helper = $fb->getRedirectLoginHelper();

            if (! $helper->getError()) {
                abort(403, 'Unauthorized action.');
            }

            dd(
                $helper->getError(),
                $helper->getErrorCode(),
                $helper->getErrorReason(),
                $helper->getErrorDescription()
            );
        }

        if (! $token->isLongLived()) {

            $oauth_client = $fb->getOAuth2Client();

            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (FacebookSDKException $e) {
                dd($e->getMessage());
            }
        }

        Setting::set('facebook_token', $token->getValue());
        $fb->setDefaultAccessToken($token);

        return redirect('/settings');
    }

}
