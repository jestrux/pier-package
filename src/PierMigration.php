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

class PierMigration extends Model
{
    protected $table = "pier";
    protected $fillable = [
        '_id', 'name', 'fields', 'display_field', 'settings'
    ];

    protected $primaryKey = '_id';
    public $incrementing = false;

    static private function pascal_to_sentence($string)
    {
        $words_splited = preg_split('/(?=[A-Z])/', $string);
        $words_capitalized = array_map("ucfirst", $words_splited);
        return trim(implode(" ", $words_capitalized));
    }

    static function describe($model)
    {
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));
        return PierMigration::where("name", $model_name)->first();
    }

    static function truncate($model)
    {
        $table_name = Str::snake($model);
        return DB::table($table_name)->delete();
    }

    static function drop($model)
    {
        $table_name = Str::snake($model);
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));
        DB::table($table_name)->delete();

        $multi_reference_fields = self::model_fields($model)->filter(function ($field) {
            return $field->type == 'multi-reference';
        });

        $multi_reference_fields->each(function ($field) use ($table_name) {
            $reference_table_name = Str::snake($field->label);
            $reference_table = $table_name . '_' . $reference_table_name;
            DB::table($reference_table)->delete();
            Schema::dropIfExists($reference_table);
        });

        Schema::dropIfExists($table_name);

        return PierMigration::where("name", $model_name)->delete();
    }

    static function model_fields($model)
    {
        $db_model = self::describe($model);
        return collect(json_decode($db_model->fields));
    }

    static function settings($model)
    {
        $db_model = self::describe($model);
        return collect(json_decode($db_model->settings));
    }

    static function update_details($model, $updated_details)
    {
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));

        $updated_fields = collect($updated_details)->except(['settings', 'fields']);

        return PierMigration::where("name", $model_name)->update($updated_fields->toArray());
    }

    static function add_field($model, $payload)
    {
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));
        $table_name = Str::snake($model);
        $field = $payload['field'];

        Schema::table($table_name, function (Blueprint $table) use ($payload, $field) {
            $meta = isset($field['meta']) ? $field['meta'] : [];

            if ($payload['placement'] == 'start')
                $meta["after"] = "_id";
            else if ($payload['placement'] == 'after')
                $meta["after"] = $payload['after'];

            $field['meta'] = $meta;

            if ($field['type'] == 'reference') {
                $references = self::browse($meta['model']);
                if (!$references->isEmpty()) {
                    $reference = $references->random(1)->first();
                    $field['default'] = "default" . $reference->_id;
                    $field['required'] = true;
                } else
                    $field['required'] = false;
            }

            self::field_type_map($table, $field);
        });

        if ($field['type'] == 'multi-reference') {
            $reference_table_name = Str::snake($field['label']);

            Schema::create($table_name . '_' . $reference_table_name, function (Blueprint $table) use ($field, $table_name, $reference_table_name) {
                $table->uuid("_id");
                $table->uuid($table_name . '_id');
                $table->uuid($reference_table_name . '_id');

                $table->foreign($table_name . '_id')
                    ->references('_id')
                    ->on($table_name)
                    ->onDelete('cascade');

                $table->foreign($reference_table_name . '_id')
                    ->references('_id')
                    ->on(Str::snake($field['meta']['model']))
                    ->onDelete('cascade');
            });
        };

        $model_fields = self::model_fields($model);

        if ($payload['placement'] == 'start')
            $model_fields->splice(0, 0, [$field]);
        else if ($payload['placement'] == 'after') {
            $index = array_search($payload['after'], $model_fields->pluck("label")->toArray());
            $model_fields->splice($index + 1, 0, [$field]);
        } else
            $model_fields->push($field);

        $pierModel = PierMigration::where("name", $model_name)->first();
        $pierModel->update([
            "fields" => $model_fields->toJson(),
        ]);

        // re-retrieve the instance to get all of the fields in the table.
        return $pierModel->fresh();
    }

    static function update_settings($model, $new_settings)
    {
        $model_name = self::pascal_to_sentence(str_replace(" ", "", $model));
        $query = PierMigration::where("name", $model_name);

        foreach (collect($new_settings) as $key => $value) {
            $query //DB::table('users')
                ->update(['settings->' . $key => $value]);
        }

        return self::settings($model);
    }

    static function search($model, $search_query)
    {
        $table_name = Str::snake($model);
        $db_model = self::describe($model);
        $display_field = $db_model->display_field;

        $results = DB::table($table_name)
            ->where($display_field, 'like', "%{$search_query}%")
            ->select(["*", $display_field . " as label"])
            ->get();

        return $results;
    }

    static function browse_model($model, $params = null)
    {
        $db_model = self::describe($model);
        $data = self::browse($model, $params);

        return [
            "model" => $db_model,
            "data" => $data,
        ];
    }

    static function model_detail($model, $row_id)
    {
        $db_model = self::describe($model);
        $data = self::detail($model, $row_id);

        return [
            "model" => $db_model,
            "data" => $data,
        ];
    }

    static function do_pluck($results, $params, $paginated, $items_per_page = null)
    {
        $pluck_props = [];
        if (isset($params['pluck'])) {
            $pluck = $params['pluck'];
            if (strlen($pluck) > 0) {
                $pluck_props = explode(',', $pluck);
                if (count($pluck_props) > 1) {
                    if (!$paginated) {
                        $results = $results->map(function ($result) use ($pluck_props) {
                            return collect($result)->only($pluck_props);
                        });
                    } else {
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
                } else {
                    if (!$paginated)
                        $results = $results->pluck($pluck);
                    else {
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
        } else {
            if (!$paginated)
                $results = $results;
            else {
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

    static function eager_load_multi_reference_values($data, $model, $flat = true)
    {
        $db_model = self::describe($model);
        $table_name = Str::snake($model);

        $fields = collect(json_decode($db_model->fields));

        $multi_reference_fields = $fields->filter(function ($field) {
            return $field->type == 'multi-reference';
        });

        if ($multi_reference_fields->count() > 0) {
            foreach ($multi_reference_fields as $field) {
                $referenced_table = Str::snake($field->label);

                try {
                    foreach ($data as $result) {
                        $reference_ids = DB::table($table_name . '_' . $referenced_table)->where(
                            $table_name . "_id",
                            '=',
                            $result->_id
                        )->pluck($referenced_table . '_id');

                        $result->{$field->label} = $reference_ids ?? [];

                        if (!$flat && $reference_ids->count() > 0) {
                            $result->{$field->label} = self::browse($field->meta->model, [
                                "whereIn" => $reference_ids
                            ]);
                        }
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }

        return $data;
    }

    static function eager_load($data, $model, $params)
    {
        $db_model = self::describe($model);
        $fields = collect(json_decode($db_model->fields));
        $table_name = Str::snake($model);

        $status_fields = $fields->filter(function ($field) {
            return $field->type == 'status';
        });

        $reference_fields = $fields->filter(function ($field) {
            return $field->type == 'reference';
        });

        $auth_fields = $fields->filter(function ($field) {
            return $field->type == 'auth';
        });

        if (count($data) > 0) {
            if ($status_fields->count() > 0) {
                foreach ($status_fields as $field) {
                    $statuses = $field->meta->availableStatuses;

                    foreach ($data as $result) {
                        if (gettype($result) != "object" || !isset($result->{$field->label})) continue;

                        $resultValue = $result->{$field->label};
                        $result->{$field->label . 'Meta'} = collect($statuses)->where("name", "=", $resultValue)->first();
                    }
                }
            }

            if ($auth_fields->count() > 0) {
                foreach ($auth_fields as $field) {
                    foreach ($data as $result) {
                        try {
                            $result->{$field->label} = DB::table("users")->where(
                                "id",
                                '=',
                                $result->{$field->label}
                            )->first();
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                }
            }

            if ($reference_fields->count() > 0) {
                foreach ($reference_fields as $field) {
                    $referenced_table = Str::snake($field->meta->model);

                    foreach ($data as $result) {
                        try {
                            $result->{$field->label} = DB::table($referenced_table)->where(
                                "_id",
                                '=',
                                $result->{$field->label}
                            )->first();
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                }
            }

            $data = self::eager_load_multi_reference_values($data, $model, false);
        }

        [$data, $pluck_props] = self::do_pluck($data, $params, false);

        if (isset($params['randomize'])) {
            $data = $data->shuffle();
        }

        if (isset($params['unique'])) {
            $data = $params['unique'] == "true"
                ? $data->unique()
                : $data->unique($params['unique']);

            $data = $data->values();
        }

        if (isset($params['limit'])) {
            $limit = $params['limit'];
            if ($limit == 1) {
                if ($data->count() == 0)
                    return null;

                return $data->first();
            }
            $data = $data->take($limit);
        }

        return $data;
    }

    static function get_param($params, $key)
    {
        $param_set = isset($params[$key]);

        if (!$param_set) return null;

        $value = $params[$key];

        if ($value == false || $value == "false") return false;

        return $value;
    }

    static function browse($model, $params = null)
    {
        if (!is_null($params) && count($params) > 0) {
            if (in_array("page", array_keys($params))) {
                return self::paginated_browse($model, $params);
            }
        }

        $db_model = self::describe($model);
        $display_field = $db_model->display_field;
        $model_fields = self::model_fields($model);

        $table_name = Str::snake($model);
        $results = DB::table($table_name);
        $param_keys = [];

        if (!is_null($params) && count($params) > 0) {
            $param_keys = array_keys($params);

            $where_params = collect($param_keys)->filter(function ($key) {
                return strpos($key, "where") === 0
                    || strpos($key, "orWhere") === 0
                    || strpos($key, "andWhere") === 0;
            });

            if ($where_params->count() > 0) {
                foreach ($where_params as $index => $param) {
                    $andWhere = $index == 0 || strpos($param, "andWhere") === 0;

                    if (strtolower($param) == "wherein") {
                        $param_value = $params[$param];
                        $value = is_string($param_value) ? explode(",", $param_value) : $param_value;

                        $results = $andWhere ? $results->whereIn('_id', $value) : $results->orWhereIn('_id', $value);
                    } else if (strtolower($param) == "wherenotin") {
                        $param_value = $params[$param];
                        $value = is_string($param_value) ? explode(",", $param_value) : $param_value;

                        $results = $andWhere ? $results->whereNotIn('_id', $value) : $results->orWhereNotIn('_id', $value);
                    } else {
                        $table_column = strtolower(str_replace(" ", "_", self::pascal_to_sentence(str_replace(["where", "andWhere", "orWhere", "isGreaterThan", "isGreaterThanOrEqual", "isLessThan", "isLessThanOrEqual"], "", $param))));
                        $copmarators = ["isGreaterThanOrEqual", "isLessThanOrEqual", "isLessThan", "isGreaterThan"];
                        $table_column = strtolower(str_ireplace(" ", "_", self::pascal_to_sentence(str_ireplace(array_merge(["andWhere", "orWhere", "where"], $copmarators), "", $param))));
                        $symbol = collect($copmarators)->first(function ($value, $key) use ($param) {
                            return strpos(strtolower($param), strtolower($value));
                        });

                        if (is_null($symbol))
                            $symbol = "Equals";

                        $symbolMap = [
                            "isGreaterThan" => ">",
                            "isGreaterThanOrEqual" => ">=",
                            "isLessThan" => "<",
                            "isLessThanOrEqual" => "<=",
                            "Equals" => "=",
                        ];

                        $copmarator = $symbolMap[$symbol];
                        $args = [$table_column, $copmarator, $params[$param]];
                        $results = $andWhere ? $results->where(...$args) : $results->orWhere(...$args);
                    }
                }
            }

            $can_order_by = in_array("orderBy", $param_keys);
            if ($can_order_by) {
                $order_by_param = $params['orderBy'];

                if (strlen($order_by_param) > 0) {
                    $order_by_props = explode(',', $order_by_param);
                    $order_by = $order_by_props[0];
                    $order_direction = "desc";

                    if (strlen($order_by) > 0) {
                        $model_field_names = $model_fields->map(function ($model) {
                            return $model->label;
                        });

                        if ($model_field_names->contains($order_by) || collect(["created_at", "updated_at", "_id"])->contains($order_by)) {
                            if (count($order_by_props) > 1 && strlen($order_by_props[1]) > 0)
                                $order_direction = $order_by_props[1];

                            $results = $results->orderBy($order_by, $order_direction);

                            $ordered = true;
                        }
                    }
                }
            }

            $search = in_array("q", $param_keys);
            if ($search) {
                $search_query = $params['q'];

                if (strlen($search_query) > 0)
                    $results = $results->where($display_field, 'like', "%{$search_query}%");
            }
        }

        $reference_fields = $model_fields->filter(function ($field) {
            return $field->type == 'reference';
        });

        $results = $results->get();

        if (isset($params['groupBy']) && strlen($params['groupBy']) > 0) {
            // [$results, $pluck_props] = self::do_pluck($results, $params, false);
            $group_by = $params['groupBy'];
            $results = collect($results)->groupBy($group_by);
            $grouped_by_reference_field = $reference_fields->firstWhere('label', $group_by);
            $groups = null;

            if ($grouped_by_reference_field != null) {
                $groups = DB::table(Str::snake($grouped_by_reference_field->meta->model))->whereIn(
                    '_id',
                    $results->keys()
                )->get();
            }

            $results = $results->reduce(function ($agg, $value, $key) use ($groups, $grouped_by_reference_field, $params, $model) {
                if ($grouped_by_reference_field != null) {
                    $group = $groups->where('_id', $key)->first();
                    $key = $group->{$grouped_by_reference_field->meta->mainField};
                }

                $agg[$key] = self::get_param($params, 'flat')
                    ? self::eager_load_multi_reference_values($value, $model)
                    : self::eager_load($value, $model, $params);

                return $agg;
            }, []);
        } else if (count($results) > 0) {
            $results = self::get_param($params, 'flat') ? self::eager_load_multi_reference_values($results, $model)
                : self::eager_load($results, $model, $params);
        }

        return $results;
    }

    static function paginated_browse($model, $params = null)
    {
        $db_model = self::describe($model);
        $display_field = $db_model->display_field;
        $model_fields = self::model_fields($model);

        $table_name = Str::snake($model);
        $results = DB::table($table_name);
        $paginated = false;
        $param_keys = [];

        if (!is_null($params) && count($params) > 0) {
            $param_keys = array_keys($params);

            $where_params = collect($param_keys)->filter(function ($key) {
                return strpos($key, "where") === 0
                    || strpos($key, "orWhere") === 0
                    || strpos($key, "andWhere") === 0;
            });

            if ($where_params->count() > 0) {
                foreach ($where_params as $index => $param) {
                    $table_column = strtolower(str_replace(" ", "_", self::pascal_to_sentence(str_replace(["where", "andWhere", "orWhere", "isGreaterThan", "isGreaterThanOrEqual", "isLessThan", "isLessThanOrEqual"], "", $param))));
                    $copmarators = ["isGreaterThanOrEqual", "isLessThanOrEqual", "isLessThan", "isGreaterThan"];
                    $table_column = strtolower(str_ireplace(" ", "_", self::pascal_to_sentence(str_ireplace(array_merge(["andWhere", "orWhere", "where"], $copmarators), "", $param))));
                    $symbol = collect($copmarators)->first(function ($value, $key) use ($param) {
                        return strpos(strtolower($param), strtolower($value));
                    });

                    if (is_null($symbol))
                        $symbol = "Equals";

                    $symbolMap = [
                        "isGreaterThan" => ">",
                        "isGreaterThanOrEqual" => ">=",
                        "isLessThan" => "<",
                        "isLessThanOrEqual" => "<=",
                        "Equals" => "=",
                    ];

                    $copmarator = $symbolMap[$symbol];

                    if ($index == 0 || strpos($param, "andWhere") === 0)
                        $results = $results->where($table_column, $copmarator, $params[$param]);
                    else
                        $results = $results->orWhere($table_column, $copmarator, $params[$param]);
                }
            }

            $can_order_by = in_array("orderBy", $param_keys);
            $ordered = false;
            if ($can_order_by) {
                $order_by_param = $params['orderBy'];

                if (strlen($order_by_param) > 0) {
                    $order_by_props = explode(',', $order_by_param);
                    $order_by = $order_by_props[0];
                    $order_direction = "desc";

                    if (strlen($order_by) > 0) {
                        $model_field_names = $model_fields->map(function ($model) {
                            return $model->label;
                        });

                        if ($model_field_names->contains($order_by) || collect(["created_at", "updated_at", "_id"])->contains($order_by)) {
                            if (count($order_by_props) > 1 && strlen($order_by_props[1]) > 0)
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
            if ($search) {
                $search_query = $params['q'];

                if (strlen($search_query) > 0)
                    $results = $results->where($display_field, 'like', "%{$search_query}%");
            }

            $paginate = in_array("page", $param_keys);
            if ($paginate) {
                $paginated = true;
                $items_per_page = $params['perPage'] ?? 25;
                [$results, $pluck_props] = self::do_pluck($results, $params, true, $items_per_page);
            }
        }

        $reference_fields = $model_fields->filter(function ($field) {
            return $field->type == 'reference';
        });

        if (!$paginated) {
            [$results, $pluck_props] = self::do_pluck($results, $params, false);
            if (isset($param_keys)) {
                $can_group_by = in_array("groupBy", $param_keys);
                $grouped = false;
                if ($can_group_by) {
                    $group_by = $params['groupBy'];
                    if (strlen($group_by) > 0) {
                        $results = collect($results)->groupBy($group_by);
                        $grouped_by_reference_field = $reference_fields->firstWhere('label', $group_by);

                        if ($grouped_by_reference_field != null) {
                            $groups = DB::table(Str::snake($grouped_by_reference_field->meta->model))->whereIn(
                                '_id',
                                $results->keys()
                            )->get();

                            $results = $results->reduce(function ($agg, $value, $key) use ($groups, $group_by, $grouped_by_reference_field) {
                                $group = $groups->where('_id', $key)->first();
                                $value->map(function ($value) use ($group, $group_by) {
                                    $value->{$group_by} = $group;
                                });
                                $newKey = $group->{$grouped_by_reference_field->meta->mainField};
                                $agg[$newKey] = $value;
                                return $agg;
                            }, []);
                        }

                        $results = collect($results);
                    }
                }
            }
        }

        $has_been_paginated = is_array($results) ? array_key_exists("data", $results) : false;
        $result_data = $has_been_paginated ? collect($results["data"]) : $results;

        if (count($result_data) > 0) {
            if (in_array("randomize", $param_keys)) {
                $result_data = $result_data->shuffle();
            }

            $status_fields = $model_fields->filter(function ($field) {
                return $field->type == 'status';
            });

            if ($status_fields->count() > 0) {
                foreach ($status_fields as $field) {
                    $statuses = $field->meta->availableStatuses;

                    foreach ($result_data as $result) {
                        if (gettype($result) != "object" || !isset($result->{$field->label})) continue;

                        $resultValue = $result->{$field->label};
                        $result->{$field->label . 'Meta'} = collect($statuses)->where("name", "=", $resultValue)->first();
                    }
                }
            }

            if ($reference_fields->count() > 0) {
                // return in_array('typesy', $result_data->keys()->toArray());

                foreach ($reference_fields as $field) {
                    $referenced_table = Str::snake($field->meta->model);

                    foreach ($result_data as $result) {
                        try {
                            $result->{$field->label} = DB::table($referenced_table)->where(
                                "_id",
                                '=',
                                $result->{$field->label}
                            )->first();
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                }
            }

            $multi_reference_fields = $model_fields->filter(function ($field) {
                return $field->type == 'multi-reference';
            });

            if ($multi_reference_fields->count() > 0) {
                foreach ($multi_reference_fields as $field) {
                    $referenced_table = Str::snake($field->label);

                    try {
                        foreach ($result_data as $result) {
                            $reference_ids = DB::table($table_name . '_' . $referenced_table)->where(
                                $table_name . "_id",
                                '=',
                                $result->_id
                            )->pluck($referenced_table . '_id');

                            if ($reference_ids->count() > 0) {
                                $result->{$field->label} = DB::table(Str::snake($field->meta->model))->whereIn(
                                    '_id',
                                    $reference_ids
                                )->get();
                            } else
                                $result->{$field->label} = [];
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }

            $auth_fields = $model_fields->filter(function ($field) {
                return $field->type == 'auth';
            });

            if ($auth_fields->count() > 0) {
                foreach ($auth_fields as $field) {
                    foreach ($result_data as $result) {
                        try {
                            $result->{$field->label} = DB::table("users")->where(
                                "id",
                                '=',
                                $result->{$field->label}
                            )->first();
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                }
            }

            if (isset($params['unique'])) {
                $result_data = $params['unique'] == "true"
                    ? $result_data->unique()
                    : $result_data->unique($params['unique']);

                $result_data = $result_data->values();
            }
        }

        if (isset($params['limit'])) {
            $result_data = $result_data->take($params['limit']);
        }

        if (isset($params['first'])) {
            if ($result_data->count() == 0) $result_data = null;

            $result_data = $result_data->first();
        }

        if ($has_been_paginated) $results['data'] = $result_data;
        else $results = $result_data;

        return $results;
    }

    static function detail($model, $row_id, $filters = [])
    {
        $table_name = Str::snake($model);
        $result = DB::table($table_name)->where("_id", '=', $row_id)->first();

        if (!$result) return null;

        $results = [$result];

        $results = self::get_param($filters, 'flat') ? self::eager_load_multi_reference_values($results, $model)
            : self::eager_load($results, $model, null);

        return $results[0];
    }

    static function deleteEntry($model, $entryId)
    {
        $table_name = Str::snake($model);
        return DB::table($table_name)->where('_id', '=', $entryId)->delete();
    }

    static function insertRow($model, $data)
    {
        $fields = self::model_fields($model);
        $table_name = Str::snake($model);
        $_id = !isset($data) || !isset($data['_id']) || is_null($data['_id']) ? Str::uuid() : $data['_id'];
        $entry = [];

        $regular_fields = $fields->filter(function ($field) {
            return $field->type != 'multi-reference';
        });

        $entry['_id'] = $_id;
        $entry['created_at'] = now();
        $entry['updated_at'] = now();

        foreach ($regular_fields as $field) {
            $value = isset($data[$field->label]) ? $data[$field->label] : null;
            if (is_null($value) && isset($field->default)) {
                if ($field->type == "date")
                    $value = \Carbon\Carbon::now()->toDateTimeString();
                else
                    $value = $field->default;
            }

            if ($field->type === 'password')
                $entry[$field->label] = Hash::make($value);
            else if ($field->type === 'location' && is_array($value))
                $entry[$field->label] = json_encode($value);
            else
                $entry[$field->label] = $value;
        }

        DB::table($table_name)->insert($entry);

        $multi_reference_fields = $fields->filter(function ($field) {
            return $field->type == 'multi-reference';
        });

        if ($multi_reference_fields->count() > 0) {
            foreach ($multi_reference_fields as $field) {
                if (isset($data[$field->label])) {
                    $values = $data[$field->label];
                    $entry[$field->label] = self::populateMultiReferenceRow($table_name, $_id, $field, $values);
                }
            }
        }

        $reference_fields = $fields->filter(function ($field) {
            return $field->type == 'reference';
        });

        if ($reference_fields->count() > 0) {
            foreach ($reference_fields as $field) {
                if (isset($entry[$field->label]))
                    $entry[$field->label] = DB::table(Str::snake($field->meta->model))->where('_id', $entry[$field->label])->first();
                else
                    $entry[$field->label] = null;
            }
        }

        return $entry;
    }

    static function updateRow($model, $row_id, $data)
    {
        $fields = self::model_fields($model);
        $table_name = Str::snake($model);
        $entry = [
            'updated_at' => now()
        ];

        $fields = $fields->filter(function ($field) use ($data) {
            return collect($data)->has($field->label);
        })->values();

        if (count($fields) < 1)
            return DB::table($table_name)->where('_id', $row_id)->get();

        $regular_fields = $fields->filter(function ($field) {
            return $field->type != 'multi-reference';
        });

        foreach ($regular_fields as $field) {
            $value = isset($data[$field->label]) ? $data[$field->label] : null;

            if ($field->type === 'password')
                $entry[$field->label] = Hash::make($value);
            else if ($field->type === 'location' && is_array($value))
                $entry[$field->label] = json_encode($value);
            else
                $entry[$field->label] = $value;
        }

        DB::table($table_name)
            ->where('_id', $row_id)
            ->update($entry);

        $multi_reference_fields = $fields->filter(function ($field) {
            return $field->type == 'multi-reference';
        });

        if ($multi_reference_fields->count() > 0) {
            foreach ($multi_reference_fields as $field) {
                self::deleteMultiReferences($table_name, $row_id, $field);

                if (isset($data[$field->label]) && $data[$field->label] != null) {
                    $values = $data[$field->label];
                    self::populateMultiReferenceRow($table_name, $row_id, $field, $values);
                }
            }
        }

        return DB::table($table_name)->where('_id', $row_id)->first();
    }

    static function populate($model, $item_count = 25)
    {
        $table_name = Str::snake($model);
        $fields = self::model_fields($model);

        $types = $fields->map(function ($field) {
            return $field->type;
        })->toArray();

        $image_fields = $fields->filter(function ($field) {
            return $field->type === "image";
        });

        $images = null;
        $faces = null;
        $videos = null;

        if (count($image_fields) > 0) {
            // try {
            $face_images = $image_fields->filter(function ($field) {
                return $field->meta->face;
            })->toArray();

            $non_face_images = $image_fields->filter(function ($field) {
                return !$field->meta->face;
            })->toArray();

            if (count($non_face_images) > 0) {
                $imageResponse = Http::withoutVerifying()->get('https://api.unsplash.com/photos?per_page=30&order_by=latest&client_id=17ef130962858e4304efe9512cf023387ee5d36f0585e4346f0f70b2f9729964');
                $images = collect($imageResponse->json())->map(function ($img) {
                    return $img['urls']['regular'];
                })->toArray();
            }

            if (count($face_images) > 0) {
                $imageResponse = Http::withoutVerifying()->get('https://api.unsplash.com/collections/3678902/photos?per_page=30&order_by=latest&client_id=17ef130962858e4304efe9512cf023387ee5d36f0585e4346f0f70b2f9729964');
                $images = collect($imageResponse->json())->map(function ($img) {
                    return $img['urls']['thumb'];
                })->shuffle()->toArray();
            }
            // } catch (\Throwable $th) {}
        }

        if (in_array("video", $types)) {
            try {
                $response = Http::withoutVerifying()->get("https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=30&q=beautiful&type=video&videoEmbeddable=true&fields=items(id)&key=AIzaSyA_Rg25Nc3IbNh3OBP6KkeHXinC9T3ajyw");
                $videos = collect($response->offsetGet('items'))->map(function ($video) {
                    return "https://www.youtube.com/watch?v=" . $video['id']['videoId'];
                })->toArray();
            } catch (\Throwable $th) {
            }
        }

        $entries = [];

        $regular_fields = $fields->filter(function ($field) {
            return $field->type != 'multi-reference';
        })->all();

        for ($i = 0; $i < $item_count; $i++) {
            $entry = self::populateRow($i, $regular_fields, $images, $faces, $videos);

            DB::table($table_name)->insert($entry);

            $entries[] = $entry;
        }

        $multi_reference_fields = $fields->filter(function ($field) {
            return $field->type == 'multi-reference';
        });

        if ($multi_reference_fields->count() > 0) {
            foreach ($entries as $entry) {
                foreach ($multi_reference_fields as $field) {
                    $entry[$field->label] = self::populateMultiReferenceRow($table_name, $entry["_id"], $field);
                }
            }
        }

        return $entries;
    }

    static function deleteMultiReferences($table_name, $row_id, $field)
    {
        $reference_table = Str::snake($field->label);
        DB::table($table_name . '_' . $reference_table)
            ->where($table_name . '_id', "=", $row_id)
            ->delete();
    }

    static function populateMultiReferenceRow($table_name, $row_id, $field, $references = null)
    {
        if (is_null($references)) {
            $faker = Faker::create();
            $references = self::browse($field->meta->model);
            $item_count = $faker->numberBetween(1, min(4, $references->count()));

            $references = $references->random($item_count)->pluck('_id');
        }

        $reference_table = Str::snake($field->label);

        foreach ($references as $reference_id) {
            $entry = [
                "_id" => Str::uuid(),
                $table_name . '_id' => $row_id,
                $reference_table . '_id' => $reference_id
            ];

            DB::table($table_name . '_' . $reference_table)->insert($entry);
        }

        return DB::table(Str::snake($field->meta->model))->whereIn(
            '_id',
            $references
        )->get();
    }

    static function populateRow($index, $fields, $images, $faces, $videos)
    {
        $pierModel = [];
        $_id = Str::uuid();
        $pierModel['_id'] = $_id;
        $pierModel['created_at'] = now();
        $pierModel['updated_at'] = now();

        foreach ($fields as $field) {
            $label = $field->label;
            $type = $field->type;
            $meta = isset($field->meta) ? $field->meta : null;

            if ($type == "image") {
                try {
                    $isFace = $field->meta->face;

                    if ($isFace && is_array($faces) && count($faces) > 0)
                        $pierModel[$label] = $faces[$index % 30];
                    else if (is_array($images) && count($images) > 0)
                        $pierModel[$label] = $images[$index % 30];
                    else
                        $pierModel[$label] = self::field_generator($field);
                } catch (\Throwable $th) {
                    if (is_array($images) && count($images) > 0)
                        $pierModel[$label] = $images[array_rand($images, 1)];
                    else
                        $pierModel[$label] = self::field_generator($field);
                }
            } else if ($type == "video" && is_array($videos) && count($videos) > 0)
                $pierModel[$label] = $videos[array_rand($videos, 1)];
            else if ($type == "auth")
                $pierModel[$label] = 1;
            else
                $pierModel[$label] = self::field_generator($field, $_id);
        };

        return $pierModel;
    }

    static function record($model, $fields, $display_field, $data = null, $settings = '{"listPageType": "table"}')
    {
        $table_name = Str::snake($model);
        $model_name = self::pascal_to_sentence($model);

        $modelEntry = [
            "_id" => Str::uuid(),
            "name" => $model_name,
            "display_field" => $display_field,
            "fields" => $fields->toJson(),
            "settings" => $settings ?? '{"listPageType": "table"}'
        ];

        $pierModel = PierMigration::create($modelEntry);

        $multi_reference_fields = $fields->filter(function ($field) {
            return $field['type'] == 'multi-reference';
        });

        $fields = $fields->filter(function ($field) {
            return $field['type'] != 'multi-reference';
        });

        Schema::create($table_name, function (Blueprint $table) use ($fields) {
            $table->uuid("_id");

            foreach ($fields as $field) {
                self::field_type_map($table, $field);
            };

            $table->timestamps();
            $table->primary("_id");
        });

        $multi_reference_fields->each(function ($field) use ($table_name) {
            $reference_table_name = Str::snake($field['label']);

            Schema::create($table_name . '_' . $reference_table_name, function (Blueprint $table) use ($field, $table_name, $reference_table_name) {
                $table->uuid("_id");
                $table->uuid($table_name . '_id');
                $table->uuid($reference_table_name . '_id');

                $table->foreign($table_name . '_id')
                    ->references('_id')
                    ->on($table_name)
                    ->onDelete('cascade');

                $table->foreign($reference_table_name . '_id')
                    ->references('_id')
                    ->on(Str::snake($field['meta']['model']))
                    ->onDelete('cascade');
            });
        });

        if (!is_null($data)) {
            foreach ($data as $entry) {
                self::insertRow($model, $entry);
            }
        }

        // re-retrieve the instance to get all of the fields in the table.
        return $pierModel->fresh();
    }

    static function migrate_model($model)
    {
        $table_name = Str::snake($model);
        $fields = self::model_fields($model);

        $multi_reference_fields = $fields->filter(function ($field) {
            return $field->type == 'multi-reference';
        });

        $fields = $fields->filter(function ($field) {
            return $field->type != 'multi-reference';
        });

        Schema::create($table_name, function (Blueprint $table) use ($fields) {
            $table->uuid("_id");

            foreach ($fields as $field) {
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

        $multi_reference_fields->each(function ($field) use ($table_name) {
            $field = (array) $field;
            $reference_table_name = Str::snake($field['label']);

            Schema::create($table_name . '_' . $reference_table_name, function (Blueprint $table) use ($field, $table_name, $reference_table_name) {
                $table->uuid("_id");
                $table->uuid($table_name . '_id');
                $table->uuid($reference_table_name . '_id');

                $table->foreign($table_name . '_id')
                    ->references('_id')
                    ->on($table_name)
                    ->onDelete('cascade');

                $table->foreign($reference_table_name . '_id')
                    ->references('_id')
                    ->on(Str::snake($field['meta']['model']))
                    ->onDelete('cascade');
            });
        });

        return self::describe($model);
    }

    static private function field_generator($field, $_id = null)
    {
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
                return collect(
                    [
                        "Before embarking on your angling journey, it's crucial to have the right gear. A dependable fishing rod, a variety of lures, and sturdy fishing line are the basic tools every angler needs. Don't forget to pack your patience and resilience, as fishing is as much about strategy as it is about luck.",
                        "One of the key aspects of abstract art is its ability to evoke emotions and provoke thought without providing a concrete narrative. Viewers are encouraged to explore the artwork freely and derive their own meanings from the piece, making each viewing a unique and personal experience.",
                        "In many cases, fire hazards stem from simple negligence. Failing to properly maintain appliances, leaving flammable materials near heat sources, or disregarding basic safety precautions can all increase the likelihood of a fire breaking out. By taking a more conscientious approach to fire safety, we can significantly reduce the chances of a catastrophic event occurring.",
                        "The consequences of a fire can be devastating, resulting in property damage, injury, or even loss of life. It's crucial to understand the gravity of the situation and take proactive measures to minimize the risks. Prevention is key when it comes to fire hazards, and by staying informed and vigilant, we can help prevent potential disasters before they occur.",
                        "For nature lovers, Tijuca National Park is a verdant oasis within the bustling city. The lush rainforest is home to diverse flora and fauna, offering visitors a chance to escape the urban hustle and immerse themselves in the beauty of nature.",
                        "Words have the power to evoke emotions, create desire, and drive decisions. A skilled copywriter understands how to use language to tap into the reader's emotions and create a sense of urgency. By choosing the right words and crafting a compelling narrative, copywriters can influence behavior and drive results.",
                        "Clear and concise copy is essential for effective communication. Avoid using jargon or complex language that may confuse your audience. Instead, focus on delivering your message in a straightforward and easy-to-understand manner. Keep your sentences short, and use bullet points or numbered lists to break up the text and make it more digestible.",
                        "From tranquil ponds to roaring rivers, the world is full of prime fishing spots waiting to be explored. Whether you prefer freshwater or saltwater fishing, each destination offers its own unique set of challenges and rewards. Dive into the adventure and discover the wonders that await beneath the surface.",
                        "In essence, semitones serve as the building blocks of musical expression, offering endless possibilities for artistic exploration and innovation. By mastering the art of semitones, musicians can elevate their compositions to new heights and connect with audiences on a profound level. Embrace the nuances of semitones, and unlock a world of musical brilliance.",
                        "Effective copywriting begins with a deep understanding of your target audience. By knowing your audience's needs, desires, and pain points, you can tailor your messaging to resonate with them on a personal level. This personal connection is key to creating content that not only captures attention but also compels action.",
                        "Brewing the perfect cup of coffee is both a science and an art. The grind size, water temperature, and brewing time all play a crucial role in determining the flavor profile of the final brew. Whether you prefer an espresso or a pour-over, there's a method out there that will suit your taste buds.",
                        "Aside from its rich flavor and aroma, coffee also boasts a myriad of health benefits. Studies have shown that coffee can improve brain function, boost metabolism, and even lower the risk of certain diseases. So go ahead, pour yourself another cup guilt-free.",
                        "One of the key objectives of cinematography is to convey emotions and moods through visual storytelling. By manipulating elements such as lighting, color, and composition, cinematographers can create a sense of tension, joy, sadness, or excitement within a scene. Every frame is carefully crafted to evoke a specific emotional response from the audience, making cinematography a powerful tool for shaping the viewer's experience.",
                        "Whether using natural light or artificial sources, cinematographers use lighting to highlight characters, create shadows, and set the tone for the story. Similarly, color plays a significant role in conveying emotions and themes within a film, with different color palettes evoking varying psychological responses from viewers.",
                        "Composition and framing are crucial aspects of cinematography that determine how viewers perceive and engage with a scene. By carefully positioning elements within the frame, cinematographers can draw attention to certain details, create visual balance, and guide the viewer's eye through the image. The rule of thirds, leading lines, and symmetry are common compositional techniques used to enhance the visual appeal of a shot.",
                        "Camera movement is another tool used by cinematographers to add depth and dynamism to a film. Whether through tracking shots, panning, or handheld camera work, movement can create a sense of urgency, intimacy, or spectacle within a scene. By incorporating movement strategically, cinematographers can enhance the narrative flow and emotional impact of a film.",
                        "Cinematography is a blend of artistry and technical skill that plays a fundamental role in shaping the visual language of cinema. From capturing emotions through lighting and color to composing frames that engage viewers, every decision made by a cinematographer contributes to the overall impact of a film. By understanding the principles of cinematography and appreciating its creative possibilities, audiences can develop a deeper appreciation for the magic of visual storytelling.",
                        "Beyond the joy of birdwatching lies a deeper purpose: conservation. By observing and appreciating birds in their natural habitat, we become more aware of the fragile ecosystems that support them. Take this opportunity to learn about local conservation efforts and ways to protect avian species for future generations.",
                        "In the world of birdwatching, every flutter of wings and every melodic chirp holds the promise of a new discovery. Whether you are a novice or a seasoned enthusiast, the art of birdwatching offers endless opportunities for exploration, learning, and connection with the natural world. So grab your binoculars, step outside, and let the magic of birdwatching unfold before your eyes.",
                        "As technology continues to advance, the future of e-commerce looks promising. Innovations like AI, AR, and blockchain are shaping the way we shop online, providing immersive experiences and enhanced security and offering convenience and accessibility to consumers worldwide. With continuous innovation and adaptation, e-commerce is set to thrive in the years to come, reshaping the way we shop and do business.",
                        "Investing in quality tools is crucial for achieving professional results in woodworking. Some essential tools for woodworking include a saw, chisel, plane, hammer, measuring tape, and sandpaper. These tools allow you to cut, shape, assemble, and finish your wood projects with precision and accuracy.",
                        "Creativity is at the heart of woodworking, allowing you to transform raw wood into functional and aesthetic pieces of art. Experimenting with different wood species, finishes, and designs enables you to push the boundaries of traditional woodworking and create innovative and original creations that reflect your personality and style.",
                        "As the lights go out and the race begins, the drivers unleash the full power of their machines, hurtling down the straightaways and tackling hairpin turns with finesse. The roar of the engines and the screech of tires create an adrenaline-fueled atmosphere that keeps fans on the edge of their seats.",
                        "The game is played in three periods of 12 minutes each, with a shorter playing time to accommodate the challenging sandy terrain. Players are barefoot and must wear jerseys, shorts, and socks. The goalkeepers have restrictions on handling the ball, adding an extra layer of strategy to the game.",
                        "Whether you're a longtime reggae enthusiast or a newcomer to the genre, there's something undeniably captivating about the laid-back melodies and thought-provoking lyrics of reggae music which provoke a cultural movement that continues to inspire and unite people across the globe. So, the next time you find yourself in need of a musical escape, turn to the soothing sounds of reggae and let its positive vibrations wash over you.",
                        "Beyond the music, hip hop has influenced fashion trends, language, and even politics. From the iconic baggy pants and chains to popularizing slang terms like “bling” and “dope,” its impact is undeniable. Hip hop has also given rise to successful entrepreneurs and philanthropists who have used their platform to give back to their communities.",
                        "At the heart of samba music are intricate rhythms played on instruments such as tamborims, surdos, and cuicas, creating a pulsating sound that compels listeners to move their bodies in sync with the beat. The lyrics of samba songs often reflect the joys and struggles of everyday life, evoking a range of emotions in those who hear them.",
                        "Whether you're tapping your feet to the beat of a samba song or swaying your hips to the rhythm of a samba dance, the essence of samba lies in its ability to uplift spirits and bring people together in joyous celebration. So why not immerse yourself in the irresistible charm of samba and let its infectious energy sweep you off your feet?",
                        "From the fiery spectacle of the Up Helly Aa festival in Scotland to the spiritual rituals of the Las Fallas festival in Spain, fire festivals offer a diverse range of customs and ceremonies that captivate the senses and ignite the imagination. Participants often dress in elaborate costumes, carry torches, and perform intricate fire dances that blend tradition with modern creativity.",
                        "If you're eager to witness the magic of a fire festival firsthand, there are numerous events held globally throughout the year. Whether you're drawn to the mystical allure of the Beltane Fire Festival in Edinburgh or the pulsating energy of the Taunggyi Hot Air Balloon Festival in Myanmar, there's a fire festival waiting to ignite your spirit.",
                        "In a world filled with chaos and uncertainty, fire festivals offer a beacon of hope and a reminder of the enduring human spirit. So, gather around the fire, feel its warmth on your skin, and let the flames guide you towards new adventures and discoveries. Unleash your passion, embrace the unknown, and let the magic of fire festivals illuminate your path.",
                        "Imagine a kaleidoscope of colors, pulsating beats, and a sense of freedom in the air. Coachella embodies an eclectic mix of musical genres, from indie and rock to hip-hop and electronic dance music. It's a melting pot of creativity and self-expression that captivates attendees year after year.",
                        "At its core, it's more than just a music festival; it's a cultural phenomenon that celebrates diversity, creativity, and self-expression. A place where strangers become friends, and memories are made that last a lifetime. An experience like no other, a place where magic happens, and dreams come to life. Experience the magic for yourself and immerse yourself in a world where music, art, fashion, and community converge to create an unforgettable journey. Join the festival-goers and be part of a celebration that transcends boundaries and embraces the essence of what it means to truly live in the moment.",
                        "Whether as a form of entertainment, artistic inspiration, or scientific exploration, kaleidoscopes continue to fascinate and delight audiences with their mesmerizing beauty and intricate patterns. Embark on a journey into the mesmerizing world of kaleidoscopes and discover the endless possibilities of light, color, and reflection.",
                        "While traditional kaleidoscopes remain popular, modern iterations have evolved to incorporate new materials and technologies. From handheld versions to digital simulations, kaleidoscopes continue to captivate audiences and serve as a source of inspiration for creative endeavors.",
                        "Despite its demise, the legacy lives on in modern society. Law, language, architecture, and engineering continue to influence our world today. It remains a symbol of power, ambition, and the consequences of unchecked hubris. It's rise and fall serve as a cautionary tale of the perils of overreach and the importance of adaptability in the face of change. As we reflect on it's legacy, we are reminded of the fragility of empires and the enduring impact of their achievements on the course of history.",
                        "The world is full of breathtaking destinations waiting to be explored. Whether you prefer the serene beauty of the mountains, the tranquility of the forest, or the excitement of the beach, there is a backpacking destination perfect for you. Research different locations to find one that aligns with your interests and experience level.",
                        "At the heart of fringe science lies a tapestry of enigmatic phenomena and unexplained mysteries that defy the constraints of traditional scientific understanding. From UFO sightings and paranormal experiences to alternative energy sources and mind-bending theories of the universe, fringe science encompasses a diverse array of subjects that captivate the imagination and provoke thought-provoking discussions.",
                        "In a world where the boundaries between science and speculation are increasingly blurred, fringe science serves as a reminder of the limitless possibilities that exist beyond the confines of mainstream scientific discourse. By embracing the complexity of the unknown and daring to question the unquestionable, fringe science invites us to expand our minds and consider the seemingly impossible.",
                        "One of the key advantages of mobile photography is its ability to capture moments on the go. Whether you're traveling, attending a special event, or simply out and about, your smartphone allows you to seize the moment and snap a photo effortlessly. The burst mode feature further enhances the spontaneity of mobile photography, enabling you to capture a series of shots in quick succession.",
                        "Mobile photography empowers you to unleash your creativity and capture moments in a unique and personal way. By mastering the art of mobile photography, you can elevate your photos to new heights and share your vision with the world. Embrace the convenience and spontaneity of mobile photography, experiment with different perspectives and lighting conditions, and harness the power of editing tools to bring out the best in your photos. Let your creativity soar as you embark on your mobile photography journey.",
                        "Despite the challenges, the future of space travel looks promising. Advancements in technology are making space travel more accessible, with private companies like SpaceX leading the way in commercial space travel. Missions to Mars and beyond are no longer just science fiction but realistic goals for the future.",
                        "Just as early explorers set out to discover new lands, astronauts today venture into the depths of space, expanding our understanding of the universe and our place within it reaching new destinations, pushing the limits of human potential and imagination. As we continue to explore the cosmos, we are bound to uncover even more mysteries and wonders that will inspire generations to come.",
                        "One of the most iconic landmarks is a towering rock formation that rises dramatically from the surrounding landscape. This natural wonder is a popular destination for rock climbers and hikers, offering breathtaking views of the surrounding plains. For those seeking a more leisurely experience, the charming town of Jackson Hole is a must-visit, with its quaint shops, galleries, and world-class ski resorts.",
                        "Decades after its initial publication, The Great Gatsby remains a literary classic that continues to enthrall readers of all ages. Its themes of love, ambition, and disillusionment are as relevant today as they were in the Jazz Age, ensuring that Fitzgerald's masterpiece will stand the test of time."
                    ]
                )->random();

            case 'string':
                return collect([
                    "The Art of Birdwatching: A Beginner's Guide",
                    "Unleashing Your Creativity: A Beginner's Guide to Woodworking",
                    "The Thrilling World of Formula 1 Racing",
                    "The Beach Soccer Phenomenon: Kick Off Your Summer Fun Now",
                    "Unraveling the Rhythms and Roots of Reggae Music",
                    "The Magic of Coachella: A Closer Look at the Ultimate Music Festival Experience",
                    "Exploring the Mesmerizing World of Kaleidoscopes",
                    "Endless Wanderlust: A Beginner's Guide to Backpacking",
                    "The Enigmatic World of Fringe Science. Mysteries, boundaries and possibility",
                    "Creativity at your fingertips: Mastering Mobile Photography",
                    "The Fascinating History and science behind Space Travel",
                    "The Natural Wonders, Rich Cultural Tapestry and Wildlife Safari Adventures of Tanzania",
                    "Unveiling the Timeless Charm of The Great Gatsby",
                    "“Learning to Fly” — Kiki’s Delivery Service and the Unchanging Economy",
                    "The Great Exhaustion",
                    "Thirteen Things a Middle-Aged Man Can Learn From the Baby-Sitters Club",
                    "You Shouldn’t Have to Pay $24 for a 2-Pack of At-Home Covid Tests",
                    "The Best Business Book of 2021 (According to Every Other Best-of-2021 List)",
                    "Tiktok Stirs up the Food Industry as It Enters The Kitchen",
                    "The Reason We May Never Have Another bell hooks, Eve Babitz, or Joan Didion",
                    "Invisible: The Fault Lines of Motherhood",
                    "I Gained Nothing From My Perfect Attendance At Work",
                    "The Very First Sermon Christ Gave Taught Us About The Jubilee",
                    "Company Loyalty Doesn’t Exist",
                    "We Were Always Disposable, and We Can’t Ignore It Anymore",
                    "In Losing The Village We Abandoned Society’s Parents",
                    "Who Was ‘First’ with a Big Idea? It’s Often Hard To Know",
                    "An Ancient Greek Weapon Could Become the Future of Solar Energy",
                    "The Art of Seeing",
                    "Uncovering Family Secrets Through At-Home DNA Tests",
                    "NFTs Are Critical for the Future of Art",
                    "What If We Aren’t As Divided As They Tell Us We Are?",
                    "An Extended Break From the Rat Race Has Changed My Life",
                    "Things I Wish My Students Knew",
                    "Ode to bell hooks",
                    "My Favorite Podcast Is Talking To You On The Phone For Hours",
                    "How to Get Addicted to Writing",
                    "When the World Feels Dark and Beautiful in the Same Moment",
                    "The Inescapable Neoliberal Bias Behind ‘Kurzgesagt — In a Nutshell'",
                    "The Best Books I Read Are All About Weirdos",
                    "When Life Is Slow, Go Even Slower",
                    "In my Abuela’s Kitchen",
                    "In ‘Drama Queens’ Podcast, ‘One Tree Hill’ Stars Rehash the Making of The Show — the Good, Bad and…",
                    "How to Ground Your Mind, Body, Spirit, and Emotions"
                ])->random();

            case 'number':
                return $faker->numberBetween(87, 532193);

            case 'rating':
                return $faker->randomFloat(1, 1, $meta->outOf);

            case 'status':
                return $faker->randomElement($meta->availableStatuses)->name;

            case 'boolean':
                return $faker->randomElement(array(0, 1));

            case 'color':
                return $faker->hexColor();

            case 'date':
                return $faker->dateTimeBetween('+2 months', '+4 months', null)->format('Y-m-d H:i:s');

            case 'reference': {
                    $reference = self::browse($meta->model)->random(1)->first();
                    return $reference->_id;
                }

            default:
                return "";
        }
    }

    static private function field_type_map(Blueprint $table, $field)
    {
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

            case 'reference': {
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

        if ($type != 'reference') {
            if (!$required) {
                if (is_null($default))
                    $processed->nullable();
                else {
                    if ($type == 'date')
                        $processed->useCurrent();
                    else
                        $processed->default($default);
                }
            }
        }

        if (isset($meta['after']))
            $processed->after($meta['after']);
    }
}
