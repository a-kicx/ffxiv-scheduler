<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportJobSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            ['name' => 'すっぴん',   'name_en' => 'Freelancer',    'sort_order' => 1],
            ['name' => 'ナイト',     'name_en' => 'Knight',        'sort_order' => 2],
            ['name' => 'モンク',     'name_en' => 'Monk',          'sort_order' => 3],
            ['name' => '吟遊詩人',   'name_en' => 'Bard',          'sort_order' => 4],
            ['name' => 'シーフ',     'name_en' => 'Thief',         'sort_order' => 5],
            ['name' => '侍',         'name_en' => 'Samurai',       'sort_order' => 6],
            ['name' => 'バーサーカー','name_en' => 'Berserker',    'sort_order' => 7],
            ['name' => '狩人',       'name_en' => 'Ranger',        'sort_order' => 8],
            ['name' => '時魔道士',   'name_en' => 'Time Mage',     'sort_order' => 9],
            ['name' => '薬師',       'name_en' => 'Chemist',       'sort_order' => 10],
            ['name' => '風水士',     'name_en' => 'Geomancer',     'sort_order' => 11],
            ['name' => '占い師',     'name_en' => 'Oracle',        'sort_order' => 12],
            ['name' => '砲術士',     'name_en' => 'Cannoneer',     'sort_order' => 13],
            ['name' => '魔法剣士',   'name_en' => 'Mystic Knight', 'sort_order' => 14],
            ['name' => '剣闘士',     'name_en' => 'Gladiator',     'sort_order' => 15],
            ['name' => '踊り子',     'name_en' => 'Dancer',        'sort_order' => 16],
        ];

        DB::table('support_jobs')->insert($jobs);
    }
}
