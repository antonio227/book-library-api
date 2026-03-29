<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Info(
 *     title="Book Library API",
 *     version="1.0.0",
 *     description="A REST API for managing a book library collection. Supports full CRUD operations on books with search and filter capabilities."
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 *
 * @OA\Schema(
 *     schema="Book",
 *     type="object",
 *     description="A book in the library",
 *     @OA\Property(property="id",           type="integer", example=1),
 *     @OA\Property(property="title",        type="string",  example="The Great Gatsby"),
 *     @OA\Property(property="publisher",    type="string",  example="Scribner"),
 *     @OA\Property(property="author",       type="string",  example="F. Scott Fitzgerald"),
 *     @OA\Property(property="genre",        type="string",  example="Fiction"),
 *     @OA\Property(property="published_at", type="string",  format="date",      example="1925-04-10"),
 *     @OA\Property(property="word_count",   type="integer", example=47094),
 *     @OA\Property(property="price",        type="number",  format="float",     example=12.99),
 *     @OA\Property(property="created_at",   type="string",  format="date-time"),
 *     @OA\Property(property="updated_at",   type="string",  format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="BookInput",
 *     type="object",
 *     required={"title","publisher","author","genre","published_at","word_count","price"},
 *     description="Payload for creating or updating a book",
 *     @OA\Property(property="title",        type="string",  example="The Great Gatsby",    maxLength=255),
 *     @OA\Property(property="publisher",    type="string",  example="Scribner",            maxLength=255),
 *     @OA\Property(property="author",       type="string",  example="F. Scott Fitzgerald", maxLength=255),
 *     @OA\Property(property="genre",        type="string",  example="Fiction",             maxLength=100),
 *     @OA\Property(property="published_at", type="string",  format="date",                 example="1925-04-10"),
 *     @OA\Property(property="word_count",   type="integer", minimum=1,                     example=47094),
 *     @OA\Property(property="price",        type="number",  format="float",   minimum=0,   example=12.99)
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties={
 *             "type": "array",
 *             "items": {"type": "string"}
 *         },
 *         example={"title": {"The title field is required."}}
 *     )
 * )
 */
class BookController extends Controller
{
    /**
     * Return a paginated / filtered list of all books in the library.
     *
     * @OA\Get(
     *     path="/books",
     *     summary="List all books",
     *     description="Returns all books. Supports full-text search and genre filtering via query parameters.",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search across title, author, publisher, and genre",
     *         required=false,
     *         @OA\Schema(type="string", example="Tolkien")
     *     ),
     *     @OA\Parameter(
     *         name="genre",
     *         in="query",
     *         description="Filter by exact genre",
     *         required=false,
     *         @OA\Schema(type="string", example="Fantasy")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Book")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Book::query();

        // Full-text search across the most useful text columns
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title',     'like', "%{$search}%")
                  ->orWhere('author',    'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('genre',     'like', "%{$search}%");
            });
        }

        // Exact genre filter — useful for browsing by category
        if ($genre = $request->query('genre')) {
            $query->where('genre', $genre);
        }

        return BookResource::collection(
            $query->orderBy('title')->get()
        );
    }

    /**
     * Store a newly created book in the library.
     *
     * @OA\Post(
     *     path="/books",
     *     summary="Create a new book",
     *     description="Adds a new book to the library. All fields are required.",
     *     tags={"Books"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BookInput")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Book")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        // Only validated fields are passed to prevent mass-assignment issues
        $book = Book::create($request->validated());

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified book.
     *
     * @OA\Get(
     *     path="/books/{id}",
     *     summary="Get a single book",
     *     description="Returns the full details of a single book by its ID.",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Numeric ID of the book",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Book")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
    public function show(Book $book): BookResource
    {
        // Laravel's route model binding automatically resolves and 404s if missing
        return new BookResource($book);
    }

    /**
     * Update the specified book (partial update — only provided fields change).
     *
     * @OA\Patch(
     *     path="/books/{id}",
     *     summary="Update a book (partial update)",
     *     description="Updates one or more fields of an existing book. Only the provided fields are changed.",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Numeric ID of the book",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BookInput")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Book")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Book not found"),
     *     @OA\Response(response=422, description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        // Only the validated fields that were actually sent will be updated
        $book->update($request->validated());

        return new BookResource($book);
    }

    /**
     * Remove the specified book from the library.
     *
     * @OA\Delete(
     *     path="/books/{id}",
     *     summary="Delete a book",
     *     description="Permanently removes a book from the library.",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Numeric ID of the book",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=204, description="Book deleted — no content returned"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        // 204 No Content — standard REST response for successful deletion
        return response()->json(null, 204);
    }
}
