<?php namespace Atog\Api\Test;

use Atog\Api\Client;
use Atog\Api\Endpoint;
use Atog\Api\Model;

class EndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Atog\Api\Endpoint
     */
    protected $endpoint;

    public function setUp()
    {
        $this->endpoint = $this->getMockForAbstractClass(
            Endpoint::class,
            [
                $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMockForAbstractClass(),
                $this->getMock(Model::class)
            ]
        );
    }

    public function testGetModel()
    {
        $this->assertInstanceOf(Model::class, $this->endpoint->getModel());
    }

    public function testGetEndpointUrl()
    {
        // set endpoint
        $reflection = new \ReflectionClass(Endpoint::class);
        $reflectionProperty = $reflection->getProperty('endpoint');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->endpoint, 'foo');

        // test
        $this->assertEquals('foo/bar', $this->endpoint->getEndpointUrl('bar', false));
        $this->assertEquals('foo/bar/', $this->endpoint->getEndpointUrl('bar', true));
    }
}
