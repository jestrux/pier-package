<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\APIController;
use Jestrux\Pier\Http\Controllers\CMSController;
use Jestrux\Pier\Http\Controllers\EditorController;
use Jestrux\Pier\Http\Controllers\HelperController;
use Jestrux\Pier\PierMigration;

// Route::get('/', [APIController::class, 'index']);

Route::view('/editor', 'pier::editor');

Route::get('/cms', [CMSController::class, 'index'])->name('cms');
Route::post('/upload_file', [CMSController::class, 'upload_file'])->name('upload_file');
Route::get('/pier-helper', [HelperController::class, 'index']);
Route::get('/link_preview', [CMSController::class, 'link_preview']);

Route::prefix('pier-helper')->group(function () {
    Route::get('/', [HelperController::class, 'index']);
    Route::get('/data-grid', [HelperController::class, 'data_grid']);
    Route::get('/data-grid-render/{model_name}', [HelperController::class, 'data_grid_render']);
});

Route::post('/data-refetch', function (Request $request) {
    $rowId = $request->input("rowId");
    $model = $request->input("model");
    $view = $request->input("view");
    $filters = $request->input("filters");

    if ($rowId == null)
        $data = PierMigration::browse($model, $filters);
    else
        $data = PierMigration::detail($model, $rowId, $filters);

    $filename = hash('sha1', $view);

    $file_location = storage_path('framework/views/');
    $filepath = storage_path('framework/views/' . $filename . '.blade.php');

    if (!file_exists($filepath))
        file_put_contents($filepath, $view);

    view()->addLocation($file_location);

    return view($filename, ["data" => $data]);
});

Route::post('/data-grid-refetch', function (Request $request) {
    $props = $request->all();
    $model = $request->input("model");
    $filters = $request->input("filters");
    $template = $request->input("template");

    $data = PierMigration::browse($model, $filters);

    $props['data'] = $data;

    return view("pier::data-grid-list.$template", $props);
});

Route::prefix('model')->group(function () {
    Route::post('/', [EditorController::class, 'create']);
    Route::get('/', [EditorController::class, 'list']);
    Route::get('{model_name}/truncate', [EditorController::class, 'truncate']);
    Route::get('{model_name}/drop', [EditorController::class, 'drop']);
    Route::post('{model_name}/migrate', [EditorController::class, 'migrate']);
    Route::post('{model_name}/populate', [EditorController::class, 'populate']);
    Route::get('{model_name}/describe', [EditorController::class, 'describe']);
    Route::get('{model_name}/fields', [EditorController::class, 'fields']);
    Route::get('{model_name}/settings', [EditorController::class, 'settings']);
    Route::get('{model_name}', [EditorController::class, 'describe']);
    Route::patch('{model_name}', [EditorController::class, 'update']);
    Route::patch('{model_name}/addField', [EditorController::class, 'add_field']);
    Route::patch('{model_name}/settings', [EditorController::class, 'update_settings']);
    Route::get('{model_name}/browse/{row_id?}', [EditorController::class, 'browse']);
});

Route::prefix('api')->group(function () {
    Route::get('{model_name}/search', [APIController::class, 'searchResource']);
    Route::get('{model_name}/{row_id?}', [APIController::class, 'resource']);
    Route::post('{model_name}', [APIController::class, 'createResource']);
    Route::post('{model_name}/upload_file', [APIController::class, 'upload_file']);
    Route::patch('{model_name}/{row_id}', [APIController::class, 'updateResource']);
    Route::delete('{model_name}/{row_id}', [APIController::class, 'deleteResource']);
});

Route::get('admin/upsertModel/{model}/{id?}', function ($model, $id = null) {
    $data = [
        "model" => $model,
        "id" => $id,
    ];

    if (isset($_GET['redirectTo']))
        $data["redirectTo"] = $_GET['redirectTo'];

    return view('pier::upsert-model', $data);
});
