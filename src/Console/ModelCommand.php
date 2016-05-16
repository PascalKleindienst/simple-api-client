<?php
namespace Atog\Api\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ModelCommand
 * @package Atog\Console
 */
class ModelCommand extends Command
{
    /**
     * Command Config
     */
    protected function configure()
    {
        $this
            ->setName('create:model')
            ->setDescription('Create a new Model')
            ->addArgument(
                'namespace',
                InputArgument::REQUIRED,
                'Namespace in Dot-Notation e.g. Acme.Api'
            )
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the Model'
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
        $path = $this->getApplication()->modelPath();
        $file = $path . str_plural($name) . '.php';

        // only write file if path exists and file does not exist yet
        if (file_exists($path) && !file_exists($file)) {
            // write file
            $endpoint = fopen($file, "w+");
            fwrite($endpoint, $this->template($namespace, $name));
            fclose($endpoint);

            // output
            $output->writeln("<info>Created Model: $name</info>");
            return;
        }

        // error
        $output->writeln("<error>Could not create file $file at $path</error>");
    }

    /**
     * File Template
     * @param string $namespace
     * @param string $name
     * @param string $modelNS
     * @return string
     */
    protected function template($namespace, $name, $modelNS = "Models")
    {
        $className = str_plural($name);
        $namespace = "{$namespace}\\{$modelNS}";

        return <<< EOT
<?php
namespace $namespace;

use Atog\Api\Model;

/**
 * Class $className
 * @package $namespace
 */
class $className extends Model
{
}

EOT;
    }
}
