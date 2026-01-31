<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTagRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * @OA\PathItem(
     *     path="/tags",
     *     @OA\Get(
     *         summary="List tags",
     *         tags={"Tags"},
     *         security={{"bearerAuth":{}}},
     *         @OA\Parameter(
     *             name="search",
     *             in="query",
     *             description="Search tags by name",
     *             required=false,
     *             @OA\Schema(type="string")
     *         ),
     *      @OA\Parameter(
     *        name="count",
     *        in="query",
     *        description="Number of items per page for pagination",
     *        required=false,
     *        @OA\Schema(type="integer", default=50)
     *      ),
     *         @OA\Response(
     *             response=200,
     *             description="List of tags",
     *             @OA\JsonContent(
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Tag::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', $request->search . '%');
        }
        $count = $request->get('count', 50);

        return response()->json($query->select(['id', 'name'])->orderBy('id')->paginate($count));
    }

    /**
     * @OA\PathItem(
     *     path="/tags",
     *     @OA\Post(
     *         summary="Create a new tag",
     *         tags={"Tags"},
     *         security={{"bearerAuth":{}}},
     *         @OA\RequestBody(
     *             required=true,
     *             description="Tag data",
     *             @OA\JsonContent(
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="Technology")
     *             )
     *         ),
     *         @OA\Response(
     *             response=201,
     *             description="Tag created successfully",
     *             @OA\JsonContent(
     *                 @OA\Property(property="name", type="string")
     *             )
     *         ),
     *         @OA\Response(response=422, description="Validation error")
     *     )
     * )
     */
    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create($request->validated());
        return response()->json($tag, 201);
    }

    /**
     * @OA\PathItem(
     *     path="/tags/{id}",
     *     @OA\Get(
     *         summary="Get tag by ID",
     *         tags={"Tags"},
     *         security={{"bearerAuth":{}}},
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             description="Tag ID",
     *             required=true,
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Tag details",
     *             @OA\JsonContent(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string")
     *             )
     *         ),
     *         @OA\Response(response=404, description="Tag not found")
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            return response()->json($tag);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tag not found'], 404);
        }
    }

    /**
     * @OA\PathItem(
     *     path="/tags/{id}",
     *     @OA\Put(
     *         summary="Update a tag",
     *         tags={"Tags"},
     *         security={{"bearerAuth":{}}},
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             description="Tag ID",
     *             required=true,
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\RequestBody(
     *             required=true,
     *             description="Tag data",
     *             @OA\JsonContent(
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="Updated Tag")
     *             )
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Tag updated successfully",
     *             @OA\JsonContent(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string")
     *             )
     *         ),
     *         @OA\Response(response=404, description="Tag not found"),
     *         @OA\Response(response=422, description="Validation error")
     *     )
     * )
     */
    public function update(StoreTagRequest $request, $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->update($request->validated());
            return response()->json($tag);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tag not found'], 404);
        }
    }

    /**
     * @OA\PathItem(
     *     path="/tags/{id}",
     *     @OA\Delete(
     *         summary="Delete a tag",
     *         tags={"Tags"},
     *         security={{"bearerAuth":{}}},
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             description="Tag ID",
     *             required=true,
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\Response(response=200, description="Tag deleted successfully"),
     *         @OA\Response(response=404, description="Tag not found")
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return response()->json(['message' => 'Tag deleted successfully'], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tag not found'], 404);
        }
    }
}
