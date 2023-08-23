<?php

use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\APIController;

Route::prefix('api')->group(function () {
    Route::post('{model_name}', [APIController::class, 'createResource']);
    Route::patch('{model_name}/{row_id}', [APIController::class, 'updateResource']);
});
