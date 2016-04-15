<?php namespace Atog\Api\Test;

use Atog\Api\Model;
use Illuminate\Support\Collection;

/**
 * Class ModelTest
 * @package Atog\Api\Test
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider attributesProvider
     * @param $attributes
     * @param $expected
     */
    public function testToArray($attributes, $expected)
    {
        $model = new Model($attributes);
        $this->assertEquals($expected, $model->toArray());
    }

    /**
     * @dataProvider attributesProvider
     * @param $attributes
     * @param $expected
     */
    public function testJsonSerialize($attributes, $expected)
    {
        $model = new Model($attributes);
        $this->assertEquals($expected, $model->jsonSerialize());
    }

    /**
     * @dataProvider attributesProvider
     * @param $attributes
     * @param $expected
     */
    public function testToJson($attributes, $expected)
    {
        $model = new Model($attributes);
        $this->assertJsonStringEqualsJsonString(json_encode($expected), $model->toJson());
    }

    /**
     * @dataProvider attributesProvider
     * @param $attributes
     * @param $expected
     */
    public function testToString($attributes, $expected)
    {
        $model = new Model($attributes);
        $this->assertJsonStringEqualsJsonString(json_encode($expected), $model->__toString());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFillThrowsExceptionIfAttributesAreNeitherArraysNorJSON()
    {
        new Model('boom');
    }

    public function testSetAttributeWithoutMutatorReturnsSelf()
    {
        $model = new Model();
        $this->assertInstanceOf(Model::class, $model->setAttribute('key', 'value'));
    }

    public function testSetAttributeWithMutator()
    {
        $model = $this->getMockBuilder(Model::class)->setMethods(['hasSetMutator', 'setThisTestAttribute'])->getMock();
        $model->method('hasSetMutator')->willReturn(true);
        $model->method('setThisTestAttribute')->willReturn('this test attribute');

        $this->assertEquals('this test attribute', $model->setAttribute('thisTest', 'value'));
    }

    /**
     * @dataProvider getAttributeProvider
     */
    public function testGetAttributeWithoutMutator($attrs, $get, $expect)
    {
        $model = new Model($attrs);
        $this->assertEquals($expect, $model->getAttribute($get));
    }

    /**
     * @dataProvider getAttributeProvider
     */
    public function testGetAttributeMagicMethod($attrs, $get, $expect)
    {
        $model = new Model($attrs);
        $this->assertEquals($expect, $model->$get);
    }

    public function testGetAttributeWithMutator()
    {
        $model = $this->getMockBuilder(Model::class)
            ->setMethods(['getAttributeValue', 'hasGetMutator', 'getThisTestAttribute'])
            ->getMock();
        $model->method('getAttributeValue')->willReturn('attr value');
        $model->method('hasGetMutator')->willReturn(true);
        $model->method('getThisTestAttribute')->willReturn('this test attribute');

        $this->assertEquals('this test attribute', $model->getAttribute('thisTest'));
    }

    /**
     * @depends testGetAttributeMagicMethod
     */
    public function testSetAttributeMagicMethod()
    {
        $model = new Model();
        $model->key = 'value';
        $this->assertEquals('value', $model->key);
    }

    public function testHasGetMutator()
    {
        $model = new Model();
        $this->assertFalse($model->hasGetMutator('test'));
    }

    public function testHasSetMutator()
    {
        $model = new Model();
        $this->assertFalse($model->hasSetMutator('test'));
    }

    public function testIsset()
    {
        $model = new Model(['foo' => 'bar']);
        $this->assertTrue(isset($model->foo));
        $this->assertFalse(isset($model->foobar));
    }

    /**
     * @depends testToArray
     * @dataProvider attributesProvider
     * @param $attributes
     * @param $expected
     */
    public function testNewInstance($attributes, $expected)
    {
        $model = new Model();
        $newInstance = $model->newInstance($attributes);

        $this->assertInstanceOf(Model::class, $newInstance);
        $this->assertEquals($expected, $newInstance->toArray());
    }

    /**
     * @depends testToArray
     */
    public function testMakeCollection()
    {
        $model = new Model();
        $attrs = [
            ['foo' => 'bar', 'loren' => 'ipsum', 'foobar'],
            ['foo' => 'bar', 'loren' => 'ipsum', 'foobar']
        ];

        $collection = $model->makeCollection($attrs, Model::class);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Model::class, $collection->first());
        $this->assertEquals($attrs[0], $collection->first()->toArray());
    }

    public function attributesProvider()
    {
        return [
            'array' => [['foo' => 'bar', 'loren' => 'ipsum', 'foobar'], ['foo' => 'bar', 'loren' => 'ipsum', 'foobar']],
            'json'  => ['{"foo":"bar","loren":"ipsum","0":"foobar"}', ['foo' => 'bar', 'loren' => 'ipsum', 'foobar']],
            'null'  => [null, []]
        ];
    }

    public function getAttributeProvider()
    {
        return [
            [['foo' => 'bar'], 'foo', 'bar'],
            [['foo' => 'bar'], 'fooBar', null]
        ];
    }
}
