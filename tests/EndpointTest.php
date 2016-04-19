<?php namespace Atog\Api\Test;

use Atog\Api\Client;
use Atog\Api\Endpoint;
use Atog\Api\Model;
use Symfony\Component\HttpFoundation\Response;

class EndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Atog\Api\Endpoint
     */
    protected $endpoint;

    public function setUp()
    {
        $model = $this->getMock(Model::class);
        $model->method('newInstance')
            ->will($this->returnArgument(0));

        $this->endpoint = $this->getMockForAbstractClass(
            Endpoint::class,
            [
                $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMockForAbstractClass(),
                $model
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
    
    public function testRespond()
    {
        $model = $this->endpoint->respond(new Response('{"foo":"bar","loren":"ipsum","0":"foobar"}'));
        $this->assertEquals('{"foo":"bar","loren":"ipsum","0":"foobar"}', $model);
    }

    public function testRespondReturnsNullIfNotOkay()
    {
        $model = $this->endpoint->respond(new Response('{ "foo": "bar" }', 404));
        $this->assertNull($model);
    }
}
