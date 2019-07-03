<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class CreateSubGroupListJob extends ContainerAwareJob
{

    public function run($args)
    {
        $orgId = $args['orgId'];
        $logger = $this->getContainer()->get('logger');
        
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Sub Group List for Organization : " . $orgId);
        $groupUploadService = $this->getContainer()->get('group_upload_service');
        $groupUploadService->createExisting($orgId);
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>>>> Updating Faculty List For Organization : " . $orgId);
        $groupFacultyUploadService = $this->getContainer()->get('groupfacultybulk_upload_service');
        $groupFacultyUploadService->createExisting($orgId);
        $logger->info(">>>>>>>>>>>>>>>>>>>>>>>>>>>> Updating Student List For Organization : " . $orgId);
        $groupStudentUploadService = $this->getContainer()->get('manage_group_student_upload_service');
        $groupStudentUploadService->createExisting($orgId);
    }
}