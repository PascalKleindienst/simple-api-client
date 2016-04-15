<?php namespace Atog\Api;

use Illuminate\Support\Collection;
use JsonSerializable;

/**
 * Class Model
 * @package Atog\Api
 */
class Model implements JsonSerializable
{
    use Jsonable;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Model constructor.
     * @param null $attributes
     */
    public function __construct($attributes = null)
    {
        if (!is_null($attributes)) {
            $this->fill($attributes);
        }
    }

    /**
     * Fill the attributes
     * @param  string|array $attributes
     * @return void
     */
    private function fill($attributes)
    {
        // json
        if (is_string($attributes)) {
            $attributes = json_decode($attributes, true);
        }

        // check if attributes are valid
        if (!is_array($attributes)) {
            throw new \InvalidArgumentException('Attributes must be of type array or a valid json string');
        }

        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * Set a given attribute on the model.
     * @param  string $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set on
        // the model, such as "json_encoding" an listing of data for storage.
        if ($this->hasSetMutator($key)) {
            $method = 'set' . studly_case($key) . 'Attribute';
        
            return $this->{$method}($value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Determine if a set mutator exists for an attribute.
     * @param  string $key
     * @return bool
     */
    public function hasSetMutator($key)
    {
        return method_exists($this, 'set' . studly_case($key) . 'Attribute');
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * Get an attribute from the $attributes array.
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = $this->getAttributeValue($key);
    
        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set.
        if ($this->hasGetMutator($key)) {
            $method = 'get' . studly_case($key) . 'Attribute';
        
            return $this->{$method}($value);
        }

        return $value;
    }
    
    /**
     * Get an attribute from the $attributes array.
     * @param  string $key
     * @return mixed
     */
    protected function getAttributeValue($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return null;
    }

    /**
     * Determine if a get mutator exists for an attribute.
     * @param  string $key
     * @return bool
     */
    public function hasGetMutator($key)
    {
        return method_exists($this, 'get' . studly_case($key) . 'Attribute');
    }
    
    /**
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Transform an array of values into a collection of models
     * @param array $values
     * @param null  $class
     * @return \Illuminate\Support\Collection
     */
    protected function makeCollection(array $values, $class = null)
    {
        $collection = new Collection($values);
    
        if (!is_null($class) && class_exists($class)) {
            $model = new $class();
        
            if ($model instanceof Model) {
                foreach ($collection as $key => $item) {
                    $collection[$key] = $model->newInstance($item);
                }
            }
        }
    
        return $collection;
    }

    /**
     * Create a new model instance
     * @param array $attributes
     * @return \Atog\Api\Model
     * @throws \InvalidArgumentException if attributes is not an array or a json object
     */
    public function newInstance($attributes = [])
    {
        return new static($attributes);
    }
}
