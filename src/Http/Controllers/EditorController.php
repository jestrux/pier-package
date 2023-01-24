<?php

namespace Jestrux\Pier\Http\Controllers;

use Jestrux\Pier\PierMigration;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EditorController extends Controller
{
    public function create(Request $request){
        $model = $request->input('name');
        $fields = collect($request->input('fields'));
        $displayField = $request->input('displayField');
        $data = $request->input('data');
        $res = PierMigration::record($model, $fields, $displayField, $data);
        return response()->json($res);
    }

    public function migrate($model){
        $res = PierMigration::migrate_model($model);
        return response()->json($res);
    }
    
    public function list(){
        $res = PierMigration::all();
        return response()->json($res);
    }
    
    public function populate($model){
        $item_count = isset($_GET['item_count']) ? $_GET['item_count'] : 25;
        $res = PierMigration::populate($model, $item_count);
        return response()->json($res);
    }
    
    public function truncate($model){
        $res = PierMigration::truncate($model);
        return response()->json($res);
    }

    public function drop_all(){
        $models = PierMigration::all()->map(function ($model) {
            return $model['name'];
        });

        Schema::disableForeignKeyConstraints();
        foreach ($models as $model) {
            PierMigration::drop($model);
        }
        Schema::enableForeignKeyConstraints();

        return response()->json(true);
    }

    public function drop($model){
        $res = PierMigration::drop($model);
        return response()->json($res);
    }

    public function describe($model){
        $res = PierMigration::describe($model);
        return response()->json($res);
    }
    
    public function fields($model){
        $res = PierMigration::model_fields($model);
        return response()->json($res);
    }

    public function settings($model){
        $res = PierMigration::settings($model);
        return response()->json($res);
    }
    
    public function update($model, Request $request){
        $res = PierMigration::update_details($model, $request->all());
        return response()->json($res);
    }

    public function add_field($model, Request $request){
        $res = PierMigration::add_field($model, $request->all());
        return response()->json($res);
    }

    public function update_settings($model, Request $request){
        $res = PierMigration::update_settings($model, $request->all());
        return response()->json($res);
    }

    public function browse($model, $rowId = null, Request $request){
        if(!is_null($rowId)){
            $res = PierMigration::model_detail($model, $rowId, $request->input());
            return response()->json($res);
        }
        else{
            $res = PierMigration::browse_model($model, $request->input());
            return response()->json($res);
        }
    }
}
