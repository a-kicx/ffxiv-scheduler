@php
    $slots = $event->slots;
    $isBinary = $event->attendance_mode->value === 'binary';
    $attendanceOptions = $event->attendance_mode->options();
    $showJobs = $event->job_mode->value !== 'none';
    $showSubJobs = $event->sub_job_mode->value !== 'none';
    $isMultiJob = $event->job_mode->value === 'multiple';
    $isMultiSubJob = $event->sub_job_mode->value === 'multiple';
@endphp

<h3 class="font-bold text-amber-300 mb-4">
    {{ $isEdit ? '✏ 回答を編集' : '＋ 参加登録' }}
</h3>

<form method="POST"
      action="{{ $isEdit ? route('events.participants.update', $event) : route('events.participants.store', $event) }}"
      x-data="participantForm()">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- Name --}}
    <div class="mb-4">
        <label class="block mb-1">名前 <span class="text-red-400">*</span></label>
        <input type="text" name="name" class="form-input max-w-sm"
               value="{{ old('name', $participant?->name ?? '') }}" required placeholder="キャラ名など">
    </div>

    {{-- Jobs --}}
    @if($showJobs)
    <div class="mb-4">
        <label class="block mb-2">参加ジョブ
            <span class="text-gray-500 text-xs ml-1">({{ $isMultiJob ? '複数選択可' : '1つ選択' }})</span>
        </label>
        <div class="flex flex-wrap gap-1">
            @foreach(['tank' => 'タンク', 'healer' => 'ヒーラー', 'melee' => '近接DPS', 'pranged' => '遠隔物理', 'mranged' => '遠隔魔法'] as $role => $roleLabel)
                @if(isset($jobsByRole[$role]) && count($jobsByRole[$role]) > 0)
                    <div class="mb-2 w-full">
                        <span class="text-xs text-gray-500">{{ $roleLabel }}</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($jobsByRole[$role] as $job)
                                @php
                                    $checked = collect($participant?->selected_jobs ?? [])->contains($job->id);
                                    $inputName = $isMultiJob ? 'selected_jobs[]' : 'selected_jobs';
                                    $inputType = $isMultiJob ? 'checkbox' : 'radio';
                                @endphp
                                <label class="flex items-center gap-1 cursor-pointer px-2 py-1 rounded border border-gray-700 hover:border-amber-500 transition-colors text-sm"
                                       :class="isJobSelected({{ $job->id }}) ? 'border-amber-500 bg-gray-800' : ''">
                                    <input type="{{ $inputType }}" name="{{ $inputName }}" value="{{ $job->id }}"
                                           x-model="{{ $isMultiJob ? 'selectedJobs' : 'selectedJob' }}"
                                           {{ $checked ? 'checked' : '' }} class="hidden">
                                    <span class="job-pill {{ $job->role->colorClass() }}">{{ $job->abbreviation }}</span>
                                    <span>{{ $job->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Sub Jobs --}}
    @if($showSubJobs)
    <div class="mb-4">
        <label class="block mb-2">サブジョブ
            <span class="text-gray-500 text-xs ml-1">({{ $isMultiSubJob ? '複数選択可' : '1つ選択' }})</span>
        </label>
        <div class="flex flex-wrap gap-1">
            @foreach(['tank' => 'タンク', 'healer' => 'ヒーラー', 'melee' => '近接DPS', 'pranged' => '遠隔物理', 'mranged' => '遠隔魔法'] as $role => $roleLabel)
                @if(isset($jobsByRole[$role]) && count($jobsByRole[$role]) > 0)
                    <div class="mb-2 w-full">
                        <span class="text-xs text-gray-500">{{ $roleLabel }}</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($jobsByRole[$role] as $job)
                                @php
                                    $checked = collect($participant?->selected_sub_jobs ?? [])->contains($job->id);
                                    $inputName = $isMultiSubJob ? 'selected_sub_jobs[]' : 'selected_sub_jobs';
                                    $inputType = $isMultiSubJob ? 'checkbox' : 'radio';
                                @endphp
                                <label class="flex items-center gap-1 cursor-pointer px-2 py-1 rounded border border-gray-700 hover:border-blue-500 transition-colors text-sm">
                                    <input type="{{ $inputType }}" name="{{ $inputName }}" value="{{ $job->id }}"
                                           {{ $checked ? 'checked' : '' }} class="hidden">
                                    <span class="job-pill {{ $job->role->colorClass() }}">{{ $job->abbreviation }}</span>
                                    <span>{{ $job->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Attendance per slot --}}
    <div class="mb-4">
        <label class="block mb-2">参加可否 <span class="text-red-400">*</span></label>
        <div class="overflow-x-auto">
            <table class="text-sm" style="border-collapse:collapse">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-gray-500 font-normal">日程</th>
                        @foreach($attendanceOptions as $key => $opt)
                            <th class="px-4 py-2 {{ $opt['class'] }}">{{ $opt['label'] }}</th>
                        @endforeach
                        <th class="px-3 py-2 text-gray-500 font-normal">補足</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slots as $slot)
                        @php
                            $existing = $participant?->responses?->firstWhere('slot_id', $slot->id);
                            $existingAttendance = old("attendance.{$slot->id}", $existing?->attendance ?? '');
                            $existingNote = old("notes.{$slot->id}", $existing?->note ?? '');
                        @endphp
                        <tr class="border-t border-gray-800">
                            <td class="px-3 py-2 text-amber-200 whitespace-nowrap">{{ $slot->label() }}</td>
                            @foreach($attendanceOptions as $key => $opt)
                                <td class="px-4 py-2 text-center">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[{{ $slot->id }}]"
                                               value="{{ $key }}"
                                               {{ $existingAttendance === $key ? 'checked' : '' }}
                                               required class="accent-amber-400 w-4 h-4">
                                    </label>
                                </td>
                            @endforeach
                            <td class="px-2 py-1" x-data="{ showNote: {{ $existingNote ? 'true' : 'false' }}, note: {{ json_encode($existingNote) }} }">
                                <button type="button" @click="showNote = !showNote"
                                        class="text-xs text-gray-500 hover:text-gray-300">
                                    <span x-text="showNote ? '▲ 補足' : '＋ 補足'"></span>
                                </button>
                                <div x-show="showNote" x-cloak class="mt-1">
                                    <input type="text" name="notes[{{ $slot->id }}]" x-model="note"
                                           class="form-input text-xs w-40" maxlength="255" placeholder="補足メモ">
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Remarks --}}
    <div class="mb-5">
        <label class="block mb-1">備考（任意）</label>
        <textarea name="remarks" class="form-input" rows="2" maxlength="1000"
                  placeholder="フリーコメントなど">{{ old('remarks', $participant?->remarks ?? '') }}</textarea>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn-primary">
            {{ $isEdit ? '✔ 更新する' : '✔ 登録する' }}
        </button>
    </div>
</form>

<script>
function participantForm() {
    return {
        selectedJobs: @json(old('selected_jobs', $participant?->selected_jobs ?? [])),
        selectedJob: @json(old('selected_jobs', $participant?->selected_jobs ? ($participant->selected_jobs[0] ?? null) : null)),
        isJobSelected(id) {
            if (Array.isArray(this.selectedJobs)) return this.selectedJobs.includes(id) || this.selectedJobs.map(Number).includes(id);
            return Number(this.selectedJob) === id;
        }
    };
}
</script>
