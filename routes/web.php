<?php

use Illuminate\Support\Facades\Route;

// Redirect the root URL to the interactive Swagger UI documentation page
Route::get('/', fn () => redirect('/api/documentation'));
