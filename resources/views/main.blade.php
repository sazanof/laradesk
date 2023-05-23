<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{$name}}</title>

    @vite('resources/js/app.js')
</head>
<body>
<div class="wrapper">
    <input type="hidden" id="appName" value="{{$name}}">
    <input type="hidden" id="appBg" value="{{$bg}}">
    <div class="bg" style="background-image: url('{{$bg}}')"></div>
    <div id="app"></div>
</div>
</body>
</html>
