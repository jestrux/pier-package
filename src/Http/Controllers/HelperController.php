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
}
