<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{{ \App\Helpers\ConfigHelper::getValue('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body style="padding:0; margin: 0;font-family: Lato, Helvetica, Roboto, Arial, sans-serif;">
<div style="background-color: #ecf2f5;padding: 30px;font-family: Lato, Helvetica, Roboto, Arial, sans-serif;">
    <img src="{{url(\App\Helpers\ConfigHelper::getValue('app.logo'))}}"
         style="margin: 16px auto;display: block; width: 100px;height: auto">
    <p style="font-size: 24px; margin-bottom:10px; color:rgb(234,91,20);font-weight: bold; text-align: center">{{\App\Models\Config::appName()}}</p>
    @isset($ticket)
        <p style="font-size:20px;
        margin-bottom: 24px;
        color:rgb(66,66,66);
        font-weight: bold;
        text-align: center">{{$ticket->department->name}}</p>
    @endisset
    <div
        style="margin:0 auto;font-family: Lato, Helvetica, Roboto, Arial, sans-serif;max-width: 500px;background-color:white;padding:20px ">
        <p style="font-size: 18px;font-weight: bold;text-align: center">{{$subject}}</p>
        @yield('content')
    </div>
</div>


</body>
</html>
