<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTranslationRequest;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/translations",
     *     summary="List translations with filters",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="key",
     *         in="query",
     *         description="Filter by translation key prefix",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         description="Filter by locale code (e.g. en, fr)",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Filter by tag name",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *    @OA\Parameter(
     *        name="count",
     *        in="query",
     *        description="Number of items per page for pagination",
     *        required=false,
     *
     *        @OA\Schema(type="integer", default=50)
     *    ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of translations",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/Translation")
     *             ),
     *
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Translation::with(['locale', 'tags'])->select(['id', 'key', 'value', 'locale_id']);

        if ($request->filled('key')) {
            $query->where('key', 'like', $request->key.'%');
        }

        if ($request->filled('locale')) {
            $query->whereHas('locale', fn ($q) => $q->where('code', $request->locale)
            );
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('name', $request->tag)
            );
        }

        $count = $request->get('count', 50);

        return response()->json(
            $query->orderBy('id')->paginate($count)
        );
    }

    /**
     * @OA\Post(
     *     path="/translations",
     *     summary="Create a translation",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"locale_id","key","value"},
     *
     *             @OA\Property(property="locale_id", type="integer", example=1),
     *             @OA\Property(property="key", type="string", example="welcome.title"),
     *             @OA\Property(property="value", type="string", example="Welcome"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 example="[1,2]",
     *
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Translation created",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Translation")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(StoreTranslationRequest $request)
    {
        $data = $request->validated();

        // process tags
        foreach ($data['tags'] as $tag) {
            $tags[] = Tag::firstOrCreate(['name' => $tag])->id;
        }
        $data['tags'] = $tags;

        $translation = Translation::create($data);
        if (! empty($data['tags'])) {
            $translation->tags()->sync($data['tags']);
        }

        return response()->json($translation->load('tags'), 201);
    }

    /**
     * @OA\Get(
     *     path="/translations/{id}",
     *     summary="Get a translation",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Translation details",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Translation")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Translation not found"
     *     )
     * )
     */
    public function show(Translation $translation)
    {
        try {
            return response()->json($translation->load('tags'));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Translation not found'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/translations/{id}",
     *     summary="Update a translation",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"locale_id","key","value"},
     *
     *             @OA\Property(property="locale_id", type="integer", example=1),
     *             @OA\Property(property="key", type="string", example="welcome.title"),
     *             @OA\Property(property="value", type="string", example="Welcome"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 example="[1,2]",
     *
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="value", type="string"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated",
     *
     *         @OA\JsonContent(ref="#/components/schemas/Translation")
     *     )
     * )
     */
    public function update(StoreTranslationRequest $request, Translation $translation)
    {
        try {
            $data = $request->validated();

            // process tags
            foreach ($data['tags'] as $tag) {
                $tags[] = Tag::firstOrCreate(['name' => $tag])->id;
            }
            $data['tags'] = $tags;

            $translation->update($data);
            if (array_key_exists('tags', $data)) {
                $translation->tags()->sync($data['tags']);
            }

            return response()->json(['message' => 'Translation updated successfully', 'translation' => $translation->load('tags')], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

    }

    /**
     * @OA\Delete(
     *     path="/translations/{id}",
     *     summary="Delete a translation",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Translation deleted"
     *     )
     * )
     */
    public function destroy(Translation $translation)
    {
        try {
            $translation->delete();

            return response()->json('Translation deleted', 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Translation not found'], 404);
        }

    }

    /**
     * @OA\Get(
     *     path="/translations/export",
     *     summary="Export translations for frontend",
     *     tags={"Translations"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Translations grouped by locale",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *         )
     *     )
     * )
     */
    public function export(Request $request)
    {
        $localeFilter = $request->get('locale');
        $tagFilter = $request->get('tag');
        $translations = Translation::query()
            ->select(['key', 'value', 'locale_id'])
            ->with('locale:id,code')
            ->when($localeFilter, fn ($q) => $q->whereHas('locale', fn ($q2) => $q2->where('code', $localeFilter)
            )
            )
            ->withTags($tagFilter ? explode(',', $tagFilter) : [])
            ->orderBy('id')
            ->get()
            ->pluck('value', 'key');

        return response()->json($translations);
    }
}
