<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportJob extends Model
{
    public $timestamps = false;

    protected $table = 'support_jobs';

    public static function all($columns = ['*'])
    {
        return parent::query()->orderBy('sort_order')->get($columns);
    }
}
