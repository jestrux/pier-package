<?php

namespace Jestrux\Pier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PierMigration extends Model{
    protected $table = "pier";
    protected $fillable = [
        '_id', 'name', 'fields', 'display_field', 'settings'
    ];

    protected $primaryKey = '_id';
    public $incrementing = false;

    static private function pascal_to_sentence($string){
        $words_splited = preg_split('/(?=[A-Z])/', $string);
        $words_capitalized = array_map("ucfirst", $words_splited);
        return trim(implode(" ", $words_capitalized));
    }

    static function describe($model){
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));
        return PierMigration::where("name", $model_name)->first();
    }

    static function truncate($model){
        $table_name = Str::snake($model);
        return DB::table($table_name)->delete();
    }

    static function drop($model){
        $table_name = Str::snake($model);
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));
        DB::table($table_name)->delete();

        $multi_reference_fields = self::model_fields($model)->filter(function($field){
            return $field->type == 'multi-reference';
        });

        $multi_reference_fields->each(function($field) use($table_name){
            $reference_table_name = Str::snake($field->label);
            $reference_table = $table_name . '_' . $reference_table_name;
            DB::table($reference_table)->delete();
            Schema::dropIfExists($reference_table);
        });

        Schema::dropIfExists($table_name);

        return PierMigration::where("name", $model_name)->delete();
    }
    
    static function model_fields($model){
        $db_model = self::describe($model);
        return collect(json_decode($db_model->fields));
    }
    
    static function settings($model){
        $db_model = self::describe($model);
        return collect(json_decode($db_model->settings));
    }
    
    static function update_details($model, $updated_details){
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));

        $updated_fields = collect($updated_details)->except(['settings', 'fields']);

        return PierMigration::where("name", $model_name)->update($updated_fields->toArray());
    }

    static function add_field($model, $payload){
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));
        $table_name = Str::snake($model);
        $field = $payload['field'];

        Schema::table($table_name, function (Blueprint $table) use($payload, $field){
            $meta = isset($field['meta']) ? $field['meta'] : [];

            if($payload['placement'] == 'start')
                $meta["after"] = "_id";
            else if($payload['placement'] == 'after')
                $meta["after"] = $payload['after'];

            $field['meta'] = $meta;

            if($field['type'] == 'reference'){
                $references = self::browse($meta['model']);
                if(!$references->isEmpty()){
                    $reference = $references->random(1)->first();
                    $field['default'] = "default" .$reference->_id;
                    $field['required'] = true;
                }
                else
                    $field['required'] = false;
            }

            self::field_type_map($table, $field);
        });

        if($field['type'] == 'multi-reference'){
            $reference_table_name = Str::snake($field['label']);

            Schema::create($table_name . '_' . $reference_table_name, function (Blueprint $table) use($field, $table_name, $reference_table_name){
                $table->uuid("_id");
                $table->uuid($table_name.'_id');
                $table->uuid($reference_table_name.'_id');

                $table->foreign($table_name.'_id')
                    ->references('_id')
                    ->on($table_name)
                    ->onDelete('cascade');

                $table->foreign($reference_table_name.'_id')
                    ->references('_id')
                    ->on(Str::snake($field['meta']['model']))
                    ->onDelete('cascade');
            });
        };
        
        $model_fields = self::model_fields($model);

        if($payload['placement'] == 'start')
            $model_fields->splice(0, 0, [$field]);
        else if($payload['placement'] == 'after'){
            $index = array_search($payload['after'], $model_fields->pluck("label")->toArray());
            $model_fields->splice($index + 1, 0, [$field]);
        }
        else
            $model_fields->push($field);

        $pierModel = PierMigration::where("name", $model_name)->first();
        $pierModel->update([
            "fields" => $model_fields->toJson(),
        ]);

        // re-retrieve the instance to get all of the fields in the table.
        return $pierModel->fresh();
    }

    static function update_settings($model, $new_settings){
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));
        $query = PierMigration::where("name", $model_name);
        
        foreach (collect($new_settings) as $key => $value) {
            $query //DB::table('users')
              ->update(['settings->'.$key => $value]);
        }

        return self::settings($model);
    }
    
    static function search($model, $search_query){
        $table_name = Str::snake($model);
        $db_model = self::describe($model);
        $display_field = $db_model->display_field;

        $results = DB::table($table_name)
            ->where($display_field,'like',"%{$search_query}%")
            ->select(["*", $display_field . " as label"])
            ->get();

        return $results;
    }
    
    static function browse_model($model, $params = null){
        $db_model = self::describe($model);
        $data = self::browse($model, $params);

        return [
            "model" => $db_model,
            "data" => $data,
        ];
    }

    static function do_pluck($results, $params, $paginated, $items_per_page = null){
        $pluck_props = [];
        if(isset($params['pluck'])){
            $pluck = $params['pluck'];
            if(strlen($pluck) > 0){
                $pluck_props = explode(',', $pluck);
                if(count($pluck_props) > 1){
                    if(!$paginated)
                        $results = $results->select(...$pluck_props)->get();
                    else{
                        $results = $results->select(...$pluck_props);
                        $results = $results->paginate($items_per_page);
                        // $results = $results->paginate($items_per_page);
                        $results = [
                            "per_page" => $results->perPage(),
                            "current_page" => $results->currentPage(),
                            "last_page" => $results->lastPage(),
                            "total_rows" => $results->total(),
                            "has_more_pages" => $results->hasMorePages(),
                            "data" => $results->items()
                        ];
                    }
                }
                else{
                    if(!$paginated)
                        $results = $results->get()->pluck($pluck);
                    else{
                        $results = $results->paginate($items_per_page);
                        $results = [
                            "per_page" => $results->perPage(),
                            "current_page" => $results->currentPage(),
                            "last_page" => $results->lastPage(),
                            "total_rows" => $results->total(),
                            "has_more_pages" => $results->hasMorePages(),
                            "data" => collect($results->items())->pluck($pluck),
                        ];
                    }
                }
            }
        }
        else {
            if(!$paginated)
                $results = $results->get();
            else{
                $results = $results->paginate($items_per_page);
                $results = [
                    "per_page" => $results->perPage(),
                    "current_page" => $results->currentPage(),
                    "last_page" => $results->lastPage(),
                    "total_rows" => $results->total(),
                    "has_more_pages" => $results->hasMorePages(),
                    "data" => $results->items(),
                ];
            }
        }

        return [$results, $pluck_props];
    }

    static function browse($model, $params = null){
        $db_model = self::describe($model);
        $display_field = $db_model->display_field;
        $model_fields = collect(json_decode($db_model->fields));

        $table_name = Str::snake($model);
        $results = DB::table($table_name);
        $paginated = false;
        $param_keys = [];

        if(!is_null($params) && count($params) > 0){
            $param_keys = array_keys($params);

            $where_params = collect($param_keys)->filter(function($key){
                return strpos($key, "where") === 0 
                    || strpos($key, "orWhere") === 0
                    || strpos($key, "andWhere") === 0;
            });

            if($where_params->count() > 0){
                foreach ($where_params as $index => $param) {
                    $table_column = strtolower(str_replace(" ", "_", self::pascal_to_sentence(str_replace(["where", "andWhere", "orWhere", "isGreaterThan", "isGreaterThanOrEqual", "isLessThan", "isLessThanOrEqual"], "", $param))));
                    $copmarators = ["isGreaterThanOrEqual", "isLessThanOrEqual", "isLessThan", "isGreaterThan"];
                    $table_column = strtolower(str_ireplace(" ", "_", self::pascal_to_sentence(str_ireplace(array_merge(["andWhere", "orWhere", "where"], $copmarators), "", $param))));
                    $symbol = collect($copmarators)->first(function ($value, $key) use ($param) {
                        return strpos(strtolower($param), strtolower($value));
                    });

                    if(is_null($symbol))
                        $symbol = "Equals";
                    
                    $symbolMap = [
                        "isGreaterThan" => ">",
                        "isGreaterThanOrEqual" => ">=", 
                        "isLessThan" => "<", 
                        "isLessThanOrEqual" => "<=",
                        "Equals" => "=",
                    ];

                    $copmarator = $symbolMap[$symbol];

                    if($index == 0 || strpos($param, "andWhere") === 0)
                        $results = $results->where($table_column, $copmarator, $params[$param]);
                    else
                        $results = $results->orWhere($table_column, $copmarator, $params[$param]);
                }
            }

            $can_order_by = in_array("orderBy", $param_keys);
            $ordered = false;
            if($can_order_by){
                $order_by_param = $params['orderBy'];
                
                if(strlen($order_by_param) > 0){
                    $order_by_props = explode(',',$order_by_param);
                    $order_by = $order_by_props[0];
                    $order_direction = "desc";
                    
                    if(strlen($order_by) > 0){
                        $model_field_names = $model_fields->map(function($model){
                            return $model->label;
                        });

                        if($model_field_names->contains($order_by) || collect(["created_at", "updated_at", "_id"])->contains($order_by)){
                            if(count($order_by_props) > 1 && strlen($order_by_props[1]) > 0)
                                $order_direction = $order_by_props[1];
                            
                            $results = $results->orderBy($order_by, $order_direction);

                            $ordered = true;
                        }
                    }
                }
            }

            // if(!$ordered)
            //     $results = $results->orderByDesc('updated_at');
            
            $search = in_array("q", $param_keys);
            if($search){
                $search_query = $params['q'];

                if(strlen($search_query) > 0)
                    $results = $results->where($display_field,'like',"%{$search_query}%");
            }

            $paginate = in_array("page", $param_keys);
            if($paginate){
                $paginated = true;
                $items_per_page = in_array("perPage", $param_keys) ? $params['perPage'] : 25;
                [$results,$pluck_props] = self::do_pluck($results, $params, true, $items_per_page);
            }
        }

        if(!$paginated){
            [$results, $pluck_props] = self::do_pluck($results, $params, false);
            if(isset($param_keys)){
                $can_group_by = in_array("groupBy", $param_keys);
                $grouped = false;
                if($can_group_by){
                    $group_by = $params['groupBy'];
                    if(strlen($group_by) > 0){
                        $results = collect($results)->groupBy($group_by);
                    }
                }
            }
        }

        $reference_fields = $model_fields->filter(function($field){
            return $field->type == 'reference';
        });

        $has_been_paginated = is_array($results) ? array_key_exists("data", $results) : false;
        $result_data = $has_been_paginated ? collect($results["data"]) : $results;

        if(count($result_data) > 0){
            if(in_array("randomize", $param_keys)){
                $result_data = $result_data->shuffle();
            }

            $status_fields = $model_fields->filter(function($field){
                return $field->type == 'status';
            });
    
            if($status_fields->count() > 0){
                foreach ($status_fields as $field) {
                    $statuses = $field->meta->availableStatuses;
                    
                    foreach ($result_data as $result) {
                        if(gettype($result) != "object" || !isset($result->{$field->label})) continue;

                        $resultValue = $result->{$field->label};
                        $result->{$field->label . 'Meta'} = collect($statuses)->where("name", "=", $resultValue)->first();
                    }
                }
            }

            if($reference_fields->count() > 0){
                // return in_array('typesy', $result_data->keys()->toArray());

                foreach ($reference_fields as $field) {
                    $referenced_table = Str::snake($field->meta->model);
    
                    foreach ($result_data as $result) {
                        try {
                            $result->{$field->label} = DB::table($referenced_table)->where(
                                "_id", '=', $result->{$field->label}
                            )->first();
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                }
            }
    
            $multi_reference_fields = $model_fields->filter(function($field){
                return $field->type == 'multi-reference';
            });
            
            if($multi_reference_fields->count() > 0){
                foreach ($multi_reference_fields as $field) {
                    $referenced_table = Str::snake($field->label);
                    
                    try {
                        foreach ($result_data as $result) {
                            $reference_ids = DB::table($table_name . '_' . $referenced_table)->where(
                                $table_name."_id", '=', $result->_id
                            )->pluck($referenced_table.'_id');
    
                            if($reference_ids->count() > 0){
                                $result->{$field->label} = DB::table(Str::snake($field->meta->model))->whereIn(
                                    '_id',
                                    $reference_ids
                                )->get();
                            }
                            else
                                $result->{$field->label} = [];
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }
            
            if(isset($params['unique'])){
                $result_data = $params['unique'] == "true" 
                    ? $result_data->unique()
                    : $result_data->unique($params['unique']);
                    
                $result_data = $result_data->values();
            }
        }

        if(isset($params['limit']) && !$paginated){
            $limit = $params['limit'];
            if($limit == 1){
                if($result_data->count() == 0)
                    return null;
                    
                return $result_data->first();
            }
            $result_data = $result_data->take($limit);
        }

        if($has_been_paginated) $results['data'] = $result_data;
        else $results = $result_data;
        
        return $results;
    }

    static function detail($model, $row_id, $params = null){
        $db_model = self::describe($model);
        $model_fields = collect(json_decode($db_model->fields));

        $table_name = Str::snake($model);
        $result = DB::table($table_name)->where("_id", '=', $row_id)->first();

        if(!$result)
            return null;

        $reference_fields = $model_fields->filter(function($field){
            return $field->type == 'reference';
        });

        if($reference_fields->count() > 0){
            foreach ($reference_fields as $field) {
                $referenced_table = Str::snake($field->meta->model);

                $result->{$field->label} = DB::table($referenced_table)->where(
                    "_id", '=', $result->{$field->label}
                )->first();
            }
        }

        $multi_reference_fields = $model_fields->filter(function($field){
            return $field->type == 'multi-reference';
        });
        
        if($multi_reference_fields->count() > 0){
            foreach ($multi_reference_fields as $field) {
                $referenced_table = Str::snake($field->label);

                $reference_ids = DB::table($table_name . '_' . $referenced_table)->where(
                    $table_name."_id", '=', $result->_id
                )->pluck($referenced_table.'_id');

                if($reference_ids->count() > 0){
                    $result->{$field->label} = DB::table($referenced_table)->whereIn(
                        '_id',
                        $reference_ids
                    )->get();
                }
                else
                    $result->{$field->label} = [];
            }
        }
        
        return $result;
    }
    
    static function deleteEntry($model, $entryId){
        $table_name = Str::snake($model);
        return DB::table($table_name)->where('_id', '=', $entryId)->delete();
    }

    static function insertRow($model, $data){
        $fields = self::model_fields($model);
        $table_name = Str::snake($model);
        $_id = Uuid::v4();
        $entry = [];

        $regular_fields = $fields->filter(function($field){
            return $field->type != 'multi-reference';
        });
        
        $entry['_id'] = $_id;
        $entry['created_at'] = now();
        $entry['updated_at'] = now();
        
        foreach ($regular_fields as $field) {
            $value = isset($data[$field->label]) ? $data[$field->label] : null;
            if(is_null($value) && isset($field->default)){
                if($field->type == "date")
                    $value = \Carbon\Carbon::now()->toDateTimeString();
                else
                    $value = $field->default;
            }

            if($field->type === 'password')
                $entry[$field->label] = Hash::make($value);
            else if($field->type === 'location' && is_array($value))
                $entry[$field->label] = json_encode($value);
            else
                $entry[$field->label] = $value;
        }
        
        DB::table($table_name)->insert($entry);

        $multi_reference_fields = $fields->filter(function($field){
            return $field->type == 'multi-reference';
        });

        if($multi_reference_fields->count() > 0){
            foreach ($multi_reference_fields as $field) {
                if(isset($data[$field->label])){
                    $values = $data[$field->label];
                    $entry[$field->label] = self::populateMultiReferenceRow($table_name, $_id, $field, $values);
                }
            }
        }

        $reference_fields = $fields->filter(function($field){
            return $field->type == 'reference';
        });

        if($reference_fields->count() > 0){
            foreach ($reference_fields as $field) {
                if(isset($entry[$field->label]))
                    $entry[$field->label] = DB::table(Str::snake($field->meta->model))->where('_id', $entry[$field->label])->first();
                else
                    $entry[$field->label] = null;
            }
        }

        return $entry;
    }

    static function updateRow($model, $row_id, $data){
        $fields = self::model_fields($model);
        $table_name = Str::snake($model);
        $entry = [
            'updated_at' => now()
        ];

        $fields = $fields->filter(function($field) use ($data){
            return collect($data)->has($field->label);
        })->values();

        if(count($fields) < 1)
            return DB::table($table_name)->where('_id', $row_id)->get();

        $regular_fields = $fields->filter(function($field){
            return $field->type != 'multi-reference';
        });
        
        foreach ($regular_fields as $field) {
            $value = isset($data[$field->label]) ? $data[$field->label] : null;

            if($field->type === 'password')
                $entry[$field->label] = Hash::make($value);
            else if($field->type === 'location' && is_array($value))
                $entry[$field->label] = json_encode($value);
            else
                $entry[$field->label] = $value;
        }
        
        DB::table($table_name)
            ->where('_id', $row_id)
            ->update($entry);

        $multi_reference_fields = $fields->filter(function($field){
            return $field->type == 'multi-reference';
        });

        if($multi_reference_fields->count() > 0){
            foreach ($multi_reference_fields as $field) {
                self::deleteMultiReferences($table_name, $row_id, $field);
                
                if(isset($data[$field->label]) && $data[$field->label] != null){
                    $values = $data[$field->label];
                    self::populateMultiReferenceRow($table_name, $row_id, $field, $values);
                }
            }
        }

        return DB::table($table_name)->where('_id', $row_id)->first();
    }
    
    static function populate($model, $item_count = 25){
        $table_name = Str::snake($model);
        $fields = self::model_fields($model);

        $types = $fields->map(function($field){
            return $field->type;
        })->toArray();

        $image_fields = $fields->filter(function($field){
            return $field->type === "image";
        });

        $images = null;
        $faces = null;
        $videos = null;

        if(count($image_fields) > 0){
            // try {
                $face_images = $image_fields->filter(function($field){
                    return $field->meta->face;
                })->toArray();
                
                $non_face_images = $image_fields->filter(function($field){
                    return !$field->meta->face;
                })->toArray();

                if(count($non_face_images) > 0){
                    $imageResponse = Http::withoutVerifying()->get('https://api.unsplash.com/photos?per_page=30&order_by=latest&client_id=17ef130962858e4304efe9512cf023387ee5d36f0585e4346f0f70b2f9729964');
                    $images = collect($imageResponse->json())->map(function($img){
                        return $img['urls']['regular'];
                    })->toArray();
                }
                
                if(count($face_images) > 0){
                    $imageResponse = Http::withoutVerifying()->get('https://api.unsplash.com/collections/3678902/photos?per_page=30&order_by=latest&client_id=17ef130962858e4304efe9512cf023387ee5d36f0585e4346f0f70b2f9729964');
                    $images = collect($imageResponse->json())->map(function($img){
                        return $img['urls']['thumb'];
                    })->shuffle()->toArray();
                }
            // } catch (\Throwable $th) {}
        }
        
        if(in_array("video", $types)){
            try {
                $response = Http::withoutVerifying()->get("https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=30&q=beautiful&type=video&videoEmbeddable=true&fields=items(id)&key=AIzaSyA_Rg25Nc3IbNh3OBP6KkeHXinC9T3ajyw");
                $videos = collect($response->offsetGet('items'))->map(function($video){
                    return "https://www.youtube.com/watch?v=".$video['id']['videoId'];
                })->toArray();
            } catch (\Throwable $th) {}
        }

        $entries = [];

        $regular_fields = $fields->filter(function($field){
            return $field->type != 'multi-reference';
        })->all();

        for ($i=0; $i < $item_count; $i++) {     
            $entry = self::populateRow($i, $regular_fields, $images, $faces, $videos);

            DB::table($table_name)->insert($entry);
            
            $entries[] = $entry;
        }

        $multi_reference_fields = $fields->filter(function($field){
            return $field->type == 'multi-reference';
        });

        if($multi_reference_fields->count() > 0){
            foreach ($entries as $entry) {
                foreach ($multi_reference_fields as $field) {
                    $entry[$field->label] = self::populateMultiReferenceRow($table_name, $entry["_id"], $field);
                }
            }
        }
        
        return $entries;
    }

    static function deleteMultiReferences($table_name, $row_id, $field){
        $reference_table = Str::snake($field->label);
        DB::table($table_name . '_' . $reference_table)
                ->where($table_name.'_id', "=", $row_id)
                ->delete();
    }

    static function populateMultiReferenceRow($table_name, $row_id, $field, $references = null){
        if(is_null($references)){
            $faker = Faker::create();
            $references = self::browse($field->meta->model);
            $item_count = $faker->numberBetween(1, min(4, $references->count()));
    
            $references = $references->random($item_count)->pluck('_id');
        }

        $reference_table = Str::snake($field->label);

        foreach ($references as $reference_id) {
            $entry = [
                "_id" => Uuid::v4(),
                $table_name.'_id' => $row_id,
                $reference_table.'_id' => $reference_id
            ];
    
            DB::table($table_name . '_' . $reference_table)->insert($entry);
        }

        return DB::table(Str::snake($field->meta->model))->whereIn(
            '_id',
            $references
        )->get();
    }

    static function populateRow($index, $fields, $images, $faces, $videos){
        $pierModel = [];
        $_id = Uuid::v4();
        $pierModel['_id'] = $_id;
        $pierModel['created_at'] = now();
        $pierModel['updated_at'] = now();

        foreach($fields as $field) {
            $label = $field->label;
            $type = $field->type;
            $meta = isset($field->meta) ? $field->meta : null;

            if($type == "image"){
                try {
                    $isFace = $field->meta->face;

                    if($isFace && is_array($faces) && count($faces) > 0)
                        $pierModel[$label] = $faces[$index % 30];
                    else if(is_array($images) && count($images) > 0)
                        $pierModel[$label] = $images[$index % 30];
                    else
                        $pierModel[$label] = self::field_generator($field);
                } catch (\Throwable $th) {
                    if(is_array($images) && count($images) > 0)
                        $pierModel[$label] = $images[array_rand($images, 1)];
                    else
                        $pierModel[$label] = self::field_generator($field);
                }
            }
            else if($type == "video" && is_array($videos) && count($videos) > 0)
                $pierModel[$label] = $videos[array_rand($videos, 1)];
            else
                $pierModel[$label] = self::field_generator($field, $_id);
                
        };

        return $pierModel;
    }

    static function record($model, $fields, $display_field){
        $table_name = Str::snake($model);
        $model_name = self::pascal_to_sentence($model);

        $modelEntry = [
            "_id" => Uuid::v4(),
            "name" => $model_name, 
            "display_field" => $display_field, 
            "fields" => $fields->toJson(),
            "settings" => '{"listPageType": "table"}'
        ];

        $pierModel = PierMigration::create($modelEntry);

        $multi_reference_fields = $fields->filter(function($field){
            return $field['type'] == 'multi-reference';
        });

        $fields = $fields->filter(function($field){
            return $field['type'] != 'multi-reference';
        });
        
        Schema::create($table_name, function (Blueprint $table) use($fields){
            $table->uuid("_id");
            
            foreach($fields as $field) {
                self::field_type_map($table, $field);
            };
            
            $table->timestamps();
            $table->primary("_id");
        });

        $multi_reference_fields->each(function($field) use($table_name){
            $reference_table_name = Str::snake($field['label']);

            Schema::create($table_name . '_' . $reference_table_name, function (Blueprint $table) use($field, $table_name, $reference_table_name){
                $table->uuid("_id");
                $table->uuid($table_name.'_id');
                $table->uuid($reference_table_name.'_id');

                $table->foreign($table_name.'_id')
                    ->references('_id')
                    ->on($table_name)
                    ->onDelete('cascade');

                $table->foreign($reference_table_name.'_id')
                    ->references('_id')
                    ->on(Str::snake($field['meta']['model']))
                    ->onDelete('cascade');
            });
        });
        
        // re-retrieve the instance to get all of the fields in the table.
        return $pierModel->fresh();
    }

    static function migrate_model($model){
        $table_name = Str::snake($model);
        $fields = self::model_fields($model);

        $multi_reference_fields = $fields->filter(function($field){
            return $field->type == 'multi-reference';
        });

        $fields = $fields->filter(function($field){
            return $field->type != 'multi-reference';
        });
        
        Schema::create($table_name, function (Blueprint $table) use($fields){
            $table->uuid("_id");
            
            foreach($fields as $field) {
                $field = (array) $field;
                $label = $field['label'];
                $type = $field['type'];
                $nullable = !$field['required'];
                $meta = isset($field['meta']) ? (array)$field['meta'] : null;

                self::field_type_map($table, $label, $type, $nullable, $meta);
            };
            
            $table->timestamps();
            $table->primary("_id");
        });

        $multi_reference_fields->each(function($field) use($table_name){
            $field = (array) $field;
            $reference_table_name = Str::snake($field['label']);

            Schema::create($table_name . '_' . $reference_table_name, function (Blueprint $table) use($field, $table_name, $reference_table_name){
                $table->uuid("_id");
                $table->uuid($table_name.'_id');
                $table->uuid($reference_table_name.'_id');

                $table->foreign($table_name.'_id')
                    ->references('_id')
                    ->on($table_name)
                    ->onDelete('cascade');

                $table->foreign($reference_table_name.'_id')
                    ->references('_id')
                    ->on(Str::snake($field['meta']['model']))
                    ->onDelete('cascade');
            });
        });
        
        return self::describe($model);
    }

    static private function field_generator($field, $_id = null){
        $faker = Faker::create();
        $label = $field->label;
        $type = $field->type;
        $meta = isset($field->meta) ? $field->meta : null;
        $sample_text = "Uniforms, and some understanding similar attempt. I writer improve those bored presentations. Of sofa times years, an from but the descriptions, we anyone whom his motors him concepts are suspicion be for her as and to have venerable, and deceleration is policeman, writing worthy had viable their there's in of location it and of to looked him, uninspired, often he to towards candidates, of an little have good they the form their choose the self-interest. Is picture support felt every in there eminent a now couldn't the hopes must or not the no presented. All my harmonic agency concept";

        switch ($type) {
            case 'name':
                return $faker->name;

            case 'email':
                return $faker->unique()->safeEmail;

            case 'password':
                return $faker->password();
                
            case 'phone':
                return $faker->phoneNumber;

            case 'image':
                return $faker->imageUrl();
                
            case 'video':
                return $faker->url;
                
            case 'file':
                return $faker->file(public_path('uploads/tmp'), public_path('uploads/files'), false);
                
            case 'link':
                return $faker->url;
                
            case 'location':
                $location = [
                    "name" => $faker->address,
                    "coords" => [
                        $faker->longitude(),
                        $faker->latitude()
                    ]
                ];
                return json_encode($location);
                
            case 'long text':
                $start_from = $faker->numberBetween(0, 30);
                return substr(
                    $sample_text, 
                    $start_from,
                    200,
                );
                
            case 'string':
                $start_from = $faker->numberBetween(0, strlen($sample_text) - 10);
                return substr(
                    $sample_text, 
                    $start_from,
                    50,
                );
                
            case 'number':
                return $faker->numberBetween(13, 237);
                
            case 'rating':
                return $faker->randomFloat(1, 1, $meta->outOf);

            case 'status':
                return $faker->randomElement($meta->availableStatuses)->name;
                
            case 'boolean':
                return $faker->randomElement(array (0,1));

            case 'color':
                return $faker->hexColor();
                
            case 'date':
                return $faker->dateTimeBetween('+2 months', '+4 months', null)->format('Y-m-d H:i:s');
                
            case 'reference':{
                $reference = self::browse($meta->model)->random(1)->first();
                return $reference->_id;
            }
            
            default:
                return "";
        }
    }
    
    static private function field_type_map(Blueprint $table, $field){
        $label = $field['label'];
        $type = $field['type'];
        $default = isset($field['default']) ? $field['default'] : null;
        $required = $field['required'];
        $meta = isset($field['meta']) ? $field['meta'] : null;

        $processed = null;

        switch ($type) {
            case 'name':
                $processed = $table->string($label);
                break;

            case 'email':
                $processed = $table->string($label);
                break;

            case 'password':
                $processed = $table->string($label);
                break;
                
            case 'phone':
                $processed = $table->string($label);
                break;

            case 'image':
                $processed = $table->text($label);
                break;
                
            case 'video':
                $processed = $table->text($label);
                break;
                
            case 'file':
                $processed = $table->text($label);
                break;
                
            case 'link':
                $processed = $table->text($label);
                break;
                
            case 'location':
                $processed = $table->text($label);
                break;
                
            case 'long text':
                $processed = $table->longText($label);
                break;
                
            case 'string':
                $processed = $table->string($label);
                break;
                
            case 'number':
                $processed = $table->bigInteger($label);
                break;
                
            case 'rating':
                $processed = $table->float($label);
                break;
                
            case 'status':
                $processed = $table->string($label);
                break;

            case 'boolean':
                $processed = $table->boolean($label);
                break;
                
            case 'date':
                $processed = $table->timestamp($label);
                break;
                
            case 'reference':{
                $referenceTable = Str::snake($meta['model']);
                $processed = $table->uuid($label);
                $table->foreign($label)
                    ->references('_id')
                    ->on($referenceTable)
                    ->onDelete('cascade');
                break;
            }
            
            default:
                $processed = $table->string($label);
                break;
        }

        if($type != 'reference'){
            if(!$required){
                if(is_null($default))
                    $processed->nullable();
                else{
                    if($type == 'date')
                        $processed->useCurrent();
                    else
                        $processed->default($default);
                }
            }
        }

        if(isset($meta['after']))
            $processed->after($meta['after']);
    }
}
