<?php

use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\CMSController;

Route::view('/editor', 'pier::editor');
Route::get('/cms', [CMSController::class, 'index'])->name('cms');
Route::view('/admin/upsertModel/{model}/{rowId?}', 'pier::upsert-model');