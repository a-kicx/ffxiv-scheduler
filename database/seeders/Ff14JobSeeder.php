<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Ff14JobSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            // Tanks
            ['abbreviation' => 'PLD', 'name' => 'ナイト',     'name_en' => 'Paladin',      'role' => 'tank',    'sort_order' => 1],
            ['abbreviation' => 'WAR', 'name' => '戦士',       'name_en' => 'Warrior',       'role' => 'tank',    'sort_order' => 2],
            ['abbreviation' => 'DRK', 'name' => '暗黒騎士',   'name_en' => 'Dark Knight',   'role' => 'tank',    'sort_order' => 3],
            ['abbreviation' => 'GNB', 'name' => 'ガンブレイカー','name_en' => 'Gunbreaker',  'role' => 'tank',    'sort_order' => 4],
            // Healers
            ['abbreviation' => 'WHM', 'name' => '白魔道士',   'name_en' => 'White Mage',    'role' => 'healer',  'sort_order' => 10],
            ['abbreviation' => 'SCH', 'name' => '学者',       'name_en' => 'Scholar',       'role' => 'healer',  'sort_order' => 11],
            ['abbreviation' => 'AST', 'name' => '占星術士',   'name_en' => 'Astrologian',   'role' => 'healer',  'sort_order' => 12],
            ['abbreviation' => 'SGE', 'name' => '賢者',       'name_en' => 'Sage',          'role' => 'healer',  'sort_order' => 13],
            // Melee DPS
            ['abbreviation' => 'MNK', 'name' => '格闘士',     'name_en' => 'Monk',          'role' => 'melee',   'sort_order' => 20],
            ['abbreviation' => 'DRG', 'name' => '竜騎士',     'name_en' => 'Dragoon',       'role' => 'melee',   'sort_order' => 21],
            ['abbreviation' => 'NIN', 'name' => '忍者',       'name_en' => 'Ninja',         'role' => 'melee',   'sort_order' => 22],
            ['abbreviation' => 'SAM', 'name' => '侍',         'name_en' => 'Samurai',       'role' => 'melee',   'sort_order' => 23],
            ['abbreviation' => 'RPR', 'name' => 'リーパー',   'name_en' => 'Reaper',        'role' => 'melee',   'sort_order' => 24],
            ['abbreviation' => 'VPR', 'name' => 'ヴァイパー', 'name_en' => 'Viper',         'role' => 'melee',   'sort_order' => 25],
            // Physical Ranged DPS
            ['abbreviation' => 'BRD', 'name' => '吟遊詩人',   'name_en' => 'Bard',          'role' => 'pranged', 'sort_order' => 30],
            ['abbreviation' => 'MCH', 'name' => '機工士',     'name_en' => 'Machinist',     'role' => 'pranged', 'sort_order' => 31],
            ['abbreviation' => 'DNC', 'name' => '踊り子',     'name_en' => 'Dancer',        'role' => 'pranged', 'sort_order' => 32],
            // Magical Ranged DPS
            ['abbreviation' => 'BLM', 'name' => '黒魔道士',   'name_en' => 'Black Mage',    'role' => 'mranged', 'sort_order' => 40],
            ['abbreviation' => 'SMN', 'name' => '召喚士',     'name_en' => 'Summoner',      'role' => 'mranged', 'sort_order' => 41],
            ['abbreviation' => 'RDM', 'name' => '赤魔道士',   'name_en' => 'Red Mage',      'role' => 'mranged', 'sort_order' => 42],
            ['abbreviation' => 'PCT', 'name' => 'ピクトマンサー','name_en' => 'Pictomancer', 'role' => 'mranged', 'sort_order' => 43],
        ];

        DB::table('ff14_jobs')->insert($jobs);
    }
}
