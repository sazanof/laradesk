<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{$name}}</title>

    @vite('resources/js/app.js')
</head>
<body>
<div id="app" @guest class="guest" @endguest style="background-image: url('{{$bg}}')"></div>
</body>
</html>
