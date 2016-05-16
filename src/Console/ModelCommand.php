<?php
namespace Atog\Api\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ModelCommand
 * @package Atog\Console
 */
class ModelCommand extends AbstractScaffoldingCommand
{
    /**
     * @var string
     */
    protected $type = 'model';

    /**
     * Execute command
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createFileTemplate($input, $output, $this->getApplication()->modelPath());
    }

    /**
     * File Template
     * @param string $namespace
     * @param string $name
     * @param string $topNSPart
     * @return string
     */
    protected function template($namespace, $name, $topNSPart = "Models")
    {
        $className = str_plural($name);
        $namespace = "{$namespace}\\{$topNSPart}";

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
