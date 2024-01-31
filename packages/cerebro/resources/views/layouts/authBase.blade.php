<!DOCTYPE html>
<html lang="en">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>{{config('app.name')}}</title>

    <link rel="apple-touch-icon" href="{{ asset('/assets/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="384x384" href="{{ asset('/assets/favicon/android-icon-384x384.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('/assets/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/assets/favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('/assets/favicon/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('/assets/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('/assets/favicon/safari-pinned-tab.svg') }}" color="white">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('/assets/favicon/mstile-150x150.png') }}">
    <meta name="msapplication-config" content="{{ asset('/assets/favicon/browserconfig.xml') }}" />

    <meta name="theme-color" content="#ffffff">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="c-app flex-row align-items-center">

@yield('content')
@yield('javascript')

</body>
</html>
