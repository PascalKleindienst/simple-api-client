<?php namespace Atog\Api\Test;

use Atog\Api\Model;

/**
 * Class ModelTest
 * @package Atog\Api\Test
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider attributesProvider
     * @param $attributes
     */
    public function testToArray($attributes)
    {
        $model = new Model($attributes);
        $this->assertEquals($attributes, $model->toArray());
    }

    /**
     * @dataProvider attributesProvider
     * @param $attributes
     */
    public function testJsonSerialize($attributes)
    {
        $model = new Model($attributes);
        $this->assertEquals($attributes, $model->jsonSerialize());
    }

    /**
     * @dataProvider attributesProvider
     * @param $attributes
     * @param $json
     */
    public function testToJson($attributes, $json)
    {
        $model = new Model($attributes);
        $this->assertJsonStringEqualsJsonString($json, $model->toJson());
    }

    /**
     * @dataProvider attributesProvider
     * @param $attributes
     * @param $json
     */
    public function testToString($attributes, $json)
    {
        $model = new Model($attributes);
        $this->assertJsonStringEqualsJsonString($json, $model->__toString());
    }

    public function attributesProvider()
    {
        return [
            [['foo' => 'bar', 'loren' => 'ipsum', 'foobar'], '{"foo":"bar","loren":"ipsum","0":"foobar"}']
        ];
    }
}
