<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Service\ActivityCategoryServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("activity_category_service")
 */
class ActivityCategoryService extends AbstractService implements ActivityCategoryServiceInterface
{

    const SERVICE_KEY = 'activity_category_service';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future
     */
    const FIELD_GRPITEM_KEY = 'group_item_key';

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
    }

    public function getActivityCategory()
    {
        $this->activityCategoryRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:ActivityCategory");
        $categoryArray = array();
        $finalList = array();
        $ActivitySubitems = array();
        $ActivityParents = $this->activityCategoryRepository->getActivityCategoryList();
        $allActivityChild = $this->activityCategoryRepository->getActivityCategoryList('all');
        if (count($ActivityParents) > 0) {
            $childArray = $this->createParentChild($ActivityParents, $allActivityChild);
            foreach ($ActivityParents as $parentsGrp) {
                $categoryArray[self::FIELD_GRPITEM_KEY] = $parentsGrp[self::FIELD_GRPITEM_KEY];
                $categoryArray['group_item_value'] = $parentsGrp['group_item_value'];
                if (isset($childArray[$parentsGrp[self::FIELD_GRPITEM_KEY]])) {
                    $ActivitySubitems = $childArray[$parentsGrp[self::FIELD_GRPITEM_KEY]];
                }
                $categoryArray['subitems'] = $ActivitySubitems;
                $finalList['category_groups'][] = $categoryArray;
            }
        }
        $this->logger->info(">>>> Get Activity Category");
        return $finalList;
    }

    /**
     * To create parent child relation in array
     *
     * @param unknown $parents            
     * @param unknown $allChild            
     * @return Array
     */
    private function createParentChild($parents, $allChild)
    {
        $parentChildArray = array();
        $childs = array();
        foreach ($parents as $parent) {
            foreach ($allChild as $child) {
                if ($parent[self::FIELD_GRPITEM_KEY] == $child['parent']) {
                    $childs['subitem_key'] = $child['subitem_key'];
                    $childs['subitem_value'] = $child['subitem_value'];
                    $parentChildArray[$parent[self::FIELD_GRPITEM_KEY]][] = $childs;
                }
            }
        }
        
        return $parentChildArray;
    }
}