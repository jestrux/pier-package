# Pier

Headless CMS and API generator for Laravel projects.

# Demo of how Pier works

- https://vimeo.com/manage/videos/737768985

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
MIX_UNSPLASH_CLIENT_ID=
MIX_S3_BUCKET=
MIX_S3_REGION=
MIX_S3_ACCESS_KEY_ID=
MIX_S3_SECRET_ACCESS_KEY=
```

# Customize

Before you can customize configs, you need to first publish the package's config file that includes some defaults for us. To publish that, run the following command.

### Publish the package config
`php artisan vendor:publish --tag=pier-config`

You will now find the config file located in `/config/pier.php`

### Add prefix

Set the value of the prefix field under `config/pier.php` to your liking

### Add middleware

Set the value of the middleware field under `config/pier.php` to your liking

## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.
