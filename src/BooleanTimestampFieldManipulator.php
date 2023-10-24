<?php

namespace Sarahman\Database\Support;

use Carbon\Carbon;

/**
 * This trait deals with the boolean-cum timestamp field.
 *
 * @property array $boolTimestampFields
 * @property array $attributes
 * @property array $casts
 *
 * @method self           append(array $attributes)
 * @method mixed|array    getOriginal(string $key = null, mixed $default = null)
 * @method \Carbon\Carbon asDateTime(mixed $value)
 * @method self           syncOriginal()
 *
 * @see \Illuminate\Database\Eloquent\Model
 */
trait BooleanTimestampFieldManipulator
{
    /**
     * Boot the trait.
     *
     * By booting this trait, it listens for the saving event of a model and
     * append the manipulated attribute values depending on the fields of defined $boolTimestampFields array.
     *
     * @throws \LogicException
     */
    protected static function bootBooleanTimestampFieldManipulator()
    {
        static::saving(function (self $model) {
            foreach ((array) $model::$boolTimestampFields as $field) {
                unset($model->attributes[$model->getAppendableAttributeName($field)]);
            }
        });

        static::saved(function (self $model) {
            $model->setBoolTimestampFieldsIntoAttributes($model);
        });

        if (method_exists(__CLASS__, 'retrieved')) {
            static::retrieved(function (self $model) {
                $model->setBoolTimestampFieldsIntoAttributes($model);
            });
        }
    }

    public function initializeBooleanTimestampFieldManipulator()
    {
        foreach ((array) self::$boolTimestampFields as $field) {
            $this->casts[$field] = 'boolean';
            $this->casts[$this->getAppendableAttributeName($field)] = 'datetime';
        }
    }

    public function setAttribute($key, $value)
    {
        if (count(self::$boolTimestampFields) && in_array($key, self::$boolTimestampFields)) {
            if ($value) {
                $this->attributes[$key] = ($original = $this->getOriginal($key)) ? $original : Carbon::now();
            } else {
                $this->attributes[$key] = null;
            }

            return;
        }

        parent::setAttribute($key, $value);
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param string $key
     * @param mixed  $value
     *
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

    public function syncOriginal()
    {
        $result = parent::syncOriginal();

        $this->setBoolTimestampFieldsIntoAttributes($this, true);

        return $result;
    }

    private static function getAppendableAttributeName($field)
    {
        return 'time_being_'.preg_replace('/^is_/', '', $field);
    }

    private function setBoolTimestampFieldsIntoAttributes(self $model, $checkAttributeExists = false)
    {
        foreach ((array) self::$boolTimestampFields as $field) {
            if ($checkAttributeExists && !array_key_exists($field, $model->attributes)) {
                continue;
            }

            $original = $model->getOriginal($field);
            $model->attributes[$field] = !empty($original);
            $model->attributes[$model->getAppendableAttributeName($field)] = $original ? $model->asDateTime($original) : $original;
        }
    }
}
