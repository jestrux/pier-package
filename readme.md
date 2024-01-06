# Pier

Headless CMS and API generator for Laravel projects.

# Demo of how Pier works

-   [https://vimeo.com/737768985](https://vimeo.com/737768985)

# How to Use

## Install Package

Add these lines to your `composer.json` file

```json
{
	"require": {
		"jestrux/pier": "dev-master"
	},
	"repositories": [
		{
			"type": "git",
			"url": "https://github.com/jestrux/pier-package.git"
		}
	]
}
```

And then in the terminal, run the following command.

`composer update`

### Add Pier table to your database

Now that we have our package installed, we need to migrate the database to add the necessary tables for Press. In the command line, run the following command.

`php artisan migrate`

### Publish package asset files

`php artisan vendor:publish --tag=pier-assets`

You will now find the pier asset files under `/public/pier`

## Add image search and S3 upload configs

Add the following to your .env file and set the correct values...

```
PIER_FORM_REDIRECT_URL=
PIER_UNSPLASH_CLIENT_ID=
PIER_S3_BUCKET=
PIER_S3_REGION=
PIER_S3_ACCESS_KEY_ID=
PIER_S3_SECRET_ACCESS_KEY=
```

## Storing uploads locally

If you plan to upload files to your asset files instead of S3, there are two ways:

### With access to storage:link

Add the following field to `.env` which points to a `post` url.

```
PIER_UPLOAD_URL=/pier-upload-file
```

The controller should return a response similar to the one below.

```blade
return response()->json([
    "success" => true,
    "path" => asset(str_replace("public", "storage", $path))
]);
```

### Without access to storage:link

Add the following field to `.env`

```
PIER_UPLOAD_DIR=pierFiles
```

Add port number to APP_URL in `.env` includes port number if you're using localhost

```diff
--APP_URL=http://localhost
++APP_URL=http://localhost:8000
```

Also update the local disk under `config.filesystems.php` as follows:

```diff
--'root' => storage_path('app'),
++'root' => public_path('img/uploads'),
++'url' => env('APP_URL') . '/img/uploads',
```

# Pier Directives

## Model

```blade
@piermodel('Article')
    {{$name}} {{ $fields }} {{ $data }}
@endpiermodel
```

## Data

```blade
@pierdata('Song')
    {{$data}}
@endpierdata()

// With filters
@pierdata([
    'model' => 'Book',
    'q' => 'harry',
    'page' => null,
    'pluck' => 'name',
    'randomize' => true,
    'limit' => 3,
    // 'first' => true
])
    {{ $data }}
@endpierdata
```

## Row

```blade
@pierrow('Book', '044365e5...')
    {{ $data }}
@endpierrow

// Alternative
@pierrow([
    'model' => 'Book',
    'rowId' => '044365e5...',
])
    {{ $data }}
@endpierrow
```

# Pier Components

## Table

```blade
<x-pier::table model="Renter" :perPage="5" />

// Paired with model for customization
@piermodel([
    'model' => 'Apartment',
    'page' => 1,
    'perPage' => 10
])
    <livewire:table :$fields :rows="$data" :$page :$perPage />
    <br />
    Showing: {{ $data->count() }} of {{ $totalRows }} results
@endpiermodel
```

# Customize

## CMS

To change the theme color, app name and app logo in the CMS, update the following:

```
APP_NAME="My App"
APP_COLOR="#1F571A"
APP_LOGO=img/logo.png
```

**Note:** Similar to the sample above, the `APP_LOGO` should be a path in your public folder, Pier will automatically wrap it with `asset()`

## Pier Configs

Before you can customize pier configs, you need to first publish the package's config file that includes some defaults for us. To publish that, run the following command.

### Publish the package config

`php artisan vendor:publish --tag=pier-config`

You will now find the config file located in `/config/pier.php`

### Add prefix

Set the value of the prefix field under `config/pier.php` to your liking

### Add middleware

Set the value of the middleware field under `config/pier.php` to your liking

## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.
