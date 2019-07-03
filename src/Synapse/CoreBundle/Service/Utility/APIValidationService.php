<?php

namespace Synapse\CoreBundle\Service\Utility;

use Doctrine\Common\Cache\RedisCache;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicUpdateBundle\EntityDto\IndividualAcademicUpdateDTO;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\RoleLangRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;

/**
 * Class for functions doing validations on V2 APIs.
 *
 * @package Synapse\CoreBundle\Service\Utility
 *
 * @DI\Service("api_validation_service")
 */
class APIValidationService extends AbstractService
{

    const SERVICE_KEY = "api_validation_service";

    // Scaffolding

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    // Repository

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var RoleLangRepository
     */
    private $roleLangRepository;

    /**
     * APIValidationService Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        $this->container = $container;
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);

        // Services
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);

        // Repository
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->roleLangRepository = $this->repositoryResolver->getRepository(RoleLangRepository::REPOSITORY_KEY);
    }

    /**
     * Updates the API error count on Redis.
     * If the client has more than X validation errors in Y time frame, throw an exception.
     *
     * @param int $organizationId
     * @param array $validationErrors
     * @throws AccessDeniedException
     */
    public function updateOrganizationAPIValidationErrorCount($organizationId, $validationErrors)
    {

        $cacheKey = "api-errors-{$organizationId}";
        $previousErrors = $this->cache->fetch($cacheKey);
        $apiErrorIntervalObject = $this->ebiConfigRepository->findOneBy(['key' => SynapseConstant::API_ERROR_INTERVAL_KEY]);
        $errorIntervalMinutes = $apiErrorIntervalObject->getValue();

        $currentDate = new \DateTime();
        $windowAdjustedCurrentDate = clone $currentDate;
        $windowAdjustedCurrentDate->add(new \DateInterval("PT" . $errorIntervalMinutes . "M"));
        $windowAdjustedCurrentDateFormatted = $windowAdjustedCurrentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        $currentErrorCount = count($validationErrors);
        if ($previousErrors) {
            $expiresDateTime = new \DateTime($previousErrors['expires_at']);
            $expiresDateTimeFormatted = $expiresDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
            $previousErrorCount = $previousErrors['count'];
            
            // If expires at time is not passed (greater than current time).
            if ($expiresDateTime > $currentDate) {
                // Check if the error count has exceeded $maxAllowedErrors
                $this->checkAndSetCacheErrors($currentErrorCount + $previousErrorCount, $cacheKey, $expiresDateTimeFormatted);
            } else {
                // The expired at time has passed Validate $currentErrorCount with allowed error count.
                // Reset expires at to the current date move ahead $errorIntervalMinutes minutes
                // in future and store the validation errors that have been processed.
                $this->checkAndSetCacheErrors($currentErrorCount, $cacheKey, $windowAdjustedCurrentDateFormatted);
            }
        } else if ($currentErrorCount > 0) {
            // There is no previous errors, but the current API call has errors.
            // Validate $currentErrorCount with allowed error count.
            $this->checkAndSetCacheErrors($currentErrorCount, $cacheKey, $windowAdjustedCurrentDateFormatted);
        }
    }

    /**
     * Validate API error count with max allowed error count and throw AccessDeniedException if count exceeded than the limit.
     * also set error count and expire at in cache.
     *
     * @param int $apiErrorCount
     * @param string $cacheKey
     * @param string $expiresAt
     * @throws AccessDeniedException
     */
    private function checkAndSetCacheErrors($apiErrorCount, $cacheKey, $expiresAt)
    {
        $apiMaxErrorSwitchObject = $this->ebiConfigRepository->findOneBy(['key' => SynapseConstant::API_MAX_ERROR_COUNT_KEY]);
        $maxAllowedErrors = $apiMaxErrorSwitchObject->getValue();
        // Set error count and expires at in cache.
        $this->cache->save($cacheKey, ['count' => $apiErrorCount, 'expires_at' => $expiresAt]);
        $this->logger->info("Cache key:" . $cacheKey . " Error count:" . $apiErrorCount . " Expires at:" . $expiresAt);

        if ($apiErrorCount > $maxAllowedErrors) {
            // $maxAllowedErrors has been exceeded. Throw an access denied exception indicating that the client has surpassed the threshold in the allowed time.
            $message = "You have exceeded the number of allowed validation errors in the allowed time frame. Please try your API call again after {$expiresAt} UTC";
            throw new AccessDeniedException($message, $message);
        }
    }

    /**
     * Validates whether or not the logged in user is the organization's API Coordinator.
     *
     * @param int $organizationId
     * @param int $personId
     * @param bool|null $throwException
     * @throws AccessDeniedException
     * @return bool
     */
    public function isOrganizationAPICoordinator($organizationId, $personId, $throwException = true)
    {

        $serviceAccountRoleObject = $this->roleLangRepository->findOneBy(['roleName' => SynapseConstant::SERVICE_ACCOUNT_ROLE_NAME]);
        if ($serviceAccountRoleObject) {
            $apiCoordinatorRoleId = $serviceAccountRoleObject->getRole()->getId();
            $apiCoordinatorObject = $this->organizationRoleRepository->findOneBy(['person' => $personId, 'organization' => $organizationId, 'role' => $apiCoordinatorRoleId]);
            if (!$apiCoordinatorObject) {
                if ($throwException) {
                    throw new AccessDeniedException("The credentials used are not associated with a service account at the organization. Please use valid credentials.");
                } else {
                    return false;
                }
            }
        } else {
            if ($throwException) {
                throw new AccessDeniedException("There was an error when fetching the service account. Please contact Mapworks Client Services.");
            } else {
                return false;
            }

        }
        return true;
    }

    /**
     * Validates if API integration is enabled.
     *
     * @throws AccessDeniedException
     * @return bool
     */
    public function isAPIIntegrationEnabled()
    {
        $apiMasterSwitchObject = $this->ebiConfigRepository->findOneBy(['key' => SynapseConstant::API_INTEGRATION_MASTER_SWITCH_KEY]);
        $isAPIIntegrationEnabled = $apiMasterSwitchObject->getValue();
        if (!$isAPIIntegrationEnabled) {
            throw new AccessDeniedException("API Integration has been disabled by Skyfactor. If you have any questions, please contact the Mapworks Client Services team.");
        }
        return true;
    }

    /**
     * Checks if the currently submitted academic update is a duplicate of the previous academic update.
     *
     * @param IndividualAcademicUpdateDTO $academicUpdate
     * @param array $previousAcademicUpdate
     * @return bool
     */
    public function isDuplicateAcademicUpdate($academicUpdate, $previousAcademicUpdate)
    {
        $currentFailureRiskLevel = $academicUpdate->getFailureRiskLevel();
        $currentInProgressGrade = $academicUpdate->getInProgressGrade();
        $currentAbsences = $academicUpdate->getAbsences();
        $currentComment = $academicUpdate->getComment();
        $currentSentToStudent = $academicUpdate->getSendToStudent();
        $currentFinalGrade = $academicUpdate->getFinalGrade();
        $previousFailureRiskLevel = $previousAcademicUpdate['failure_risk_level'];
        $previousInProgressGrade = $previousAcademicUpdate['in_progress_grade'];
        $previousAbsences = $previousAcademicUpdate['absences'];
        $previousComment = $previousAcademicUpdate['comment'];
        $previousSentToStudent = $previousAcademicUpdate['send_to_student'];
        $previousFinalGrade = $previousAcademicUpdate['final_grade'];

        if ($currentFailureRiskLevel != $previousFailureRiskLevel || $currentInProgressGrade != $previousInProgressGrade || $currentAbsences != $previousAbsences || $currentComment != $previousComment || $currentSentToStudent != $previousSentToStudent || $previousFinalGrade != $currentFinalGrade) {
            $isDuplicate = false;
        } else {
            $isDuplicate = true;
        }

        return $isDuplicate;
    }

    /**
     * Gets the request body size by counting all of the base level arrays at the passed in key
     *
     * @param array $dataArray - json_decoded DTO object.
     * @param string $keyToCheck - pass in base-level key to count on
     * @return int
     */
    public function getRequestSize($dataArray, $keyToCheck)
    {

        $baseKeyCount = 0;
        if (is_array($dataArray)) {
            foreach ($dataArray as $data) {
                if (is_array($data)) {
                    if (array_key_exists($keyToCheck, $data)) {
                        $baseKeyData = $data[$keyToCheck];
                        if (is_array($baseKeyData)) {
                            $baseKeyCount += count($baseKeyData);
                        }
                    } else {
                        $baseKeyCount += $this->getRequestSize($data, $keyToCheck);
                    }
                }
            }
        }
        return $baseKeyCount;
    }

    /**
     * Validates if request size is allowed
     *
     * @param string $requestJSON
     * @param string $keyToCheck - Base-level key to count contents.
     * @return boolean
     * @throws AccessDeniedException
     */
    public function isRequestSizeAllowed($requestJSON, $keyToCheck)
    {
        $coursesWithAcademicUpdatesArray = json_decode($requestJSON, true);
        $academicUpdatesCount = 0;
        if ($keyToCheck) {
            $academicUpdatesCount = $this->getRequestSize($coursesWithAcademicUpdatesArray, $keyToCheck);
        }
        $limitForPost = $this->ebiConfigService->get(SynapseConstant::POST_PUT_MAX_RECORD_COUNT);

        if ($academicUpdatesCount > $limitForPost) {
            throw new AccessDeniedException("The body of your POST / PUT request has exceeded the maximum number of create / update records. Please make sure your request contains less than $limitForPost records at the base level of the JSON body.");
        }

        return true;
    }

    /**
     * Add error to organization API Error count if there is validation error
     *
     * @param int $organizationId
     * @param array $validationErrors
     * @param bool $isInternalIds
     * @return bool
     * @throws SynapseValidationException
     */
    public function addErrorsToOrganizationAPIErrorCount($organizationId, $validationErrors, $isInternalIds)
    {
        if (!empty($validationErrors)) {
            if (!$isInternalIds) {
                $this->updateOrganizationAPIValidationErrorCount($organizationId, $validationErrors);
            }
            $validationErrorsArray = array_map('current', $validationErrors);
            $validationErrorString = implode(',', $validationErrorsArray);
            throw new SynapseValidationException($validationErrorString);
        } else {
            return true;
        }
    }
}