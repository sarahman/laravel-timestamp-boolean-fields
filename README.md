# Dealing with the Timestamp based Boolean fields for the Laravel PHP Framework

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sarahman/laravel-timestamp-boolean-fields.svg?style=flat-square)](https://packagist.org/packages/sarahman/laravel-timestamp-boolean-fields)
[![Build Status](https://img.shields.io/travis/sarahman/laravel-timestamp-boolean-fields/master.svg?style=flat-square)](https://travis-ci.org/sarahman/laravel-timestamp-boolean-fields)
[![Quality Score](https://img.shields.io/scrutinizer/g/sarahman/laravel-timestamp-boolean-fields.svg?style=flat-square)](https://scrutinizer-ci.com/g/sarahman/laravel-timestamp-boolean-fields)
[![StyleCI](https://styleci.io/repos/178542300/shield)](https://styleci.io/repos/178542300)
[![Total Downloads](https://img.shields.io/packagist/dt/sarahman/laravel-timestamp-boolean-fields.svg?style=flat-square)](https://packagist.org/packages/sarahman/laravel-timestamp-boolean-fields)

## Introduction

This library can be used in the scenarios when you want to understand the db boolean field value as not only its status but also the time when it becomes true.

## Code Sample

```php
<?php

namespace App;

use Sarahman\Database\Support\BooleanTimestampFieldManipulator;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BooleanTimestampFieldManipulator;

    protected $fillable = ['title', 'description', 'is_active'];

    protected static $boolTimestampFields = ['is_active'];
}
```

## Installation

This trait is installed via [Composer](http://getcomposer.org/). To install, simply add to your `composer.json` file:

```bash
composer require sarahman/laravel-timestamp-boolean-fields:1.1.*
```

After installing, you would just use it in your eloquent/model class and define the timestamp-based boolean fields in the `$boolTimestampFields` property as well as in `$fillable` property.

**Note**: The timestamp-based field names must be prefixed by `is_`; and the field names without its `is_` prefix will be appended in the model attributes with `time_being_` prefix; i.e. `is_active` field name will appended in the model attributes as `time_being_active` name. The `is_active` field value will be boolean and `time_being_active` field value will the timestamp value.

## Support

If you are having general issues with this package, feel free to contact me through [Gmail](mailto:aabid048@gmail.com).

If you believe you have found an issue, please report it using the [GitHub issue tracker](https://github.com/sarahman/laravel-timestamp-boolean-fields/issues), or better yet, fork the repository and submit a pull request.

## Contribution

If you're using this package, I'd love to hear your thoughts. Thanks! Please feel free to contribute in this library and send us [pull requests](https://github.com/sarahman/laravel-timestamp-boolean-fields/pulls).

## License

The MIT License (MIT). Please see [License File](license.txt) for more information.
