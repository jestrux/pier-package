<?php

namespace Jestrux\Pier\View\Components\Livewire;

use Livewire\Component;
use Jestrux\Pier\PierMigration;

class CMS extends Component
{
    public $modelName;
    public $models;
    public $appName;
    public $appColor;
    public $appLogo;
    public $currentModel;
    public $q = "";
    public $filters = [];

    public function mount()
    {
        $this->models = PierMigration::orderBy('name')->get()->map(function ($model) {
            $model->fields = collect(json_decode($model->fields));
            return $model;
        });
        $this->currentModel = $this->modelName
            ? $this->models->first(fn ($model) => $model->name == $this->modelName)
            : $this->models[0];

        $this->appName = env('APP_NAME') ?? "";
        $this->appLogo = env('APP_LOGO') ?? null;
        $this->appColor = env('APP_COLOR') ?? '#2c5282';

        // "unsplashClientId": "{{env('PIER_UNSPLASH_CLIENT_ID')}}",
        // "fileUploadUrl": "{{$uploadUrl}}",
        // "s3": {
        //     bucketName: "{{env('PIER_S3_BUCKET')}}",
        //     region: "{{env('PIER_S3_REGION')}}",
        //     accessKeyId: "{{env('PIER_S3_ACCESS_KEY_ID')}}",
        //     secretAccessKey: "{{env('PIER_S3_SECRET_ACCESS_KEY')}}",
        // },
        // "authUser": "{{Auth::check() ? Auth::id() : null}}",
    }

    public function render()
    {
        return view('pier::components.livewire.cms.index');
    }
}
