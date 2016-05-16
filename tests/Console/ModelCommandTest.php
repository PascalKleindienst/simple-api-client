<?php namespace Atog\Api\Test;

use Atog\Api\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ModelCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        mkdir(__DIR__ . '/../../src/Models/');
    }

    public function tearDown()
    {
        unlink(__DIR__ . '/../../src/Models/Tests.php');
        rmdir(__DIR__ . '/../../src/Models/');
    }

    public function testExecuteSuccess()
    {
        // setup
        $application = new Application();
        $command = $application->find('create:model');
        $commandTester = new CommandTester($command);

        // run
        $commandTester->execute([
            'command'   => $command->getName(),
            'namespace' => 'Acme.Api',
            'name'      => 'Test'
        ]);

        // check
        $this->assertRegExp('/Created model/', $commandTester->getDisplay());
        $this->assertFileExists(__DIR__ . '/../../src/Models/Tests.php');
    }

    /**
     * @depends testExecuteSuccess
     */
    public function testExecuteFailsBecauseFileAlreadyExists()
    {
        // setup
        $application = new Application();
        $command = $application->find('create:model');
        $commandTester = new CommandTester($command);

        // run
        $commandTester->execute([
            'command'   => $command->getName(),
            'namespace' => 'Acme.Api',
            'name'      => 'Test'
        ]);
        $commandTester->execute([
            'command'   => $command->getName(),
            'namespace' => 'Acme.Api',
            'name'      => 'Test'
        ]);

        // check
        $this->assertRegExp('/Could not create file Test/', $commandTester->getDisplay());
        $this->assertFileExists(__DIR__ . '/../../src/Models/Tests.php');
    }
}
