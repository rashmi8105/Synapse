<?php
use Codeception\Util\Stub;
use Synapse\UploadBundle\Command\StudentUploadCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class StudentUploadCommandTest extends \Codeception\Test\Unit
{

    /**
    * @var UnitTester
    */
    protected $tester;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
    }

    // public function testHasSuccessfulOutput()
    // {
    //     $application = new Application($this->getModule('Symfony2')->kernel);
    //     $application->add(new StudentUploadCommand());

    //     $command = $application->find('studentUpload:process');
    //     $commandTester = new CommandTester($command);
    //     $commandTester->execute(
    //         array('command' => $command->getName(), 'file' => 'tests/_data/student_upload_file.csv', 'orgId' => 1)
    //     );

    //     $this->assertRegExp('/.*created.*/', $commandTester->getDisplay());
    // }

    // /**
    //  * @expectedException     Exception
    //  * @expectedExceptionMessage CSV file not found
    //  */
    // public function testExceptionInvalidCSV()
    // {
    //     $application = new Application($this->getModule('Symfony2')->kernel);
    //     $application->add(new StudentUploadCommand());

    //     $command = $application->find('studentUpload:process');
    //     $commandTester = new CommandTester($command);
    //     $commandTester->execute(
    //         array('command' => $command->getName(), 'file' => 'invalid.csv', 'orgId' => 1)
    //     );
    // }

    /**
     * {@inheritDoc}
     */
    protected function _after()
    {

    }
}