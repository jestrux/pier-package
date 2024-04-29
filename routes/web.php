<?php

use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\CMSController;

Route::view('/editor', 'pier::editor');
Route::get('/admin/upsertModel/{model}/{rowId?}', function ($model, $rowId = null) {
    return view('pier::upsert-model', [
        "model" => $model,
        "rowId" => $rowId,
        "plain" => $_GET['plain'] ?? null,
        "successMessage" => $_GET['successMessage'] ?? null,
        "onSave" => $_GET['onSave'] ?? null,
    ]);
});
Route::get('/cms-old', [CMSController::class, 'index'])->name('cms');
Route::view('/cms/{modelName?}', 'pier::livewire-cms')->middleware('web');
Route::get('/cms/{modelName}/upsert/{rowId?}', function ($modelName, $rowId = null) {
    return view('pier::livewire-cms', [
        "upsert" => true,
        "modelName" => $modelName,
        "rowId" => $rowId,
        "plain" => $_GET['plain'] ?? null,
        "successMessage" => $_GET['successMessage'] ?? null,
        "onSave" => $_GET['onSave'] ?? null,
    ]);
})->middleware('web');
