<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Jestrux\Pier\Http\Controllers\CMSController;
use Jestrux\Pier\Http\Controllers\EditorController;
use Jestrux\Pier\Http\Controllers\HelperController;
use Jestrux\Pier\PierMigration;


Route::post('/upload_file', [CMSController::class, 'upload_file'])->name('upload_file');
Route::get('/link_preview', [CMSController::class, 'link_preview']);

Route::prefix('pier-helper')->group(function () {
    Route::get('/', [HelperController::class, 'index']);
    Route::get('/data-grid', [HelperController::class, 'data_grid']);
    Route::get('/data-grid-render/{model_name}', [HelperController::class, 'data_grid_render']);
});

Route::post('/pier-sort-list', function (Request $request) {
    $model = $request->input("model");
    $rowId = $request->input("rowId");
    $field = $request->input("field");
    $position = $request->input("position");
    $current = pierRow($model, $rowId)->{$field};
    $after = $position;

    // If there was no position change, don't shift...
    if ($current === $after) return response()->json(["success" => true]);

    $movedDown = $current < $after;

    $changedItems = DB::table(Str::snake($model))->whereBetween($field, [
        min($current, $after),
        max($current, $after),
    ])->get();

    $changedItems->each(fn ($item) => pierUpdateRow($model, $item->_id, [$field => $movedDown ? $item->{$field} - 1 : $item->{$field} + 1]));

    pierUpdateRow($model, $rowId, [$field => $after]);

    return response()->json(["success" => true]);
});

Route::post('/data-refetch', function (Request $request) {
    $rowId = $request->input("rowId");
    $model = $request->input("model");
    $view = $request->input("view");
    $filters = $request->input("filters");

    $modelDetails = PierMigration::describe($model);
    $modelDetails->fields = collect(json_decode($modelDetails->fields));
    $modelDetails->settings = collect(json_decode($modelDetails->settings));

    $modelData = $rowId == null ? PierMigration::browse($model, $filters) : PierMigration::detail($model, $rowId, $filters);

    $filename = hash('sha1', $view);

    $file_location = storage_path('framework/views/');
    $filepath = storage_path('framework/views/' . $filename . '.blade.php');

    if (!file_exists($filepath))
        file_put_contents($filepath, $view);

    view()->addLocation($file_location);

    return view($filename, [...$request->all(), "modelData" => $modelData, "modelDetails" => $modelDetails]);
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
    Route::get('/dropAll', [EditorController::class, 'drop_all']);
    Route::get('{model_name}/truncate', [EditorController::class, 'truncate']);
    Route::get('{model_name}/drop', [EditorController::class, 'drop']);
    Route::post('{model_name}/migrate', [EditorController::class, 'migrate']);
    Route::post('{model_name}/populate', [EditorController::class, 'populate']);
    // Route::get('{model_name}/describe', [EditorController::class, 'describe']);
    Route::get('{model_name}/fields', [EditorController::class, 'fields']);
    Route::get('{model_name}/settings', [EditorController::class, 'settings']);
    Route::get('{model_name}', [EditorController::class, 'describe']);
    Route::patch('{model_name}', [EditorController::class, 'update']);
    Route::patch('{model_name}/addField', [EditorController::class, 'add_field']);
    Route::patch('{model_name}/settings', [EditorController::class, 'update_settings']);
    Route::get('{model_name}/browse/{row_id?}', [EditorController::class, 'browse']);
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/pier-export-data',  function () {
        $_GET['flat'] = true; // manually disable eager loading

        return PierMigration::orderBy('name')->get()->map(function ($model) {
            $model['data'] = PierMigration::browse($model['name']);
            return $model;
        });
    })->name('pier-export-data');
});
