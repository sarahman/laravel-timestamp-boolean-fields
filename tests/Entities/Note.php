<?php

namespace Tests\Entities;

use Illuminate\Database\Eloquent\Model;
use Sarahman\Database\Support\BooleanTimestampFieldManipulator;

class Note extends Model
{
    use BooleanTimestampFieldManipulator;

    protected $fillable = ['user_id', 'title', 'description', 'is_unpublished', 'is_reported'];

    protected static $boolTimestampFields = ['is_unpublished', 'is_reported'];
}
