<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a single book in the library.
 *
 * @property int    $id
 * @property string $title        Book title
 * @property string $publisher    Publisher name
 * @property string $author       Author full name
 * @property string $genre        Book genre (e.g. Fiction, Science Fiction)
 * @property \Illuminate\Support\Carbon $published_at  Publication date
 * @property int    $word_count   Total number of words in the book
 * @property string $price        Book price in US Dollars (decimal)
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Book extends Model
{
    use HasFactory;

    /**
     * Fields that are allowed for mass assignment via create() / update().
     */
    protected $fillable = [
        'title',
        'publisher',
        'author',
        'genre',
        'published_at',
        'word_count',
        'price',
    ];

    /**
     * Cast model attributes to native PHP types for consistent API output.
     */
    protected $casts = [
        'published_at' => 'date:Y-m-d',
        'word_count'   => 'integer',
        'price'        => 'decimal:2',
    ];
}
