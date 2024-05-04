<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased">
        <h1>الاسم : {{ $data['name'] }}</h1>
        <h1>البريد الالكترونى : {{ $data['email'] }}</h1>
        <h1>رقم الهاتف : {{ $data['phone'] }}</h1>
        <h1>الرساله : {{ $data['message'] }}</h1>
    </body>
</html>