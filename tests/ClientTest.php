<?php namespace Atog\Api\Test;

use Atog\Api\Client;
use Atog\Api\Endpoint;
use Atog\Api\Model;
use Atog\Api\Test\Endpoints\InvalidEndpoint;
use Atog\Api\Test\Endpoints\ValidEndpoint;
use Jyggen\Curl\Response;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use ReflectionClass;

class ModelForEndpoint extends Model {}

/**
 * Class ClientTest
 * @package Atog\Api\Test
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Atog\Api\Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = $this->getMockForAbstractClass(
            Client::class,
            [
                ['InvalidEndpoint' => InvalidEndpoint::class, ValidEndpoint::class],
                ['secret' => 'foobar', 'curl' => [CURLOPT_TIMEOUT => 60]]
            ]
        );
        $reflection = new ReflectionClass(Client::class);
        $this->setObjectAttribute($this->client, $reflection, 'domain', 'http://example.com');
    }

    protected function setObjectAttribute(MockObject $mock, ReflectionClass $reflection, $property, $value)
    {
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($mock, $value);
    }
    
    public function testGetConfig()
    {
        $this->assertEquals(
            ['secret' => 'foobar', 'curl' => [CURLOPT_TIMEOUT => 60]],
            $this->client->getConfig()
        );
    }

    /**
     * @expectedException \Atog\Api\Exceptions\InvalidEndpointException
     */
    public function testSetEndpointsWithInvalidClass()
    {
        $this->getMockForAbstractClass(
            Client::class,
            [
                ['invalid', 'InvalidClass']
            ]
        );
    }

    /**
     * @expectedException \Atog\Api\Exceptions\InvalidEndpointException
     */
    public function testGetEndpointClassDoesNotExists()
    {
        $this->client->getEndpoint('Foo');
    }

    /**
     * @expectedException \Atog\Api\Exceptions\InvalidEndpointException
     */
    public function testGetEndpointIsNotValidSubclass()
    {
        $this->client->getEndpoint('InvalidEndpoint');
    }

    public function testGetEndpointValid()
    {
        $client = $this->getMockForAbstractClass(
            Client::class,
            [
                [ValidEndpoint::class],
                ['models' => ['ValidEndpoint' => ModelForEndpoint::class]]
            ]
        );
        $endpoint = $client->getEndpoint('ValidEndpoint');
        $this->assertInstanceOf(ValidEndpoint::class, $endpoint);
        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertInstanceOf(ModelForEndpoint::class, $endpoint->getModel());
    }

    /**
     * @depends testGetEndpointValid
     */
    public function testGetEndpointCached()
    {
        $client = $this->getMockForAbstractClass(
            Client::class,
            [
                [ValidEndpoint::class],
                ['models' => ['ValidEndpoint' => ModelForEndpoint::class]]
            ]
        );

        // check if cached
        $endpoint = $client->getEndpoint('ValidEndpoint');
        $reflection = new ReflectionClass(Client::class);
        $property = $reflection->getProperty('cachedEndpoints');
        $property->setAccessible(true);
        $endpointVal = $property->getValue($client);

        $this->assertArrayHasKey('ValidEndpoint', $endpointVal);
        $this->assertInstanceOf(ValidEndpoint::class, $endpointVal['ValidEndpoint']);

        // get cached one
        $cached = $client->getEndpoint('ValidEndpoint');
        $this->assertEquals($cached, $endpoint);
    }

    /**
     * @expectedException \Atog\Api\Exceptions\InvalidEndpointException
     */
    public function testGetEndpointMagicMethodClassDoesNotExists()
    {
        $this->client->foo;
    }

    /**
     * @expectedException \Atog\Api\Exceptions\InvalidEndpointException
     */
    public function testGetEndpointMagicMethodIsNotValidSubclass()
    {
        $this->client->invalidEndpoint;
    }

    public function testGetEndpointMagicMethodValid()
    {
        $client = $this->getMockForAbstractClass(
            Client::class,
            [
                [ValidEndpoint::class],
                ['models' => ['ValidEndpoint' => ModelForEndpoint::class]]
            ]
        );
        $endpoint = $client->validEndpoint;
        $this->assertInstanceOf(ValidEndpoint::class, $endpoint);
        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertInstanceOf(ModelForEndpoint::class, $endpoint->getModel());
    }

    /**
     * @depends testGetEndpointValid
     */
    public function testGetEndpointtMagicMethodCached()
    {
        $client = $this->getMockForAbstractClass(
            Client::class,
            [
                [ValidEndpoint::class],
                ['models' => ['ValidEndpoint' => ModelForEndpoint::class]]
            ]
        );

        // check if cached
        $endpoint = $client->validEndpoint;
        $reflection = new ReflectionClass(Client::class);
        $property = $reflection->getProperty('cachedEndpoints');
        $property->setAccessible(true);
        $endpointVal = $property->getValue($client);

        $this->assertArrayHasKey('ValidEndpoint', $endpointVal);
        $this->assertInstanceOf(ValidEndpoint::class, $endpointVal['ValidEndpoint']);

        // get cached one
        $cached = $client->validEndpoint;
        $this->assertEquals($cached, $endpoint);
    }

    public function testGetRequest()
    {
        $response = $this->client->get('');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $response);
    }
}
