<html class="overflow-x-hidden">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>@stack('title')</title>

<meta name="csrf-token" content="{{ csrf_token() }}">



@stack('meta')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Serif:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">


<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
<link rel="manifest" href="/favicons/site.webmanifest">
<link rel="shortcut icon" href="/favicons/favicon.ico">
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('styles')
@stack('scripts')

@livewireStyles
</head>
<body class="overflow-x-hidden">

<x-layout.navbar />

<main>
@if(isset($slot))
{{ $slot }}
@else
@yield('content')
@endif
</main>

@livewireScripts

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-22785728-11"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-22785728-11');
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.dispatchEvent(new CustomEvent("reset-notifications"));
    })

    function iOS() {
        return [
                'iPad Simulator',
                'iPhone Simulator',
                'iPod Simulator',
                'iPad',
                'iPhone',
                'iPod'
            ].includes(navigator.platform)
            // iPad on iOS 13 detection
            || (navigator.userAgent.includes("Mac") && "ontouchend" in document)
    }

    function isMobile() {
        return /Mobi/i.test(navigator.userAgent)
    }
</script>

@include('scripts.notify')
<x-layout.notification />

</body>
</html>
