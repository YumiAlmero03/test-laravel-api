<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Locale",
 *     type="object",
 *     required={"id","code"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="en"),
 *     @OA\Property(property="name", type="string", example="English")
 * )
 *
 * @OA\Schema(
 *     schema="Tag",
 *     type="object",
 *     required={"id","name"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="mobile")
 * )
 *
 * @OA\Schema(
 *     schema="Translation",
 *     type="object",
 *     required={"id","key","value","locale"},
 *
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="key", type="string", example="welcome.title"),
 *     @OA\Property(property="value", type="string", example="Welcome"),
 *     @OA\Property(property="locale", ref="#/components/schemas/Locale"),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/Tag")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     required={"message","errors"},
 *
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties=@OA\Schema(
 *             type="array",
 *
 *             @OA\Items(type="string", example="This field is required.")
 *         )
 *     )
 * )
 */
class Schemas {}
