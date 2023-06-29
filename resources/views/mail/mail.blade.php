<div
    style="font-family: Lato, Helvetica, Roboto, Arial, sans-serif;max-width: 600px;margin: 30px auto;border-radius: 20px;border:3px solid #efefef;padding: 30px">
    <p style="font-size: 24px; color:rgb(57,60,155);font-weight: bold; text-align: center">{{\App\Models\Config::appName()}}</p>
    <p style="font-size: 18px;font-weight: bold;text-align: center">{{$subject}}</p>
    @yield('content')
</div>
