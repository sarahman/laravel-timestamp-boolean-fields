<?php

namespace Sarahman\Database\Support;

use Illuminate\Support\Str;

/**
 * Trait BooleanTimestampFieldManipulator
 *
 * @package Sarahman\Database\Support
 * @property array $boolTimestampFields
 * @property array $attributes
 * @property array $casts
 * @method self append($attributes)
 * @method mixed|array getOriginal($key = null, $default = null)
 */
trait BooleanTimestampFieldManipulator
{
    /**
     * Boot the trait.
     *
     * Listen for the saving event of a model, and append the manipulated attribute values
     * depending on the fields of defined $boolTimestampFields array.
     *
     * @throws \LogicException
     */
    protected static function bootBooleanTimestampFieldManipulator()
    {
        static::saved(function (self $model) {
            if (count($model::$boolTimestampFields)) {
                foreach ($model::$boolTimestampFields AS $field) {
                    $model->attributes[$model->getAppendAttributeName($field)] = $model->attributes[$field];
                }
            }
        });
    }

    public function initializeBooleanTimestampFieldManipulator()
    {
        if (count(self::$boolTimestampFields)) {
            $temp = [];
            foreach (self::$boolTimestampFields AS $field) {
                $temp[] = $this->getAppendAttributeName($field);
                $this->casts[$field] = 'boolean';
            }

            $this->append($temp);
        }
    }

    public function setAttribute($key, $value)
    {
        if (count(self::$boolTimestampFields) && in_array($key, self::$boolTimestampFields)) {
            if ($value) {
                $this->attributes[$key] = ($original = $this->getOriginal($key)) ? $original : \Carbon\Carbon::now();
            } else {
                $this->attributes[$key] = null;
            }

            return $this->attributes[$key];
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (count(self::$boolTimestampFields)) {
            foreach (self::$boolTimestampFields AS $field) {
                if ($method == 'get' . Str::studly($this->getAppendAttributeName($field)) . 'Attribute') {
                    return $this->attributes[$field];
                }
            }
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        if (count(self::$boolTimestampFields) && in_array($key, self::$boolTimestampFields)) {
            return !empty($value);
        }

        return parent::castAttribute($key, $value);
    }

    private static function getAppendAttributeName($field)
    {
        return 'time_being_' . preg_replace('/^is_/', '', $field);
    }
}
