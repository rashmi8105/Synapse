<?php
namespace Synapse\AcademicBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Synapse\AcademicBundle\Entity\OrgAcademicTerms;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\EntityDto\AcademicTermDto;
use Synapse\AcademicBundle\EntityDto\AcademicTermListResponseDto;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;


/**
 * @DI\Service("academicterm_service")
 */
class AcademicTermService extends AbstractService
{

    const SERVICE_KEY = 'academicterm_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    private $rbacManager;

    /**
     * @var LegacyValidator
     */
    private $validator;

    // Services

    /**
     * @var APIValidationService
     */
    private $apiValidationService;

    /**
     * @var IDConversionService
     */
    private $idConversionService;


    // Repositories

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCoursesRepository;

    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetadataRepository;


    /**
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     *
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container"),
     * })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->validator = $this->container->get('validator');

        // Services
        $this->apiValidationService = $this->container->get(APIValidationService::SERVICE_KEY);
        $this->idConversionService = $this->container->get(IDConversionService::SERVICE_KEY);

        // Repositories
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
    }

    /**
     * Creates an academic term.
     *
     * @param AcademicTermDto $academicTermDto
     * @param integer $loggedInUserId
     * @return AcademicTermDto
     * @throws SynapseValidationException
     */
    public function createAcademicTerm($academicTermDto, $loggedInUserId)
    {
        $organizationId = $academicTermDto->getOrganizationId();
        $this->rbacManager->checkAccessToOrganization($organizationId);

        // Validating organization
        $organization = $this->organizationRepository->find($organizationId, new SynapseValidationException('Organization Not Found.'));

        // Validating loggedInUser is Coordinator
        $this->organizationRoleRepository->findOneBy([
            'organization' => $organizationId,
            'person' => $loggedInUserId
        ], new SynapseValidationException('The logged in person is not a coordinator.'));

        // Validating the academic year associated to the organization
        $academicYear = $this->orgAcademicYearRepository->find($academicTermDto->getAcademicYearId(), new SynapseValidationException('Academic Year Not Found.'));

        $academicTerm = new OrgAcademicTerms();
        $academicTerm->setOrganization($organization);
        $academicTerm->setOrgAcademicYear($academicYear);
        $academicTerm->setTermCode($academicTermDto->getTermCode());

        // Validating Term Duplication
        $errors = $this->validator->validate($academicTerm);
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                $errorsString .= $error->getMessage();
            }
            throw new SynapseValidationException($errorsString);
        }
        $academicTermStartDate = $academicTermDto->getStartDate()->setTime(0, 0, 0);
        $academicTermEndDate = $academicTermDto->getEndDate()->setTime(0, 0, 0);
        // Validating the term start date and end date
        $this->validateDate($academicTermStartDate, $academicTermEndDate, $academicYear);

        $academicTerm->setName($academicTermDto->getName());
        $academicTerm->setStartDate($academicTermStartDate);
        $academicTerm->setEndDate($academicTermEndDate);
        $academicTerms = $this->orgAcademicTermRepository->persist($academicTerm);
        $academicTermDto->setTermId($academicTerms->getId());
        return $academicTermDto;
    }

    /**
     * Gets academic term details.
     *
     * @param integer $organizationId
     * @param integer $yearId
     * @param integer $termId
     * @param integer $loggedInUserId
     * @return AcademicTermDto
     * @throws SynapseValidationException
     */
    public function getAcademicTerm($organizationId, $yearId, $termId, $loggedInUserId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);
        $academicTermDto = new AcademicTermDto();

        // Validating organization
        $organization = $this->organizationRepository->find($organizationId, new SynapseValidationException('Organization Not Found.'));

        // Validating loggedInUser is Coordinator
        $this->organizationRoleRepository->findOneBy([
            'organization' => $organizationId,
            'person' => $loggedInUserId
        ], new SynapseValidationException('The logged in person is not a coordinator.'));

        // Validating the academic year associated to the organization
        $academicYear = $this->orgAcademicYearRepository->findOneBy([
            'id' => $yearId,
            'organization' => $organization
        ], new SynapseValidationException('Academic Year Not Found.'));

        // Validating the academic term associated to the organization and academic year
        $academicTerm = $this->orgAcademicTermRepository->findOneBy([
            'id' => $termId,
            'orgAcademicYearId' => $academicYear,
            'organization' => $organization
        ], new SynapseValidationException('Academic Term Not Found.'));


        // Validating if any data is associated with the term
        $canDelete = $this->isAssociated($academicTerm);
        if ($canDelete) {
            $academicTermDto->setCanDelete(true);
        } else {
            $academicTermDto->setCanDelete(false);
        }

        // Setting the Response
        $academicTermDto->setTermId($academicTerm->getId());
        $academicTermDto->setOrganizationId($academicTerm->getOrganization()->getId());
        $academicTermDto->setAcademicYearId($academicYear->getId());
        $academicTermDto->setName($academicTerm->getName());
        $academicTermDto->setTermCode($academicTerm->getTermCode());
        $academicTermDto->setStartDate($academicTerm->getStartDate());
        $academicTermDto->setEndDate($academicTerm->getEndDate());
        return $academicTermDto;
    }


    /**
     * Gets academic terms for a given Academic Year and Organization.
     *
     * @param integer $organizationId
     * @param integer|null $organizationAcademicYearId
     * @param integer $loggedInUserId
     * @param string|null $userType - (staff, coordinator)
     * @param boolean $isInternal
     * @param string|null $yearId
     * @return AcademicTermListResponseDto
     * @throws SynapseValidationException
     */
    public function listAcademicTerms($organizationId, $organizationAcademicYearId = null, $loggedInUserId, $userType = null, $isInternal = true, $yearId = null)
    {
        // Validating organization
        $organization = $this->organizationRepository->find($organizationId);

        //check access
        if ($isInternal) {
            $this->rbacManager->checkAccessToOrganization($organizationId);
            // Validating loggedInUser is Coordinator
            if (trim($userType) != 'staff') {
                $this->organizationRoleRepository->findOneBy([
                    'organization' => $organizationId,
                    'person' => $loggedInUserId
                ], new SynapseValidationException('The logged in person is not a coordinator.'));
            }
        }
        //columnToSearch
        if ($isInternal) {
            $columnToSearch = 'id';
        } else {
            $columnToSearch = 'yearId';
        }
        //valueToSearch
        if ($isInternal) {
            $valueToSearch = $organizationAcademicYearId;
        } else {
            $valueToSearch = $yearId;
        }

        $organizationAcademicYear = $this->orgAcademicYearRepository->findOneBy([$columnToSearch => $valueToSearch, 'organization' => $organization], new SynapseValidationException('Academic Year Not Found'));
        if (!$organizationAcademicYear) {
            if (!$isInternal) {
                $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, [$valueToSearch => 'Academic Year Not Found']);
            }
        }
        $organizationAcademicYearId = $organizationAcademicYear->getId();
        $academicTermsList = new AcademicTermListResponseDto();
        $organizationAcademicTerms = $this->orgAcademicTermRepository->getAcademicTermsForYear($organizationAcademicYearId, $organizationId);
        $termsArray = [];
        if ($isInternal) {
            $academicTermsList->setOrganizationId($organizationId);
            $academicTermsList->setAcademicYearId($organizationAcademicYearId);
        } else {
            $academicTermsList->setAcademicYearId($yearId);
        }
        foreach ($organizationAcademicTerms as $academicTerm) {
            $academicTermDto = new AcademicTermDto();
            if ($isInternal) {
                // Check whether the term is associated with a course or profile data, and hence whether it can be deleted.
                $canDelete = $this->isAssociated($academicTerm['org_academic_term_id']);
                if ($canDelete) {
                    $academicTermDto->setCanDelete(true);
                } else {
                    $academicTermDto->setCanDelete(false);
                }
                $academicTermDto->setTermId($academicTerm['org_academic_term_id']);
                $academicTermDto->setTermCode($academicTerm['term_code']);
            }
            $academicTermDto->setName($academicTerm['name']);
            if (!$isInternal) {
                $academicTermDto->setTermId($academicTerm['term_code']);
            }
            $academicTermDto->setStartDate(new \DateTime($academicTerm['start_date']));
            $academicTermDto->setEndDate(new \DateTime($academicTerm['end_date']));
            $academicTermDto->setCurrentAcademicTermFlag($academicTerm['is_current_academic_term']);
            $termsArray[] = $academicTermDto;
        }
        $academicTermsList->setAcademicTerms($termsArray);
        return $academicTermsList;
    }

    /**
     * Edits an academic term.
     *
     * @param AcademicTermDto $academicTermDto
     * @param integer $loggedInUserId
     * @return AcademicTermDto
     * @throws SynapseValidationException
     */
    public function editAcademicTerm($academicTermDto, $loggedInUserId)
    {
        $organizationId = $academicTermDto->getOrganizationId();
        $this->rbacManager->checkAccessToOrganization($organizationId);

        // Validating organization
        $organization = $this->organizationRepository->find($organizationId, new SynapseValidationException('Organization Not Found.'));

        // Validating loggedInUser is Coordinator
        $this->organizationRoleRepository->findOneBy([
            'organization' => $organizationId,
            'person' => $loggedInUserId
        ], new SynapseValidationException('The logged in person is not a coordinator.'));

        // Validating the academic year associated to the organization
        $academicYear = $this->orgAcademicYearRepository->findOneBy([
            'id' => $academicTermDto->getAcademicYearId(),
            'organization' => $organization
        ], new SynapseValidationException('Academic Year Not Found.'));

        // Validating the academic term associated to the organization and academic year
        $academicTerm = $this->orgAcademicTermRepository->findOneBy([
            'id' => $academicTermDto->getTermId(),
            'orgAcademicYearId' => $academicYear,
            'organization' => $organization
        ], new SynapseValidationException('Academic Term Not Found.'));

        // Validating if any courses are related to the term
        $canDelete = $this->isAssociated($academicTerm);
        if (!$canDelete) {
            $academicTerm->setName($academicTermDto->getName());
        } else {
            $academicTermStartDate = $academicTermDto->getStartDate()->setTime(0, 0, 0);
            $academicTermEndDate = $academicTermDto->getEndDate()->setTime(0, 0, 0);
            // Validating the term start date and end date
            $this->validateDate($academicTermStartDate, $academicTermEndDate, $academicYear);
            // Setting the academic Term Object
            $academicTerm->setName($academicTermDto->getName());
            $academicTerm->setTermCode($academicTermDto->getTermCode());
            // Validating Term Duplication
            $errors = $this->validator->validate($academicTerm);
            if (count($errors) > 0) {
                $errorsString = "";
                foreach ($errors as $error) {
                    $errorsString .= $error->getMessage();
                }
                throw new SynapseValidationException($errorsString);
            }
            $academicTerm->setStartDate($academicTermStartDate);
            $academicTerm->setEndDate($academicTermEndDate);
        }
        $this->orgAcademicTermRepository->persist($academicTerm);
        return $academicTermDto;
    }

    /**
     * Deletes an academic term.
     *
     * @param integer $termId
     * @param integer $organizationId
     * @param integer $loggedInUserId
     * @return integer
     * @throws SynapseValidationException
     */
    public function deleteAcademicTerm($termId, $organizationId, $loggedInUserId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);

        // Validating organization
        $organization = $this->organizationRepository->find($organizationId, new SynapseValidationException('Organization Not Found.'));

        // Validating loggedInUser is Coordinator
        $this->organizationRoleRepository->findOneBy([
            'organization' => $organizationId,
            'person' => $loggedInUserId
        ], new SynapseValidationException('The logged in person is not a coordinator.'));

        // Validating the academic term associated to the organization and academic year
        $academicTerm = $this->orgAcademicTermRepository->findOneBy([
            'id' => $termId,
            'organization' => $organization
        ], new SynapseValidationException('Academic Term Not Found.'));

        $this->orgAcademicTermRepository->deleteTerm($academicTerm);
        $this->orgAcademicTermRepository->flush();

        $academicTermId = $academicTerm->getId();
        return $academicTermId;
    }

    /**
     * Validates a term's start and end date.
     *
     * @param Date $startDate
     * @param Date $endDate
     * @param OrgAcademicYear $academicYear
     * @return boolean
     * @throws SynapseValidationException
     */
    private function validateDate($startDate, $endDate, $academicYear)
    {
        if ($startDate > $endDate) {
            throw new SynapseValidationException('End-Date should be greater than Start-Date');
        }

        if (($startDate < $academicYear->getStartDate()) || ($endDate > $academicYear->getEndDate())) {
            throw new SynapseValidationException('Term Period beyond Year Period');
        }
        return true;
    }

    /**
     * Finds if a term is associated with a course or ebiMetaData.
     * Used to determine if an academic term can be deleted.
     * If it is associated with a course or ebiMetaData, returns false.
     * Returns true if not associated with either.
     *
     * @param OrgAcademicTerms $academicTerm
     * @return boolean
     * @throws SynapseValidationException
     */
    private function isAssociated($academicTerm)
    {
        $courses = null;
        $ebiMetadata = null;

        $courses = $this->orgCoursesRepository->findOneBy([
            'orgAcademicTerms' => $academicTerm
        ]);
        if ($courses) {
            return false;
        }
        
        $ebiMetadata = $this->personEbiMetadataRepository->findOneBy([
            'orgAcademicTerms' => $academicTerm
        ]);
        
        if ($ebiMetadata) {
            return false;
        }
        
        return true;
    }

    /**
     * Checks that the given $orgAcademicTermId exists and belongs to the given organization.
     * If $orgAcademicYearId is provided, also validates that the term belongs to the year.
     *
     * @param integer $orgAcademicTermId
     * @param integer $organizationId
     * @param integer|null $orgAcademicYearId
     * @throws SynapseValidationException
     */
    public function validateAcademicTerm($orgAcademicTermId, $organizationId, $orgAcademicYearId = null)
    {
        $orgAcademicTermObject = $this->orgAcademicTermRepository->find($orgAcademicTermId);
        if (empty($orgAcademicTermObject)) {
            throw new SynapseValidationException('The academic term selected does not exist.');
        } else {
            $organizationIdAssociatedWithTerm = $orgAcademicTermObject->getOrganization()->getId();
            if ($organizationIdAssociatedWithTerm != $organizationId) {
                throw new SynapseValidationException('The academic term selected does not belong to the organization.');
            }

            if (isset($orgAcademicYearId)) {
                $orgAcademicYearIdAssociatedWithTerm = $orgAcademicTermObject->getOrgAcademicYear()->getId();
                if ($orgAcademicYearIdAssociatedWithTerm != $orgAcademicYearId) {
                    throw new SynapseValidationException('The academic term does not belong to the academic year.');
                }
            }
        }
    }
}
