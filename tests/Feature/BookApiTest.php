<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for all Book Library API endpoints.
 *
 * Each test runs against an in-memory SQLite database (configured in phpunit.xml)
 * that is fully reset between tests via RefreshDatabase.
 *
 * Coverage:
 *   - GET    /api/books         (list, search, filter)
 *   - POST   /api/books         (create + validation)
 *   - GET    /api/books/{id}    (show + 404)
 *   - PATCH  /api/books/{id}    (update + validation + 404)
 *   - DELETE /api/books/{id}    (delete + 404)
 */
class BookApiTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Shared fixture data used across multiple tests
    // -------------------------------------------------------------------------

    /** @var array<string, mixed> */
    private array $validBook = [
        'title'        => 'The Great Gatsby',
        'publisher'    => 'Scribner',
        'author'       => 'F. Scott Fitzgerald',
        'genre'        => 'Fiction',
        'published_at' => '1925-04-10',
        'word_count'   => 47_094,
        'price'        => 12.99,
    ];

    // =========================================================================
    // GET /api/books — List books
    // =========================================================================

    /** Returns 200 with an empty data array when no books exist. */
    public function test_index_returns_empty_list_when_no_books_exist(): void
    {
        $this->getJson('/api/books')
             ->assertOk()
             ->assertJson(['data' => []]);
    }

    /** Returns all books as a JSON collection wrapped in a 'data' key. */
    public function test_index_returns_all_books(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/books');

        $response->assertOk()
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [['id', 'title', 'author', 'publisher', 'genre', 'published_at', 'word_count', 'price']],
                 ]);
    }

    /** Results are sorted alphabetically by title. */
    public function test_index_returns_books_sorted_by_title(): void
    {
        Book::factory()->create(['title' => 'Zebra Book']);
        Book::factory()->create(['title' => 'Alpha Book']);

        $data = $this->getJson('/api/books')->assertOk()->json('data');

        $this->assertEquals('Alpha Book', $data[0]['title']);
        $this->assertEquals('Zebra Book', $data[1]['title']);
    }

    /** Search query matches against the title column. */
    public function test_index_search_filters_by_title(): void
    {
        Book::factory()->create(['title' => 'The Great Gatsby']);
        Book::factory()->create(['title' => 'Animal Farm']);

        $data = $this->getJson('/api/books?search=Gatsby')->assertOk()->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals('The Great Gatsby', $data[0]['title']);
    }

    /** Search query also matches against the author column. */
    public function test_index_search_filters_by_author(): void
    {
        Book::factory()->create(['author' => 'George Orwell']);
        Book::factory()->create(['author' => 'Jane Austen']);

        $data = $this->getJson('/api/books?search=Orwell')->assertOk()->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals('George Orwell', $data[0]['author']);
    }

    /** Genre query parameter filters by exact genre value. */
    public function test_index_genre_filter_returns_matching_books(): void
    {
        Book::factory()->count(2)->create(['genre' => 'Fantasy']);
        Book::factory()->count(3)->create(['genre' => 'Thriller']);

        $data = $this->getJson('/api/books?genre=Fantasy')->assertOk()->json('data');

        $this->assertCount(2, $data);
        $this->assertEquals('Fantasy', $data[0]['genre']);
    }

    /** Genre filter is case-sensitive and returns no results for mismatches. */
    public function test_index_genre_filter_returns_empty_for_nonexistent_genre(): void
    {
        Book::factory()->create(['genre' => 'Fantasy']);

        $this->getJson('/api/books?genre=NonExistent')
             ->assertOk()
             ->assertJsonCount(0, 'data');
    }

    // =========================================================================
    // POST /api/books — Create book
    // =========================================================================

    /** Creating a book with valid data returns 201 with the new book. */
    public function test_store_creates_book_and_returns_201(): void
    {
        $response = $this->postJson('/api/books', $this->validBook);

        $response->assertCreated()
                 ->assertJsonFragment(['title' => 'The Great Gatsby', 'price' => 12.99])
                 ->assertJsonStructure(['data' => ['id', 'title', 'author', 'publisher', 'genre', 'published_at', 'word_count', 'price', 'created_at', 'updated_at']]);

        $this->assertDatabaseHas('books', ['title' => 'The Great Gatsby']);
    }

    /** All fields are required — omitting them must yield a 422. */
    public function test_store_fails_with_422_when_required_fields_are_missing(): void
    {
        $this->postJson('/api/books', [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['title', 'publisher', 'author', 'genre', 'published_at', 'word_count', 'price']);
    }

    /** word_count must be an integer, not a string. */
    public function test_store_fails_when_word_count_is_not_an_integer(): void
    {
        $this->postJson('/api/books', array_merge($this->validBook, ['word_count' => 'many']))
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['word_count']);
    }

    /** word_count must be at least 1. */
    public function test_store_fails_when_word_count_is_zero_or_negative(): void
    {
        $this->postJson('/api/books', array_merge($this->validBook, ['word_count' => 0]))
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['word_count']);
    }

    /** price must be a numeric value. */
    public function test_store_fails_when_price_is_not_numeric(): void
    {
        $this->postJson('/api/books', array_merge($this->validBook, ['price' => 'free']))
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['price']);
    }

    /** price cannot be negative. */
    public function test_store_fails_when_price_is_negative(): void
    {
        $this->postJson('/api/books', array_merge($this->validBook, ['price' => -1.00]))
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['price']);
    }

    /** published_at must be a valid date in Y-m-d format. */
    public function test_store_fails_when_published_at_is_invalid_date(): void
    {
        $this->postJson('/api/books', array_merge($this->validBook, ['published_at' => 'not-a-date']))
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['published_at']);
    }

    // =========================================================================
    // GET /api/books/{id} — Show single book
    // =========================================================================

    /** Returns the correct book resource for a valid ID. */
    public function test_show_returns_the_correct_book(): void
    {
        $book = Book::factory()->create(['title' => 'Specific Book']);

        $this->getJson("/api/books/{$book->id}")
             ->assertOk()
             ->assertJsonFragment(['title' => 'Specific Book'])
             ->assertJsonStructure(['data' => ['id', 'title', 'author', 'publisher', 'genre', 'published_at', 'word_count', 'price']]);
    }

    /** Requesting a non-existent ID must return 404. */
    public function test_show_returns_404_for_nonexistent_book(): void
    {
        $this->getJson('/api/books/99999')
             ->assertNotFound();
    }

    // =========================================================================
    // PATCH /api/books/{id} — Partial update
    // =========================================================================

    /** Updating specific fields leaves other fields unchanged. */
    public function test_update_modifies_only_the_provided_fields(): void
    {
        $book = Book::factory()->create([
            'title'  => 'Old Title',
            'author' => 'Original Author',
            'price'  => 9.99,
        ]);

        $this->patchJson("/api/books/{$book->id}", ['title' => 'New Title', 'price' => 19.99])
             ->assertOk()
             ->assertJsonFragment(['title' => 'New Title', 'price' => 19.99]);

        // Author was not in the patch body — must remain unchanged
        $this->assertDatabaseHas('books', [
            'id'     => $book->id,
            'title'  => 'New Title',
            'author' => 'Original Author',
            'price'  => '19.99',
        ]);
    }

    /** Updating a non-existent book must return 404. */
    public function test_update_returns_404_for_nonexistent_book(): void
    {
        $this->patchJson('/api/books/99999', ['title' => 'New Title'])
             ->assertNotFound();
    }

    /** Sending an invalid type for a field must return 422. */
    public function test_update_fails_with_422_when_field_type_is_invalid(): void
    {
        $book = Book::factory()->create();

        $this->patchJson("/api/books/{$book->id}", ['word_count' => 'not-a-number'])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['word_count']);
    }

    // =========================================================================
    // DELETE /api/books/{id} — Delete book
    // =========================================================================

    /** Successfully deleting a book returns 204 No Content. */
    public function test_destroy_deletes_book_and_returns_204(): void
    {
        $book = Book::factory()->create();

        $this->deleteJson("/api/books/{$book->id}")
             ->assertNoContent();

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /** Deleting a non-existent book must return 404. */
    public function test_destroy_returns_404_for_nonexistent_book(): void
    {
        $this->deleteJson('/api/books/99999')
             ->assertNotFound();
    }
}
