<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TagApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_list_tags()
    {
        Tag::factory()->count(3)->create();

        $this->getJson('/api/tags')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_tag()
    {
        $response = $this->postJson('/api/tags', [
            'name' => 'new-tag',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'new-tag']);

        $this->assertDatabaseHas('tags', ['name' => 'new-tag']);
    }

    public function test_tag_name_must_be_unique()
    {
        Tag::create(['name' => 'existing-tag']);

        $this->postJson('/api/tags', ['name' => 'existing-tag'])
            ->assertStatus(422);
    }

    public function test_can_update_tag()
    {
        $tag = Tag::create(['name' => 'old-name']);

        $this->putJson("/api/tags/{$tag->id}", [
            'name' => 'updated-name',
        ])
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'updated-name']);
    }

    public function test_can_delete_tag()
    {
        $tag = Tag::create(['name' => 'to-be-deleted']);

        $this->deleteJson("/api/tags/{$tag->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('tags', ['name' => 'to-be-deleted']);
    }

    public function test_tags_list_loads_10k_rows_in_under_5_seconds()
    {
        // Seed 100,000 tags
        for ($i = 1; $i <= 10; $i++) {
            Tag::factory()->count(1000)->create();
            // Tag::create(['name' => "Tag {$i}"]);
        }

        // Authenticate
        Sanctum::actingAs(User::factory()->create());

        // Start timing
        $start = microtime(true);

        // Make the API request
        $response = $this->getJson('/api/tags?count=100000');

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
            "Tags endpoint exceeded 5 seconds: {$duration}s"
        );

        echo "Tags endpoint loaded 100k rows in {$duration}s\n";
    }
}
