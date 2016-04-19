<?php namespace Atog\Api;

use Atog\Api\Exceptions\InvalidEndpointException;
use Jyggen\Curl\Request;

/**
 * Abstract Client
 * @package Atog\Api
 */
abstract class Client
{
    /**
     * Endpoints
     * @var string
     */
    protected $endpoints = [];
    
    /**
     * @var string
     */
    protected $domain;
    
    /**
     * @var array
     */
    protected $config;

    /**
     * A array containing the cached endpoints
     * @var array
     */
    private $cachedEndpoints = [];
    
    /**
     * Create a new client instance.
     * @param array $endpoints
     * @param array $config
     */
    public function __construct(array $endpoints, array $config = [])
    {
        $this->setEndpoints($endpoints);
        $this->config = $config;
    }

    /**
     * @param array $endpoints
     * @throws \Atog\Api\Exceptions\InvalidEndpointException
     */
    protected function setEndpoints(array $endpoints)
    {
        foreach ($endpoints as $key => $endpoint) {
            // check if class exists
            if (!class_exists($endpoint)) {
                throw new InvalidEndpointException("Class {$endpoint} does not exists");
            }

            // get key
            if (!is_string($key)) {
                $parts = explode('\\', $endpoint);
                $key = end($parts);
            }

            // save
            $this->endpoints[studly_case($key)] = $endpoint;
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Make a GET Request to an endpoint
     * @param       $endpoint
     * @param array $params
     * @return \Jyggen\Curl\Response
     * @throws \Jyggen\Curl\Exception\CurlErrorException
     * @throws \Jyggen\Curl\Exception\ProtectedOptionException
     */
    public function get($endpoint, $params = [])
    {
        // build url
        $url = $this->domain . '/' . $endpoint;

        if (!empty($params)) {
            $url = $url . '?' . http_build_query($params);
        }

        $request = new Request($url);
        
        // Add client secret header
        if (isset($this->config['secret'])) {
            $request->headers->add(['X-Client-Secret' => $this->config['secret']]);
        }
        
        // add curl options
        if (isset($this->config['curl']) && is_array($this->config['curl'])) {
            foreach ($this->config['curl'] as $option => $value) {
                $request->setOption($option, $value);
            }
        }
        
        $request->execute();

        return $request->getResponse();
    }
    
    /**
     * Get an API endpoint
     * @param string $endpoint
     * @return \Atog\Api\Endpoint
     * @throws \Atog\Api\Exceptions\InvalidEndpointException
     */
    public function __get($endpoint)
    {
        return $this->getEndpoint($endpoint);
    }
    
    /**
     * Get an API endpoint.
     * @param string $endpoint
     * @return \Atog\Api\Endpoint
     * @throws \Atog\Api\Exceptions\InvalidEndpointException
     */
    public function getEndpoint($endpoint)
    {
        // Get Endpoint Class name
        $endpoint = studly_case($endpoint);

        if (!array_key_exists($endpoint, $this->endpoints)) {
            throw new InvalidEndpointException("Endpoint {$endpoint} does not exists");
        }

        $class = $this->endpoints[$endpoint];

        // Check if an instance has already been initiated
        if (isset($this->cachedEndpoints[$endpoint]) === false) {
            // check if class is an EndPoint
            $endpointClass = new \ReflectionClass($class);
            if (!$endpointClass->isSubclassOf('Atog\Api\Endpoint')) {
                throw new InvalidEndpointException("Class {$class} does not extend Atog\\Api\\Endpoint");
            }
            
            // check for model
            $model = new Model();
            if (array_key_exists('models', $this->config) && array_key_exists($endpoint, $this->config['models'])) {
                $modelClass = $this->config['models'][$endpoint];
                if (class_exists($modelClass)) {
                    $model = new $modelClass();
                }
            }
            $this->cachedEndpoints[$endpoint] = new $class($this, $model);
        }
        
        return $this->cachedEndpoints[$endpoint];
    }
}
