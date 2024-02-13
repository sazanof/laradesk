<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="public/css/pdf.css"/>
    <title>Ticket Export</title>
    <style>

    </style>
</head>
<body>
<div class="pdf">
    <div class="header">
        <img class="app-logo" src="{{public_path(\App\Helpers\ConfigHelper::getValue('app.logo'))}}"/>
        <div class="app-title">
            {{\App\Helpers\ConfigHelper::getValue('app.name')}}
        </div>
    </div>
    @yield('content')

    <div class="gen">{{__('pdf.gen')}}: {{\Illuminate\Support\Carbon::now()->format('d.m.Y H:i:s')}}</div>
</div>

</body>
</html>
