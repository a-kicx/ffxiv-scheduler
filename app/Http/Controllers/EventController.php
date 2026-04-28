<?php

namespace App\Http\Controllers;

use App\Enums\PartyType;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\Ff14Job;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function create(): View
    {
        $jobsByRole = Ff14Job::groupedByRole();
        $partyTypes = PartyType::cases();

        return view('events.create', compact('jobsByRole', 'partyTypes'));
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $ulid = Event::generateUlid();
        $adminToken = Event::generateAdminToken();

        $event = Event::create([
            'ulid' => $ulid,
            'admin_token' => $adminToken,
            'name' => $validated['name'],
            'party_type' => $validated['party_type'],
            'role_config' => $validated['role_config'],
            'job_mode' => $validated['job_mode'],
            'sub_job_mode' => $validated['sub_job_mode'],
            'attendance_mode' => $validated['attendance_mode'],
        ]);

        $this->syncSlots($event, $validated);

        return redirect()->route('admin.show', ['event' => $ulid, 'adminToken' => $adminToken])
            ->with('admin_created', true);
    }

    public function show(Event $event): View
    {
        $event->load(['slots', 'participants.responses']);
        $jobsByRole = Ff14Job::groupedByRole();

        $myToken = request()->cookie('participant_' . $event->ulid);
        $myParticipant = null;
        if ($myToken) {
            $myParticipant = $event->participants->firstWhere('participant_token', $myToken);
        }

        return view('events.show', compact('event', 'jobsByRole', 'myParticipant'));
    }

    public static function syncSlots(Event $event, array $validated): void
    {
        $dateFrom = Carbon::parse($validated['date_from']);
        $dateTo = Carbon::parse($validated['date_to']);
        $times = !empty($validated['times']) ? $validated['times'] : [null];

        $newSlots = [];
        $order = 0;
        $current = $dateFrom->copy();
        while ($current->lte($dateTo)) {
            foreach ($times as $time) {
                $newSlots[] = [
                    'date' => $current->toDateString(),
                    'time' => $time,
                    'order' => $order++,
                ];
            }
            $current->addDay();
        }

        // Diff existing slots
        $existing = $event->slots()->get()->keyBy(fn($s) => $s->slot_date->toDateString() . '_' . ($s->slot_time ?? ''));
        $newKeys = collect($newSlots)->keyBy(fn($s) => $s['date'] . '_' . ($s['time'] ?? ''));

        // Delete removed
        foreach ($existing as $key => $slot) {
            if (!$newKeys->has($key)) {
                $slot->delete();
            }
        }

        // Insert new
        foreach ($newSlots as $slotData) {
            $key = $slotData['date'] . '_' . ($slotData['time'] ?? '');
            if (!$existing->has($key)) {
                Slot::create([
                    'event_id' => $event->id,
                    'slot_date' => $slotData['date'],
                    'slot_time' => $slotData['time'],
                    'sort_order' => $slotData['order'],
                ]);
            } else {
                $existing[$key]->update(['sort_order' => $slotData['order']]);
            }
        }
    }
}
