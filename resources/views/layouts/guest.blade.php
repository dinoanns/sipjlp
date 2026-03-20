<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - {{ config('app.name', 'SIPJLP') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-icon.png') }}">
    <!-- CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css" rel="stylesheet" />
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }

        .login-right-panel {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #1e6f5c 100%);
            position: relative;
        }

        .login-right-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('{{ asset('images/logocipayungori.png') }}') center center no-repeat;
            background-size: 60%;
            opacity: 0.08;
        }

        .login-content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body class="d-flex flex-column bg-white">
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js" defer></script>
    <div class="row g-0 flex-fill">
        <div class="col-12 col-lg-6 col-xl-5 border-top-wide border-primary d-flex flex-column justify-content-center">
            <div class="container container-tight my-5 px-lg-5">
                @yield('content')
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-7 d-none d-lg-block">
            <div
                class="login-right-panel h-100 min-vh-100 d-flex flex-column justify-content-center align-items-center text-white p-5">
                <div class="login-content text-center">
                    <img src="{{ asset('images/logocipayungori.png') }}" alt="RSUD Cipayung" height="120"
                        class="mb-4" style="filter: brightness(0) invert(1); opacity: 0.95;">
                    <h1 class="display-6 fw-bold mb-2">Sistem Informasi Pengelolaan PJLP</h1>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
