<?php
/**
 * Created by PhpStorm.
 * User: jlowenthal
 * Date: 1/11/16
 * Time: 10:21 AM
 */

namespace Synapse\ReportsBundle\Job;


use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;

abstract class ReportsJob extends ContainerAwareJob
{

    /**
     * ReportsJob constructor.
     */
    public function __construct()
    {
        $this->queue = ReportsConstants::REPORTS_QUEUE;
    }
}