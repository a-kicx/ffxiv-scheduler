@extends('layouts.app')
@section('title', '回答を編集 - ' . $event->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('events.show', $event) }}" class="text-ff-gold text-sm hover:underline">← イベントに戻る</a>
    </div>
    <div class="card p-5">
        @include('participants._form', [
            'event' => $event,
            'participant' => $participant,
            'jobsByRole' => $jobsByRole,
            'supportJobs' => $supportJobs,
            'isEdit' => true,
        ])
    </div>
</div>
@endsection
