<?php

namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\OrgPersonStudentYear;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RoleLangRepository;
use Synapse\CoreBundle\SynapseConstant;

/**
 * @DI\Service("user_management_service")
 */
class UserManagementService extends AbstractService
{

    const SERVICE_KEY = 'user_management_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;


    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;


    // Repositories

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var RoleLangRepository
     */
    private $roleLangRepository;



    /**
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->roleLangRepository = $this->repositoryResolver->getRepository(RoleLangRepository::REPOSITORY_KEY);

    }


    /**
     * Uses the ebi_config table to determine whether longitudinal student management is in place.
     * If so, returns true if the student is active (has 1 in org_person_student_year) for the current academic year, and false otherwise.
     * If not, returns true if the student is active (status 1 in org_person_student), and false otherwise.
     *
     * @param int $studentId
     * @param int|null $organizationId
     * @return bool
     */
    public function isStudentActive($studentId, $organizationId = null)
    {
        $studentIsActive = false;
        $longitudinalStudentManagement = false;
        $studentObject = $this->getMultiyearOrganizationStudentObject($studentId, $longitudinalStudentManagement, $organizationId);
        if ($studentObject) {
            if ($longitudinalStudentManagement) {
                $studentIsActive = $studentObject->getIsActive() == true ? true : false;
            } else {
                $studentIsActive = $studentObject->getStatus() == 1 ? true : false;
            }
        }

        return $studentIsActive;
    }


    /**
     * Returns true if the student is participating (has any non-deleted record in org_person_student_year) for the current academic year,
     * and false otherwise.
     *
     * @param int $studentId
     * @param int|null $organizationId
     * @return bool
     */
    public function isStudentMemberOfCurrentAcademicYear($studentId, $organizationId = null)
    {
        $longitudinalStudentManagement = false;
        $studentMemberOrganizationObject = $this->getMultiyearOrganizationStudentObject($studentId, $longitudinalStudentManagement, $organizationId);
        if ($studentMemberOrganizationObject) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns true/false on whether a user is allowed to login
     *
     * @param int $personId
     * @return bool
     */
    public function isUserAllowedToLogin($personId)
    {
        $studentLogin = false;
        $staffLogin = false;

        $isStudentActive = $this->isStudentActive($personId);

        if ($isStudentActive) {
            $studentLogin = true;
        }

        $isStaff = $this->orgPersonFacultyRepository->findOneBy(['person' => $personId]);
        if ($isStaff) {
            $staffStatus = $isStaff->getStatus();
            if (is_null($staffStatus) || $staffStatus == 1) {
                $staffLogin = true;
            }
        }
        if ($staffLogin || $studentLogin) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the student records if the student is participating (has any non-deleted record in org_person_student_year) for the current academic year
     *  else get record in org_person_student
     * .
     * @param int $studentId
     * @param int|null $organizationId
     * @param bool $longitudinalStudentManagement
     * @return OrgPersonStudent|OrgPersonStudentYear
     */
    public function getMultiyearOrganizationStudentObject($studentId, &$longitudinalStudentManagement, $organizationId = null)
    {

        $ebiConfigObject = $this->ebiConfigRepository->findOneBy(['key' => 'Longitudinal_Student_Management']);
        if ($ebiConfigObject) {
            $longitudinalStudentManagement = $ebiConfigObject->getValue();
        }
        if ($longitudinalStudentManagement) {
            if (empty($organizationId)) {
                $personObject = $this->personRepository->find($studentId);
                $organizationId = $personObject->getOrganization()->getId();
            }
            $currentOrgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
            $orgPersonStudentYearObject = $this->orgPersonStudentYearRepository->findOneBy(['person' => $studentId, 'orgAcademicYear' => $currentOrgAcademicYearId]);
            return $orgPersonStudentYearObject;
        } else {
            $orgPersonStudentObject = $this->orgPersonStudentRepository->findOneBy(['person' => $studentId]);
            return $orgPersonStudentObject;
        }

    }

}