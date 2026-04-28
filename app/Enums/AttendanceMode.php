<?php

namespace App\Enums;

enum AttendanceMode: string
{
    case Binary = 'binary';
    case Ternary = 'ternary';

    public function label(): string
    {
        return match($this) {
            self::Binary => '○× のみ',
            self::Ternary => '○△× の3種類',
        };
    }

    public function options(): array
    {
        return match($this) {
            self::Binary => [
                'attend' => ['label' => '○', 'class' => 'text-green-400'],
                'decline' => ['label' => '×', 'class' => 'text-red-400'],
            ],
            self::Ternary => [
                'attend' => ['label' => '○', 'class' => 'text-green-400'],
                'maybe' => ['label' => '△', 'class' => 'text-yellow-400'],
                'decline' => ['label' => '×', 'class' => 'text-red-400'],
            ],
        };
    }
}
