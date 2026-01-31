<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Locale;

class LocaleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_list_locales()
    {
        Locale::factory()->count(3)->create();

        $this->getJson('/api/locales')
            ->assertOk();
    }

    public function test_can_create_locale()
    {
        $response = $this->postJson('/api/locales', [
            'code' => 'nl',
            'name' => 'Netherlands',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['code' => 'nl', 'name' => 'Netherlands']);

        $this->assertDatabaseHas('locales', ['code' => 'nl', 'name' => 'Netherlands']);
    }

    public function test_locale_code_must_be_unique()
    {
        Locale::create(['code' => 'en', 'name' => 'English']);
        $this->postJson('/api/locales', ['code' => 'en', 'name' => 'English Duplicate'])
             ->assertStatus(422);
    }

    public function test_can_update_locale()
    {
        $locale = Locale::firstOrCreate(['code' => 'es', 'name' => 'Spanish']);
        $this->putJson("/api/locales/{$locale->id}", [
            'code' => 'es',
            'name' => 'Español',
        ])
        ->assertStatus(200)
        ->assertJsonFragment(['name' => 'Español']);
    }

    public function test_can_delete_locale()
    {
        $locale = Locale::create(['code' => 'l1', 'name' => 'Locale to be deleted']);
        $this->deleteJson("/api/locales/{$locale->id}")
             ->assertStatus(204);
        $this->assertDatabaseMissing('locales', ['id' => $locale->id]);
    }
}
