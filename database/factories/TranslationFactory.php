<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Translation;
use App\Models\Locale;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        static $i = 1;
        $i++;
        return [
            'locale_id' => 1,
            'key' => 'app.test' .   $i,
            'value' => 'This is a test translation ' . $i,
        ];
    }

}
