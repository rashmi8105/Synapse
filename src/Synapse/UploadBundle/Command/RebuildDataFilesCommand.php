<?php

namespace Synapse\UploadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use JMS\DiExtraBundle\Annotation as DI;

use Synapse\CoreBundle\Util\CSVFile;
use Synapse\CoreBundle\Service\PersonServiceInterface;
use Synapse\CoreBundle\Entity\Person;

use Synapse\UploadBundle\Util\Constants\UploadConstant;


class RebuildDataFilesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('rebuild:dataFiles')
            ->setDescription('Rebuilds data files')
            ->addArgument(UploadConstant::ORGID, InputArgument::OPTIONAL, 'Organization Id for doing a single organization')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $orgId = $input->getArgument(UploadConstant::ORGID);
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $orgRepository = $repositoryResolver->getRepository('SynapseCoreBundle:Organization');

        if (!empty($orgId)){
            $organizations = array($orgRepository->find($orgId));
        } else {
            $organizations = $orgRepository->findAll();
        }

        foreach ($organizations as $organization) {
            $organizationId = $organization->getId();
            $organizationSubdomain = $organization->getSubdomain();

            $output->writeln("Creating jobs to update data files for organization $organizationId - Subdomain: $organizationSubdomain");

            $synapseUploadService = $this->getContainer()->get('synapse_upload_service');
            $synapseUploadService->updateDataFile($organizationId, "Faculty");

            $courseFacultyUploadService = $this->getContainer()->get('course_faculty_upload_service');
            $courseFacultyUploadService->updateDataFile($organizationId);

            $courseStudentUploadService = $this->getContainer()->get('course_student_upload_service');
            $courseStudentUploadService->updateDataFile($organizationId);

            $courseUploadService = $this->getContainer()->get('course_upload_service');
            $courseUploadService->updateDataFile($organizationId);

            $groupStudentUploadService = $this->getContainer()->get('manage_group_student_upload_service');
            $groupStudentUploadService->updateDataFile($organizationId);

            $groupFacultyUploadService = $this->getContainer()->get('groupfacultybulk_upload_service');
            $groupFacultyUploadService->updateDataFile($organizationId);

            $groupUploadService = $this->getContainer()->get('group_upload_service');
            $groupUploadService->updateDataFile($organizationId);

            $output->writeln("    Finished creating jobs for organization $organizationId");
        }
    }

}