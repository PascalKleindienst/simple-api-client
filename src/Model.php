<?php namespace Peek\Api;

use JsonSerializable;

class Model implements JsonSerializable
{
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
        if (is_array($attributes)) {
            $this->fill($attributes);
        }
    }

    /**
     * Fill the attributes
     * @param  array $attributes
     * @return void
     */
    private function fill(array $attributes)
    {
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
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
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
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray(), true);
    }
}
