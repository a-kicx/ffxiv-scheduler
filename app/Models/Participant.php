<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use App\Models\SupportJob;

class Participant extends Model
{
    protected $fillable = [
        'event_id', 'participant_token', 'name',
        'selected_jobs', 'selected_sub_jobs', 'remarks',
    ];

    protected $casts = [
        'selected_jobs' => 'array',
        'selected_sub_jobs' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function getResponseForSlot(int $slotId): ?Response
    {
        return $this->responses->firstWhere('slot_id', $slotId);
    }

    public function resolvedJobs(): Collection
    {
        if (empty($this->selected_jobs)) {
            return collect();
        }
        return Ff14Job::whereIn('id', $this->selected_jobs)->orderBy('sort_order')->get();
    }

    public function resolvedSupportJobs(): Collection
    {
        if (empty($this->selected_sub_jobs)) {
            return collect();
        }
        return SupportJob::whereIn('id', $this->selected_sub_jobs)->orderBy('sort_order')->get();
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
