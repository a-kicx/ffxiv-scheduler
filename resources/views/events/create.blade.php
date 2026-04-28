@extends('layouts.app')
@section('title', 'イベントを作成')

@section('content')
<div class="max-w-3xl mx-auto" x-data="eventCreateForm()">
    <h1 class="text-2xl font-bold text-ff-gold mb-6">📅 新規イベント作成</h1>

    <form method="POST" action="{{ route('events.store') }}">
        @csrf

        {{-- イベント名 --}}
        <div class="card p-5 mb-4">
            <h2 class="font-bold text-ff-gold mb-3">① イベント名</h2>
            <input type="text" name="name" class="form-input" placeholder="例：絶アレキサンダー討滅戦 固定メンバー募集" value="{{ old('name') }}" required>
        </div>

        {{-- パーティ構成 --}}
        <div class="card p-5 mb-4">
            <h2 class="font-bold text-ff-gold mb-3">② パーティ構成</h2>
            <label class="block mb-1">パーティタイプ</label>
            <select name="party_type" class="form-select mb-4" x-model="partyType" @change="updateRoleConfig()">
                @foreach($partyTypes as $type)
                    <option value="{{ $type->value }}" {{ old('party_type') === $type->value ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>

            <div class="mt-3">
                <label class="block mb-2 text-sm text-ff-muted">ロール構成（変更可能）</label>
                <template x-for="(unit, idx) in roleConfig.units" :key="idx">
                    <div class="flex flex-wrap gap-3 items-center mb-2 p-2 bg-ff-card rounded">
                        <span class="text-ff-gold text-sm font-bold w-28" x-text="unit.label"></span>
                        <label class="flex items-center gap-1 text-sm">
                            <span class="job-pill role-tank">{{ config('const.JOB_ROLE.TANK') }}</span>
                            <input type="number" :name="`role_config[units][${idx}][tank]`" x-model.number="unit.tank" class="form-input w-14 text-center" min="0" max="8">
                        </label>
                        <label class="flex items-center gap-1 text-sm">
                            <span class="job-pill role-healer">{{ config('const.JOB_ROLE.HEALER') }}</span>
                            <input type="number" :name="`role_config[units][${idx}][healer]`" x-model.number="unit.healer" class="form-input w-14 text-center" min="0" max="8">
                        </label>
                        <label class="flex items-center gap-1 text-sm">
                            <span class="job-pill role-melee">{{ config('const.JOB_ROLE.DPS') }}</span>
                            <input type="number" :name="`role_config[units][${idx}][dps]`" x-model.number="unit.dps" class="form-input w-14 text-center" min="0" max="8">
                        </label>
                        <input type="hidden" :name="`role_config[units][${idx}][label]`" :value="unit.label">
                    </div>
                </template>
            </div>
        </div>

        {{-- ジョブ選択 --}}
        <div class="card p-5 mb-4">
            <h2 class="font-bold text-ff-gold mb-3">③ ジョブ選択設定</h2>
            <label class="block mb-1">参加ジョブの選択方式</label>
            <select name="job_mode" class="form-select">
                <option value="none" {{ old('job_mode','none') === 'none' ? 'selected':'' }}>指定なし（ジョブ選択なし）</option>
                <option value="single" {{ old('job_mode') === 'single' ? 'selected':'' }}>単一選択（1ジョブのみ）</option>
                <option value="multiple" {{ old('job_mode') === 'multiple' ? 'selected':'' }}>複数選択（複数ジョブ可）</option>
            </select>
        </div>

        {{-- サブジョブ (Alliance Special only) --}}
        <div class="card p-5 mb-4" x-show="partyType === 'alliance_special'" x-cloak>
            <h2 class="font-bold text-ff-gold mb-3">④ サポートジョブ選択設定（クレセントアイルのみ）</h2>
            <label class="block mb-1">サブジョブの選択方式</label>
            <select name="sub_job_mode" class="form-select">
                <option value="none" {{ old('sub_job_mode','none') === 'none' ? 'selected':'' }}>指定なし</option>
                <option value="single" {{ old('sub_job_mode') === 'single' ? 'selected':'' }}>単一選択</option>
                <option value="multiple" {{ old('sub_job_mode') === 'multiple' ? 'selected':'' }}>複数選択</option>
            </select>
        </div>
        <input type="hidden" name="sub_job_mode" value="none" :disabled="partyType === 'alliance_special'">

        {{-- 日程 --}}
        <div class="card p-5 mb-4">
            <h2 class="font-bold text-ff-gold mb-3">⑤ 日程設定</h2>
            <div class="flex flex-wrap gap-4 mb-3">
                <div>
                    <label class="block mb-1">開始日</label>
                    <input type="date" name="date_from" class="form-input" value="{{ old('date_from', today()->toDateString()) }}" required>
                </div>
                <div>
                    <label class="block mb-1">終了日</label>
                    <input type="date" name="date_to" class="form-input" value="{{ old('date_to', today()->toDateString()) }}" required>
                </div>
            </div>

            <div x-data="timeSlotBuilder()">
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" x-model="useTimes" class="w-4 h-4 accent-ff-gold">
                    <span class="text-sm">時刻も指定する</span>
                </label>

                <div x-show="useTimes" x-cloak>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <template x-for="(t, i) in times" :key="i">
                            <div class="flex items-center gap-1 bg-ff-card rounded px-2 py-1">
                                <input type="time" x-model="times[i]" :name="`times[${i}]`" class="form-input w-28 text-sm">
                                <button type="button" @click="removeTime(i)" class="text-ff-decline hover:text-ff-decline text-lg leading-none">×</button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addTime()" class="btn-secondary text-sm">＋ 時刻を追加</button>
                </div>
            </div>
        </div>

        {{-- 参加可否 --}}
        <div class="card p-5 mb-6">
            <h2 class="font-bold text-ff-gold mb-3">⑥ 参加可否の形式</h2>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="attendance_mode" value="ternary" {{ old('attendance_mode','ternary') === 'ternary' ? 'checked':'' }} class="accent-ff-gold">
                    <span class="text-ff-attend font-bold">○</span>
                    <span class="text-ff-maybe font-bold">△</span>
                    <span class="text-ff-decline font-bold">×</span>
                    <span class="text-sm text-ff-muted">（3種類）</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="attendance_mode" value="binary" {{ old('attendance_mode') === 'binary' ? 'checked':'' }} class="accent-ff-gold">
                    <span class="text-ff-attend font-bold">○</span>
                    <span class="text-ff-decline font-bold">×</span>
                    <span class="text-sm text-ff-muted">（2種類）</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary text-base px-8 py-2">
                ✔ イベントを作成する
            </button>
        </div>
    </form>
</div>

<script>
function eventCreateForm() {
    const defaults = @json(collect(\App\Enums\PartyType::cases())->mapWithKeys(fn($t) => [$t->value => $t->defaultRoleConfig()]));

    return {
        partyType: '{{ old('party_type', 'light') }}',
        roleConfig: defaults['{{ old('party_type', 'light') }}'],
        updateRoleConfig() {
            this.roleConfig = defaults[this.partyType];
        }
    };
}

function timeSlotBuilder() {
    return {
        useTimes: {{ old('times') ? 'true' : 'false' }},
        times: @json(old('times', ['22:00'])),
        addTime() { this.times.push('22:00'); },
        removeTime(i) { this.times.splice(i, 1); }
    };
}
</script>
@endsection
