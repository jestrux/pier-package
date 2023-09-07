<?php

use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\APIController;
use Jestrux\Pier\Http\Controllers\EditorController;

// ./pier-internals.php
Route::prefix('model')->group(function () {
    Route::get('{model_name}/describe', [EditorController::class, 'describe']);
});

// ./pier-internals.php
Route::prefix('api')->group(function () {
    Route::post('{model_name}', [APIController::class, 'createResource']);
    Route::patch('{model_name}/{row_id}', [APIController::class, 'updateResource']);
});
