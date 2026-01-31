<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Support\Facades\DB;

class SampleTranslationSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        // 1️⃣ Ensure locales exist
        $localeMap = Locale::pluck('id', 'code')->toArray();

        // 2️⃣ Translation source data
        $translations = [
            [
                "key" => "app.title",
                "en" => "My Application",
                "tg" => "Aking Aplikasyon",
                "de" => "Meine Anwendung",
                "es" => "Mi Aplicación",
                "tag" => ['Home']
            ],
            [
                "key" => "app.description",
                "en" => "This is a sample application.",
                "tg" => "Ito ay isang halimbawa ng aplikasyon.",
                "de" => "Dies ist eine Beispielanwendung.",
                "es" => "Esta es una aplicación de ejemplo.",
                "tag" => ['Home']
            ],
            [
                "key" => "button.submit",
                "en" => "Submit",
                "tg" => "Isumite",
                "de" => "Einreichen",
                "es" => "Enviar",
                "tag" => ['Form']
            ],
            [
                "key" => "button.cancel",
                "en" => "Cancel",
                "tg" => "Kanselahin",
                "de" => "Abbrechen",
                "es" => "Cancelar",
                "tag" => ['Form']
            ],
            [
                "key" => "message.welcome",
                "en" => "Welcome to our application!",
                "tg" => "Maligayang pagdating sa aming aplikasyon!",
                "de" => "Willkommen in unserer Anwendung!",
                "es" => "¡Bienvenido a nuestra aplicación!",
                "tag" => ['Home']
            ],
            [
                "key" => "button.language",
                "en" => "Language",
                "tg" => "Wika",
                "de" => "Sprache",
                "es" => "Idioma",
                "tag" => ['Header']
            ]
        ];

        // 3️⃣ Insert translations + attach tags
        foreach ($translations as $row) {
            // Create or fetch tags once
            $tagIds = collect($row['tag'])
                ->map(fn ($tag) =>
                    Tag::firstOrCreate(['name' => $tag])->id
                )
                ->toArray();

            foreach ($localeMap as $code => $localeId) {
                if (!isset($row[$code])) {
                    continue;
                }

                $translation = Translation::firstOrCreate(
                    [
                        'locale_id' => $localeId,
                        'key' => $row['key'],
                    ],
                    [
                        'value' => $row[$code],
                    ]
                );

                // Attach tags (sync without detach avoids duplicates)
                $translation->tags()->syncWithoutDetaching($tagIds);
            }
        }
    }
}
