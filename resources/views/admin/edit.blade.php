@extends('layouts.app')
@section('title', 'イベント編集 - ' . $event->name)

@section('content')
<div class="max-w-3xl mx-auto" x-data="eventCreateForm()">
    <div class="mb-4">
        <a href="{{ route('admin.show', ['event' => $event->ulid, 'adminToken' => $adminToken]) }}" class="text-amber-400 text-sm hover:underline">← 管理ページに戻る</a>
    </div>

    <h1 class="text-2xl font-bold text-amber-400 mb-6">✏ イベント編集</h1>

    <form method="POST" action="{{ route('admin.update', ['event' => $event->ulid, 'adminToken' => $adminToken]) }}">
        @csrf
        @method('PUT')

        {{-- イベント名 --}}
        <div class="card p-5 mb-4">
            <h2 class="font-bold text-amber-300 mb-3">① イベント名</h2>
            <input type="text" name="name" class="form-input"
                   value="{{ old('name', $event->name) }}" required>
        </div>

        {{-- パーティ構成 --}}
        <div class="card p-5 mb-4">
            <h2 class="font-bold text-amber-300 mb-3">② パーティ構成</h2>
            <label class="block mb-1">パーティタイプ</label>
            <select name="party_type" class="form-select mb-4" x-model="partyType" @change="updateRoleConfig()">
                @foreach(\App\Enums\PartyType::cases() as $type)
                    <option value="{{ $type->value }}" {{ old('party_type', $event->party_type->value) === $type->value ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>

            <div class="mt-3">
                <label class="block mb-2 text-sm text-gray-400">ロール構成</label>
                <template x-for="(unit, idx) in roleConfig.units" :key="idx">
                    <div class="flex flex-wrap gap-3 items-center mb-2 p-2 bg-gray-900 rounded">
                        <span class="text-amber-300 text-sm font-bold w-28" x-text="unit.label"></span>
                        <label class="flex items-center gap-1 text-sm">
                            <span class="job-pill role-tank">TK</span>
                            <input type="number" :name="`role_config[units][${idx}][tank]`" x-model.number="unit.tank" class="form-input w-14 text-center" min="0" max="8">
                        </label>
                        <label class="flex items-center gap-1 text-sm">
                            <span class="job-pill role-healer">HL</span>
                            <input type="number" :name="`role_config[units][${idx}][healer]`" x-model.number="unit.healer" class="form-input w-14 text-center" min="0" max="8">
                        </label>
                        <label class="flex items-center gap-1 text-sm">
                            <span class="job-pill role-melee">DPS</span>
                            <input type="number" :name="`role_config[units][${idx}][dps]`" x-model.number="unit.dps" class="form-input w-14 text-center" min="0" max="8">
                        </label>
                        <input type="hidden" :name="`role_config[units][${idx}][label]`" :value="unit.label">
                    </div>
                </template>
            </div>
        </div>

        {{-- ジョブ選択 --}}
        <div class="card p-5 mb-4">
            <h2 class="font-bold text-amber-300 mb-3">③ ジョブ選択設定</h2>
            <select name="job_mode" class="form-select">
                <option value="none" {{ old('job_mode', $event->job_mode->value) === 'none' ? 'selected':'' }}>指定なし</option>
                <option value="single" {{ old('job_mode', $event->job_mode->value) === 'single' ? 'selected':'' }}>単一選択</option>
                <option value="multiple" {{ old('job_mode', $event->job_mode->value) === 'multiple' ? 'selected':'' }}>複数選択</option>
            </select>
        </div>

        {{-- サブジョブ --}}
        <div class="card p-5 mb-4" x-show="partyType === 'alliance_special'" x-cloak>
            <h2 class="font-bold text-amber-300 mb-3">④ サポートジョブ選択設定（クレセントアイルのみ）</h2>
            <select name="sub_job_mode" class="form-select">
                <option value="none" {{ old('sub_job_mode', $event->sub_job_mode->value) === 'none' ? 'selected':'' }}>指定なし</option>
                <option value="single" {{ old('sub_job_mode', $event->sub_job_mode->value) === 'single' ? 'selected':'' }}>単一選択</option>
                <option value="multiple" {{ old('sub_job_mode', $event->sub_job_mode->value) === 'multiple' ? 'selected':'' }}>複数選択</option>
            </select>
        </div>
        <input type="hidden" name="sub_job_mode" value="none" :disabled="partyType === 'alliance_special'">

        {{-- 日程 --}}
        <div class="card p-5 mb-4">
            <h2 class="font-bold text-amber-300 mb-3">⑤ 日程設定</h2>
            <p class="text-yellow-600 text-xs mb-3">⚠ 日程を変更すると、削除された日程の回答データが失われます。</p>
            @php
                $existingSlots = $event->slots;
                $dateFrom = $existingSlots->first()?->slot_date?->format('Y-m-d') ?? '';
                $dateTo = $existingSlots->last()?->slot_date?->format('Y-m-d') ?? '';
                $existingTimes = $existingSlots->whereNotNull('slot_time')->pluck('slot_time')->unique()->values()->map(fn($t) => substr($t,0,5))->toArray();
            @endphp
            <div class="flex flex-wrap gap-4 mb-3">
                <div>
                    <label class="block mb-1">開始日</label>
                    <input type="date" name="date_from" class="form-input" value="{{ old('date_from', $dateFrom) }}" required>
                </div>
                <div>
                    <label class="block mb-1">終了日</label>
                    <input type="date" name="date_to" class="form-input" value="{{ old('date_to', $dateTo) }}" required>
                </div>
            </div>

            <div x-data="timeSlotBuilder()">
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" x-model="useTimes" class="w-4 h-4 accent-amber-400">
                    <span class="text-sm">時刻も指定する</span>
                </label>
                <div x-show="useTimes" x-cloak>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <template x-for="(t, i) in times" :key="i">
                            <div class="flex items-center gap-1 bg-gray-800 rounded px-2 py-1">
                                <input type="time" x-model="times[i]" :name="`times[${i}]`" class="form-input w-28 text-sm">
                                <button type="button" @click="removeTime(i)" class="text-red-400 text-lg">×</button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addTime()" class="btn-secondary text-sm">＋ 時刻を追加</button>
                </div>
            </div>
        </div>

        {{-- 参加可否 --}}
        <div class="card p-5 mb-6">
            <h2 class="font-bold text-amber-300 mb-3">⑥ 参加可否の形式</h2>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="attendance_mode" value="ternary"
                           {{ old('attendance_mode', $event->attendance_mode->value) === 'ternary' ? 'checked':'' }} class="accent-amber-400">
                    <span class="text-green-400 font-bold">○</span>
                    <span class="text-yellow-400 font-bold">△</span>
                    <span class="text-red-400 font-bold">×</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="attendance_mode" value="binary"
                           {{ old('attendance_mode', $event->attendance_mode->value) === 'binary' ? 'checked':'' }} class="accent-amber-400">
                    <span class="text-green-400 font-bold">○</span>
                    <span class="text-red-400 font-bold">×</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.show', ['event' => $event->ulid, 'adminToken' => $adminToken]) }}" class="btn-secondary">キャンセル</a>
            <button type="submit" class="btn-primary">✔ 更新する</button>
        </div>
    </form>
</div>

<script>
function eventCreateForm() {
    const defaults = @json(collect(\App\Enums\PartyType::cases())->mapWithKeys(fn($t) => [$t->value => $t->defaultRoleConfig()]));
    const current = @json($event->role_config);
    const currentType = '{{ old('party_type', $event->party_type->value) }}';

    return {
        partyType: currentType,
        roleConfig: current,
        updateRoleConfig() {
            this.roleConfig = defaults[this.partyType];
        }
    };
}

function timeSlotBuilder() {
    const existingTimes = @json($existingTimes);
    return {
        useTimes: existingTimes.length > 0,
        times: existingTimes.length > 0 ? existingTimes : ['22:00'],
        addTime() { this.times.push('22:00'); },
        removeTime(i) { this.times.splice(i, 1); }
    };
}
</script>
@endsection
