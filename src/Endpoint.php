<?php namespace Atog\Api;

/**
 * Abstract Class Endpoint
 * @package Atog\Api
 */
abstract class Endpoint
{
    /**
     * @var \Atog\Api\Client
     */
    protected $client;
    
    /**
     * @var \Atog\Api\Model
     */
    protected $model;
    
    /**
     * @var string
     */
    protected $endpoint;
    
    /**
     * Endpoint constructor.
     * @param \Atog\Api\Client $client
     * @param \Atog\Api\Model  $model
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
