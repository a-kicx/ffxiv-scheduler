<?php

namespace App\Http\Controllers;

use App\Enums\JobMode;
use App\Http\Requests\ParticipantRequest;
use App\Models\Event;
use App\Models\Ff14Job;
use App\Models\Participant;
use App\Models\Response;
use App\Models\SupportJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function store(ParticipantRequest $request, Event $event): RedirectResponse
    {
        $validated = $request->validated();
        $token = Cookie::get('participant_' . $event->ulid);

        $participant = null;
        if ($token) {
            $participant = $event->participants()->where('participant_token', $token)->first();
        }

        if (!$participant) {
            $token = Participant::generateToken();
            $participant = new Participant();
            $participant->event_id = $event->id;
            $participant->participant_token = $token;
        }

        $participant->name = $validated['name'];
        $participant->remarks = $validated['remarks'] ?? null;
        $participant->selected_jobs = $this->normalizeJobSelection($event->job_mode, $validated['selected_jobs'] ?? null);
        $participant->selected_sub_jobs = $this->normalizeJobSelection($event->sub_job_mode, $validated['selected_sub_jobs'] ?? null);
        $participant->save();

        // Upsert responses
        $slots = $event->slots()->pluck('id');
        foreach ($slots as $slotId) {
            $attendance = $validated['attendance'][$slotId] ?? null;
            if ($attendance) {
                Response::updateOrCreate(
                    ['participant_id' => $participant->id, 'slot_id' => $slotId],
                    [
                        'attendance' => $attendance,
                        'note' => $validated['notes'][$slotId] ?? null,
                    ]
                );
            }
        }

        $cookie = cookie(
            'participant_' . $event->ulid,
            $token,
            60 * 24 * 365,
            '/',
            null,
            false,
            true,
            false,
            'Lax'
        );

        return redirect()->route('events.show', $event)->withCookie($cookie);
    }

    public function edit(Event $event): View|RedirectResponse
    {
        $token = request()->cookie('participant_' . $event->ulid);
        if (!$token) {
            return redirect()->route('events.show', $event);
        }

        $participant = $event->participants()->where('participant_token', $token)->with('responses')->first();
        if (!$participant) {
            return redirect()->route('events.show', $event);
        }

        $event->load('slots');
        $jobsByRole = Ff14Job::groupedByRole();
        $supportJobs = SupportJob::orderBy('sort_order')->get();

        return view('participants.edit', compact('event', 'participant', 'jobsByRole', 'supportJobs'));
    }

    public function update(ParticipantRequest $request, Event $event): RedirectResponse
    {
        $token = request()->cookie('participant_' . $event->ulid);
        if (!$token) {
            return redirect()->route('events.show', $event);
        }

        $participant = $event->participants()->where('participant_token', $token)->first();
        if (!$participant) {
            return redirect()->route('events.show', $event);
        }

        $validated = $request->validated();

        $participant->name = $validated['name'];
        $participant->remarks = $validated['remarks'] ?? null;
        $participant->selected_jobs = $this->normalizeJobSelection($event->job_mode, $validated['selected_jobs'] ?? null);
        $participant->selected_sub_jobs = $this->normalizeJobSelection($event->sub_job_mode, $validated['selected_sub_jobs'] ?? null);
        $participant->save();

        $slots = $event->slots()->pluck('id');
        foreach ($slots as $slotId) {
            $attendance = $validated['attendance'][$slotId] ?? null;
            if ($attendance) {
                Response::updateOrCreate(
                    ['participant_id' => $participant->id, 'slot_id' => $slotId],
                    [
                        'attendance' => $attendance,
                        'note' => $validated['notes'][$slotId] ?? null,
                    ]
                );
            }
        }

        return redirect()->route('events.show', $event);
    }

    private function normalizeJobSelection(JobMode $mode, mixed $value): ?array
    {
        if ($mode === JobMode::None || $value === null) {
            return null;
        }
        if ($mode === JobMode::Single) {
            return is_array($value) ? $value : [$value];
        }
        return is_array($value) ? $value : [$value];
    }
}
