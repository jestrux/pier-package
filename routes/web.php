<?php

use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\CMSController;

Route::view('/editor', 'pier::editor');
Route::get('/cms', [CMSController::class, 'index'])->name('cms');
Route::view('/pier-cms/{modelName?}', 'pier::livewire-cms')->middleware('web');
Route::get('/admin/upsertModel/{model}/{rowId?}', function ($model, $rowId = null) {
    return view('pier::upsert-model', [
        "model" => $model,
        "rowId" => $rowId,
        "plain" => $_GET['plain'] ?? null,
        "successMessage" => $_GET['successMessage'] ?? null,
        "onSave" => $_GET['onSave'] ?? null,
    ]);
});
