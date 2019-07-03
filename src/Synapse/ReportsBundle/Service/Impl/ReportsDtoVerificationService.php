<?php


namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;
use Synapse\RestBundle\Exception\ValidationException;


/**
 * @DI\Service("report_dto_verification_service")
 */
class ReportsDtoVerificationService
{

    const SERVICE_KEY = 'report_dto_verification_service';

    public function __construct()
    {
    }

    /**
     * @param int $authorizedOrgId
     * @param ReportRunningStatusDto $reportDto
     */
    public function verifyOrganizationInDto($authorizedOrgId, $reportDto){

        if($reportDto->getOrganizationId() != $authorizedOrgId && !is_null($reportDto->getOrganizationId() ) )
            throw new ValidationException($errors = ["validation_errors"], $message = "Bad Request: Requested Organization id does not match authorized organization id", $code = "validation_errors", 403);

    }
    public function verifyPersonInDto($authorizedPersonId, $reportDto){
        if($reportDto->getPersonId() != $authorizedPersonId)
            throw new ValidationException($errors = ["validation_errors"], $message = "Bad Request: Requested Person id does not match authorized person id", $code = "validation_errors", 403);

    }
    public function verifyDto($authorizedOrgId, $authorizedPersonId, $reportDto){
        $this->verifyOrganizationInDto($authorizedOrgId, $reportDto);
        $this->verifyPersonInDto($authorizedPersonId, $reportDto);

    }





}