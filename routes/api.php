<?php

use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\APIController;

Route::prefix('api')->group(function () {
    Route::get('{model_name}/search', [APIController::class, 'searchResource']);
    Route::get('{model_name}/{row_id?}', [APIController::class, 'resource']);
    // Route::post('{model_name}', [APIController::class, 'createResource']);
    Route::post('{model_name}/upload_file', [APIController::class, 'upload_file']);
    // Route::patch('{model_name}/{row_id}', [APIController::class, 'updateResource']);
    Route::delete('{model_name}/{row_id}', [APIController::class, 'deleteResource']);
});
