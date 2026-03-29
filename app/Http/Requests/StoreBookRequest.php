<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the request body when creating a new book.
 * All fields are required on creation.
 */
class StoreBookRequest extends FormRequest
{
    /**
     * All authenticated (or public) users may create books.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for the book creation payload.
     */
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'publisher'    => ['required', 'string', 'max:255'],
            'author'       => ['required', 'string', 'max:255'],
            'genre'        => ['required', 'string', 'max:100'],
            // Accept ISO date format YYYY-MM-DD only
            'published_at' => ['required', 'date', 'date_format:Y-m-d'],
            // Word count must be a positive integer
            'word_count'   => ['required', 'integer', 'min:1'],
            // Price must be non-negative with up to 2 decimal places
            'price'        => ['required', 'numeric', 'min:0', 'decimal:0,2'],
        ];
    }
}
