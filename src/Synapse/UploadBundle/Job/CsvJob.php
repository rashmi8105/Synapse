<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

abstract class CsvJob extends ContainerAwareJob
{
    /**
     * CsvJob constructor.
     */
    public function __construct()
    {
        $this->queue = UploadConstant::CSV_QUEUE;
    }
}