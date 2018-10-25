<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Set base to fix for chunk error in Webpack and Vue Router -->
<base href="/"/>

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'laratt') }}</title>
<link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon">
@stack('css')
@stack('header_js')
</head>
<body>
    @yield('content')
    @stack('footer_js')
</body>
</html>
