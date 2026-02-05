# Quickly Generate Laravel Mini Crud Generator

## Installation

You can install this package via composer:

```bash
composer require davion190510/mini-crud-generator:dev-main
```

after installation was done. Add in " app.php ":

```
Davion190510\MiniCRUDGenerator\MiniCRUDGeneratorServiceProvider::class,
```

and after you can run following command to publish config files:

```
php artisan vendor:publish --tag=config
```

## :gear: Configuration

for configure this package go to `config/minicrud.php` and if you want to customize namespace you can do like this

```php
<?php

return [
    'namespace' => 'Laravel',
    'modules' => 'modules',
];
```

add line in `composer.json` `autoload` block

```
"autoload": {
        "psr-4": {
            //
            "Laravel\\": "modules/"
        }
    },
```

and then

```
composer dump-autoload
```

## Usage

```
php artisan make:coreFeature--all

php artisan make:coreFeature--logic

but you need to create folder first admin/demo

php artisan add-fields-to-view --model=Demo

```

## Credits

- [Nay Ba La](https://github.com/naybala)
- [All Contributors](../../contributors)

## :scroll: License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Packagist

This package was register and publish at ( https://packagist.org/ )
