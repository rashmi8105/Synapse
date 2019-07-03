<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\EmailDto;

interface EmailActivityServiceInterface
{
    public function createEmail(EmailDto $emailDto);
}