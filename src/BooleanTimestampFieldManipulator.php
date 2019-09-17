<?php

namespace Sarahman\Database\Support;

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
        static::saving(function (self $model) {
            if (count($model::$boolTimestampFields)) {
                foreach ($model::$boolTimestampFields as $field) {
                    unset($model->attributes[$model->getAppendableAttributeName($field)]);
                }
            }
        });

        static::saved(function (self $model) {
            if (count($model::$boolTimestampFields)) {
                foreach ($model::$boolTimestampFields as $field) {
                    if (!isset($model->attributes[$field])) {
                        continue;
                    }

                    $model->attributes[$model->getAppendableAttributeName($field)] = $model->attributes[$field];
                }
            }
        });

        static::retrieved(function (self $model) {
            if (count($model::$boolTimestampFields)) {
                foreach ($model::$boolTimestampFields as $field) {
                    $model->attributes[$model->getAppendableAttributeName($field)] = ($original = $model->getOriginal($field)) ? $model->asDateTime($original) : $original;
                }
            }
        });
    }

    public function initializeBooleanTimestampFieldManipulator()
    {
        if (count(self::$boolTimestampFields)) {
            foreach (self::$boolTimestampFields as $field) {
                $this->casts[$field] = 'boolean';
                $this->casts[$this->getAppendableAttributeName($field)] = 'datetime';
            }
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
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        if (count(self::$boolTimestampFields)) {
            if (in_array($key, self::$boolTimestampFields)) {
                return !empty($value);
            } elseif (in_array(preg_replace('/^time_being_/', 'is_', $key), self::$boolTimestampFields)) {
                return empty($value) ? null : $this->asDateTime($value);
            }
        }

        return parent::castAttribute($key, $value);
    }

    private static function getAppendableAttributeName($field)
    {
        return 'time_being_' . preg_replace('/^is_/', '', $field);
    }
}
