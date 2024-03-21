<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/js/error.js')
    <title>@yield('title')</title>

    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
    </style>
</head>
<body class="error-page" id="errorPage" style="display: none">
<div class="bg" style="background-image: url('{{\App\Helpers\ConfigHelper::getValue('app.bg')}}')"></div>
<div class="error-logo">
    <img src="{{\App\Helpers\ConfigHelper::getValue('app.logo')}}" alt="" width="120">
    <div class="error-code">
        @yield('code')
    </div>

    <div class="error-message">
        @yield('message')
    </div>
</div>

</body>
</html>
