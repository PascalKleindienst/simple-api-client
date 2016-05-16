<?php
namespace Atog\Api\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EndpointCommand
 * @package Atog\Console
 */
class EndpointCommand extends AbstractScaffoldingCommand
{
    /**
     * @var string
     */
    protected $type = 'endpoint';

    /**
     * Execute command
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createFileTemplate($input, $output, $this->getApplication()->endpointPath());
    }

    /**
     * File Template
     * @param string $namespace
     * @param string $name
     * @param string $topNSPart
     * @return string
     */
    protected function template($namespace, $name, $topNSPart = "Endpoints")
    {
        $className = str_plural($name);
        $endpointName = strtolower($name);
        $namespace = "{$namespace}\\{$topNSPart}";

        return <<< EOT
<?php
namespace $namespace;

use Atog\Api\Endpoint;

/**
 * Class $className
 * @package $namespace
 */
class $className extends Endpoint
{
    /**
     * @var string
     */
    protected \$endpoint = '$endpointName';
}

EOT;
    }
}
