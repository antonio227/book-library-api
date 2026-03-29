<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms a Book model instance into a consistent JSON API response.
 * Ensures all dates use ISO 8601 and price is always a float.
 */
class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'publisher'    => $this->publisher,
            'author'       => $this->author,
            'genre'        => $this->genre,
            'published_at' => $this->published_at?->format('Y-m-d'),
            'word_count'   => $this->word_count,
            'price'        => (float) $this->price,
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
