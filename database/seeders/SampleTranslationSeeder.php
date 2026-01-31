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
                "jp" => "私のアプリケーション",
                "tag" => ['Home']
            ],
            [
                "key" => "app.description",
                "en" => "This is a sample application.",
                "tg" => "Ito ay isang halimbawa ng aplikasyon.",
                "de" => "Dies ist eine Beispielanwendung.",
                "es" => "Esta es una aplicación de ejemplo.",
                "jp" => "これはサンプルアプリケーションです。",
                "tag" => ['Home']
            ],
            [
                "key" => "link.docs",
                "en" => "Go to OpenAPI Documentation",
                "tg" => "Pumunta sa Dokumentasyon ng OpenAPI",
                "de" => "Gehe zur OpenAPI-Dokumentation",
                "es" => "Ir a la Documentación de la OpenAPI",
                "jp" => "OpenAPIドキュメントへ移動",
                "tag" => ['Header']
            ],
            [
                "key" => "link.languages",
                "en" => "Languages",
                "tg" => "Mga Wika",
                "de" => "Sprachen",
                "es" => "Idiomas",
                "jp" => "言語",
                "tag" => ['Header']
            ],
            [
                "key" => "button.submit",
                "en" => "Submit",
                "tg" => "Isumite",
                "de" => "Einreichen",
                "es" => "Enviar",
                "jp" => "送信",
                "tag" => ['Form']
            ],
            [
                "key" => "button.cancel",
                "en" => "Cancel",
                "tg" => "Kanselahin",
                "de" => "Abbrechen",
                "es" => "Cancelar",
                "jp" => "キャンセル",
                "tag" => ['Form']
            ],
            [
                "key" => "message.welcome",
                "en" => "Welcome to our application!",
                "tg" => "Maligayang pagdating sa aming aplikasyon!",
                "de" => "Willkommen in unserer Anwendung!",
                "es" => "¡Bienvenido a nuestra aplicación!",
                "jp" => "私たちのアプリケーションへようこそ！",
                "tag" => ['Home']
            ],
            [
                "key" => "button.language",
                "en" => "Select Language",
                "tg" => "Pumili ng Wika",
                "de" => "Sprache auswählen",
                "es" => "Seleccionar Idioma",
                "jp" => "言語を選択",
                "tag" => ['Header']
            ],
            [
                "key" => "form.credentials",
                "en" => "Credentials",
                "tg" => "Mga Kredensyal",
                "de" => "Anmeldeinformationen",
                "es" => "Credenciales",
                "jp" => "資格情報",
                "tag" => ['Form']
            ],
            [
                "key" => "form.password",
                "en" => "Password",
                "tg" => "Password",
                "de" => "Passwort",
                "es" => "Contraseña",
                "jp" => "パスワード",
                "tag" => ['Form']
            ],
            [
                "key" => "form.email",
                "en" => "Email",
                "tg" => "Email",
                "de" => "E-Mail",
                "es" => "Correo electrónico",
                "jp" => "メール",
                "tag" => ['Form']
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
