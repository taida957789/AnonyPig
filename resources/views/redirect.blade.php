<!doctype html>
<html lang="zh_TW">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ Config::get('app.title') }}</title>
    <link href="assets/css/app.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="app">
    <div class="center-container">
        <h1>新告白元智 YZU Crushes</h1>

        <h2>預計將在 <span id="seconds"> {{ $seconds  }}</span> 後發布文章</h2>
    </div>
</div>
<script type="text/javascript">

    var redirect_url = '{{ $url }}';
    var s = parseInt('{{ $seconds }}');
    function countDown() {
        document.getElementById('seconds').innerText = s;
        s--;
        if(s > 0)
            setTimeout(countDown, 1000);
        else
            location.href = redirect_url;
    }

    countDown();
</script>
</body>
</html>