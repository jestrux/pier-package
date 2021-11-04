<?php

namespace Jestrux\Pier\Http\Controllers;

use Exception;
use Jestrux\Pier\PierMigration;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// use Dusterio\LinkPreview\Client;

class CMSController extends Controller
{
    public function index(){
        $models = PierMigration::all();
        return view('pier::cms', compact('models'));
    }
    
    public function link_preview(){
        return [];
        // $link = $_GET['link'];
        // $previewClient = new Client($link);
        
        // $previews = $previewClient->getPreviews();

        // // Get a preview from specific parser
        // $preview = $previewClient->getPreview('general');

        // return $preview->toArray();
    }

    public function upload_file(Request $request){
        if($request->hasFile('file')) {
            $file = $request->file('file');
            $destination_path = $request->input('destination_path');

            $file = request()->file('file');

            $uploaded_file = Storage::disk('public_uploads')->put($destination_path, $file);
            if(!$uploaded_file)
                throw new Exception("File not uploaded", 1);
            else
                return response()->json([
                    "file" => $uploaded_file
                ]);

            throw new Exception("File not uploaded", 1);
            return;
        }

        throw new Exception("No file received", 1);
    }
}
