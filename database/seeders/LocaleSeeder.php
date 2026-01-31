<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('locales')->insert([
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'tg', 'name' => 'Tagalog'],
            ['code' => 'de', 'name' => 'German'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'jp', 'name' => 'Japanese'],
        ]);
    }
}
