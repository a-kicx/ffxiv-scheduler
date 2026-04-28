<?php

namespace App\Models;

use App\Enums\JobRole;
use Illuminate\Database\Eloquent\Model;

class Ff14Job extends Model
{
    public $timestamps = false;

    protected $table = 'ff14_jobs';

    protected $casts = [
        'role' => JobRole::class,
    ];

    public static function groupedByRole(): array
    {
        $groups = [];
        self::orderBy('sort_order')->get()->each(function (Ff14Job $job) use (&$groups) {
            $groups[$job->role->value][] = $job;
        });
        return $groups;
    }
}
