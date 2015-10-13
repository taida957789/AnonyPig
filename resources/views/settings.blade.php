<!doctype html>
<html lang="zh_TW">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ Config::get('app.title') }}</title>
    <link href="assets/css/settings.css" rel="stylesheet" type="text/css">
    <link href="assets/css/hover-min.css" rel="stylesheet" rel="stylesheet" media="all">
</head>
<body>
    <div class="app">
        <div class="center-container">
            <h1>頁面設定</h1>
            @if(isset($message))
            <div class="message-box">
                <div class="message">{{ $message }}</div>
            </div>
            @endif
            <form id="appform" action="/settings" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <ul>
                    <li>粉絲專業編號</li>
                    <li><input name="page_id" type="text" value="{{ $pageId }}"/></li>
                </ul>
                <ul>
                    <li>臉書授權代碼</li>
                    <li><textarea name="facebook_token">{{ $fbToken }}</textarea></li>
                </ul>
                <ul>
                    <li>
                        <a id="submit" type="submit" class="button hvr-sweep-to-right" onclick="javascript:appform.submit();">儲存網站設定</a>
                    </li>
                </ul>
            </form>

            <ul>
                <li>
                    <a id="fblogin" href="/settings/facebook/login" class="button hvr-sweep-to-right">
                        重新取得Facebook授權
                    </a>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>