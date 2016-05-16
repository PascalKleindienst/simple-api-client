<?php
namespace Atog\Api\Console;

/**
 * Class EndpointCommand
 * @package Atog\Console
 */
class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var array
     */
    protected $dirs = [
        'endpoint'  => '',
        'model'     => ''
    ];

    /**
     * Application constructor.
     * @param string $name
     * @param string $version
     */
    public function __construct($name = "Atog.Simple-Api-Client", $version = "v1.0")
    {
        parent::__construct($name, $version);

        // set paths
        $sep = DIRECTORY_SEPARATOR;
        $this->basePath = __DIR__ . "$sep..$sep..$sep";
        $this->dirs['endpoint'] = "src{$sep}Endpoints";
        $this->dirs['model'] = "src{$sep}Models";

        // add commands
        $this->add(new EndpointCommand());
        $this->add(new ModelCommand());
    }

    /**
     * @param string $path
     * @return string
     */
    public function endpointPath($path = "")
    {
        return $this->basePath . $this->dirs['endpoint'] . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @param string $path
     * @return string
     */
    public function modelPath($path = "")
    {
        return $this->basePath . $this->dirs['model'] . DIRECTORY_SEPARATOR . $path;
    }
}
