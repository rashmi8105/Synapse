<?php
namespace Synapse\CoreBundle\Service\Impl;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Constraints\Date;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Entity\OrgPermissionsetFeatures;
use Synapse\CoreBundle\Entity\OrgPermissionsetMetadata;
use Synapse\CoreBundle\Entity\OrgPermissionsetQuestion;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\DatablockMasterLangRepository;
use Synapse\CoreBundle\Repository\DatablockMasterRepository;
use Synapse\CoreBundle\Repository\FeatureMasterRepository;
use Synapse\CoreBundle\Repository\OrgFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetDatablockRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetQuestionRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgQuestionRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\OrgPermissionsetConstant;
use Synapse\RestBundle\Entity\BlockDto;
use Synapse\RestBundle\Exception\ValidationException;

class OrgPermissionsetServiceHelper extends AbstractService
{

    const ORG_PERMISSIONSET_REPO = "SynapseCoreBundle:OrgPermissionset";

    const FEATURE_MASTER_REPO = "SynapseCoreBundle:FeatureMaster";

    const ORG_METADAT_REPO = "SynapseCoreBundle:OrgMetadata";

    const DATABLOCK_MASTER_REPO = "SynapseCoreBundle:DatablockMaster";

    const DATABLOCK_MASTER_LANG_REPO = "SynapseCoreBundle:DatablockMasterLang";

    const ORG_PERMISSIONSET_DATABLOCK_REPO = "SynapseCoreBundle:OrgPermissionsetDatablock";

    const ORG_QUESTION_REPO = "SynapseCoreBundle:OrgQuestion";

    const ORG_PERMISSIONSET_METADATA_REPO = "SynapseCoreBundle:OrgPermissionsetMetadata";

    const ORG_PERMISSIONSET_QUESTION_REPO = "SynapseCoreBundle:OrgPermissionsetQuestion";

    const ORG_FEATURES_REPO = "SynapseCoreBundle:OrgFeatures";

    const ORG_PERMISSIONSET_FEATURES_REPO = "SynapseCoreBundle:OrgPermissionsetFeatures";

    const ORG_GROUP_FACULTY_REPO = "SynapseCoreBundle:OrgGroupFaculty";

    const ORG_GROUP_STUDENT_REPO = "SynapseCoreBundle:OrgGroupStudents";

    //Repositories
    /**
     * @var DatablockMasterRepository
     */
    protected $dataBlockMasterRepository;

    /**
     *
     * @var DatablockMasterLangRepository
     */
    protected $dataBlockMasterLangRepository;

    /**
     *
     * @var FeatureMasterRepository
     */
    protected $featureMasterRepository;

    /**
     * @var OrgFeaturesRepository
     */
    protected $orgFeaturesRepository;

    /**
     *
     * @var OrgMetadataRepository
     */
    protected $orgMetadataRepository;

    /**
     * @var OrgPermissionsetDatablockRepository
     */
    protected $orgPermissionsetDatablockRepository;

    /**
     * @var OrgPermissionsetFeaturesRepository
     */
    protected $orgPermissionsetFeaturesRepository;

    /**
     * @var OrgPermissionsetMetadataRepository
     */
    protected $orgPermissionsetMetadataRepository;

    /**
     * @var OrgPermissionsetQuestionRepository
     */
    protected $orgPermissionsetQuestionRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    protected $orgPermissionsetRepository;

    /**
     * @var OrgQuestionRepository
     */
    protected $orgQuestionRepository;

    /**
     * @var OrgToolsPermissionsetRepository
     */
    protected $orgToolsPermissionsetRepository;

    //services

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var FeatureService
     */
    protected $featureService;

    /**
     * @var PermissionSetService
     */
    protected $permissionsetService;

    /**
     * @var Date
     */
    protected $lastUpdated;

    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
    }

    protected function createIsqBlock($isqBlocks, $orgPermissionSet)
    {
        $surveyRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:SurveyLang");
        $organization = $orgPermissionSet->getOrganization();
        if (count($isqBlocks) > 0) {
            $this->orgQuestionRepository = $this->repositoryResolver->getRepository(self::ORG_QUESTION_REPO);
            $this->orgPermissionsetQuestionRepository = $this->repositoryResolver->getRepository(self::ORG_PERMISSIONSET_QUESTION_REPO);
            foreach ($isqBlocks as $isqBlock) {
                /**
                 * checking the block selection-if true only inserts
                 */
                if ($isqBlock->getBlockSelection()) {
                    $orgQuestionId = $isqBlock->getId();
                    $orgQuestion = $this->orgQuestionRepository->find($orgQuestionId);
                    if (! $orgQuestion) {
                        throw new ValidationException([
                            "ISQ Not Found"
                        ], "ISQ Not Found", OrgPermissionsetConstant::ERROR_KEY);
                    }

                    $survey = $surveyRepo->findOneBySurvey($isqBlock->getSurveyId());
                    if (! $survey) {
                        throw new ValidationException([
                            "ISQ Survey Not Found"
                            ], "ISQ Survey Not Found", OrgPermissionsetConstant::ERROR_KEY);
                    }
                    $isqPermission = new OrgPermissionsetQuestion();
                    $isqPermission->setOrganization($organization);
                    $isqPermission->setOrgQuestion($orgQuestion);
                    $isqPermission->setOrgPermissionset($orgPermissionSet);
                    $isqPermission->setSurvey($survey->getSurvey());
                    $isqPermission->setCohortCode($isqBlock->getCohortId());

                    $this->orgPermissionsetQuestionRepository->persist($isqPermission, false);
                }
            }
        }
    }

    protected function createIspBlock($ispBlocks, $orgPermissionSet)

    {
        $organization = $orgPermissionSet->getOrganization();
        if (count($ispBlocks) > 0) {
            foreach ($ispBlocks as $ispBlock) {
                /**
                 * checking the block selection-if true only inserts
                 */
                if ($ispBlock->getBlockSelection()) {
                    $orgMetadataId = $ispBlock->getId();
                    $orgMetadata = $this->orgMetadataRepository->find($orgMetadataId);
                    if (! $orgMetadata) {
                        throw new ValidationException([
                            OrgPermissionsetConstant::ISP_NOT_FOUND
                        ], OrgPermissionsetConstant::ISP_NOT_FOUND, OrgPermissionsetConstant::ERROR_KEY);
                    }

                    $ispPermission = new OrgPermissionsetMetadata();
                    $ispPermission->setOrganization($organization);
                    $ispPermission->setOrgMetadata($orgMetadata);
                    $ispPermission->setOrgPermissionset($orgPermissionSet);
                    $this->orgPermissionsetMetadataRepository->persist($ispPermission, false);
                }
            }
        }
    }

    /**
     * Create OrgFeaturesPermission
     *
     * @param array $features
     * @param OrgPermissionset $orgPermissionSet
     * @param boolean $copyFlag
     * @throws SynapseValidationException
     */
    protected function createOrgFeaturesPermission($features, $orgPermissionSet, $copyFlag)
    {
        $organization = $orgPermissionSet->getOrganization();
        foreach ($features as $feature) {

            $featureId = $feature->getId();
            $featureMaster = $this->featureMasterRepository->find($featureId);
            if (!$featureMaster) {
                throw new SynapseValidationException('Feature Not Found');
            }
            if ($copyFlag) {

                // This will execute copy from EBI To Org
                $orgPermissionsetFeatures = new OrgPermissionsetFeatures();
                $orgPermissionsetFeatures->setFeature($featureMaster);
                $orgPermissionsetFeatures->setOrganization($organization);
                $orgPermissionsetFeatures->setOrgPermissionset($orgPermissionSet);

                if ($featureId == SynapseConstant::REFERRAL_FEATURE_ID) {
                    $this->setAccesslevelReferal($orgPermissionsetFeatures, $feature);
                } else {
                    $this->setAccesslevelFeatures($orgPermissionsetFeatures, $feature);
                }

                $this->orgPermissionsetFeaturesRepository->persist($orgPermissionsetFeatures, false);
            } else {
                $orgFeature = $this->orgFeaturesRepository->findOneBy([
                    'organization' => $organization,
                    'feature' => $featureMaster
                ]);

                if (empty($orgFeature)) {
                    throw new SynapseValidationException('Feature not found for this organization');
                }

                if ($orgFeature && $orgFeature->getConnected()) {
                    $orgPermissionsetFeatures = new OrgPermissionsetFeatures();
                    $orgPermissionsetFeatures->setFeature($featureMaster);
                    $orgPermissionsetFeatures->setOrganization($organization);
                    $orgPermissionsetFeatures->setOrgPermissionset($orgPermissionSet);

                    if ($featureId == SynapseConstant::REFERRAL_FEATURE_ID) {
                        $this->setAccesslevelReferal($orgPermissionsetFeatures, $feature);
                    } else {
                        $this->setAccesslevelFeatures($orgPermissionsetFeatures, $feature);
                    }

                    $this->orgPermissionsetFeaturesRepository->persist($orgPermissionsetFeatures, false);
                }
            }
        }
    }

    protected function getProfileBlocks($ebiTemplate)
    {
        $profileBlocks = [];
        if ($ebiTemplate->getProfileBlocks() && count($ebiTemplate->getProfileBlocks()) > 0) {
            foreach ($ebiTemplate->getProfileBlocks() as $pblocks) {
                $pBlock = new BlockDto();
                $pBlock->setBlockId($pblocks->getBlockId());
                $pBlock->setBlockSelection((bool) $pblocks->getBlockSelection());
                $profileBlocks[] = $pBlock;
            }
        }
        return $profileBlocks;
    }

    protected function getSurveyBlocks($ebiTemplate)
    {
        $surveyBlocks = [];
        if ($ebiTemplate->getSurveyBlocks() && count($ebiTemplate->getSurveyBlocks()) > 0) {
            foreach ($ebiTemplate->getSurveyBlocks() as $pblocks) {
                $pBlock = new BlockDto();
                $pBlock->setBlockId($pblocks->getBlockId());
                $pBlock->setBlockSelection((bool) $pblocks->getBlockSelection());
                $surveyBlocks[] = $pBlock;
            }
        }
        return $surveyBlocks;
    }

    /**
     * Edit feature permissions
     *
     * @param array $features
     * @param OrgPermissionset $orgPermissionSet
     * @throws SynapseValidationException
     */
    protected function editOrgFeaturesPermission($features, $orgPermissionSet)
    {
        $organization = $orgPermissionSet->getOrganization();
        if (count($features) > 0) {
            foreach ($features as $feature) {

                $featureId = $feature->getId();
                $featureMaster = $this->featureMasterRepository->find($featureId);
                if (!$featureMaster) {
                    throw new SynapseValidationException('Feature Not Found');
                }
                $orgFeaturePermission = $this->orgPermissionsetFeaturesRepository->findOneBy([
                    'feature' => $featureMaster,
                    'orgPermissionset' => $orgPermissionSet
                ]);
                if ($orgFeaturePermission) {
                    if ($featureId == SynapseConstant::REFERRAL_FEATURE_ID) {
                        $this->setAccesslevelReferal($orgFeaturePermission, $feature);
                    } else {
                        $this->setAccesslevelFeatures($orgFeaturePermission, $feature);
                    }
                    $orgFeaturePermission->setReceiveReferral($feature->getReceiveReferrals());
                } else {
                    // create new one
                    $orgFeature = $this->orgFeaturesRepository->findOneBy([
                        'organization' => $organization,
                        'feature' => $featureMaster
                    ]);

                    if (empty($orgFeature)) {
                        throw new SynapseValidationException('Feature not found for this organization');
                    }
                    if ($orgFeature && $orgFeature->getConnected()) {
                        $orgFeaturePermission = new OrgPermissionsetFeatures();
                        $orgFeaturePermission->setFeature($featureMaster);
                        $orgFeaturePermission->setOrganization($organization);
                        $orgFeaturePermission->setOrgPermissionset($orgPermissionSet);

                        if ($featureId == SynapseConstant::REFERRAL_FEATURE_ID) {
                            $this->setAccesslevelReferal($orgFeaturePermission, $feature);
                        } else {
                            $this->setAccesslevelFeatures($orgFeaturePermission, $feature);
                        }
                        $this->orgPermissionsetFeaturesRepository->persist($orgFeaturePermission, false);
                    }
                }
            }
        }
    }

    private function setAccesslevelFeatures($orgFeaturePermission, $feature)
    {
        $orgFeaturePermission->setPrivateCreate($feature->getPrivateShare()
            ->getCreate());
        $orgFeaturePermission->setPublicCreate($feature->getPublicShare()
            ->getCreate());
        $orgFeaturePermission->setPublicView($feature->getPublicShare()
            ->getView());
        $orgFeaturePermission->setTeamCreate($feature->getTeamsShare()
            ->getCreate());
        $orgFeaturePermission->setTeamView($feature->getTeamsShare()
            ->getView());
        $orgFeaturePermission->setReceiveReferral(NULL);
    }

    private function setAccesslevelReferal($orgFeaturePermission, $feature)
    {
        $directFeature = $feature->getDirectReferral();
        $reasonRoutedReferral = $feature->getReasonRoutedReferral();
        // Direct
        $orgFeaturePermission->setPrivateCreate($directFeature->getPrivateShare()
            ->getCreate());
        $orgFeaturePermission->setPublicCreate($directFeature->getPublicShare()
            ->getCreate());
        $orgFeaturePermission->setPublicView($directFeature->getPublicShare()
            ->getView());
        $orgFeaturePermission->setTeamCreate($directFeature->getTeamsShare()
            ->getCreate());
        $orgFeaturePermission->setTeamView($directFeature->getTeamsShare()
            ->getView());
        // Reason routed
        $orgFeaturePermission->setReasonReferralPrivateCreate($reasonRoutedReferral->getPrivateShare()
            ->getCreate());
        $orgFeaturePermission->setReasonReferralPublicCreate($reasonRoutedReferral->getPublicShare()
                                ->getCreate());
        $orgFeaturePermission->setReasonReferralPublicView($reasonRoutedReferral->getPublicShare()
                                ->getView());
        $orgFeaturePermission->setReasonReferralTeamCreate($reasonRoutedReferral->getTeamsShare()
                                ->getCreate());
        $orgFeaturePermission->setReasonReferralTeamView($reasonRoutedReferral->getTeamsShare()
                                ->getView());
        $orgFeaturePermission->setReceiveReferral((bool) $feature->getReceiveReferrals());
    }

    protected function editIspBlock($ispBlocks, $orgPermissionSet)
    {
        $organization = $orgPermissionSet->getOrganization();
        if (count($ispBlocks) > 0) {
            foreach ($ispBlocks as $ispBlock) {
                $orgMetadataId = $ispBlock->getId();
                $orgMetadata = $this->orgMetadataRepository->find($orgMetadataId);
                if (! $orgMetadata) {
                    throw new ValidationException([
                        OrgPermissionsetConstant::ISP_NOT_FOUND
                    ], OrgPermissionsetConstant::ISP_NOT_FOUND, OrgPermissionsetConstant::ERROR_KEY);
                }
                $ispPermission = $this->orgPermissionsetMetadataRepository->findOneBy([
                    'orgMetadata' => $orgMetadata,
                    'orgPermissionset' => $orgPermissionSet
                ]);
                if ($ispPermission && ! $ispBlock->getBlockSelection()) {
                    $this->orgPermissionsetMetadataRepository->remove($ispPermission);
                } elseif ((! $ispPermission) && ($ispBlock->getBlockSelection())) {
                    $ispPermission = new OrgPermissionsetMetadata();
                    $ispPermission->setOrganization($organization);
                    $ispPermission->setOrgMetadata($orgMetadata);
                    $ispPermission->setOrgPermissionset($orgPermissionSet);
                    $this->orgPermissionsetMetadataRepository->persist($ispPermission, false);
                }
            }
        }
    }

    protected function setLastUpdateDateBlocks($dataBlocks)
    {
        if (count($dataBlocks) > 0) {
            foreach ($dataBlocks as $dataBlock) {
                if ($this->lastUpdated < $dataBlock->getLastUpdated()) {
                    $this->lastUpdated = $dataBlock->getLastUpdated();
                }
            }
        }
    }
}