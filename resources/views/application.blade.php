<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ប្រព័ន្ធព័ត៌មានគ្រប់គ្រងការអនុវត្តស្តង់ដាសាលារៀនគំរូ</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('loader.css') }}" />
    @vite(['resources/ts/main.ts'])

</head>

<body>
    <div id="app">
        <div id="loading-bg">
            <div class="loading-logo">
                <img style="width: 150px" src="{{ asset('/images/logo.svg') }}" alt="logo">
            </div>
            <div class=" loading">
                <div class="effect-1 effects"></div>
                <div class="effect-2 effects"></div>
                <div class="effect-3 effects"></div>
            </div>
        </div>
    </div>

    <script>
        const loaderColor = localStorage.getItem('vuexy-initial-loader-bg') || 'white'
        // const primaryColor = localStorage.getItem('vuexy-initial-loader-color') || '#FF830F'
        const primaryColor = '#FF830F'

        if (loaderColor)
            document.documentElement.style.setProperty('--initial-loader-bg', loaderColor)

        if (primaryColor)
            document.documentElement.style.setProperty('--initial-loader-color', primaryColor)
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Martian+Mono:wght@100..800&family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap');
        * {
            font-family: "Public Sans",sans-serif,-apple-system,blinkmacsystemfont,"Segoe UI",roboto,"Helvetica Neue",arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
        }
    </style>
</body>

</html>
