<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slot extends Model
{
    public $timestamps = false;

    protected $fillable = ['event_id', 'slot_date', 'slot_time', 'sort_order'];

    protected $casts = [
        'slot_date' => 'date',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function label(): string
    {
        $date = $this->slot_date->format('n/j');
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $dow = $weekdays[(int) $this->slot_date->format('w')];
        $label = "{$date}（{$dow}）";

        if ($this->slot_time) {
            $label .= ' ' . substr($this->slot_time, 0, 5);
        }

        return $label;
    }
}
