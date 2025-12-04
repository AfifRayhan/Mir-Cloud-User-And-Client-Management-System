<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Mir Cloud - Login</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/custom-login.css', 'resources/css/custom-dashboard.css', 'resources/css/custom-add-customer.css','resources/js/app.js', 'resources/js/custom-js-styles.js', 'resources/css/custom-customer-index.css'])
    </head>
    <body class="bg-light">
        {{ $slot }}
    </body>
</html>
