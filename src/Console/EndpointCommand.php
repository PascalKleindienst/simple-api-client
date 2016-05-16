<?php
namespace Atog\Api\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EndpointCommand
 * @package Atog\Console
 */
class EndpointCommand extends Command
{
    /**
     * Command Config
     */
    protected function configure()
    {
        $this
            ->setName('create:endpoint')
            ->setDescription('Create a new Endpoint')
            ->addArgument(
                'namespace',
                InputArgument::REQUIRED,
                'Namespace in Dot-Notation e.g. Acme.Api'
            )
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the Endpoint'
            )
        ;
    }

    /**
     * Execute command
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // args
        $namespace = str_replace('.', '\\', $input->getArgument('namespace'));
        $name = $input->getArgument('name');
        $path = $this->getApplication()->endpointPath();
        $file = $path . str_plural($name) . '.php';

        // only write file if path exists and file does not exist yet
        if (file_exists($path) && !file_exists($file)) {
            // write file
            $endpoint = fopen($file, "w+");
            fwrite($endpoint, $this->template($namespace, $name));
            fclose($endpoint);

            // output
            $output->writeln("<info>Created Endpoint: $name</info>");
            return;
        }

        // error
        $output->writeln("<error>Could not create file $file at $path</error>");
    }

    /**
     * File Template
     * @param string $namespace
     * @param string $name
     * @param string $endpointNS
     * @return string
     */
    protected function template($namespace, $name, $endpointNS = "Endpoints")
    {
        $className = str_plural($name);
        $endpointName = strtolower($name);
        $namespace = "{$namespace}\\{$endpointNS}";

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
