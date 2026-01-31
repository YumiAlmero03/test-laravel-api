<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $total = 100_000;
        $chunkSize = 1_000;
        $now = now();

        $locales = DB::table('locales')->pluck('id')->toArray();

        if (empty($locales)) {
            throw new \RuntimeException('Locales must be seeded first.');
        }

        for ($i = 1; $i <= $total; $i += $chunkSize) {
            $rows = [];

            for ($j = $i; $j < $i + $chunkSize && $j <= $total; $j++) {
                $rows[] = [
                    'locale_id' => $locales[array_rand($locales)],
                    'key' => 'app.key_'.$j,
                    'value' => 'Translation value '.$j,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('translations')->insert($rows);
        }
    }
}
