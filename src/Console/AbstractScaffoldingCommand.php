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
abstract class AbstractScaffoldingCommand extends Command
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Command Config
     */
    protected function configure()
    {
        $this
            ->setName('create:' . $this->type)
            ->setDescription('Create a new ' . ucfirst($this->type))
            ->addArgument(
                'namespace',
                InputArgument::REQUIRED,
                'Namespace in Dot-Notation e.g. Acme.Api'
            )
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the ' . ucfirst($this->type)
            )
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string                                            $path
     */
    protected function createFileTemplate(InputInterface $input, OutputInterface $output, $path)
    {
        // args
        $namespace = str_replace('.', '\\', $input->getArgument('namespace'));
        $name = $input->getArgument('name');
        $filename = $path . str_plural($name) . '.php';

        // only write file if path exists and file does not exist yet
        if (file_exists($path) && !file_exists($filename)) {
            // write file
            $file = fopen($filename, "w+");
            fwrite($file, $this->template($namespace, $name));
            fclose($file);

            // output
            $output->writeln("<info>Created {$this->type}</info>");
            return;
        }

        // error
        $output->writeln("<error>Could not create file $filename at $path</error>");
    }

    /**
     * File Template
     * @param string $namespace
     * @param string $name
     * @param string $topNSPart
     * @return string
     */
    abstract protected function template($namespace, $name, $topNSPart = "");
}
