<!doctype html>
<html lang="zh_TW">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ Config::get('app.title') }}</title>
    <link href="assets/css/app.css" rel="stylesheet" type="text/css">
    <link href="assets/css/hover-min.css" rel="stylesheet" rel="stylesheet" media="all">
</head>
<body>
<div class="app">
    <div class="center-container">
        <h1>新告白元智 YZU Crushes</h1>

        <form id="appform" action="/" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <ul class="row">
                <li>告白內容</li>
                <li>
                    <textarea name="content" id="content" ></textarea>
                </li>
            </ul>
            <ul class="rules row">
                <li>告白須知</li>
                <li>
                    <ul>
                        <li>請勿做任何人身攻擊</li>
                        <li>請勿發佈任何具有性騷擾、猥褻、令人不舒服的字眼</li>
                    </ul>
                </li>
            </ul>
            <ul class="row">
                <li>
                    <a id="submit" type="submit" class="button hvr-sweep-to-right" onclick="javascript:appform.submit();">我要告白</a>
                </li>
            </ul>
        </form>
    </div>
</div>
</body>
</html>