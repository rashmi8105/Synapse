<?php
namespace Synapse\ReportsBundle\Command;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Util\CommandUtils;
use Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository;
use Synapse\ReportsBundle\Service\Impl\StudentSurveyReportService;

class StudentReportCommand extends ContainerAwareCommand
{
    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var OrgCalcFlagsStudentReportsRepository
     */
    private $orgCalcFlagsStudentReportsRepository;


    protected function configure()
    {
        $this
            ->setName('studentReport:pdf')
            ->setDescription('Generate PDF report for the student')
            ->addArgument('totalServersRunning', InputArgument::REQUIRED, 'How many servers will be running this command in parallel (e.g., 6)?')
            ->addArgument('currentServerNumber', InputArgument::REQUIRED, 'What is the unique server number for this server (e.g., 3)');
    }

    /**
     * Generates the student survey report PDF.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if (CommandUtils::commandAlreadyRunning(CommandUtils::GENERATE_STUDENT_REPORT_COMMAND_KEY)) {
            $output->writeln("\nGenerate Student Report Command is already running. Exiting this instance.");
            return;
        }

        $totalServers = $input->getArgument('totalServersRunning');

        //In the remote environments Servers are labeled by numbers,
        // with local smtp tests/setups this is not necessarily the case
        if (is_numeric($input->getArgument('currentServerNumber'))) {
            $thisServer = $input->getArgument('currentServerNumber');
        } else {
            $thisServer = 1;
        }

        $output->writeln("Starting PDF generation for Student Survey Report.  Server " . $thisServer . " of " . $totalServers . " servers running in parallel");
        try {
            $resultsArray[] = $this->getContainer()->get(StudentSurveyReportService::SERVICE_KEY)->generateStudentSurveyReport($totalServers, $thisServer);
            $prettyOutput = json_encode($resultsArray, JSON_PRETTY_PRINT);
            $output->writeln($prettyOutput);
            $output->writeln("Student Report Generation Completed.");

        } catch (\Exception $e) {
            $output->writeln("\n**************** Error ******************");
            $output->writeln($e->getMessage());
            $output->writeln("*****************************************\n");
        }


        CommandUtils::commandFinishedRunning(CommandUtils::GENERATE_STUDENT_REPORT_COMMAND_KEY);
    }

}