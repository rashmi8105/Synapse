<?php
namespace Synapse\UploadBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RiskBundle\Entity\OrgRiskvalCalcInputs;
use Synapse\RiskBundle\Repository\OrgRiskvalCalcInputsRepository;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;

/**
 * @DI\Service("org_calc_flags_risk__service")
 */
class OrgCalcFlagsRiskService extends AbstractService
{

    const SERVICE_KEY = 'org_calc_flags_risk__service';

    const PERSON_NOT_FOUND_ERROR_MESSAGE = 'Person does not exist and risk flag was not set';

    const PERSON_NOT_STUDENT_ERROR_MESSAGE = 'Person is not a student and risk flag was not set';


    /**
     * @var RepositoryResolver
     */
    protected $repositoryResolver;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgRiskvalCalcInputsRepository
     */
    private $orgRiskvalCalcInputsRepository;

    /**
     * @var RiskGroupPersonHistoryRepository
     */
    private $riskGroupPersonHistoryRepository;


    /**
     * @param $repositoryResolver
     * @param $logger
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {

        parent::__construct($repositoryResolver, $logger);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgRiskvalCalcInputsRepository = $this->repositoryResolver->getRepository(OrgRiskvalCalcInputsRepository::REPOSITORY_KEY);
        $this->riskGroupPersonHistoryRepository = $this->repositoryResolver->getRepository(RiskGroupPersonHistoryRepository::REPOSITORY_KEY);
    }

    /**
     * Update a Students' Risk Flags for Risk Group (Used in Admin Changes)
     *
     * This is inputing into the buffer table org_riskval_calc_inputs to avoid Table locking issues
     * A trigger on org_riskval_calc_inputs will then actually set the risk flag in org_calc_flags_risk
     *
     * @param int $riskGroupId
     * @param int $organizationId
     * @return int
     */
    public function addStudentsToRiskFlagCalculation($riskGroupId, $organizationId, $batchSize = 50)
    {

        $riskGroupStudents = $this->riskGroupPersonHistoryRepository->getRiskGroupByOrg($organizationId, $riskGroupId);

        $flushCount = 0;
        foreach ($riskGroupStudents as $StudentWithRiskGroup) {

            $personObject = $this->personRepository->find($StudentWithRiskGroup['id']);
            if ($personObject) {
                $organizationObject = $personObject->getOrganization();
                $flushCount = $flushCount + $this->addStudentToCalcFlag($organizationObject, $personObject);

                if (($flushCount % $batchSize) === 0) {
                    $this->orgRiskvalCalcInputsRepository->flush();
                    $this->orgRiskvalCalcInputsRepository->clear();
                }
            } else {
                $this->logger->error(self::PERSON_NOT_FOUND_ERROR_MESSAGE);
            }


        }

        $this->orgRiskvalCalcInputsRepository->flush();
        $this->orgRiskvalCalcInputsRepository->clear();
        return $flushCount;
    }


    /**
     * Update a Student's Risk Flag
     *
     * This is inputing into the buffer table org_riskval_calc_inputs to avoid table locking issues
     * A trigger on org_riskval_calc_inputs will then actually set the risk flag in org_calc_flags_risk
     *
     * @param Organization | null $organization
     * @param Person | null $student
     * @return int
     */
    private function addStudentToCalcFlag($organization, $student)
    {
        $orgRiskvalCalcInputs = $this->orgRiskvalCalcInputsRepository->findOneBy([
            'org' => $organization,
            'person' => $student
        ]);
        if ($orgRiskvalCalcInputs) {
            $orgRiskvalCalcInputs->setIsRiskvalCalcRequired('y');
            $orgRiskvalCalcInputs->setModifiedAt(new \DateTime());
        } else {
            $orgRiskvalCalcInputs = new OrgRiskvalCalcInputs();
            $orgRiskvalCalcInputs->setOrg($organization);
            $orgRiskvalCalcInputs->setPerson($student);
            $this->orgRiskvalCalcInputsRepository->persist($orgRiskvalCalcInputs);

        }

        return 1;

    }

    /**
     * Updates Students Risk Flags (Used By Uploads)
     * (THIS IS FOR EXTERNAL STUDENT IDS)
     *
     * This is inputing into the buffer table org_riskval_calc_inputs to avoid table locking issues
     * A trigger on org_riskval_calc_inputs will then actually set the risk flag in org_calc_flags_risk
     *
     * @param array $studentsToUpdate - an array of students to update; should be their externalId
     * @param int $organizationId - the organization's Id
     * @param int $batchSize - After how many rows should the person Repository be flushed
     * @return int
     */
    public function updateStudentRiskFlags($studentsToUpdate, $organizationId, $batchSize = 30)
    {
        $flushCount = 0;
        foreach ($studentsToUpdate as $studentToUpdate) {

            if (empty(trim($studentToUpdate))) {
                continue;
            }

            $personObject = $this->personRepository->findOneBy([
                'externalId' => $studentToUpdate,
                'organization' => $organizationId
            ]);

            $flushCount = $flushCount + $this->updateStudentRiskFlag($personObject, $organizationId);

            if (($flushCount % $batchSize) === 0) {
                $this->personRepository->flush();
            }
        }
        $this->personRepository->flush();
        return $flushCount;
    }

    /**
     * Updates Students Risk Flags (Used By Academic Updates)
     * (THIS IS FOR INTERNAL STUDENT IDS)
     *
     * This is inputing into the buffer table org_riskval_calc_inputs to avoid table locking issues
     * A trigger on org_riskval_calc_inputs will then actually set the risk flag in org_calc_flags_risk
     *
     * @param array $studentsToUpdate - an array of students to update; should be their internal ids
     * @param int $organizationId - the organization's Id
     * @param int $batchSize - After how many rows should the person Repository be flushed
     * @return int
     */
    public function updateStudentRiskFlagsWithInternalIds($studentsToUpdate, $organizationId, $batchSize = 30)
    {
        $flushCount = 0;
        foreach ($studentsToUpdate as $studentToUpdate) {

            $personObject = $this->personRepository->find($studentToUpdate);

            $flushCount = $flushCount + $this->updateStudentRiskFlag($personObject, $organizationId);

            if (($flushCount % $batchSize) === 0) {
                $this->personRepository->flush();
            }
        }
        $this->personRepository->flush();
        return $flushCount;
    }


    /**
     * Check Statuses and Update a Students Risk Flag
     *
     * @param Person $personObject
     * @param int $organizationId
     * @return int
     */
    private function updateStudentRiskFlag($personObject, $organizationId) {
        $flushCount = 0;
        if ($personObject) {
            $orgPersonStudentObject = $this->orgPersonStudentRepository->findOneBy([
                'person' => $personObject,
                'organization' => $organizationId
            ]);
            if ($orgPersonStudentObject) {
                $studentId = $personObject->getId();
                $organization = $personObject->getOrganization();
                //Need to check student id associated to risk group
                $currentDateTime = new \DateTime();
                $currentDateTimeString = $currentDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                $isStudentInValidRiskGroup = $this->riskGroupPersonHistoryRepository->isStudentInValidRiskGroup($studentId, $currentDateTimeString);
                if ($isStudentInValidRiskGroup) {
                    $flushCount = $flushCount + $this->addStudentToCalcFlag($organization, $personObject);
                }
            } else {
                $this->logger->error(self::PERSON_NOT_STUDENT_ERROR_MESSAGE);
            }

        } else {
            $this->logger->error(self::PERSON_NOT_FOUND_ERROR_MESSAGE);
        }
        return $flushCount;
    }

}