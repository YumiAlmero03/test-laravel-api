<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocaleRequest;
use App\Models\Locale;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocaleController extends Controller
{
    /**
     * @OA\PathItem(
     *     path="/locales",
     *
     *     @OA\Get(
     *         summary="List all locales",
     *         tags={"Locales"},
     *
     *         @OA\Response(
     *             response=200,
     *             description="List of locales",
     *
     *             @OA\JsonContent(
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="code", type="string", example="en"),
     *                     @OA\Property(property="name", type="string", example="English")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $query = Locale::orderBy('code')->select(['id', 'code', 'name'])->get();

        return response()->json($query);
    }

    /**
     * @OA\PathItem(
     *     path="/locales",
     *
     *     @OA\Post(
     *         summary="Create a new locale",
     *         tags={"Locales"},
     *         security={{"bearerAuth":{}}},
     *
     *         @OA\RequestBody(
     *             required=true,
     *             description="Locale data",
     *
     *             @OA\JsonContent(
     *                 required={"code", "name"},
     *
     *                 @OA\Property(property="code", type="string", example="jp"),
     *                 @OA\Property(property="name", type="string", example="Japanese")
     *             )
     *         ),
     *
     *         @OA\Response(
     *             response=201,
     *             description="Locale created successfully",
     *
     *             @OA\JsonContent(
     *
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="code", type="string"),
     *                 @OA\Property(property="name", type="string")
     *             )
     *         ),
     *
     *         @OA\Response(response=422, description="Validation error")
     *     )
     * )
     */
    public function store(StoreLocaleRequest $request)
    {
        $data = $request->validated();
        $locale = Locale::create($data);

        return response()->json($locale, 201);
    }

    /**
     * @OA\PathItem(
     *     path="/locales/{id}",
     *
     *     @OA\Get(
     *         summary="Get locale by ID",
     *         tags={"Locales"},
     *         security={{"bearerAuth":{}}},
     *
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             description="Locale ID",
     *             required=true,
     *
     *             @OA\Schema(type="integer")
     *         ),
     *
     *         @OA\Response(
     *             response=200,
     *             description="Locale details",
     *
     *             @OA\JsonContent(
     *
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="code", type="string"),
     *                 @OA\Property(property="name", type="string")
     *             )
     *         ),
     *
     *         @OA\Response(response=404, description="Locale not found")
     *     )
     * )
     */
    public function show(Locale $locale)
    {
        try {
            return response()->json($locale);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Locale not found'], 404);
        }
    }

    /**
     * @OA\PathItem(
     *     path="/locales/{id}",
     *
     *     @OA\Put(
     *         summary="Update a locale",
     *         tags={"Locales"},
     *         security={{"bearerAuth":{}}},
     *
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             description="Locale ID",
     *             required=true,
     *
     *             @OA\Schema(type="integer")
     *         ),
     *
     *         @OA\RequestBody(
     *             required=true,
     *             description="Locale data",
     *
     *             @OA\JsonContent(
     *                 required={"code", "name"},
     *
     *                 @OA\Property(property="code", type="string", example="fr"),
     *                 @OA\Property(property="name", type="string", example="French")
     *             )
     *         ),
     *
     *         @OA\Response(
     *             response=200,
     *             description="Locale updated successfully",
     *
     *             @OA\JsonContent(
     *
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="code", type="string"),
     *                 @OA\Property(property="name", type="string")
     *             )
     *         ),
     *
     *         @OA\Response(response=404, description="Locale not found"),
     *         @OA\Response(response=422, description="Validation error")
     *     )
     * )
     */
    public function update(StoreLocaleRequest $request, Locale $locale)
    {
        try {
            $locale->update($request->validated());

            return response()->json(['message' => 'Locale updated successfully', 'locale' => $locale], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Locale not found'], 404);
        }
    }

    /**
     * @OA\PathItem(
     *     path="/locales/{id}",
     *
     *     @OA\Delete(
     *         summary="Delete a locale",
     *         tags={"Locales"},
     *         security={{"bearerAuth":{}}},
     *
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             description="Locale ID",
     *             required=true,
     *
     *             @OA\Schema(type="integer")
     *         ),
     *
     *         @OA\Response(response=200, description="Locale deleted successfully"),
     *         @OA\Response(response=404, description="Locale not found")
     *     )
     * )
     */
    public function destroy($locale)
    {
        try {
            $locale = Locale::findOrFail($locale);
            $locale->delete();

            return response()->json(['message' => 'Locale deleted successfully'], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Locale not found'], 404);
        }
    }
}
