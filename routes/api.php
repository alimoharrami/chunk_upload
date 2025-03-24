<?php

use App\Http\Controllers\ChunkFileController;
use Illuminate\Support\Facades\Route;

Route::post('/chunk-upload', ChunkFileController::class);
Route::patch('/chunk-upload', ChunkFileController::class);
