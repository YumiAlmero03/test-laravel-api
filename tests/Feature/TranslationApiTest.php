<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_list_translations()
    {
        $this->getJson('/api/translations')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_translation()
    {
        $locale = Locale::firstOrCreate(['code' => 'en', 'name' => 'English']);
        $tag = Tag::firstOrCreate(['name' => 'greeting']);

        $response = $this->postJson('/api/translations', [
            'locale_id' => $locale->id,
            'key' => 'welcome.message',
            'value' => 'Welcome to our application!',
            'tags' => [$tag->id],
        ]);
        $response->assertStatus(201)
            ->assertJsonFragment(['key' => 'welcome.message', 'value' => 'Welcome to our application!']);
        $this->assertDatabaseHas('translations', ['key' => 'welcome.message', 'value' => 'Welcome to our application!']);
    }

    public function test_translation_key_value_must_be_unique_per_locale()
    {
        $locale = Locale::firstOrCreate(['code' => 'en', 'name' => 'English']);
        Translation::create([
            'locale_id' => $locale->id,
            'key' => 'duplicate.key',
            'value' => 'Duplicate Value',
        ]);

        $this->postJson('/api/translations', [
            'locale_id' => $locale->id,
            'key' => 'duplicate.key',
            'value' => 'Duplicate Value',
        ])->assertStatus(422);
    }

    public function test_can_update_translation()
    {
        $locale = Locale::firstOrCreate(['code' => 'tg', 'name' => 'Tagalog']);
        $translation = Translation::firstOrCreate([
            'locale_id' => $locale->id,
            'key' => 'update.this',
            'value' => 'the Old Value',
        ]);
        $this->putJson("/api/translations/{$translation->id}", [
            'locale_id' => $locale->id,
            'key' => 'update.this',
            'value' => 'Updated Value',
        ])
            ->assertStatus(200);
        $this->assertDatabaseHas('translations', ['id' => $translation->id, 'value' => 'Updated Value']);
    }

    public function test_can_delete_translation()
    {
        $locale = Locale::firstOrCreate(['code' => 'en', 'name' => 'English']);
        $translation = Translation::firstOrCreate([
            'locale_id' => $locale->id,
            'key' => 'delete.me',
            'value' => 'To be deleted',
        ]);
        $this->deleteJson("/api/translations/{$translation->id}")
            ->assertStatus(204);
        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    public function test_translations_list_loads_10k_rows_in_under_5_seconds()
    {
        // Seed 100,000 translations
        for ($i = 1; $i <= 100; $i++) {
            Translation::factory()->count(100)->create();
        }

        // Authenticate
        Sanctum::actingAs(User::factory()->create());

        // Start timing
        $start = microtime(true);

        // Make the API request
        $response = $this->getJson('/api/translations?count=10000');

        // End timing
        $duration = microtime(true) - $start;

        // Assertions
        $response->assertOk()
            ->assertJsonStructure(['data']); // pagination structure

        $data = $response->json('data');
        $this->assertCount(10_000, $data, 'Expected 10k rows, got '.count($data));

        // Check performance
        $this->assertLessThan(
            5, // 5 seconds for 10k rows
            $duration,
            "Translations endpoint exceeded 5 seconds: {$duration}s"
        );

        echo "Translations endpoint loaded 100k rows in {$duration}s\n";
    }
}
