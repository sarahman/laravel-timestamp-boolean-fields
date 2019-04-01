<?php

namespace Tests\Entities;

use Illuminate\Database\Eloquent\Model;
use Sarahman\Database\Support\BooleanTimestampFieldManipulator;

class User extends Model
{
    use BooleanTimestampFieldManipulator;

    protected $fillable = ['name', 'is_active'];

    protected $boolTimestampFields = ['is_active'];
}
