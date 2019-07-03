<?php
namespace Synapse\MultiCampusBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\LanguageMasterService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\MultiCampusBundle\Service\CampusServiceInterface;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\EntityDto\ListCampusDto;
use Synapse\MultiCampusBundle\EntityDto\TierDto;
use Synapse\MultiCampusBundle\EntityDto\UsersDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\MultiCampusBundle\EntityDto\ChangeRequestDto;
use Synapse\MultiCampusBundle\Entity\OrgChangeRequest;
use Synapse\MultiCampusBundle\EntityDto\CampusChangeRequestDto;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Util\Constants\UsersConstant;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\MultiCampusBundle\Job\MoveCampus;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Util\Helper;

/**
 * @DI\Service("campus_service")
 */
class CampusService extends CampusServiceHelper implements CampusServiceInterface
{

    const SERVICE_KEY = 'campus_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_REPO = 'SynapseCoreBundle:Organization';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGLANG_REPO = 'SynapseCoreBundle:OrganizationLang';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_CONFLICT = 'SynapseMultiCampusBundle:OrgConflict';

    /**
     * @var LanguageMasterService
     */
    private $langService;

    /**
     * @var OrganizationService
     */
    private $orgService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var Container
     */
    private $container;

    /**
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->langService = $this->container->get('lang_service');
        $this->orgService = $this->container->get('org_service');
        $this->emailService = $this->container->get('email_service');
        $this->ebiConfigService = $this->container->get('ebi_config_service');
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(AppointmentsConstant::EBICONFIG_REPO);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:EmailTemplateLang");
    }

    public function createHierarchyCampus($tierId, CampusDto $campusDto)
    {
        // Check is this secondary tier
        $this->validateTier($tierId, 'secondary');
        $this->campusRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $language = $this->langService->getLanguageById($campusDto->getLangid());
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(self::ORGLANG_REPO);
        $campus = new Organization();
        $campus->setSubdomain($campusDto->getSubdomain());
        $campus->setTimeZone($campusDto->getTimezone());
        $campus->setCampusId($campusDto->getCampusId());
        $campus->setParentOrganizationId($tierId);
        $campus->setTier(3);
        $status = ($campusDto->getStatus() == TierConstant::STRING_ACTIVE) ? 'A' : 'I';
        $campus->setStatus($status);
        $validator = $this->container->get(TierConstant::STRING_VALIDATOR);
        $this->logger->info("Hierarchy Campus to be created ");
        $errors = $validator->validate($campus);
        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();
            $this->logger->error(" Multi Campus Bundle - Campus Service - createHierarchyCampus - " . $errorsString . TierConstant::ERROR_KEY_CAMPUS);
            throw new ValidationException([
                $errorsString
            ], $errorsString, TierConstant::ERROR_KEY_CAMPUS);
        }
        $this->campusRepository->createOrganization($campus);
        $campusLang = new OrganizationLang();
        $campusLang->setNickName($campusDto->getCampusNickName());
        $campusLang->setOrganizationName($campusDto->getCampusName());
        $campusLang->setLang($language);
        $campusLang->setOrganization($campus);
        $errors = $validator->validate($campusLang);
        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();
            $this->logger->error(" Multi Campus Bundle - Campus Service - createHierarchyCampus - " . $errorsString . 'create_campus_duplicate');
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'create_campus_duplicate_error');
        }
        $this->organizationlangRepository->createOrganizationLang($campusLang);
        $this->campusRepository->flush();
        $this->logger->info(" Hierarchy campus is created ");
        $this->container->get('org_service')->copyEBIPermissions($campus, $campusDto->getLangid());
        return $this->getCampus($campus, $campusLang);
    }

    public function updateMoveHierarchyCampus($tierId, CampusDto $campusDto)
    {
        if ($campusDto->getType() == "edit") {
            $campuses = $this->updateHierarchyCampus($tierId, $campusDto);
        }
        if ($campusDto->getSourceCampusType() == 'hierarchy') {
            $campuses = $this->moveHierarchyCampus($tierId, $campusDto);
        }
        if ($campusDto->getSourceCampusType() == 'standalone') {
            $campuses = $this->moveStandaloneCampus($tierId, $campusDto);
        }
        return $campuses;
    }

    public function listSoloCampuses()
    {
        $this->logger->info("Listing all solo campuses");
        $this->campusRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrganizationRole');
        $listCampuses = $this->campusRepository->listCampuses('', '0');
        $listSoloCampusDto = new ListCampusDto();
        $campusList = array();
        if (! empty($listCampuses)) {
            foreach ($listCampuses as $campus) {
                $campusDto = new CampusDto();
                $campusId = $campus[TierConstant::ORGID];
                $campusDto->setId($campusId);
                $campusDto->setCampusId($campus[TierConstant::CAMPUSID]);
                $campusDto->setCampusName($campus[TierConstant::ORGNAME]);
                $campusDto->setSubdomain($campus[TierConstant::SUBDOMAIN]);
                $coordinators = $this->orgRoleRepository->getCoordinators($campusId);
                $campusDto->setCountUsers(count($coordinators));
                $campusList[] = $campusDto;
            }
        }
        $listSoloCampusDto->setCampus($campusList);
        $this->logger->info("Hierarchy campus are listed");
        return $listSoloCampusDto;
    }

    public function viewCampuses($tierId, $campusId)
    {
        $this->validateParentTier($tierId, $campusId);
        $this->campuslangRepository = $this->repositoryResolver->getRepository(self::ORGLANG_REPO);
        $campusLang = $this->campuslangRepository->findOneBy(array(
            TierConstant::FIELD_ORGANIZATION => $campusId
        ));
        $this->logger->info("View campus details");
        if (empty($campusLang)) {
            $this->logger->error(" Multi Campus Bundle - Campus Service - viewCampuses - " . TierConstant::ERROR_CAMPUS_NOT_FOUND . TierConstant::ERROR_CAMPUS_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::ERROR_CAMPUS_NOT_FOUND
            ], TierConstant::ERROR_CAMPUS_NOT_FOUND, TierConstant::ERROR_CAMPUS_NOT_FOUND_KEY);
        }
        $campus = $campusLang->getOrganization();
        $this->logger->info("Hierarchy campus details are listed");
        return $this->getCampus($campus, $campusLang);
    }

    public function listHierarchyCampus($tierId, $paramFetcher)
    {
        if ($paramFetcher->get('campus') == '' & $paramFetcher->get('filter') == '') {
            $campuses = $this->listCampuses($tierId);
        } else {
            $campusType = $paramFetcher->get('campus');
            $filter = $paramFetcher->get('filter');
            $campuses = $this->listHierarchyCampuses($tierId, $campusType, $filter);
        }
        return $campuses;
    }

    public function deleteHierarchyCampus($tierId, $campusId)
    {
        $this->logger->info("Delete Hierarchy campus");
        $this->isCampus($campusId);
        $this->validateParentTier($tierId, $campusId);
        return $this->orgService->deleteOrganization($campusId);
    }

    public function createChangeRequest(ChangeRequestDto $changeRequestDto)
    {
        $this->orgChangeRequestRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_CHANGE_REQUEST_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $this->tierRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        
        $sourseCampus = $this->tierRepository->findOneBy(array(
            'id' => $changeRequestDto->getSourceCampus()
        ));
        $this->logger->info("Create New Change Request");
        if (empty($sourseCampus)) {
            $this->logger->error(" MultiCampus Bundle - Campus Service - createChangeRequest - " . TierConstant::INVALID_SOURCE_CAMPUS . TierConstant::INVALID_SOURCE_CAMPUS_KEY);
            throw new ValidationException([
                TierConstant::INVALID_SOURCE_CAMPUS
            ], TierConstant::INVALID_SOURCE_CAMPUS, TierConstant::INVALID_SOURCE_CAMPUS_KEY);
        }
        $destinationCampus = $this->tierRepository->findOneBy(array(
            'id' => $changeRequestDto->getDestinationCampus()
        ));
        if (empty($destinationCampus)) {
            $this->logger->error(" MultiCampus Bundle - Campus Service - createChangeRequest - " . TierConstant::INVALID_DEST_CAMPUS . TierConstant::INVALID_DEST_CAMPUS_KEY);
            throw new ValidationException([
                TierConstant::INVALID_DEST_CAMPUS
            ], TierConstant::INVALID_DEST_CAMPUS, TierConstant::INVALID_DEST_CAMPUS_KEY);
        }
        $requestedByPerson = $this->personRepository->find($changeRequestDto->getRequestedBy());
        if (empty($requestedByPerson)) {
            $this->logger->error(" MultiCampus Bundle - Campus Service - createChangeRequest - " . TierConstant::INVALID_PERSON . TierConstant::INVALID_REQUESTED_PERSON);
            throw new ValidationException([
                TierConstant::INVALID_PERSON
            ], TierConstant::INVALID_PERSON, TierConstant::INVALID_REQUESTED_PERSON);
        }
        $requestedForPerson = $this->personRepository->find($changeRequestDto->getRequestedFor());
        if (empty($requestedForPerson)) {
            $this->logger->error(" MultiCampus Bundle - Campus Service - createChangeRequest - " . TierConstant::INVALID_PERSON . TierConstant::INVALID_REQUESTED_PERSON);
            throw new ValidationException([
                TierConstant::INVALID_PERSON
            ], TierConstant::INVALID_PERSON, TierConstant::INVALID_REQUESTED_PERSON);
        }
        $organization = $requestedByPerson->getOrganization();
        $timezone = $this->getOrganizationTimezone($organization);
        $orgCurrentUtcDate = Helper::getUtcDate(new \DateTime('now'), $timezone);
        
        $changeRequest = new OrgChangeRequest();
        $changeRequest->setOrgSource($sourseCampus);
        $changeRequest->setOrgDestination($destinationCampus);
        $changeRequest->setPersonRequestedBy($requestedByPerson);
        $changeRequest->setPersonStudent($requestedForPerson);
        $changeRequest->setDateSubmitted($orgCurrentUtcDate);
        $this->orgChangeRequestRepository->create($changeRequest);
        $this->orgChangeRequestRepository->flush();
        $this->logger->info("Change Request is created");
        return $changeRequestDto;
    }

    public function deleteChangeRequest($requestId)
    {
        $this->orgChangeRequestRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_CHANGE_REQUEST_REPO);
        $changeRequest = $this->orgChangeRequestRepository->findOneBy(array(
            'id' => $requestId
        ));
        $this->logger->info("Delete a change request ");
        if (empty($changeRequest)) {
            $this->logger->error(" MultiCampus Bundle - Campus Service - deleteChangeRequest - " . TierConstant::INVALID_REQUESTED_ID . TierConstant::INVALID_REQUESTED_ID_KEY);
            throw new ValidationException([
                TierConstant::INVALID_REQUESTED_ID
            ], TierConstant::INVALID_REQUESTED_ID, TierConstant::INVALID_REQUESTED_ID_KEY);
        }
        $this->orgChangeRequestRepository->remove($changeRequest);
        $this->orgChangeRequestRepository->flush();
        $this->logger->info("Change Request is deleted");
        return $requestId;
    }

    public function listChangeRequest($type, $loggedUserId, $campusId)
    {
        $this->orgChangeRequestRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_CHANGE_REQUEST_REPO);
        $this->logger->info("List send/received change requests");
        if ($type == 'received') {
            $changeRequest = $this->orgChangeRequestRepository->findBy([
                'orgSource' => $campusId,
                'approvalStatus' => NULL
            ]);
            $campusCoordinators = array_column($changeRequest, 'org_destination');
            $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
            $changeRequestUsers = array();
            if (! empty($changeRequest)) {
                foreach ($changeRequest as $request) {
                    $usersDto = new UsersDto();
                    $campus = $request->getPersonRequestedBy()
                        ->getOrganization()
                        ->getId();
                    $usersDto->setRequestId($request->getId());
                    $usersDto->setFirstName($request->getPersonStudent()
                        ->getFirstname());
                    $usersDto->setLastName($request->getPersonStudent()
                        ->getLastname());
                    $usersDto->setExternalId($request->getPersonStudent()
                        ->getExternalId());
                    $usersDto->setEmail($request->getPersonStudent()
                        ->getContacts()[0]
                        ->getPrimaryEmail());
                    $usersDto->setRequestedDate($request->getDateSubmitted());
                    $changeRequestDto = new ChangeRequestDto();
                    $changeRequestDto->setFirstName($request->getPersonRequestedBy()
                        ->getFirstname());
                    $changeRequestDto->setLastName($request->getPersonRequestedBy()
                        ->getLastname());
                    $changeRequestDto->setCampus($this->getOrganization($campus));
                    $changeRequestDto->setRole($this->getRole($request->getPersonRequestedBy(), $campus));
                    $usersDto->setRequestedBy($changeRequestDto);
                    $changeRequestUsers[] = $usersDto;
                }
            }
            $this->logger->info("Received Change Requests are listed");
            return $changeRequestUsers;
        }
        
        if ($type == 'sent') {
            $changeRequest = $this->orgChangeRequestRepository->findBy([
                'personRequestedBy' => $loggedUserId,
                'approvalStatus' => NULL
            ]);
            $changeRequestUsers = array();
            if (! empty($changeRequest)) {
                foreach ($changeRequest as $request) {
                    $usersDto = new UsersDto();
                    $campus = $request->getPersonRequestedBy()
                        ->getOrganization()
                        ->getId();
                    $usersDto->setRequestId($request->getId());
                    $usersDto->setFirstName($request->getPersonStudent()
                        ->getFirstname());
                    $usersDto->setLastName($request->getPersonStudent()
                        ->getLastname());
                    $usersDto->setExternalId($request->getPersonStudent()
                        ->getExternalId());
                    $usersDto->setEmail($request->getPersonStudent()
                        ->getContacts()[0]
                        ->getPrimaryEmail());
                    $usersDto->setRequestDate($request->getDateSubmitted());
                    $changeRequestDto = new ChangeRequestDto();
                    $changeRequestDto->setFirstName($request->getPersonRequestedBy()
                        ->getFirstname());
                    $changeRequestDto->setLastName($request->getPersonRequestedBy()
                        ->getLastname());
                    $changeRequestDto->setCampus($this->getOrganization($campus));
                    $changeRequestDto->setRole($this->getRole($request->getPersonRequestedBy(), $campus));
                    $usersDto->setRequestedFrom($changeRequestDto);
                    $changeRequestUsers[] = $usersDto;
                }
            }
            $this->logger->info("Change requests which was sent by that coordinator is listed");
            return $changeRequestUsers;
        }
    }

    public function updateChangeRequest(CampusChangeRequestDto $campusChangeRequestDto, $campusId)
    {
        $this->orgChangeRequestRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_CHANGE_REQUEST_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $this->campusRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $this->personStudentRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPersonStudent");
        $this->logger->info("Approve/Cancel the change requests");
        $changeRequest = $this->orgChangeRequestRepository->find($campusChangeRequestDto->getRequestId());
        $status = $campusChangeRequestDto->getStatus();
        $changeRequest->setApprovalStatus($status);
        $studentId = $changeRequest->getPersonStudent()->getId();
        $person = $this->personRepository->find($studentId);
        $requestor = $this->personRepository->find($changeRequest->getPersonRequestedBy()
            ->getId());
        $this->isExists($person, 'Person Not Found', 'person_not_found');
        if ($status == 'yes') {
            $requestedHomeCampus = $changeRequest->getOrgDestination()->getId();
            $campus = $this->campusRepository->find($requestedHomeCampus);
            $this->isExists($campus, 'Campus Not Found', TierConstant::ERROR_CAMPUS_NOT_FOUND_KEY);
            $this->isCampus($requestedHomeCampus);
            $person->setOrganization($campus);
            $this->logger->info("Approve change requests");
            $this->removeExistingHomeCampus($person);
            $personStudent = $this->personStudentRepository->findOneBy(array(
                'person' => $person,
                TierConstant::FIELD_ORGANIZATION => $campus
            ));
            if (! empty($personStudent)) {
                if ($personStudent->getIsHomeCampus()) {
                    $this->logger->error(" MultiCampus Bundle - Campus Service - updateChangeRequest - " . TierConstant::STUDENT_ALREADY_EXIST . TierConstant::STUDENT_ALREADY_EXIST_KEY);
                    throw new ValidationException([
                        TierConstant::STUDENT_ALREADY_EXIST
                    ], TierConstant::STUDENT_ALREADY_EXIST, TierConstant::STUDENT_ALREADY_EXIST_KEY);
                } else {
                    $personStudent->setIsHomeCampus('1');
                    $this->personStudentRepository->flush();
                }
            } else {
                $personStudentObj = new OrgPersonStudent();
                $personStudentObj->setOrganization($campus);
                $personStudentObj->setPerson($person);
                $personStudentObj->setIsHomeCampus('1');
                $this->personStudentRepository->persist($personStudentObj);
            }
            $this->logger->info("Change Request is approved");
            $this->logger->info("Send email notification - Change request is approved");
            $emailKey = "Accept_Change_Request";
            $this->sendEmailChangeRequest($person, $campusId, $emailKey, $requestor);
        }
        
        if ($status == 'no') {
            $this->logger->info("Change Request is denied");
            $this->logger->info("Send email notification - Change request is denied");
            $emailKey = "Deny_Change_Request";
            $this->sendEmailChangeRequest($person, $campusId, $emailKey, $requestor);
            $this->logger->info("Change request is denied - Email sent");
        }
        $this->orgChangeRequestRepository->flush();
        return $campusChangeRequestDto;
    }

    private function updateHierarchyCampus($tierId, CampusDto $campusDto)
    {
        $this->validateTier($tierId, 'secondary');
        $campusId = $campusDto->getId();
        $this->logger->info("Update Hierarchy campus ");
        $this->validateParentTier($tierId, $campusId);
        $this->campuslangRepository = $this->repositoryResolver->getRepository(self::ORGLANG_REPO);
        $campusLang = $this->campuslangRepository->findOneBy(array(
            TierConstant::FIELD_ORGANIZATION => $campusDto->getId()
        ));
        if ($campusLang) {
            $campus = $campusLang->getOrganization();
            $campus->setSubdomain($campusDto->getSubdomain());
            $campus->setTimeZone($campusDto->getTimezone());
            $campus->setCampusId($campusDto->getCampusId());
            $status = ($campusDto->getStatus() == TierConstant::STRING_ACTIVE) ? 'A' : 'I';
            $campus->setStatus($status);
            $validator = $this->container->get(TierConstant::STRING_VALIDATOR);
            $errors = $validator->validate($campus);
            if (count($errors) > 0) {
                $errorsString = $errors[0]->getMessage();
                $this->logger->error(" MultiCampus Bundle - CampusService - updateHierarchyCampus " . $errorsString . TierConstant::ERROR_KEY_CAMPUS);
                throw new ValidationException([
                    $errorsString
                ], $errorsString, TierConstant::ERROR_KEY_CAMPUS);
            }
        } else {
            $this->logger->error(" MultiCampus Bundle - CampusService - updateHierarchyCampus " - TierConstant::ERROR_CAMPUS_NOT_FOUND . TierConstant::ERROR_CAMPUS_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::ERROR_CAMPUS_NOT_FOUND
            ], TierConstant::ERROR_CAMPUS_NOT_FOUND, TierConstant::ERROR_CAMPUS_NOT_FOUND_KEY);
        }
        $campusLang->setOrganizationName($campusDto->getCampusName());
        $campusLang->setNickName($campusDto->getCampusNickName());
        $validator = $this->container->get(TierConstant::STRING_VALIDATOR);
        $errors = $validator->validate($campusLang);
        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();
            $this->logger->error(" Multi Campus Bundle - Campus Service - updateHierarchyCampus - " . $errorsString . TierConstant::ERROR_KEY_CAMPUS);
            throw new ValidationException([
                $errorsString
            ], $errorsString, TierConstant::ERROR_KEY_CAMPUS);
        }
        $this->campuslangRepository->flush();
        $this->logger->info("Hierarchy campus is updated");
        return $this->getCampus($campus, $campusLang);
    }

    public function listCampuses($tierId)
    {
        $this->campusRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_ROLE_REPO);
        $primaryTier = $this->campusRepository->findOneBy([
            'id' => $tierId
        ]);
        $this->logger->info("List campuses under the primary tier");
        $listCampuses = $this->campusRepository->listCampuses($tierId, '3');
        $listCampusDto = new ListCampusDto();
        $campusList = array();
        $listCampusDto->setPrimaryTierId($primaryTier->getParentOrganizationId());
        $listCampusDto->setSecondaryTierId($tierId);
        $listCampusDto->setTotalCampus(count($listCampuses));
        if (! empty($listCampuses)) {
            foreach ($listCampuses as $campus) {
                $campusDto = new CampusDto();
                $campusId = $campus[TierConstant::ORGID];
                $campusDto->setId($campusId);
                $campusDto->setCampusId($campus[TierConstant::CAMPUSID]);
                $campusDto->setCampusName($campus[TierConstant::ORGNAME]);
                $coordinators = $this->orgRoleRepository->getCoordinators($campusId);
                $campusDto->setCountUsers(count($coordinators));
                $campusList[] = $campusDto;
            }
        }
        $listCampusDto->setCampus($campusList);
        $this->logger->info("Campuses are listed under primary tier");
        return $listCampusDto;
    }

    private function moveHierarchyCampus($tierId, CampusDto $moveCampusDto)
    {
        $this->campusRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $campusId = $moveCampusDto->getSourceOrgId();
        $campus = $this->campusRepository->find($campusId);
        $this->logger->info("Move hierarchy campus between one tier to another tier");
        // Checking whether the given campus is hierarchy campus and gievn secondary tier id in URI is matching with campus parent
        if ($campus->getTier() == 3) {
            $secondaryParent = $this->campusRepository->findOneBy([
                'id' => $campus->getParentOrganizationId()
            ]);
            $primaryParent = $secondaryParent->getParentOrganizationId();
        } else {
            $this->logger->error(" Multi Campus Bundle - Campus Service - moveHierarchyCampus - " . TierConstant::INVALID_CAMPUS . TierConstant::INVALID_CAMPUS_KEY);
            throw new ValidationException([
                TierConstant::INVALID_CAMPUS
            ], TierConstant::INVALID_CAMPUS, TierConstant::INVALID_CAMPUS_KEY);
        }
        $campus->setparentOrganizationId($tierId);
        $this->campusRepository->flush();
        $this->logger->info("Hierarchy campus is moved from one tier to another tier");
    }

    private function getCampus($campus, $campusLang)
    {
        $campusDto = new CampusDto();
        $this->logger->info("List campus details" . $campusLang->getOrganizationName());
        $campusDto->setId($campus->getId());
        $campusDto->setCampusName($campusLang->getOrganizationName());
        $campusDto->setCampusNickName($campusLang->getNickName());
        $campusDto->setSubdomain($campus->getSubdomain());
        $campusDto->setTimezone($campus->getTimeZone());
        $campusDto->setCampusId($campus->getCampusId());
        $status = ($campus->getStatus() == 'A') ? TierConstant::STRING_ACTIVE : 'Inactive';
        $campusDto->setStatus($status);
        return $campusDto;
    }

    public function listTierUsersCampus($loggerUserId)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(self::ORG_REPO);
        $this->tierUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $person = $this->personRepository->findOneBy(array(
            'id' => $loggerUserId
        ));
        $this->logger->info("List all institutions/tiers for that logged in person");
        $id = $person->getOrganization()->getId();
        $tierDetails = $this->tierRepository->findOneBy(array(
            'id' => $id
        ));
        $parentOrgId = $tierDetails->getParentOrganizationId();
        $tierDetails1 = $this->tierRepository->findOneBy(array(
            'id' => $parentOrgId
        ));
        
        $superparentOrgId = $tierDetails1->getParentOrganizationId();
        $tierDetails2 = $this->tierRepository->findOneBy(array(
            'id' => $superparentOrgId
        ));
        
        $primaryusersArray = [];
        $secondaryusersArray = [];
        $tierDto = new TierDto();
        $tierDto->setCampusId($id);
        $primaryTierUsers = $this->tierUsersRepository->findBy(array(
            TierConstant::FIELD_ORGANIZATION => $tierDetails2->getId()
        ));
        $secondaryTierUsers = $this->tierUsersRepository->findBy(array(
            TierConstant::FIELD_ORGANIZATION => $tierDetails1->getId()
        ));
        
        $primaryUsersArray = $this->tierUsersBinding($primaryTierUsers, "Tier 1 Admin");
        $secondaryUsersArray = $this->tierUsersBinding($secondaryTierUsers, "Tier 2 Admin");
        $tierUsersArray = [];
        $tierUsersArray = array_merge($primaryUsersArray, $secondaryUsersArray);
        $tierDto->setUsers($tierUsersArray);
        return $tierDto;
    }

    /**
     * This method sends email after changing organization request
     *
     * @param Person $person
     * @param int $campusId
     * @param string $emailKey
     * @param Person $requester
     * return null
     */
    private function sendEmailChangeRequest($person, $campusId, $emailKey, $requester)
    {
        $organizationLang = $this->orgService->getOrganizationDetailsLang($campusId);
        $languageId = $organizationLang->getLang()->getId();
        $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $languageId);
        $supportEmail = $this->ebiConfigRepository->findOneByKey('Coordinator_Support_Helpdesk_Email_Address');
        $responseArray = array();
        if ($emailTemplate) {
            $emailBody = $emailTemplate->getBody();
            $tokenValues = array();
            $tokenValues[AppointmentsConstant::EMAIL_SKY_LOGO] = "";
            $systemUrl = $this->ebiConfigService->getSystemUrl($campusId);
            if ($systemUrl) {
                $tokenValues[AppointmentsConstant::EMAIL_SKY_LOGO] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
            }
            $tokenValues[UsersConstant::SUPPORT_HELPDESK] = $supportEmail->getValue();
            $tokenValues[UsersConstant::FIELD_FIRSTNAME] = $person->getFirstname();
            $tokenValues[UsersConstant::FIELD_LASTNAME] = $person->getLastname();
            $tokenValues['staff_firstname'] = $requester->getFirstname();
            $email = $requester->getUsername();
            $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);
            $bcc = $emailTemplate->getEmailTemplate()->getBccRecipientList();
            $subject = $emailTemplate->getSubject();
            $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
            $responseArray['email_detail'] = array(
                'from' => $from,
                'subject' => $subject,
                'bcc' => $bcc,
                'body' => $emailBody,
                'to' => $email,
                'emailKey' => $emailKey,
                'organizationId' => $campusId
            );          
            $emailInst = $this->emailService->sendEmailNotification($responseArray['email_detail']);
            $this->emailService->sendEmail($emailInst);
        }
    }

    public function moveStandaloneCampus($destinationId, CampusDto $moveCampusDto)
    {
        /* Resque job start here */
        $jobNumber = uniqid();
        $job = new MoveCampus();
        $job->moveCampusDtoObj = $moveCampusDto;
        $this->resque = $this->container->get('bcc_resque.resque');
        $job->args = array(
            'jobNumber' => $jobNumber,
            'destinationId' => $destinationId,
            'sourceId' => $moveCampusDto->getSourceOrgId()
        );
        $this->resque->enqueue($job, true);
        return $moveCampusDto;
        /* Resque job end here */
    }

    private function getOrganizationTimeZone($organization)
    {
        $timeZone = '';
        $timezone = $this->repositoryResolver->getRepository('SynapseCoreBundle:MetadataListValues')->findByListName($organization->getTimezone());
        if ($timezone) {
            $timeZone = $timezone[0]->getListValue();
        }
        return $timeZone;
    }
}