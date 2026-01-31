<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $total = 100_000;
        $chunkSize = 1_000;
        $now = now();

        for ($i = 1; $i <= $total; $i += $chunkSize) {
            $tags = [];

            for ($j = $i; $j < $i + $chunkSize && $j <= $total; $j++) {
                $tags[] = [
                    'name' => 'tag_'.$j,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('tags')->insert($tags);
        }
    }
}
