# Dealing with the Timestamp based Boolean fields for the Laravel PHP Framework
## v1.0.0

## Introduction

In scenarios when you want to understand the db boolean field value as not only its status but also the time
 when it becomes true.

Normally, you would just use it in your model class and define the timestamp-based boolean fields in the
 `$boolTimestampFields` attribute.

**Note**: The timestamp-based field names must be prefixed by `is_`; and the field names without its `is_` prefix will 
 be appended in the model attributes with `time_being_` prefix; i.e. `is_active` field name will appended in the model
 attributes as `time_being_active` name. The `is_active` field value will be boolean and `time_being_active` field value
 will the timestamp value.

## Code Sample

```php
<?php

namespace App;

use Sarahman\Database\Support\BooleanTimestampFieldManipulator;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BooleanTimestampFieldManipulator;

    protected $boolTimestampFields = ['is_active'];
}
```


## Installation

This trait is installed via [Composer](http://getcomposer.org/). To install, simply add to your `composer.json` file:

```
$ composer require sarahman/laravel-timestamp-boolean-fields
```

## Support

If you are having general issues with this package, feel free to contact me through [Gmail](mailto:aabid048@gmail.com).

If you believe you have found an issue, please report it using the [GitHub issue tracker](https://github.com/sarahman/laravel-timestamp-boolean-fields/issues), or better yet, fork the repository and submit a pull request.

If you're using this package, I'd love to hear your thoughts. Thanks!
