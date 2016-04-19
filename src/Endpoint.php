<?php namespace Atog\Api;

use Symfony\Component\HttpFoundation\Response;

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
     * @return \Atog\Api\Model
     */
    public function getModel()
    {
        return $this->model;
    }
    
    /**
     * Get the endpoint url for the request
     * @param string $path
     * @param bool   $withTrailingSlash
     * @return string
     */
    public function getEndpointUrl($path, $withTrailingSlash)
    {
        $url = $this->endpoint . '/' . $path;
        
        if ($withTrailingSlash) {
            $url = $url . '/';
        }
        
        return $url;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return \Atog\Api\Model|null
     */
    public function respond(Response $response)
    {
        // return new model instance with fetched content if response is okay
        if ($response->isOk()) {
            return $this->model->newInstance($response->getContent());
        }

        return null;
    }
}
