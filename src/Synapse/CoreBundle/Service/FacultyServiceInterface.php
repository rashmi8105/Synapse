<?php
namespace Synapse\CoreBundle\Service;

interface FacultyServiceInterface
{
    /**
     * @param int $personId
     * @return bool
     */
    public function softDeleteById($personId);

    public function getGroupsList($orgId, $personId);

    /**
     * @param $personId
     * @param $groupId
     * @param bool|false $permissionsetId
     * @param bool|false $isInvisible
     * @return bool
     */
    public function addGroup($personId, $groupId, $permissionsetId = false, $isInvisible = false);

    /**
     * @param int $personId
     * @param int $groupId
     * @return bool
     */
    public function removeGroup($personId, $groupId);

    /**
     * @param $personId
     * @param $courseId
     * @param int $permissionsetId
     * @return bool
     */
    public function addCourse($personId, $courseId, $permissionsetId = null);

    /**
     * @param int $personId
     * @param int $courseId
     * @return bool
     */
    public function removeCourse($personId, $courseId);

    /**
     * @param $personId
     * @param $teamId
     * @param null $role
     * @return bool
     */
    public function addTeam($personId, $teamId, $role = null);

    /**
     * @param int $personId
     * @param int $teamId
     * @return bool
     */
    public function removeTeam($personId, $teamId);
}
