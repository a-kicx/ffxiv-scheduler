@extends('layouts.app')
@section('title', $event->name)

@section('content')
<div x-data="{ showJoinForm: false, showEditNote: null }">
    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <div>
            <h1 class="text-xl font-bold text-amber-400">{{ $event->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $event->party_type->label() }}
                &nbsp;|&nbsp;
                参加可否: {{ $event->attendance_mode->label() }}
            </p>
        </div>
        <button @click="showJoinForm = !showJoinForm" class="btn-primary">
            <span x-text="showJoinForm ? '▲ 閉じる' : '＋ 参加登録・編集'"></span>
        </button>
    </div>

    {{-- Join/Edit Form --}}
    <div x-show="showJoinForm" x-cloak class="card p-5 mb-6">
        @if($myParticipant)
            @include('participants._form', ['event' => $event, 'participant' => $myParticipant, 'jobsByRole' => $jobsByRole, 'isEdit' => true])
        @else
            @include('participants._form', ['event' => $event, 'participant' => null, 'jobsByRole' => $jobsByRole, 'isEdit' => false])
        @endif
    </div>

    {{-- Schedule Grid --}}
    <div class="card p-4 mb-6">
        <h2 class="font-bold text-amber-300 mb-3 text-sm">📋 回答一覧</h2>

        @if($event->participants->isEmpty())
            <p class="text-gray-500 text-sm">まだ回答がありません。上の「参加登録・編集」から追加してください。</p>
        @else
            @include('events._grid', ['event' => $event])
        @endif
    </div>

    {{-- Role config summary --}}
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
