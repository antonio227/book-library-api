<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the `books` table that stores all library book records.
 *
 * Columns align 1:1 with the Book model's $fillable fields.
 */
return new class extends Migration
{
    /**
     * Run the migration — create the books table.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->string('title');           // Book title
            $table->string('publisher');       // Publishing company
            $table->string('author');          // Author full name
            $table->string('genre', 100);      // Genre / category

            // Publication date stored as a DATE (no time component needed)
            $table->date('published_at');

            // Unsigned int — word count can't be negative
            $table->unsignedInteger('word_count');

            // 10 total digits, 2 decimal places — supports prices up to $99,999,999.99
            $table->decimal('price', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migration — drop the books table.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
