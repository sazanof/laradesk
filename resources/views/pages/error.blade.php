<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>File not found</title>
    @vite('resources/js/error.js')
</head>
<body>
<div id="error" style="background-image:url('{{$bg}}')">
    <div class="error-text">
        <h1>{{$name}}</h1>
        <div class="error-title">{{$data}}</div>
        <div class="error-description">@yield('content')</div>
    </div>

</div>
</body>
</html>
