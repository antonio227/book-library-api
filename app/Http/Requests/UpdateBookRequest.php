<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the request body for a PATCH (partial update) on an existing book.
 * All fields are optional — only the provided fields are updated.
 */
class UpdateBookRequest extends FormRequest
{
    /**
     * All authenticated (or public) users may update books.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules — 'sometimes' means the rule only applies if the field is present.
     */
    public function rules(): array
    {
        return [
            'title'        => ['sometimes', 'string', 'max:255'],
            'publisher'    => ['sometimes', 'string', 'max:255'],
            'author'       => ['sometimes', 'string', 'max:255'],
            'genre'        => ['sometimes', 'string', 'max:100'],
            'published_at' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'word_count'   => ['sometimes', 'integer', 'min:1'],
            'price'        => ['sometimes', 'numeric', 'min:0', 'decimal:0,2'],
        ];
    }
}
