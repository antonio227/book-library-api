<?php

use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Book Library — API Routes
|--------------------------------------------------------------------------
| All routes here are automatically prefixed with /api by the framework.
|
| Registered resource routes:
|   GET    /api/books              → BookController@index   (list + search)
|   POST   /api/books              → BookController@store   (create)
|   GET    /api/books/{book}       → BookController@show    (single book)
|   PATCH  /api/books/{book}       → BookController@update  (partial update)
|   DELETE /api/books/{book}       → BookController@destroy (delete)
*/

Route::apiResource('books', BookController::class);
