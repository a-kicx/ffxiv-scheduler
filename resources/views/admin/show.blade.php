@extends('layouts.app')
@section('title', '管理 - ' . $event->name)

@section('content')
<div>
    {{-- Admin header --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-amber-600 text-xs text-black font-bold px-2 py-0.5 rounded">管理者画面</span>
                <h1 class="text-xl font-bold text-amber-400">{{ $event->name }}</h1>
            </div>
            <p class="text-sm text-gray-500">
                {{ $event->party_type->label() }}
            </p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('events.show', $event) }}" class="btn-secondary text-sm">👁 参加者用ページ</a>
            <a href="{{ route('admin.edit', ['event' => $event->ulid, 'adminToken' => $adminToken]) }}" class="btn-secondary text-sm">✏ イベント編集</a>
        </div>
    </div>

    {{-- Admin URL reminder --}}
    @if(session('admin_created'))
    <div class="card p-4 mb-4 border-amber-600">
        <p class="text-amber-300 font-bold mb-2">🎉 イベントを作成しました！</p>
        <p class="text-sm text-gray-400 mb-2">以下の管理者URLを保存してください。このURLを持つ人だけが管理できます。</p>
        <div class="flex items-center gap-2">
            <input type="text" readonly class="form-input font-mono text-xs"
                   value="{{ route('admin.show', ['event' => $event->ulid, 'adminToken' => $adminToken]) }}"
                   onclick="this.select()">
            <button onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='✔ コピー済'"
                    class="btn-secondary text-sm whitespace-nowrap">📋 コピー</button>
        </div>
        <p class="text-sm text-gray-400 mt-3 mb-2">参加者向け共有URL:</p>
        <div class="flex items-center gap-2">
            <input type="text" readonly class="form-input font-mono text-xs"
                   value="{{ route('events.show', $event) }}"
                   onclick="this.select()">
            <button onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='✔ コピー済'"
                    class="btn-secondary text-sm whitespace-nowrap">📋 コピー</button>
        </div>
    </div>
    @endif

    {{-- Share URLs --}}
    @unless(session('admin_created'))
    <div class="card p-4 mb-4">
        <p class="text-sm text-gray-400 mb-2">参加者向け共有URL:</p>
        <div class="flex items-center gap-2">
            <input type="text" readonly class="form-input font-mono text-xs"
                   value="{{ route('events.show', $event) }}"
                   onclick="this.select()">
            <button onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='✔ コピー済'"
                    class="btn-secondary text-sm whitespace-nowrap">📋 コピー</button>
        </div>
    </div>
    @endunless

    {{-- Grid --}}
    <div class="card p-4 mb-4">
        <h2 class="font-bold text-amber-300 mb-3 text-sm">📋 回答一覧</h2>
        @if($event->participants->isEmpty())
            <p class="text-gray-500 text-sm">まだ回答がありません。</p>
        @else
            @include('events._grid', ['event' => $event])
        @endif
    </div>

    {{-- Clear actions --}}
    <div class="card p-4 mb-4" x-data="{ confirmHard: false }">
        <h2 class="font-bold text-red-400 mb-3 text-sm">🗑 データクリア</h2>
        <div class="flex flex-wrap gap-3">
            {{-- Soft clear: keep names --}}
            <form method="POST"
                  action="{{ route('admin.clear.soft', ['event' => $event->ulid, 'adminToken' => $adminToken]) }}">
                @csrf
                <button type="submit" class="btn-secondary text-sm"
                        onclick="return confirm('参加者の名前を残して回答・ジョブ・備考をクリアしますか？')">
                    📝 回答のみクリア（名前は残す）
                </button>
            </form>

            {{-- Hard clear: remove all --}}
            <div>
                <button type="button" @click="confirmHard = true" class="btn-danger text-sm">
                    👥 参加者ごと全員削除
                </button>
                <div x-show="confirmHard" x-cloak class="mt-2 p-3 bg-red-950 border border-red-700 rounded text-sm">
                    <p class="text-red-300 mb-2">⚠ 参加者を全員削除します。この操作は元に戻せません。</p>
                    <div class="flex gap-2">
                        <form method="POST"
                              action="{{ route('admin.clear.hard', ['event' => $event->ulid, 'adminToken' => $adminToken]) }}">
                            @csrf
                            <button type="submit" class="btn-danger text-sm">はい、全員削除する</button>
                        </form>
                        <button type="button" @click="confirmHard = false" class="btn-secondary text-sm">キャンセル</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Role config --}}
    <div class="card p-4">
        <h2 class="font-bold text-amber-300 mb-3 text-sm">⚙ パーティ構成</h2>
        <div class="flex flex-wrap gap-3">
            @foreach($event->role_config['units'] as $unit)
            <div class="bg-gray-900 rounded p-2 text-sm">
                <span class="font-bold text-amber-200">{{ $unit['label'] }}</span>
                <span class="ml-2 job-pill role-tank">TK×{{ $unit['tank'] }}</span>
                <span class="job-pill role-healer">HL×{{ $unit['healer'] }}</span>
                <span class="job-pill role-melee">DPS×{{ $unit['dps'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
