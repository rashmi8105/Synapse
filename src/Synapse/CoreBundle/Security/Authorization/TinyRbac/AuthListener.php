<?php

namespace Synapse\CoreBundle\Security\Authorization\TinyRbac;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Synapse\RestBundle\Controller\AbstractAuthController;

/**
 * TinyRbac\AuthListener handles view-level RBAC authorizations.
 *
 * It is responsible for tying the TinyRbac authorization
 * system into the core Symfony2 authorization mechanism. It basically
 * intercepts every Action dispatch, and if the Controller is extended from
 * AbstractAuthController, it will then ensure that the user has the required
 * permissions.
 *
 * View-level permissions are set in app/config/parameters.yml in the
 * "permissions" configuration section.
 *
 * @package Synapse\CoreBundle\Security\Authorization\TinyRbac
 */
class AuthListener
{
    private $requiredPermissions;

    /**
     * @param array? $requiredPermissions
     */
    public function __construct($requiredPermissions = null)
    {
        $this->requiredPermissions = $requiredPermissions;
    }

    /**
     * Handles RBAC auth at the View level. Called on every Symfony Action dispatch.
     *
     * This ties in the TinyRbac authorization system into the core of Symfony2.
     * In order to configure permissions at the View level, set the permissions in
     * app/config/parameters.yml in the 'permissions' section.
     *
     * @param FilterControllerEvent $event
     * @return bool
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controllerArray = $event->getController();

        /*
         * $controllerArray passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controllerArray)) {
            return;
        }
        $controller = $controllerArray[0];
        $controllerActionName = $controllerArray[1];

        if ($controller instanceof AbstractAuthController) {
            // Grab the pre configured permissions, if any.
            $permissions = $controller->fetchRequiredPermissionsFromConfig();
            $controller->setRequiredPermissions($permissions);
            $requestPath = $_SERVER["REQUEST_URI"];
            $queryString = $_SERVER["QUERY_STRING"];
            $controller->logAction($controllerActionName, $requestPath, $queryString, false);
            $access = $controller->ensureAccess($controller->getRequiredPermissions());
            return $access;
        }
    }
}
