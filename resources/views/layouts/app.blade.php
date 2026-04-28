<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FF14 スケジューラー') | FF14 スケジューラー</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/sass/app.scss'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="min-h-screen">
    <header class="site-header px-4 py-3">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('events.create') }}" class="site-logo flex items-center gap-2">
                <span>⚔</span> FF14 スケジューラー
            </a>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="flash-error">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
