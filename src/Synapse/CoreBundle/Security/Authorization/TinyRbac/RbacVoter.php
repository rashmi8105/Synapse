<?php

namespace Synapse\CoreBundle\Security\Authorization\TinyRbac;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Synapse\CoreBundle\Entity\Person;

/**
 * TinyRbac/RbacVoter integrates TinyRbac into 3rd party Symfony2 components.
 *
 * See http://symfony.com/doc/current/cookbook/security/voters_data_permission.html
 *
 * @package Synapse\CoreBundle\Security\Authorization\TinyRbac
 */
class RbacVoter implements VoterInterface
{
    /** @var Rbac **/
    protected $rbac;
    /** @var Session */
    protected $session;

    /**
     * @param Session $session
     * @param Rbac $rbac
     */
    public function __construct(Session $session, Rbac $rbac = null)
    {
        $this->session = $session;

        if (!$rbac) {
            $rbac = new Rbac();
        }
        $this->rbac = $rbac;
    }

    public function supportsAttribute($attribute)
    {
        $permList = $this->rbac->getPermissions();
        return (isset($permList[$attribute]));
    }

    public function supportsClass($class)
    {
        // By default, Rbac is not class-aware.
        return true;
    }

    /**
     * @param TokenInterface $token
     * @param null|object $targetClass
     * @param array $permissions
     * @return int
     */
    public function vote(TokenInterface $token, $targetClass, array $permissions)
    {
        // Remove the IS_AUTHENTICATED_FULLY permission, as it is granted earlier.
        $permissions = array_diff($permissions, ['IS_AUTHENTICATED_FULLY']);

        $user = $token->getUser();
        if (empty($permissions) || !is_object($user) || !method_exists($user, 'getId') || !($userId = $user->getId())) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        try {
            $accessTree = Manager::fetchUserAccessTreeFromCache($userId, $this->session);
            if (!empty($accessTree) && is_array($accessTree)) {
                $this->rbac->setAccessTree($accessTree);
                foreach ($permissions as $permission) {
                    if (!$this->rbac->hasAccess($permission)) {
                        return VoterInterface::ACCESS_DENIED;
                    }
                }

                return VoterInterface::ACCESS_GRANTED;
            }
        } catch (\Exception $e) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
