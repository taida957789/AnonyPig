<!doctype html>
<html lang="zh_TW">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ Config::get('app.title') }}</title>
    <link href="/assets/css/app.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="app">
    <div class="center-container">
        <h1>新告白元智 YZU Crushes</h1>

        <h2>預計將在 <span id="seconds"> {{ $seconds  }}</span> 秒後發布文章</h2>
    </div>
</div>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var postToken = '{{ $postToken }}';
    var s = parseInt('{{ $seconds }}');

    (function countdown() {
        document.getElementById('seconds').innerText = s;
        s--;
        if(s >= 0)
            setTimeout(countdown, 1000);
    })();

    (function check() {
        $.ajax({
            url : '/ping',
            method : "POST",
            dataType : 'json',
            data : {
                postToken : postToken
            },
            success: function(data) {

                if (data.facebook_id == null) {
                    setTimeout(check, Math.max(s/4, 1000));
                } else {
                    location.href = 'http://fb.com/' + data.facebook_id;
                }
            }
        });
    })();

</script>
</body>
</html>