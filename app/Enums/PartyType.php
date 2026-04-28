<?php

namespace App\Enums;

enum PartyType: string
{
    case Light = 'light';
    case Full = 'full';
    case Alliance = 'alliance';
    case AllianceSpecial = 'alliance_special';

    public function label(): string
    {
        return match($this) {
            self::Light => 'ライトパーティ（4人）',
            self::Full => 'フルパーティ（8人）',
            self::Alliance => 'アライアンスパーティ（8人×3）',
            self::AllianceSpecial => 'アライアンス特殊パーティ（8人×6）',
        };
    }

    public function unitCount(): int
    {
        return match($this) {
            self::Light => 1,
            self::Full => 1,
            self::Alliance => 3,
            self::AllianceSpecial => 6,
        };
    }

    public function unitLabels(): array
    {
        return match($this) {
            self::Light => ['PT'],
            self::Full => ['PT'],
            self::Alliance => ['Aアラ', 'Bアラ', 'Cアラ'],
            self::AllianceSpecial => ['Aアラ', 'Bアラ', 'Cアラ', 'Dアラ（1アラ）', 'Eアラ（2アラ）', 'Fアラ（3アラ）'],
        };
    }

    public function defaultRoleConfig(): array
    {
        $labels = $this->unitLabels();
        $units = [];

        foreach ($labels as $label) {
            $units[] = match($this) {
                self::Light => ['label' => $label, 'tank' => 1, 'healer' => 1, 'dps' => 2],
                self::Full,
                self::Alliance,
                self::AllianceSpecial => ['label' => $label, 'tank' => 2, 'healer' => 2, 'dps' => 4],
            };
        }

        return ['units' => $units];
    }
}
