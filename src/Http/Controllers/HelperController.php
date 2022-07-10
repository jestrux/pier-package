<?php

namespace Jestrux\Pier\Http\Controllers;

use Exception;
use Jestrux\Pier\PierMigration;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// use Dusterio\LinkPreview\Client;

class HelperController extends Controller
{
    public function index()
    {
        $models = PierMigration::all();
        return view('pier::helper.index', compact('models'));
    }

    public function data_grid()
    {
        $models = PierMigration::all();
        return view('pier::helper.data-grid', compact('models'));
    }

    public function data_grid_render($model, Request $request)
    {
        $res = PierMigration::browse($model, $request->input());
        $params = $request->input();

        return view('pier::helper.new-data-grid.index', [
            "data" => $res,
            "grouped" => isset($params["groupBy"]),
            'imageField' => isset($params["imageField"]) ? $params["imageField"] : "image",
            'metaField' => isset($params["metaField"]) ? $params["metaField"] : "meta",
            'titleField' => isset($params["titleField"]) ? $params["titleField"] : "title",
            'descriptionField' => isset($params["descriptionField"]) ? $params["descriptionField"] : "description",
        ]);
    }
}
