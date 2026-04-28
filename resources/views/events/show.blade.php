@extends('layouts.app')
@section('title', $event->name)

@section('content')
<div x-data="{ showJoinForm: false, showEditNote: null }">
    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <div>
            <h1 class="text-xl font-bold text-ff-gold">{{ $event->name }}</h1>
            <p class="text-sm text-ff-muted mt-1">
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
        <h2 class="font-bold text-ff-gold mb-3 text-sm">📋 回答一覧</h2>

        @if($event->participants->isEmpty())
            <p class="text-ff-muted text-sm">まだ回答がありません。上の「参加登録・編集」から追加してください。</p>
        @else
            @include('events._grid', ['event' => $event])
        @endif
    </div>

    {{-- Role config summary --}}
    <div class="card p-4">
        <h2 class="font-bold text-ff-gold mb-3 text-sm">⚙ パーティ構成</h2>
        <div class="flex flex-wrap gap-3">
            @if($event->role_config['units'])
                @foreach($event->role_config['units'] as $unit)
                <div class="bg-ff-card rounded p-2 text-sm">
                    <span class="font-bold text-ff-gold">{{ $unit['label'] }}</span>
                    <span class="ml-2 job-pill role-tank">{{ config('const.JOB_ROLE.TANK') }}×{{ $unit['tank'] }}</span>
                    <span class="job-pill role-healer">{{ config('const.JOB_ROLE.HEALER') }}×{{ $unit['healer'] }}</span>
                    <span class="job-pill role-melee">{{ config('const.JOB_ROLE.DPS') }}×{{ $unit['dps'] }}</span>
                </div>
                @endforeach
            @else
                <p>role_config['units'] が空です。デバッグ: {{ config('const.JOB_ROLE.TANK') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
