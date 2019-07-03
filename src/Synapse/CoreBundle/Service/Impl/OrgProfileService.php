<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Synapse\CoreBundle\Entity\OrgMetadata;
use Synapse\CoreBundle\Entity\OrgMetadataListValues;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetMetadataRepository;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\OrgProfileConstant;
use Synapse\CoreBundle\Util\Constants\ProfileConstant;
use Synapse\RestBundle\Entity\ProfileDto;
use Synapse\RestBundle\Entity\ReOrderProfileDto;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("orgprofile_service")
 */
class OrgProfileService extends AbstractService
{

    const SERVICE_KEY = 'orgprofile_service';

    //Class level variables

    private  $validCalenderAssignment = array(
        'N',
        'Y',
        'T'
    );

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

    //Services

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionsetService;
    // Repositories

    /**
     * @var OrgMetadataListValuesRepository
     */
    private $orgMetadataListRepository;

    /**
     * @var OrgMetadataRepository
     */
    private $orgMetadataRepository;

    /**
     * @var orgPermissionsetMetadataRepository
     */
    private $orgPermissionsetMetadataRepository;


    /**
     * @var PersonOrgMetaDataRepository
     */
    private $personOrgMetadataRepository;

    /**
     * OrgProfileService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
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

        // Scaffolding
        $this->rbacManager = $this->container->get(SynapseConstant::TINYRBAC_MANAGER);
        $this->validator = $this->container->get(SynapseConstant::VALIDATOR);
        // Service
        $this->orgPermissionsetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);

        //Repositories
        $this->orgMetadataListRepository = $this->repositoryResolver->getRepository(OrgMetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgMetadataRepository = $this->repositoryResolver->getRepository(OrgMetadataRepository::REPOSITORY_KEY);
        $this->orgPermissionsetMetadataRepository = $this->repositoryResolver->getRepository(OrgPermissionsetMetadataRepository::REPOSITORY_KEY);
        $this->personOrgMetadataRepository = $this->repositoryResolver->getRepository(PersonOrgMetaDataRepository::REPOSITORY_KEY);

    }

    /**
     *
     * @param ProfileDto $profileDto            
     */
    public function createProfile(ProfileDto $profileDto)
    {
        $this->rbacManager->checkAccessToOrganization($profileDto->getOrganizationId());
        $logContent = $this->container->get('loggerhelper_service')->getLog($profileDto);
        $this->logger->debug(" Creating Organization Profile -  " . $logContent);
        
        $this->orgMetadataRepository = $this->repositoryResolver->getRepository(OrgProfileConstant::ORGMETADATA_REPO);
        $this->checkExistingColumnNames($profileDto->getOrganizationId(), $profileDto->getItemLabel(), 'EBI');
        $organization = $this->container->get('org_service')->find($profileDto->getOrganizationId());
        $sequence = $this->orgMetadataRepository->getOrgProfileCount($organization);
        /**
         * Assign to next values
         */
        $sequence ++;
        $orgMetaData = new OrgMetadata();
        $orgMetaData->setMetaKey($profileDto->getItemLabel());
        $orgMetaData->setMetaName($profileDto->getDisplayName());
        $orgMetaData->setMetadataType($profileDto->getItemDataType());
        $orgMetaData->setMetaDescription($profileDto->getItemSubtext());
        $orgMetaData->setDefinitionType('O');
       
        
        
        if ($profileDto->getCalenderAssignment() != null && ! in_array($profileDto->getCalenderAssignment(), $this->validCalenderAssignment)) {
            $this->logger->info("The calender assignment is invalid.");
            throw new ValidationException([
                'The calender assignment is invalid.'
                ]);
        }
        $orgMetaData->setScope($profileDto->getCalenderAssignment());
        if ($profileDto->getItemDataType() == 'N') {
            $numberDetail = $profileDto->getNumberType();
            if (count($numberDetail) > 0) {
                if (is_numeric($numberDetail[OrgProfileConstant::FIELD_MINDIGITS])) {
                    $orgMetaData->setMinRange($numberDetail[OrgProfileConstant::FIELD_MINDIGITS]);
                }
                if (is_numeric($numberDetail[OrgProfileConstant::FIELD_MAXDIGITS])) {
                    $orgMetaData->setMaxRange($numberDetail[OrgProfileConstant::FIELD_MAXDIGITS]);
                }
                $orgMetaData->setNoOfDecimals($numberDetail[OrgProfileConstant::FIELD_DECIMALPTS]);
            }
        }
        $orgMetaData->setOrganization($organization);
        $orgMetaData->setIsRequired((bool) $orgMetaData->getIsRequired());
        $orgMetaData->setSequence($sequence);
        $orgMetaData->setStatus(OrgProfileConstant::ACTIVE);
        $validator = $this->container->get('validator');
        $errors = $validator->validate($orgMetaData);
        $this->buildAndThrowExceptionForErrors($errors);
        $this->isEbiExists($profileDto->getItemLabel());
        $this->orgMetadataRepository->persist($orgMetaData, false);
        $metadataListValues = $profileDto->getCategoryType();
        if ($orgMetaData->getMetadataType() == 'S' && isset($metadataListValues) && count($metadataListValues) > 0) {
            // Check unique
            $valArray = array();
            $ansArray = [];
            foreach ($metadataListValues as $metadataListValue) {
                $this->checkListValueAndAnswerForDuplicateValuesWithinRequest($metadataListValue, $valArray, $ansArray);
                $valArray[] = $metadataListValue[OrgProfileConstant::FIELD_VALUE];
                $ansArray[] = $metadataListValue[OrgProfileConstant::FIELD_ANS];
                $listVals = new OrgMetadataListValues();
                $listVals->setOrgMetadata($orgMetaData);
                $listVals->setListName($metadataListValue[OrgProfileConstant::FIELD_ANS]);
                $listVals->setListValue($metadataListValue[OrgProfileConstant::FIELD_VALUE]);
                $listVals->setSequence($metadataListValue[OrgProfileConstant::FIELD_SEQNO]);
                $listVals = $this->orgMetadataRepository->persist($listVals, false);
            }
        }
        $this->orgMetadataRepository->flush();
        $profileDto->setId($orgMetaData->getId());
        $profileDto->setSequenceNo($sequence);
        $profileDto->setStatus($orgMetaData->getStatus());
        $this->logger->info("Organization Profile Created");
        return $profileDto;
    }

    /**
     * Validate and update selected ISP data
     *
     * @param ProfileDto $profileDto
     * @return ProfileDto
     * @throws SynapseValidationException
     */
    public function editProfile(ProfileDto $profileDto)
    {
        $this->rbacManager->checkAccessToOrganization($profileDto->getOrganizationId());
        $metadataId = $profileDto->getId();
        $organizationId = $profileDto->getOrganizationId();

        $orgMetadataObject = $this->orgMetadataRepository->findOneBy(['organization' => $organizationId, 'id' => $metadataId
        ], new SynapseValidationException('Profile not found'));

        $metaDataTypeInOrgMetadata = $orgMetadataObject->getMetadataType();
        $valuesForListValue = $this->personOrgMetadataRepository->findOneBy(["orgMetadata" => $metadataId]);
        $isMetaDataMappedOnISPLevel = !empty($valuesForListValue) ? true : false;

        $dtoItemDataType = $profileDto->getItemDataType();

        if (($isMetaDataMappedOnISPLevel) && ($metaDataTypeInOrgMetadata != $dtoItemDataType)) {
            throw  new SynapseValidationException("Wrong metadata type selected");
        }

        $orgMetadataObject->setMetadataType($dtoItemDataType);

        if ($dtoItemDataType == 'N') {

            $minRange = $orgMetadataObject->getMinRange();
            $maxRange = $orgMetadataObject->getMaxRange();

            if ($isMetaDataMappedOnISPLevel) {
                if (isset($minRange) && $profileDto->getNumberType()['min_digits'] > $minRange) {
                    throw  new SynapseValidationException("Min Digits cannot be larger than current value.");
                }
                if (isset($maxRange) && $profileDto->getNumberType()['max_digits'] < $maxRange) {
                    throw  new SynapseValidationException("Max Digits cannot be smaller than current value.");
                }
            }

            $itemDataTypeNumber = $profileDto->getNumberType();
            if (count($itemDataTypeNumber) > 0) {
                $minRange = $itemDataTypeNumber['min_digits'] ? $itemDataTypeNumber['min_digits'] : null;
                $maxRange = $itemDataTypeNumber['max_digits'] ? $itemDataTypeNumber['max_digits'] : null;
                $decimalNumber = $itemDataTypeNumber['decimal_points'] ? $itemDataTypeNumber['decimal_points'] : 0;

                $orgMetadataObject->setMinRange($minRange);
                $orgMetadataObject->setMaxRange($maxRange);

                if (!$isMetaDataMappedOnISPLevel) {
                    $orgMetadataObject->setNoOfDecimals($decimalNumber);
                }
            }
        } else {
            $orgMetadataObject->setMaxRange(0);
            $orgMetadataObject->setMinRange(0);
            $orgMetadataObject->setNoOfDecimals(0);
        }

        if (!$isMetaDataMappedOnISPLevel) {
            $orgMetadataObject->setMetaKey($profileDto->getItemLabel());
            $orgMetadataObject->setMetaName($profileDto->getDisplayName());
        }

        $orgMetadataObject->setMetaDescription($profileDto->getItemSubtext());

        if ($profileDto->getCalenderAssignment() != null && !in_array($profileDto->getCalenderAssignment(), $this->validCalenderAssignment)) {
            throw new SynapseValidationException("The calender assignment is invalid.");
        }

        if (!$isMetaDataMappedOnISPLevel) {
            $orgMetadataObject->setScope($profileDto->getCalenderAssignment());
        }

        $errors = $this->validator->validate($orgMetadataObject);
        $this->buildAndThrowExceptionForErrors($errors);
        $this->isEbiExists($profileDto->getItemLabel());

        if ($metaDataTypeInOrgMetadata != 'S') {
            $this->removeMetaListValues($orgMetadataObject);
        }

        if ($orgMetadataObject->getMetadataType() == 'S') {
            $metadataListValues = $profileDto->getCategoryType();
            $this->editISPListItems($metadataListValues, $orgMetadataObject, $isMetaDataMappedOnISPLevel);
        }

        $this->orgMetadataRepository->flush();

        $status = null;
        if (!empty($orgMetadataObject->getStatus())) {
            $status = $orgMetadataObject->getStatus();
        }

        $profileDto->setStatus($status);
        return $profileDto;
    }


    /**
     * Get all the profile block items for an organzation .
     * TODO: Technical Debt for this function in ESPRJ-14941
     *
     * @param string $organizationId
     * @param bool $exclude
     * @param string $status
     * @param bool $authorizationFlag
     * @param bool $excludeType
     * @param int|null $userId => will only get Profile Items that a user has access to
     * @return array
     */
    public function getInstitutionSpecificProfileBlockItems($organizationId, $exclude = false, $status = 'all', $authorizationFlag = true, $excludeType = false, $userId = null)
    {
        if ($authorizationFlag) {
            $this->rbacManager->checkAccessToOrganization($organizationId);
        }

        $excludeMetaDataTypeArray = array(
            "T" => 'term',
            "Y" => 'year',
            "N" => 'none'
        );

        $blockId = $this->getProfileBlocksWithPermission();

        if ($excludeType) {
            if (!in_array($excludeType, $excludeMetaDataTypeArray)) {
                throw new SynapseValidationException("Invalid Exclude Type");
            }
            $excludeType = array_search($excludeType, $excludeMetaDataTypeArray);
        }

        $profileItems = $this->orgMetadataRepository->getProfile($organizationId, $exclude, $blockId, $status, $excludeType);

        // This is a optional check to make sure that the person in question
        // has access to all ISPs that are returned
        $profileItemsPersonHasAccessTo = array();
        if ($userId) {
            $profileItemsPersonHasAccessTo = $this->orgPermissionsetMetadataRepository->getAllISPsByPersonIdWithRelationToStudentAccess($userId, $organizationId);
        }


        $archivedItemsCount = $this->orgMetadataRepository->getProfile($organizationId, $exclude, $blockId, 'archive');
        $response = array();
        $profileItemsArray = array();
        $categoryTypesArray = array();
        $itemsArray = array();
        $response['display_name'] = 'Institution Specific Profile Items';
        $response['organization_id'] = $organizationId;
        $response['total_archive_count'] = count($archivedItemsCount);

        foreach ($profileItems as $profileItem) {

            if ($userId) {
                if (!in_array($profileItem['org_metadata_id'], $profileItemsPersonHasAccessTo)) {
                    continue;
                }
            }


            $profileItemsArray['id'] = $profileItem['org_metadata_id'];
            $profileItemsArray['modified_at'] = $profileItem['modified_at'];
            $profileItemsArray['display_name'] = $profileItem['display_name'];
            $profileItemsArray['item_data_type'] = $profileItem['item_data_type'];
            $profileItemsArray['definition_type'] = $profileItem['definition_type'];
            $profileItemsArray['item_label'] = $profileItem['item_label'];
            $profileItemsArray['item_subtext'] = $profileItem['item_subtext'];
            $profileItemsArray['sequence_no'] = $profileItem['sequence_no'];
            $profileItemsArray['calendar_assignment'] = is_null($profileItem['calendar_assignment']) ? "" : $profileItem['calendar_assignment'];

            if ($profileItem['item_data_type'] == "N") {
                $profileItemsArray['number_type']['decimal_points'] = $profileItem['decimal_points'];
                $profileItemsArray['number_type']['min_digits'] = $profileItem['min_digits'];
                $profileItemsArray['number_type']['max_digits'] = $profileItem['max_digits'];
            }

            if (!empty($profileItemsArray['year_term'])) {
                unset($profileItemsArray['year_term']);
            }

            if ($profileItem['calendar_assignment'] == 'Y' || $profileItem['calendar_assignment'] == 'T') {

                $yearAndTermOrgMetadata = $this->personOrgMetadataRepository->getYearAndTermByOrgMetadataId($profileItem['org_metadata_id']);

                foreach ($yearAndTermOrgMetadata as $yearAndTermOrgMetadataValue) {
                    $profileItemsArray['year_term'][] = $yearAndTermOrgMetadataValue;
                }
            }

            if ($profileItem['item_data_type'] == "S") {

                $orgMetadataListValuesObjectArray = $this->orgMetadataListRepository->findBy([
                    "orgMetadata" => $profileItem['org_metadata_id']
                ]);


                foreach ($orgMetadataListValuesObjectArray as $orgMetadataListValuesObject) {

                    $categoryTypesArray['answer'] = $orgMetadataListValuesObject->getListName();
                    $categoryTypesArray['value'] = $orgMetadataListValuesObject->getListValue();
                    $categoryTypesArray['sequence_no'] = $orgMetadataListValuesObject->getSequence();
                    $profileItemsArray['category_type'][] = $categoryTypesArray;
                }
            }
            if ($profileItem['status'] == 'active' || $profileItem['status'] == null) {
                $status = 'active';
            } else {
                $status = 'archive';
            }
            $profileItemsArray['status'] = $status;
            if ($profileItem['pom_id'] or $profileItem['au_id']) {
                $profileItemsArray['item_used'] = true;
            } else {
                $profileItemsArray['item_used'] = false;
            }
            $itemsArray[] = $profileItemsArray;
            unset($profileItemsArray);
        }
        $response['profile_items'] = $itemsArray;
        return $response;
    }

    /**
     * Returns ISP data in the form of profile dto
     *
     * @param int $metadataId
     * @return ProfileDto
     */
    public function getProfile($metadataId)
    {
        $orgProfile = $this->orgMetadataRepository->find($metadataId, new SynapseValidationException("Profile not found"));
        $metadataOrgId = $orgProfile->getOrganization()->getId();
        $this->rbacManager->checkAccessToOrganization($metadataOrgId);
        $metadataType = $orgProfile->getMetadataType();

        $profileDto = new ProfileDto();
        $profileDto->setId($orgProfile->getId());
        $profileDto->setDefinitionType($orgProfile->getDefinitionType());
        $profileDto->setItemDataType($metadataType);
        $profileDto->setSequenceNo($orgProfile->getSequence());
        $profileDto->setDisplayName($orgProfile->getMetaName());
        $profileDto->setItemLabel($orgProfile->getMetaKey());
        $profileDto->setItemSubtext(empty($orgProfile->getMetaDescription()) ? "" : $orgProfile->getMetaDescription());
        $profileDto->setCalenderAssignment(is_null($orgProfile->getScope()) ? "" : $orgProfile->getScope());

        $metaListValues = $this->orgMetadataListRepository->findBy(["orgMetadata" => $orgProfile]);

        if ($metadataType == 'S') {
            $existingListValuePersonMapping = $this->buildOrgMetadataListValuesListValueMap($orgProfile);
            $listValues = [];
            foreach ($metaListValues as $metaListValue) {
                $listValue = [];
                $metadataListValueId = $metaListValue->getId();
                $listValue['can_edit_list_row'] = $existingListValuePersonMapping[$metadataListValueId]['has_values'];
                $listValue['answer'] = $metaListValue->getListName();
                $listValue['value'] = $metaListValue->getListValue();
                $listValue['sequence_no'] = $metaListValue->getSequence();
                $listValue['org_metadata_list_value_id'] = $metadataListValueId;
                $listValues[] = $listValue;
            }
            $profileDto->setCategoryType($listValues);
        }

        $valuesForListValue = $this->personOrgMetadataRepository->findBy(["orgMetadata" => $metadataId]);

        $fieldNameCanBeEditedIfMetaDataMapped = [];
        $isMetaDataMappedOnISPLevel = !empty($valuesForListValue) ? true : false;

        if ($metadataType == 'N') {
            $numberArray = [];
            $numberArray['min_digits'] = is_null($orgProfile->getMinRange()) ? "" : (double)$orgProfile->getMinRange();
            $numberArray['max_digits'] = is_null($orgProfile->getMaxRange()) ? "" : (double)$orgProfile->getMaxRange();
            $numberArray['decimal_points'] = is_null($orgProfile->getNoOfDecimals()) ? "" : (double)$orgProfile->getNoOfDecimals();
            $profileDto->setNumberType($numberArray);
        }

        $status = ($orgProfile->getStatus() == 'active' || empty($orgProfile->getStatus())) ? 'active' : 'archive';
        $profileDto->setStatus($status);

        if (($isMetaDataMappedOnISPLevel) && ($metadataType == 'N' || $metadataType == 'T' || $metadataType == 'D')) {
            if ($metadataType == 'N') {
                $fieldNameCanBeEditedIfMetaDataMapped['min_digits'] = true;
                $fieldNameCanBeEditedIfMetaDataMapped['max_digits'] = true;
            }
            $fieldNameCanBeEditedIfMetaDataMapped['item_subtext'] = true;
        }

        $profileDto->setIsMetaDataMapped($isMetaDataMappedOnISPLevel);
        $profileDto->setFieldNameCanBeEditedIfMetaDataMapped($fieldNameCanBeEditedIfMetaDataMapped);
        return $profileDto;
    }

    public function deleteProfile($metadataId)
    {
        $this->logger->debug("Delete Profile for Metadata Id" . $metadataId);
        $this->orgMetadataRepository = $this->repositoryResolver->getRepository(OrgProfileConstant::ORGMETADATA_REPO);
        $this->orgMetadataListRepository = $this->repositoryResolver->getRepository(OrgProfileConstant::ORGMETADATALIST_REPO);
        $metadataMaster = $this->getValidProfile($metadataId);

        $this->rbacManager->checkAccessToOrganization($metadataMaster->getOrganization()->getId());

        $auMetadataRepo = $this->repositoryResolver->getRepository('SynapseAcademicUpdateBundle:AcademicUpdateRequestMetadata');
        $isIspExists = $auMetadataRepo->isIspExists($metadataId);
        if (count($isIspExists) > 0) {
            $this->logger->error(" OrgProfile Service - deleteProfile - Academic Profile Reference Error" );
            throw new ValidationException([
                'Academic Update attached this profile'
            ], 'Academic Update attached this profile', 'au_profile_reference_error');
        }
        $definitionType = $metadataMaster->getDefinitionType();
        $oldSeq = $metadataMaster->getSequence();
        $metadataMaster->setMetaKey(uniqid() . "-Deleted");
        $masterValues = $this->orgMetadataListRepository->findBy(array(
            OrgProfileConstant::FIELD_ORGMETADATA => $metadataMaster
        ));
        if ($masterValues) {
            foreach ($masterValues as $mValue) {
                $this->orgMetadataListRepository->remove($mValue);
            }
        }
        $metadataMaster->setSequence(NULL);
        $this->orgMetadataRepository->flush();
        /**
         * Removing from Associated Org Permission
         */
        $this->removeOrgPermissionset($metadataMaster);
        
        $this->orgMetadataRepository->remove($metadataMaster);
        $this->orgMetadataRepository->flush();
        $this->orgMetadataRepository->clear();
        $metadataMasterSequence = NULL;
        $metadataMasterSequence = $this->orgMetadataRepository->findOneBy(array(
            OrgProfileConstant::SEQUENCE => ($oldSeq + 1),
            OrgProfileConstant::DEFINITION_TYPE => $definitionType,
            OrgProfileConstant::FIELD_ORG => $metadataMaster->getOrganization()
        ));
        
        /* Reseting Sequence */
        while ($metadataMasterSequence) {
            $metadataMasterSequence->setSequence(($metadataMasterSequence->getSequence() - 1));
            $oldSeq ++;
            $metadataMasterSequence = $this->orgMetadataRepository->findOneBy(array(
                OrgProfileConstant::SEQUENCE => ($oldSeq + 1),
                OrgProfileConstant::DEFINITION_TYPE => $definitionType,
                OrgProfileConstant::FIELD_ORG => $metadataMaster->getOrganization()
            ));
        }
        $this->orgMetadataRepository->flush();
        $this->logger->info("Delete Profile for Metadata Id" . $metadataId);
        return $metadataMaster;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\CoreBundle\Service\ProfileServiceInterface::reorderProfile()
     */
    public function reorderProfile(ReOrderProfileDto $reOrderProfileDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($reOrderProfileDto);
        $this->logger->debug(" ReOrder Organization Profile -  " . $logContent);
        
        $this->orgMetadataRepository = $this->repositoryResolver->getRepository(OrgProfileConstant::ORGMETADATA_REPO);
        $metadataId = $reOrderProfileDto->getId();
        $newSequenceId = $reOrderProfileDto->getSequenceNo();
        $metadataMaster = $this->orgMetadataRepository->find($metadataId);
        $this->getValidProfile($metadataId);
        
        $oldSequenceId = $metadataMaster->getSequence();
        $organizationId = $metadataMaster->getOrganization()->getId();
        
        $definitionType = $metadataMaster->getDefinitionType();
        $maxSequenceId = $this->getMaxSequenceId($metadataMaster);
        
        /* Scenario# 3 */
        if ($newSequenceId > $maxSequenceId) {
            $newSequenceId = $maxSequenceId;
        }
        
        /* Scenario# 1 */
        if ($oldSequenceId < $newSequenceId) {
            for ($i = $oldSequenceId + 1; $i <= $newSequenceId; $i ++) {
                $metadataMasterSequence = $this->getMasterSequence($i, $definitionType, $metadataMaster);
                if ($metadataMasterSequence) {
                    $j = $i - 1;
                    $metadataMasterSequence->setSequence($j);
                    $this->orgMetadataRepository->merge($metadataMasterSequence);
                }
            }
            $metadataMaster->setSequence($newSequenceId);
            $this->orgMetadataRepository->merge($metadataMaster);
        }
        
        /* Scenario# 3 */
        if ($oldSequenceId > $newSequenceId) {
            for ($i = $oldSequenceId - 1; $i >= $newSequenceId; $i --) {
                $metadataMasterSequence = $this->getMasterSequence($i, $definitionType, $metadataMaster);
                if ($metadataMasterSequence) {
                    $j = $i + 1;
                    $metadataMasterSequence->setSequence($j);
                    $this->orgMetadataRepository->merge($metadataMasterSequence);
                }
            }
            $metadataMaster->setSequence($newSequenceId);
            $this->orgMetadataRepository->merge($metadataMaster);
        }
        
        $this->orgMetadataRepository->flush();
        $this->logger->info("Organization Profile Reordered");
        return $metadataMaster;
    }

    private function getMaxSequenceId($metadataMaster)
    {
        $maxSequenceId = $this->orgMetadataRepository->getOrgProfileCount($metadataMaster->getOrganization());
        return $maxSequenceId;
    }

    private function getMasterSequence($i, $definitionType, $metadataMaster)
    {
        $metadataMasterSequence = $this->orgMetadataRepository->findOneBy(array(
            OrgProfileConstant::SEQUENCE => $i,
            OrgProfileConstant::DEFINITION_TYPE => $definitionType,
            OrgProfileConstant::FIELD_ORG => $metadataMaster->getOrganization()
        ));
        return $metadataMasterSequence;
    }

    private function getValidProfile($metadataId)
    {
        $metadataMaster = $this->orgMetadataRepository->find($metadataId);
        if (! isset($metadataMaster)) {
            throw new ValidationException([
                OrgProfileConstant::PROFILE_NOT_FOUND
            ], OrgProfileConstant::PROFILE_NOT_FOUND, OrgProfileConstant::PROFILE_NOT_FOUND);
        }
        return $metadataMaster;
    }

    private function createMetaDataList($metadataListValues, $metadataMaster)
    {
        $valArray = array();
        $ansArray = [];
        foreach ($metadataListValues as $metadataListValue) {
            $this->checkListValueAndAnswerForDuplicateValuesWithinRequest($metadataListValue, $valArray, $ansArray);
            $valArray[] = $metadataListValue[OrgProfileConstant::FIELD_VALUE];
            $ansArray[] = $metadataListValue[OrgProfileConstant::FIELD_ANS];
            $listVals = new OrgMetadataListValues();
            
            $listVals->setOrgMetadata($metadataMaster);
            $listVals->setListName($metadataListValue[OrgProfileConstant::FIELD_ANS]);
            $listVals->setListValue($metadataListValue[OrgProfileConstant::FIELD_VALUE]);
            $listVals->setSequence($metadataListValue[OrgProfileConstant::FIELD_SEQNO]);
            $listVals = $this->orgMetadataListRepository->persist($listVals, false);
        }
    }

    private function removeMetaListValues($metadataMaster)
    {
        $metaListValues = $this->orgMetadataListRepository->findBy(array(
            OrgProfileConstant::FIELD_ORGMETADATA => $metadataMaster
        ));
        
        if (isset($metaListValues)) {
            foreach ($metaListValues as $metaListValue) {
                $this->orgMetadataListRepository->remove($metaListValue);
            }
        }
    }

    private function checkListValueAndAnswerForDuplicateValuesWithinRequest($metadataListValue, $valArray, $ansArray)
    {
        if (in_array($metadataListValue[OrgProfileConstant::FIELD_VALUE], $valArray)) {
            $errorsString = "List Value already exists.";
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'metalistvalue_duplicate_Error');
        }
        if (in_array($metadataListValue[OrgProfileConstant::FIELD_ANS], $ansArray)) {
            $errorsString = "List Answer already exists.";
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'metalistvalue_duplicate_Error');
        }
    }

    /**
     * @param $errors
     * @deprecated
     */
    private function buildAndThrowExceptionForErrors($errors)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                $errorsString .= (!stristr($errorsString, $error->getMessage()))? $error->getMessage():'';
            }
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'metakey_duplicate_Error');
        }
    }

    private function removeOrgPermissionset($orgMetadata)
    {
        $orgPermissionsetMetadataRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionsetMetadata');
        $orgMetadatas = $orgPermissionsetMetadataRepository->findBy([
            'orgMetadata' => $orgMetadata
        ]);
        if ($orgMetadatas) {
            foreach ($orgMetadatas as $orgMetadt) {
                $orgPermissionsetMetadataRepository->remove($orgMetadt);
            }
        }
    }

    private function getProfileBlocksWithPermission()
    {
        $ispPermissionBlocks = $this->orgPermissionsetService->getAllowedIspIsqBlocks('isp');
        $blockId = array();
        foreach ($ispPermissionBlocks as $isp) {
            foreach ($isp as $isps) {
                $blockId[] = $isps['id'];
            }
        }
        return $blockId;
    }
    
    public function checkExistingColumnNames($orgId, $newField, $type)
    {
        $this->logger->debug("Check Existing ColumnNames for Organization Id" . $orgId . "New Field" . "Type" . $type);
        $excludeArr = [
            'id',
            'createdAt',
            'createdBy',
            'modifiedAt',
            'modifiedBy',
            'deletedBy',
            'deletedAt'
        ];
        $personItems = $this->container->get('doctrine')
            ->getManager()
            ->getClassMetadata('Synapse\CoreBundle\Entity\Person')
            ->getFieldNames();
        $personItems = array_map(function ($value)
        {
            return strtolower($value);
        }, $personItems);
        
        $contactItems = $this->container->get('doctrine')
            ->getManager()
            ->getClassMetadata('Synapse\CoreBundle\Entity\ContactInfo')
            ->getFieldNames();
        $contactItems = array_map(function ($value)
        {
            return strtolower($value);
        }, $contactItems);
        
        if ($type == 'ISP') {
            $profileItems = $this->getInstitutionSpecificProfileBlockItems($orgId,false,'',false);
            $profileItems = array_column($profileItems['profile_items'], OrgProfileConstant::ITEM_LABEL);
        } else {
            $profileItems = $this->container->get('profile_service')->getProfiles();
            $profileItems = array_column($profileItems, OrgProfileConstant::ITEM_LABEL);
        }
        
        $finalArr = array_merge($personItems, $contactItems, $profileItems);
        $finalArr = array_unique($finalArr);
        $finalArr = array_map(function ($value)
        {
            return strtolower($value);
        }, $finalArr);
        $excludeArr = array_map(function ($value)
        {
            return strtolower($value);
        }, $excludeArr);
        $finalArr = array_diff($finalArr, $excludeArr);
        
        if (in_array(strtolower($newField), $finalArr)) {
            $this->logger->error(" OrgProfileService - checkExistingColumnNames -  Meta Key Duplicate Error");
            throw new ValidationException([
                "Item Label already exists."
            ], "Item Label already exists.", 'metakey_duplicate_Error');
        }
    }
    
    public function updateProfileStatus(ProfileDto $profileDto)
    {
        $this->orgMetadataRepository = $this->repositoryResolver->getRepository(OrgProfileConstant::ORGMETADATA_REPO);
        $orgMetadata = $this->orgMetadataRepository->findOneById($profileDto->getId());
        
        if(strtolower($profileDto->getStatus()) == 'archive'){
            $orgMetadata->setStatus('archived');
        }else{
            $orgMetadata->setStatus('active');
        }
        $this->orgMetadataRepository->flush();
        return $profileDto;
    }
    
    public function isEbiExists($metakey)
    {
        $personRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $profile = $personRepo->getPredefinedProfile();
        $metakey = strtolower($metakey);
        if(in_array($metakey, $profile))
        {
            throw new ValidationException([
                'Profile item already exists as Person Record Information fields'
                ], 'Profile item already exists as Person Record Information fields', 'metakey_duplicate_Error');
        
        }else{
			$ebiMetadataRepository = $this->repositoryResolver->getRepository(ProfileConstant::EBI_METADATA_REPO);
            $isExists = $ebiMetadataRepository->IsEbiProfileExists($metakey);
            if(!$isExists)
            {
                throw new ValidationException([
                    'Profile item already exists'
                    ], 'Profile item already exists', 'metakey_duplicate_Error');
            }
            return true;
        }
    }

    /**
     * Validates if an ISP list option can be edited, then edits that list object if allowed
     *
     * @param array $dtoMetadataListValues - profileDto
     * @param OrgMetadata $orgMetadataObject
     * @param boolean $isMetaDataMappedOnISPLevel
     * @return OrgMetadataListValues $orgMetadataListValueObject
     * @throws SynapseValidationException
     */
    public function editISPListItems($dtoMetadataListValues, $orgMetadataObject, $isMetaDataMappedOnISPLevel)
    {
        $orgMetadataListValuesObjectResponse = null;
        $validListValues = [];
        $validListAnswers = [];

        $existingListValuePersonMapping = $this->buildOrgMetadataListValuesListValueMap($orgMetadataObject);
        $dtoOrgMetadataListValuesIds = [];
        foreach ($dtoMetadataListValues as $metadataListValueProfileDto) {
            $this->checkListValueAndAnswerForDuplicateValuesWithinRequest($metadataListValueProfileDto, $validListValues, $validListAnswers);
            $metadataListValue = $metadataListValueProfileDto['value'];
            $metadataListValueAnswer = $metadataListValueProfileDto['answer'];
            $validListValues[] = $metadataListValue;
            $validListAnswers[] = $metadataListValueAnswer;
            $existingListValueId = $metadataListValueProfileDto['org_metadata_list_value_id'];

            if (!empty($existingListValueId)) {
                $existingListValues = $existingListValuePersonMapping[$existingListValueId];
                $valueInExistingListValues = $existingListValues['list_value'];
                $answerInExistingListValues = $existingListValues['list_answer'];
                $hasValuesInExistingListValues = $existingListValues['has_values'];
                $dtoOrgMetadataListValuesIds[] = $existingListValueId;
            } else {
                $valueInExistingListValues = null;
            }

            $orgMetadataListValuesObject = $this->orgMetadataListRepository->findOneBy([
                "orgMetadata" => $orgMetadataObject,
                "listValue" => $valueInExistingListValues
            ]);

            if ($valueInExistingListValues != $metadataListValue || $answerInExistingListValues != $metadataListValueAnswer || $valueInExistingListValues == null) {
                if ($isMetaDataMappedOnISPLevel) {
                    $doesListValueKeyExists = array_key_exists($existingListValueId, $existingListValuePersonMapping);
                    if ($doesListValueKeyExists && !$hasValuesInExistingListValues && isset($orgMetadataListValuesObject)) {
                        $listValueToModify = $orgMetadataListValuesObject;
                    } else if ($doesListValueKeyExists && $hasValuesInExistingListValues) {
                        throw new SynapseValidationException("The requested option value cannot be edited, as there are values associated with students.");
                    } else if (!$doesListValueKeyExists && is_null($existingListValueId)) {
                        $listValueToModify = new OrgMetadataListValues();
                    } else {
                        throw new SynapseValidationException("The requested option ID is invalid.");
                    }
                } else {
                    if ($existingListValueId) {
                        $orgMetadataListValuesObject = $this->orgMetadataListRepository->find($existingListValueId);
                        $listValueToModify = $orgMetadataListValuesObject;
                    } else {
                        $listValueToModify = new OrgMetadataListValues();
                    }
                }
                $orgMetadataListValuesObjectResponse = $this->buildAndPersistListValueObject($listValueToModify, $orgMetadataObject, $metadataListValueProfileDto, false);
            }
        }

        // This currently assumes that the frontend will pass ALL ISP list values every single time it's called,
        // and deleting the ISPs that are not passed. This should use an indicator instead to show which ISPs are to be deleted in the short term,
        // and a new API separate from this one to delete them in the long term.
        foreach ($existingListValuePersonMapping as $orgMetadataListValueId => $listData) {
            $orgMetadataListValueFoundForDeletion = !in_array($orgMetadataListValueId, $dtoOrgMetadataListValuesIds);
            //find and soft delete list values from org_metadata_list_values based on mapping of person_org_metadata
            if ($orgMetadataListValueFoundForDeletion) {
                if (!$listData['has_values']) {
                    $orgMetadataListValuesObject = $this->orgMetadataListRepository->find($orgMetadataListValueId, new SynapseValidationException("Requested list item ID not found"));
                    $this->orgMetadataRepository->remove($orgMetadataListValuesObject);
                } else {
                    throw new SynapseValidationException("The requested option list value cannot be edited, as there are values associated with students.");
                }
            }
        }
        return $orgMetadataListValuesObjectResponse;
    }

    /**
     * Returns array containing mapping information of org_metadata_list_values and person_org_metadata
     *
     * @param OrgMetadata $orgMetadataObject
     * @return array
     */
    public function buildOrgMetadataListValuesListValueMap($orgMetadataObject)
    {
        $existingListValuePersonMapping = [];
        $orgMetadataListValues = $this->orgMetadataListRepository->findBy(array(
            "orgMetadata" => $orgMetadataObject
        ));

        foreach ($orgMetadataListValues as $listValue) {
            $valueInList = $listValue->getListValue();
            $answerInList = $listValue->getListName();
            $listValueId = $listValue->getId();

            $personOrgMetadataObject = $this->personOrgMetadataRepository->findOneBy(["metadataValue" => $valueInList, "orgMetadata" => $orgMetadataObject]);

            $existingListValuePersonMapping[$listValueId] = [
                "has_values" => !empty($personOrgMetadataObject) ? true : false,
                "list_value" => $valueInList,
                "list_answer" => $answerInList
            ];
        }
        return $existingListValuePersonMapping;
    }

    /**
     * Generates and persist orgMetadataListValue Object
     *
     * @param OrgMetadataListValues $orgMetadataListValuesObject - detail of list values associated with the org_metadata
     * @param OrgMetadata $orgMetadataObject
     * @param array $metadataListValueProfileDto
     * @param bool $flush
     * @return OrgMetadataListValues $orgMetadataListValueObject
     */
    public function buildAndPersistListValueObject($orgMetadataListValuesObject, $orgMetadataObject, $metadataListValueProfileDto, $flush = true)
    {
        $orgMetadataListValueObject = $this->buildOrgMetadataListValueObject($orgMetadataListValuesObject, $orgMetadataObject, $metadataListValueProfileDto);
        $this->orgMetadataListRepository->persist($orgMetadataListValueObject, $flush);
        return $orgMetadataListValueObject;
    }

    /**
     * Generates a orgMetadataListValue object
     *
     * @param OrgMetadataListValues $orgMetadataListValuesObject
     * @param OrgMetadata $orgMetadataObject
     * @param array $metadataListValueProfileDto
     * @return OrgMetadataListValues $orgMetadataListValuesObject
     */
    public function buildOrgMetadataListValueObject($orgMetadataListValuesObject, $orgMetadataObject, $metadataListValueProfileDto)
    {
        $orgMetadataListValuesObject->setOrgMetadata($orgMetadataObject);
        $orgMetadataListValuesObject->setListName($metadataListValueProfileDto['answer']);
        $orgMetadataListValuesObject->setListValue($metadataListValueProfileDto['value']);
        $orgMetadataListValuesObject->setSequence($metadataListValueProfileDto['sequence_no']);
        return $orgMetadataListValuesObject;
    }
}