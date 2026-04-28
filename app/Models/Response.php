<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Response extends Model
{
    public $timestamps = false;

    protected $fillable = ['participant_id', 'slot_id', 'attendance', 'note'];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    public function attendanceLabel(): string
    {
        return match($this->attendance) {
            'attend' => '○',
            'maybe' => '△',
            'decline' => '×',
            default => '-',
        };
    }

    public function attendanceCssClass(): string
    {
        return match($this->attendance) {
            'attend' => 'attend-circle',
            'maybe' => 'attend-triangle',
            'decline' => 'attend-cross',
            default => '',
        };
    }
}
