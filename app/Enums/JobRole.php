<?php

namespace App\Enums;

enum JobRole: string
{
    case Tank = 'tank';
    case Healer = 'healer';
    case Melee = 'melee';
    case PhysRanged = 'pranged';
    case MagRanged = 'mranged';

    public function label(): string
    {
        return match($this) {
            self::Tank => 'タンク',
            self::Healer => 'ヒーラー',
            self::Melee => '近接DPS',
            self::PhysRanged => '遠隔物理DPS',
            self::MagRanged => '遠隔魔法DPS',
        };
    }

    public function colorClass(): string
    {
        return match($this) {
            self::Tank => 'role-tank',
            self::Healer => 'role-healer',
            self::Melee => 'role-melee',
            self::PhysRanged => 'role-pranged',
            self::MagRanged => 'role-mranged',
        };
    }
}
