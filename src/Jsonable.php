<?php namespace Peek\Api;

/**
 * Trait Jsonable implements JsonSerializable interface
 * @package Peek\Api
 */
trait Jsonable
{
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
