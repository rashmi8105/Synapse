<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\AccessLogDto;

interface AccessLogServiceInterface
{

    public function createAccessLog(AccessLogDto $accessLogDto);
}