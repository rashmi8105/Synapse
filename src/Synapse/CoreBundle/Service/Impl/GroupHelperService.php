<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\GroupConstant;

class GroupHelperService extends AbstractService
{

    protected function validateOrgGroup($errors)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                
                $errorsString .= $error->getMessage();
            }
            
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'group_duplicate_error');
        }
    }

    protected function getAllSubgroupIds($allSubs, $grp)
    {
        $allIds = array();
        foreach ($allSubs as $val) {
            if ($val != 0 && $val != $grp[GroupConstant::GROUP_ID]) {
                $allIds[] = $val;
            }
        }
        return $allIds;
    }

    protected function getGroupPersonCount($resultCount)
    {
        $count = 0;
        foreach ($resultCount as $rCount) {
            $count += $rCount[GroupConstant::PERSON_COUNT];
        }
        return $count;
    }

    protected function flatten($array)
    {
        $flattened_array = array();
        array_walk_recursive($array, function ($a, $b) use(&$flattened_array)
        {
            if ($b == GroupConstant::GROUP_ID) {
                $flattened_array[] = $a;
            }
        });
        return $flattened_array;
    }

    protected function getSubGroupIds($subgroups)
    {
        $subgrpArr = array();
        foreach ($subgroups as $sub) {
            $subgrpArr[] = $sub[GroupConstant::GROUP_ID];
        }
        return $subgrpArr;
    }
}
