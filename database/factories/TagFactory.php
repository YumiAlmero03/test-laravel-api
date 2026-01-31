<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tag;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        static $i = 1;
        $i++;
        return [
            'name' => "Tag {$i}",
        ];
    }
}
