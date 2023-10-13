<?php

namespace Tests\Entities;

use Illuminate\Database\Eloquent\Model;
use Sarahman\Database\Support\BooleanTimestampFieldManipulator;

/**
 * Class `Note` is a sample eloquent model.
 *
 * @property int    user_id
 * @property string title
 * @property string description
 * @property bool   is_unpublished
 * @property bool   is_reported
 * @property null|string time_being_unpublished
 * @property null|string time_being_reported
 */
class Note extends Model
{
    use BooleanTimestampFieldManipulator;

    protected $fillable = ['user_id', 'title', 'description', 'is_unpublished', 'is_reported'];

    protected static $boolTimestampFields = ['is_unpublished', 'is_reported'];
}
