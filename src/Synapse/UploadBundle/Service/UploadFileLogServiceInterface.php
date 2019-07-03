<?php


namespace Synapse\UploadBundle\Service;


use Synapse\UploadBundle\Entity\UploadFileLog;
use Synapse\CoreBundle\Exception\EntityNotFoundException;
use Synapse\CoreBundle\Exception\InvalidArgumentException;

interface UploadFileLogServiceInterface {

    public function findAllStudentUploadLogs($organizationId);
    public function findOneStudentUploadLog($id);
    public function updateValidRowCount($id, $count);
    public function updateJobStatus($jobNumber, $status, $message = null);
    public  function listHistory($loggedInUser,$orgId, $pageNo, $offset, $sortBy, $filter);
}
