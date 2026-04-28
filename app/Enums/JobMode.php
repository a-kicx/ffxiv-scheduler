<?php

namespace App\Enums;

enum JobMode: string
{
    case None = 'none';
    case Single = 'single';
    case Multiple = 'multiple';

    public function label(): string
    {
        return match($this) {
            self::None => '指定なし',
            self::Single => '単一選択',
            self::Multiple => '複数選択',
        };
    }
}
