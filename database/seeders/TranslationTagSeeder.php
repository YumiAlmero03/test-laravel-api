<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationTagSeeder extends Seeder
{
    public function run(): void
    {
        $translations = DB::table('translations')->pluck('id')->toArray();
        $tags = DB::table('tags')->pluck('id')->toArray();

        if (empty($translations) || empty($tags)) {
            return;
        }

        $rows = [];
        $now = now();

        foreach (array_slice($translations, 0, 20_000) as $translationId) {
            $rows[] = [
                'translation_id' => $translationId,
                'tag_id' => $tags[array_rand($tags)],
            ];
        }

        DB::table('tag_translations')->insertOrIgnore($rows);

    }
}
