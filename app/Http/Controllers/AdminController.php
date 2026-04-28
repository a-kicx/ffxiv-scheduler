<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\Ff14Job;
use App\Models\Participant;
use App\Models\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function show(Event $event, string $adminToken): View|RedirectResponse
    {
        if (!$this->verifyToken($event, $adminToken)) {
            abort(403, '管理者トークンが無効です。');
        }

        $event->load(['slots', 'participants.responses']);
        $jobsByRole = Ff14Job::groupedByRole();

        return view('admin.show', compact('event', 'adminToken', 'jobsByRole'));
    }

    public function edit(Event $event, string $adminToken): View|RedirectResponse
    {
        if (!$this->verifyToken($event, $adminToken)) {
            abort(403, '管理者トークンが無効です。');
        }

        $event->load('slots');
        $jobsByRole = Ff14Job::groupedByRole();

        return view('admin.edit', compact('event', 'adminToken', 'jobsByRole'));
    }

    public function update(EventRequest $request, Event $event, string $adminToken): RedirectResponse
    {
        if (!$this->verifyToken($event, $adminToken)) {
            abort(403);
        }

        $validated = $request->validated();

        $event->update([
            'name' => $validated['name'],
            'party_type' => $validated['party_type'],
            'role_config' => $validated['role_config'],
            'job_mode' => $validated['job_mode'],
            'sub_job_mode' => $validated['sub_job_mode'],
            'attendance_mode' => $validated['attendance_mode'],
        ]);

        EventController::syncSlots($event, $validated);

        return redirect()->route('admin.show', ['event' => $event->ulid, 'adminToken' => $adminToken])
            ->with('success', 'イベントを更新しました。');
    }

    public function clearSoft(Event $event, string $adminToken): RedirectResponse
    {
        if (!$this->verifyToken($event, $adminToken)) {
            abort(403);
        }

        $participantIds = $event->participants()->pluck('id');
        Response::whereIn('participant_id', $participantIds)->delete();
        Participant::where('event_id', $event->id)->update([
            'selected_jobs' => null,
            'selected_sub_jobs' => null,
            'remarks' => null,
        ]);

        return redirect()->route('admin.show', ['event' => $event->ulid, 'adminToken' => $adminToken])
            ->with('success', '参加情報（回答・ジョブ・備考）をクリアしました。');
    }

    public function clearHard(Event $event, string $adminToken): RedirectResponse
    {
        if (!$this->verifyToken($event, $adminToken)) {
            abort(403);
        }

        Participant::where('event_id', $event->id)->delete();

        return redirect()->route('admin.show', ['event' => $event->ulid, 'adminToken' => $adminToken])
            ->with('success', '参加者を全員削除しました。');
    }

    private function verifyToken(Event $event, string $token): bool
    {
        return hash_equals($event->admin_token, $token);
    }
}
