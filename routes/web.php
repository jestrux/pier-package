<?php

use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\CMSController;

Route::view('/editor', 'pier::editor');
Route::get('/cms', [CMSController::class, 'index'])->name('cms');