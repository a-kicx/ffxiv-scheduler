<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FF14 スケジューラー') | FF14 スケジューラー</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { background-color: #0d1117; color: #e6e6e6; font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif; }
        .card { background: #161b22; border: 1px solid #30363d; border-radius: 8px; }
        .btn-primary { background: #c8973a; color: #0d1117; font-weight: 700; padding: 0.5rem 1.2rem; border-radius: 6px; transition: background 0.15s; }
        .btn-primary:hover { background: #e0ab4a; }
        .btn-secondary { background: #21262d; color: #e6e6e6; border: 1px solid #30363d; padding: 0.5rem 1.2rem; border-radius: 6px; transition: background 0.15s; }
        .btn-secondary:hover { background: #30363d; }
        .btn-danger { background: #da3633; color: #fff; padding: 0.5rem 1.2rem; border-radius: 6px; transition: background 0.15s; }
        .btn-danger:hover { background: #f85149; }
        .form-input { background: #0d1117; border: 1px solid #30363d; color: #e6e6e6; border-radius: 6px; padding: 0.4rem 0.75rem; width: 100%; }
        .form-input:focus { outline: none; border-color: #c8973a; box-shadow: 0 0 0 2px rgba(200,151,58,0.2); }
        .form-select { background: #0d1117; border: 1px solid #30363d; color: #e6e6e6; border-radius: 6px; padding: 0.4rem 0.75rem; width: 100%; }
        .form-select:focus { outline: none; border-color: #c8973a; }
        label { font-size: 0.875rem; color: #8b949e; }
        .attend-circle { color: #3fb950; font-weight: 700; }
        .attend-triangle { color: #d29922; font-weight: 700; }
        .attend-cross { color: #f85149; font-weight: 700; }
        /* Role colors */
        .role-tank { background: #1e6ecd; color: #fff; }
        .role-healer { background: #1a7f4f; color: #fff; }
        .role-melee { background: #c73737; color: #fff; }
        .role-pranged { background: #8c5a11; color: #fff; }
        .role-mranged { background: #6a3d9e; color: #fff; }
        .job-pill { display: inline-block; font-size: 0.7rem; font-weight: 700; padding: 1px 6px; border-radius: 4px; margin: 1px; }
        /* Grid */
        .schedule-grid { overflow-x: auto; }
        .schedule-grid table { border-collapse: collapse; min-width: 100%; }
        .schedule-grid th, .schedule-grid td { border: 1px solid #30363d; padding: 6px 10px; white-space: nowrap; font-size: 0.85rem; }
        .schedule-grid th { background: #161b22; text-align: center; }
        .schedule-grid td { text-align: center; }
        .schedule-grid tr:nth-child(even) td { background: #0d1117; }
        .schedule-grid tr:nth-child(odd) td { background: #111318; }
        .participant-name-link { color: #c8973a; text-decoration: none; }
        .participant-name-link:hover { text-decoration: underline; }
        .summary-bar { display: flex; gap: 4px; justify-content: center; font-size: 0.75rem; }
        .tooltip-wrapper { position: relative; display: inline-block; }
        .tooltip-wrapper .tooltip-text { visibility: hidden; background: #21262d; color: #e6e6e6; border: 1px solid #30363d; border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; white-space: nowrap; position: absolute; bottom: calc(100% + 4px); left: 50%; transform: translateX(-50%); z-index: 10; }
        .tooltip-wrapper:hover .tooltip-text { visibility: visible; }
        .note-icon { cursor: help; color: #8b949e; font-size: 0.8rem; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen">
    <header class="border-b border-gray-800 px-4 py-3">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('events.create') }}" class="text-amber-400 font-bold text-lg tracking-wide flex items-center gap-2">
                <span>⚔</span> FF14 スケジューラー
            </a>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-900 border border-green-700 text-green-300 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-900 border border-red-700 text-red-300 text-sm">
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
