<?php namespace Peek\Api;

/**
 * Abstract Class Endpoint
 * @package Peek\Api
 */
abstract class Endpoint
{
    /**
     * @var \Peek\Api\Client
     */
    protected $client;
    
    /**
     * @var \Peek\Api\Model
     */
    protected $model;
    
    /**
     * @var string
     */
    protected $endpoint;
    
    /**
     * Endpoint constructor.
     * @param \Peek\Api\Client $client
     * @param \Peek\Api\Model  $model
     */
    public function __construct(Client $client, Model $model)
    {
        $this->client = $client;
        $this->model = $model;
    }
    
    /**
     * Get the endpoint url for the request
     * @param string $path
     * @param bool   $withTrailingSlash
     * @return string
     */
    protected function getEndpointUrl($path, $withTrailingSlash)
    {
        $url = $this->endpoint . '/' . $path;
        
        if ($withTrailingSlash) {
            $url = $url . '/';
        }
        
        return $url;
    }
}
