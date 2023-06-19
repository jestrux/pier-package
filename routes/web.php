<?php

use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\CMSController;

Route::view('/editor', 'pier::editor');
Route::get('/cms', [CMSController::class, 'index'])->name('cms');
Route::get('/admin/upsertModel/{model}/{rowId?}', function ($model, $rowId = null) {
    return view('pier::upsert-model', [
        "model" => $model,
        "rowId" => $rowId,
        "plain" => $_GET['plain'] ?? null,
        "successMessage" => $_GET['successMessage'] ?? null,
    ]);
});
