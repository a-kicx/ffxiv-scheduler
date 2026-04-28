<?php

namespace App\Models;

use App\Enums\AttendanceMode;
use App\Enums\JobMode;
use App\Enums\PartyType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'ulid', 'admin_token', 'name', 'party_type',
        'role_config', 'job_mode', 'sub_job_mode', 'attendance_mode',
    ];

    protected $casts = [
        'party_type' => PartyType::class,
        'job_mode' => JobMode::class,
        'sub_job_mode' => JobMode::class,
        'attendance_mode' => AttendanceMode::class,
        'role_config' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class)->orderBy('sort_order');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class)->orderBy('created_at');
    }

    public static function generateUlid(): string
    {
        return strtolower((string) Str::ulid());
    }

    public static function generateAdminToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
