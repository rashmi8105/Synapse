<?php
namespace Synapse\CoreBundle\Security\Authorization\TinyRbac;

/**
 * TinyRbac/Rbac is a simplified, efficient RBAC authorization processor.
 *
 * For standard RBAC: It is roughly 800% on average, and up to 100x faster
 * than the major competing PHP complex-authorization systems, including
 * OWASP/PHP-RBAC, ZendFramework2/Permissions/Rbac, and Symfony/Security/ACL.
 *
 * It also offers a simplified, 2-dimensional RBAC option, which is used by
 * Synapse, that offers O(1) performance, allowing for thousands of concurrent
 * authorization checks.
 *
 * == About Role-Based Access Control (RBAC):
 *
 * Role-Based Access Control. It primarily is for granting access to actions based
 * upon groups one belongs to, versus DAC (Direct Access Control), which is what we
 * commonly think of (users given access to certain views [account, admin, etc.])
 * and ACL (Access Control Lists), which is used to give individual users and/or
 * individual groups certain permissions for individual assets (folders, files, etc.).
 *
 * DAC is good in systems that do not have a lot of actions, groups, or views which
 * they wish to grant group-level permissions on.
 *
 * ACL is good in systems where many low-level admins grant user permissions on a
 * per-user, per-asset basis (think Windows Domain admins).
 *
 * RBAC is good in systems where high-level admins control permissions for users by
 * assigning them to certain groups, particularly when actions are what are
 * primarily guarded.
 *
 * ACL is *actually* a specialized subset of RBAC: One can implement ACL in RBAC by
 * simply making, say, the ability to view certain assets into its own role. If an
 * individual user needs custom access to a particular asset, that itself would end
 * up being a custom RBAC permission.
 *
 * @package Synapse\CoreBundle\Security\Authorization\TinyRbac
 */
class Rbac
{

    const ACCESS_ABSTAIN = '';

    const ACCESS_GRANTED = '*';

    const ACCESS_DENIED = '-';

    const THIS = '_this';

    private $accessTree;

    protected $permissionsList = array();

    protected $usersMap = array();

    public function __construct()
    {
        $accessTree = array(
            '/' => array()
        );
        $this->accessTree = $accessTree;
    }

    /**
     * Manually set the access tree.
     *
     * Most useful for initializing the permissions for the Symfony
     * Voter authorization mechanism.
     *
     * @param array $accessTree            
     */
    public function setAccessTree(array $accessTree)
    {
        $this->accessTree = $accessTree;
        $this->permissionsList = $this->flattenPermissions($accessTree);
    }

    /**
     * Flattens the accessTree into a 2D array.
     *
     * This is mostly to facilitate less complex RBAC authorization checks.
     * Allows checks like '/profileBlocks-<id>' instead of
     * '/user-<id>/permissionSet-<id>/group-<id>/profileBlock-<id>/'
     *
     * @param array $accessTree            
     * @return array
     */
    protected function flattenPermissions($accessTree)
    {
        $flattened = [];
        array_walk_recursive($accessTree, function ($value, $key) use(&$flattened)
        {
            
            // Always grant a permission if any group has it.
            if ($key === self::THIS || ($value !== self::ACCESS_GRANTED && isset($flattened[$key]))) {
                return;
            }
            $flattened[$key] = $value;
        });
        
        return $flattened;
    }

    /**
     *
     * @return string[] Permissions
     *        
     */
    public function getPermissions()
    {
        $this->getAccessTree();
        return $this->permissionsList;
    }

    public function getAccessTree()
    {
        return $this->accessTree;
    }

    /**
     * Checks if a permission exists and access is granted.
     *
     * This is useful for Synapse's 2D perm structure. Think of it as
     * RBAC-lite [thus TinyRBAC]. This check is *very* perforant O(1).
     *
     * @param
     *            $perm
     * @return bool
     */
    public function hasAccess($perm)
    {
        if (empty($this->permissionsList[$perm])) {
            return false;
        }
        return ($this->permissionsList[$perm] === self::ACCESS_GRANTED);
    }

    /**
     * Checks if a complex RBAC permission path exists and access is granted.
     *
     * This enables full RBAC permission checking, by traditional RBAC permission URLs.
     * E.g., '/user-<id>/parentGroup-<id>/group-<id>/permssion-<id>/'
     * It inherently supports unlimited hierarchical *and* inherited permissions.
     * This check is *NOT* performant, and runs about O(n^2 + o*2)
     * where n=number of groups, and o=number of permissions.
     *
     * It is still roughly 800% more performant than the OWASP/PHP-RBAC project,
     * albeit with substantially less extraneous functionality, which Synapse
     * doesn't need at all.
     *
     * @param
     *            $url
     * @return bool
     */
    public function hasComplexAccess($url)
    {
        $pathinfo = parse_url($url);
        $pathname = explode("/", $pathinfo["path"]);
        
        $hasAccess = false;
        $newUrl = '';
        $access = $this->accessTree;
        while (count($pathname) > 0) {
            $path = array_shift($pathname);
            if (empty($path)) {
                $path = '/';
                $newUrl = $path;
            } elseif ($path != '/') {
                $newUrl .= $path;
            }
            
            if (isset($access[$path])) {
                $access = $access[$path];
                if ($access) {
                    if (is_string($access)) {
                        $hasAccess = ($access === '*');
                    } else 
                        if (is_array($access) && ! empty($access[self::THIS]) && $access[self::THIS] === '*') {
                            if ($newUrl === $url) {
                                $hasAccess = true;
                            }
                        }
                }
            }
        }
        
        return $hasAccess;
    }

    /**
     * Add a group to the user's Access Tree.
     *
     * Step 1 in Access Tree setup.
     *
     * @param
     *            $groupName
     * @param null $parentGroupName            
     * @throws \InvalidArgumentException
     */
    public function addGroup($groupName, $parentGroupName = null)
    {
        if (! is_string($groupName)) {
            throw new \InvalidArgumentException('groupName must be a string');
        }
        
        if (! $parentGroupName) {
            $this->accessTree['/'][$groupName] = array();
        } else {
            $iterator = new \RecursiveIteratorIterator(new \ParentIterator(new \RecursiveArrayIterator($this->accessTree)), \RecursiveIteratorIterator::SELF_FIRST);
            
            foreach ($iterator as $key => $value) {
                if (array_key_exists($parentGroupName, $value) && is_array($value[$parentGroupName])) {
                    // Append the permission.
                    $value[$parentGroupName][$groupName] = array();
                    $this->accessTree[$key] = $value;
                    break;
                }
            }
        }
    }

    /**
     *
     * @param array $groups            
     * @throws
     *
     */
    public function addGroups(array $groups)
    {
        foreach ($groups as $groupName) {
            $this->addGroup($groupName);
        }
    }

    /**
     * Attaches a user to an Access Tree.
     *
     * Step 2 in Access Tree setup.
     *
     * @param
     *            $userId
     * @param
     *            $groupName
     */
    public function attachUser($userId, $groupName)
    {
        // Invalidate the user access tree cache.
        if (! empty($this->usersMap[$userId]['/'])) {
            unset($this->usersMap[$userId]['/']);
        }
        
        $this->usersMap[$userId][$groupName] = [];
    }

    /**
     * Adds a permission to a user's Access Tree.
     *
     * Step 3 in Access Tree setup.
     *
     * @param
     *            $permissionName
     * @param
     *            $groupName
     * @param string $permValue            
     */
    public function addPerm($permissionName, $groupName, $permValue = self::ACCESS_GRANTED)
    {
        $foundGroup = false;
        
        $iterator = new \RecursiveIteratorIterator(new \ParentIterator(new \RecursiveArrayIterator($this->accessTree)), \RecursiveIteratorIterator::SELF_FIRST);
        
        foreach ($iterator as $key => $value) {
            if (array_key_exists($groupName, $value) && is_array($value[$groupName])) {
                // Append the permission.
                $value[$groupName][$permissionName] = $permValue;
                $this->accessTree[$key] = $value;
                $foundGroup = true;
                break;
            }
        }
        
        if (! $foundGroup) {
            throw new \RuntimeException('Could not find TinyRbac group "' . $groupName . '"');
        }
    }

    /**
     *
     * @param array $permissions            
     * @param
     *            $groupName
     * @param string $permValue            
     */
    public function addPerms(array $permissions, $groupName, $permValue = self::ACCESS_GRANTED)
    {
        foreach ($permissions as $permissionName) {
            $this->addPerm($permissionName, $groupName, $permValue);
        }
    }

    /**
     *
     * @param
     *            $userId
     * @return array
     */
    public function getUserAccessTree($userId)
    {
        if (! isset($this->usersMap[$userId])) {
            return false;
        }
        
        if (empty($this->usersMap[$userId]['/'])) {
            $this->buildUserAccessTree($userId);
        }
        
        return [
            '/' => $this->usersMap[$userId]['/']
        ];
    }

    /**
     *
     * @param int $userId            
     */
    protected function buildUserAccessTree($userId)
    {
        $userMap = $this->usersMap[$userId];
        
        $iterator = new \RecursiveIteratorIterator(new \ParentIterator(new \RecursiveArrayIterator($this->accessTree)), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $key => $value) {
            if ($key !== '/' && array_key_exists($key, $userMap)) {
                $userMap['/'][$key] = $value;
            }
        }
        
        $this->usersMap[$userId] = $userMap;
    }

    /**
     *
     * @param int $userId            
     * @return array
     */
    public function getUserPermissions($userId)
    {
        return $this->flattenPermissions($this->getUserAccessTree($userId));
    }
}
