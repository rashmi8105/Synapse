<?php
namespace Synapse\ReportsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Util\CommandUtils;
use Synapse\ReportsBundle\Entity\OrgCalcFlagsStudentReports;
use Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository;


class StudentReportEmailCommand extends ContainerAwareCommand
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
        $this->setName('studentReportEmail:send')
            ->setDescription('Send Student Report to Students')
            ->addArgument('totalServersRunning', InputArgument::REQUIRED, 'How many servers will be running this command in parallel (e.g., 6)?')
            ->addArgument('currentServerNumber', InputArgument::REQUIRED, 'What is the unique server number for this server (e.g., 3)');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if (CommandUtils::commandAlreadyRunning(CommandUtils::EMAIL_STUDENT_REPORT_COMMAND_KEY)) {
            $output->writeln("\nEmail Student Report Command is already running. Exiting this instance.");
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

        $this->repositoryResolver = $this->getContainer()->get('repository_resolver');
        $this->orgCalcFlagsStudentReportsRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:OrgCalcFlagsStudentReports');
        $output->writeln("Starting Email Generation for Student Survey Report.  Server " . $thisServer . " of " . $totalServers . " servers running in parallel");
        $studentsWithCompletedReports = $this->orgCalcFlagsStudentReportsRepository->getStudentsNeedingCompletedStudentReportEmail();
        $studentsWithPartiallyCompleteReports = $this->orgCalcFlagsStudentReportsRepository->getStudentsNeedingPartiallyCompleteStudentReportEmail();

        $this->sendEmailsForListOfStudents($studentsWithCompletedReports, $totalServers, $thisServer, $output, true);
        $this->sendEmailsForListOfStudents($studentsWithPartiallyCompleteReports, $totalServers, $thisServer, $output, false);

        $output->writeln("Email Student Report Completed.");

        CommandUtils::commandFinishedRunning(CommandUtils::EMAIL_STUDENT_REPORT_COMMAND_KEY);

    }


    /**
     * Sends Emails given a list of students on the appropriate server
     *
     * @param array $studentsNeedingEmailSent
     * @param int $totalServers
     * @param int $thisServer
     * @param OutputInterface $output
     * @param bool $isCompletionEmail
     * @param $thisServer
     */
    private function sendEmailsForListOfStudents($studentsNeedingEmailSent, $totalServers, $thisServer, $output, $isCompletionEmail)
    {
        if (!empty($studentsNeedingEmailSent)) {
            $serverStudentList = [];
            foreach ($studentsNeedingEmailSent as $row) {
                $studentId = $row['student_id'];
                //Determine if this job on this server should be processing the student
                if ($studentId % $totalServers === $thisServer - 1) {
                    $serverStudentList[$studentId] = $row['ocfsr_id'];
                }
            }

            foreach ($serverStudentList as $studentId => $orgCalcFlagsStudentReportsId) {
                try {
                    $output->writeln("Generate Email for student - " . $studentId);
                    $resultsArray = $this->getContainer()->get('student_report_email_service')->sendStudentReportEmails($orgCalcFlagsStudentReportsId, $studentId, $isCompletionEmail);
                    $prettyOutput = json_encode($resultsArray, JSON_PRETTY_PRINT);
                    $output->writeln($prettyOutput);
                } catch (\Exception $e) {
                    $output->writeln("\n**************** Error ******************");
                    $output->writeln($e->getMessage());
                    $output->writeln("*****************************************\n");
                }
                sleep(1); // We sleep one second because we have to throttle the emails sends and keep them below the
                          // Amazon email send limit of 14 per second. Since this job can be running on multiple
                          // servers, in multiple environments, one email send per server per second is a nice safe number.
            }
        }
    }
}