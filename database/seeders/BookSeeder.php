<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

/**
 * Seeds the database with a set of well-known real-world books plus
 * a batch of randomly generated books via the BookFactory.
 */
class BookSeeder extends Seeder
{
    /**
     * Real-world book fixtures — give the library realistic seed data.
     */
    public function run(): void
    {
        $books = [
            [
                'title'        => 'The Great Gatsby',
                'publisher'    => 'Scribner',
                'author'       => 'F. Scott Fitzgerald',
                'genre'        => 'Fiction',
                'published_at' => '1925-04-10',
                'word_count'   => 47_094,
                'price'        => 12.99,
            ],
            [
                'title'        => 'To Kill a Mockingbird',
                'publisher'    => 'J. B. Lippincott & Co.',
                'author'       => 'Harper Lee',
                'genre'        => 'Fiction',
                'published_at' => '1960-07-11',
                'word_count'   => 100_388,
                'price'        => 14.99,
            ],
            [
                'title'        => '1984',
                'publisher'    => 'Secker & Warburg',
                'author'       => 'George Orwell',
                'genre'        => 'Science Fiction',
                'published_at' => '1949-06-08',
                'word_count'   => 88_942,
                'price'        => 11.99,
            ],
            [
                'title'        => 'Dune',
                'publisher'    => 'Chilton Books',
                'author'       => 'Frank Herbert',
                'genre'        => 'Science Fiction',
                'published_at' => '1965-08-01',
                'word_count'   => 188_000,
                'price'        => 17.99,
            ],
            [
                'title'        => 'The Lord of the Rings',
                'publisher'    => 'George Allen & Unwin',
                'author'       => 'J.R.R. Tolkien',
                'genre'        => 'Fantasy',
                'published_at' => '1954-07-29',
                'word_count'   => 481_103,
                'price'        => 24.99,
            ],
            [
                'title'        => 'A Brief History of Time',
                'publisher'    => 'Bantam Books',
                'author'       => 'Stephen Hawking',
                'genre'        => 'Non-Fiction',
                'published_at' => '1988-03-01',
                'word_count'   => 55_000,
                'price'        => 15.99,
            ],
            [
                'title'        => "The Hitchhiker's Guide to the Galaxy",
                'publisher'    => 'Pan Books',
                'author'       => 'Douglas Adams',
                'genre'        => 'Science Fiction',
                'published_at' => '1979-10-12',
                'word_count'   => 46_333,
                'price'        => 13.99,
            ],
            [
                'title'        => 'Pride and Prejudice',
                'publisher'    => 'T. Egerton',
                'author'       => 'Jane Austen',
                'genre'        => 'Romance',
                'published_at' => '1813-01-28',
                'word_count'   => 122_189,
                'price'        => 9.99,
            ],
            [
                'title'        => 'The Catcher in the Rye',
                'publisher'    => 'Little, Brown and Company',
                'author'       => 'J.D. Salinger',
                'genre'        => 'Fiction',
                'published_at' => '1951-07-16',
                'word_count'   => 73_404,
                'price'        => 12.49,
            ],
            [
                'title'        => 'Brave New World',
                'publisher'    => 'Chatto & Windus',
                'author'       => 'Aldous Huxley',
                'genre'        => 'Science Fiction',
                'published_at' => '1932-08-18',
                'word_count'   => 64_531,
                'price'        => 11.49,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }

        // Fill the library with additional randomly generated books
        Book::factory()->count(15)->create();
    }
}
