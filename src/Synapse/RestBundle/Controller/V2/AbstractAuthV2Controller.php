<?php
namespace Synapse\RestBundle\Controller\V2;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Controller\AbstractAuthController;


/**
 * Base controller for user authorization. Inherited from AbstractController
 *
 * @package Synapse\RestBundle\Controller
 */
abstract class AbstractAuthV2Controller extends AbstractAuthController
{

    /**
     * Do the process of access check. Get the logged in user id from the Security Token Storage.
     *
     * @param array $permissions
     * @return bool
     */
    public function ensureAccess(array $permissions)
    {
        // check authentication for logged in user
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->isAPIIntegrationEnabled();
        $this->isOrganizationAPICoordinator();
        return true;
    }

    /**
     * Call the isAPIIntegrationEnabled method of the AbstractService
     *
     * @return bool
     */
    public function isAPIIntegrationEnabled()
    {
        $this->apiValidationService->isAPIIntegrationEnabled();
        return true;
    }

    /**
     * Call the isRequestSizeAllowed method of the AbstractService
     *
     * @param string $keyToCheck
     * @return bool
     */
    public function isRequestSizeAllowed($keyToCheck)
    {
        return $this->apiValidationService->isRequestSizeAllowed($this->getRequestBodyAsString(), $keyToCheck);
    }
}