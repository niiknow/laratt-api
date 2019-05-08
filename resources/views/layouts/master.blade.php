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

<title>{{ config('app.name') }}</title>
<link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon">
<link rel="stylesheet" href="https://cdn.materialdesignicons.com/3.5.95/css/materialdesignicons.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<link rel="stylesheet" href="{{ mix('/css/myapp.css') }}">
@stack('css')
<!-- Initial State -->
<script>
    window.appSettings = {!! json_encode(array_merge(
        $appSettings, [
            // Add any additional config here
            'locales' => app()->getLocale(),
            'ua'      => config('admin.ua')
        ]
    ))!!}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://www.google-analytics.com/analytics.js"></script>
@stack('header_js')
</head>
<body>
  <noscript><strong>We're sorry but this site doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js"></script>
@yield('content')
<script src="{{ remix('js/manifest.js') }}"></script>
<script src="{{ mix('js/vendor.js') }}"></script>
@stack('footer_js')
</body>
</html>
