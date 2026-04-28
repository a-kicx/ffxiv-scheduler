@php
    $slots = $event->slots;
    $participants = $event->participants;

    // Count attendance per slot
    $slotCounts = [];
    foreach ($slots as $slot) {
        $counts = ['attend' => 0, 'maybe' => 0, 'decline' => 0];
        foreach ($participants as $p) {
            $response = $p->responses->firstWhere('slot_id', $slot->id);
            if ($response) {
                $counts[$response->attendance]++;
            }
        }
        $slotCounts[$slot->id] = $counts;
    }
@endphp

<div class="schedule-grid">
    <table>
        <thead>
            <tr>
                <th class="text-left" style="min-width:120px">名前 / ジョブ</th>
                @foreach($slots as $slot)
                    <th>{{ $slot->label() }}</th>
                @endforeach
                <th>備考</th>
            </tr>
        </thead>
        <tbody>
            {{-- Summary row --}}
            <tr>
                <td class="text-xs text-gray-500">集計</td>
                @foreach($slots as $slot)
                    <td>
                        <div class="summary-bar">
                            <span class="attend-circle">○{{ $slotCounts[$slot->id]['attend'] }}</span>
                            @if($event->attendance_mode->value === 'ternary')
                                <span class="attend-triangle">△{{ $slotCounts[$slot->id]['maybe'] }}</span>
                            @endif
                            <span class="attend-cross">×{{ $slotCounts[$slot->id]['decline'] }}</span>
                        </div>
                    </td>
                @endforeach
                <td></td>
            </tr>

            @foreach($participants as $participant)
            <tr>
                <td class="text-left">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('editBtn').click();"
                       class="participant-name-link font-bold">{{ $participant->name }}</a>
                    @php $jobs = $participant->resolvedJobs(); @endphp
                    @if($jobs->isNotEmpty())
                        <div class="mt-1 flex flex-wrap gap-0.5">
                            @foreach($jobs as $job)
                                <span class="job-pill {{ $job->role->colorClass() }}">{{ $job->abbreviation }}</span>
                            @endforeach
                        </div>
                    @endif
                    @php $subJobs = $participant->resolvedSubJobs(); @endphp
                    @if($subJobs->isNotEmpty())
                        <div class="mt-0.5 flex flex-wrap gap-0.5">
                            @foreach($subJobs as $job)
                                <span class="job-pill bg-gray-700 text-gray-200">{{ $job->abbreviation }}</span>
                            @endforeach
                        </div>
                    @endif
                </td>
                @foreach($slots as $slot)
                    @php $response = $participant->responses->firstWhere('slot_id', $slot->id); @endphp
                    <td>
                        @if($response)
                            @if($response->note)
                                <div class="tooltip-wrapper">
                                    <span class="{{ $response->attendanceCssClass() }}">{{ $response->attendanceLabel() }}</span>
                                    <span class="note-icon ml-0.5">📝</span>
                                    <span class="tooltip-text">{{ $response->note }}</span>
                                </div>
                            @else
                                <span class="{{ $response->attendanceCssClass() }}">{{ $response->attendanceLabel() }}</span>
                            @endif
                        @else
                            <span class="text-gray-600">-</span>
                        @endif
                    </td>
                @endforeach
                <td class="text-left text-xs text-gray-400 max-w-xs">
                    @if($participant->remarks)
                        <div class="tooltip-wrapper">
                            <span class="note-icon cursor-help">💬 備考あり</span>
                            <span class="tooltip-text max-w-xs whitespace-normal" style="max-width:200px; white-space:normal;">{{ $participant->remarks }}</span>
                        </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
