<!doctype html>
<html lang="zh_TW">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ Config::get('app.title') }}</title>
    <link href="{{ cached_asset('/assets/css/app.css') }}" rel="stylesheet" type="text/css">
    <link href="/assets/css/hover-min.css" rel="stylesheet" rel="stylesheet" media="all">
</head>
<body>
<div class="app">
    <div class="center-container">
        <h1>新告白元智</h1>
        <h2>YZU Crushes</h2>
        <form id="appform" action="/" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            @if ($errors->has())
                <ul class="row">
                    <li>
                        <ul class="message-box">
                            @foreach ($errors->all() as $error)
                                <li class="message-error">
                                    ⋆ {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>

            @endif
            <ul class="row">
                <li class="desc">在下方欄位寫下你想要告白的內容，請勿留下任何敏感資訊。</li>
                <li class="desc">圖片在內容中插入一行在 <a href="http://imgur.com">imgur</a> 所上傳的圖片的網址即可顯示</li>
                <li>
                    <textarea name="content" id="content" ></textarea>
                </li>
            </ul>
            <ul class="recaptcha row">
                <li>你是人類逆？</li>
                <li>
                    {!! Recaptcha::render() !!}
                </li>
            </ul>
            <ul class="rules row">
                <li>告白規範</li>
                <li>
                    <ul>
                        <li>請勿發佈測試文</li>
                        <li>請勿做任何人身攻擊</li>
                        <li>請勿發佈任何具有性騷擾、猥褻、令人不舒服的字眼</li>
                    </ul>
                </li>

            </ul>
            <ul class="row">
                <li>
                    <input type="checkbox" name="check_rule" required value="true"/> 我遵守此規範
                </li>
            </ul>
            <ul class="row">
                <li>
                    <button class="button hvr-sweep-to-right" type="submit" >我要告白</button>
                </li>
            </ul>
        </form>
    </div>
</div>
</body>
</html>