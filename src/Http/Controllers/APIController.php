<?php

namespace Jestrux\Pier\Http\Controllers;

use Jestrux\Pier\PierMigration;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class APIController extends Controller
{
    public function index()
    {
        return response()->json([
            "package" => "pier"
        ]);
    }
    
    public function resource($model, Request $request, $rowId = null){
        if(!is_null($rowId)){
            $res = PierMigration::detail($model, $rowId, $request->input());
            return response()->json($res);
        }
        else{
            $res = PierMigration::browse($model, $request->input());
            return response()->json($res);
        }   
    }
    
    public function searchResource($model){
        $res = PierMigration::search($model, $_GET['q']);
        return response()->json($res);
    }
    
    public function createResource($model, Request $request){
        $res = PierMigration::insertRow($model, $request->all());
        return response()->json($res);
    }

    public function updateResource($model, $rowId, Request $request){
        $res = PierMigration::updateRow($model, $rowId, $request->all());
        return response()->json($res);
    }
    
    public function deleteResource($model, $rowId){
        $res = PierMigration::deleteEntry($model, $rowId);
        return response()->json($res);
    }

    public function upload_file($model, Request $request){
        $folder_name = Str::snake($model);
        $file = $request->file('photo');
        $imageNameExt = $file->getClientOriginalName();
        $imageName = pathinfo($imageNameExt,PATHINFO_FILENAME);
        $imageNameStore = $imageName . '_' . time() . '.' . pathinfo($imageNameExt, PATHINFO_EXTENSION);
        $path = $file->storeAs($folder_name ?? "", $imageNameStore);

        return response()->json([
            "success" => true,
            "path" => Storage::url($path),
        ]);
    }
}
