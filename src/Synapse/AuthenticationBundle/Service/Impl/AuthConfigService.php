<?php
namespace Synapse\AuthenticationBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\AuthenticationBundle\EntityDto\AuthConfigDto;
use Synapse\AuthenticationBundle\EntityDto\LdapConfigDto;
use Synapse\AuthenticationBundle\EntityDto\SamlConfigDto;
use Synapse\AuthenticationBundle\Entity\OrgAuthConfig;
use Synapse\AuthenticationBundle\Entity\OrgLdapConfig;
use Synapse\AuthenticationBundle\Entity\OrgSamlConfig;
use Synapse\CoreBundle\Util\Helper;


/**
 * @DI\Service("auth_config_service")
 */
class AuthConfigService extends AbstractService
{

    const SERVICE_KEY = 'auth_config_service';

    private $securityContext;
    private $authConfigRepository;
    private $ldapConfigRepository;
    private $samlConfigRepository;
    private $ebiConfigService;
    private $organizationService;

    /**
     *
     * @param $repositoryResolver
     * @param $logger @DI\InjectParams({
     *        "repositoryResolver" = @DI\Inject("repository_resolver"),
     *        "logger" = @DI\Inject("logger"),
     *        "securityContext" = @DI\Inject("security.context"),
     *        "ebiConfigService" = @DI\Inject("ebi_config_service"),
     *        "organizationService" = @DI\Inject("org_service")
     * })
     */
    public function __construct($repositoryResolver, $logger, $securityContext, $ebiConfigService, $organizationService)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->securityContext = $securityContext;
        $this->authConfigRepository = $repositoryResolver->getRepository('SynapseAuthenticationBundle:OrgAuthConfig');
        $this->ldapConfigRepository = $repositoryResolver->getRepository('SynapseAuthenticationBundle:OrgLdapConfig');
        $this->samlConfigRepository = $repositoryResolver->getRepository('SynapseAuthenticationBundle:OrgSamlConfig');
        $this->ebiConfigService = $ebiConfigService;
        $this->organizationService = $organizationService;
    }

    public function getConfig($organization)
    {
        $authConfigDto = new AuthConfigDto;

        $ldapConfig = $this->ldapConfigRepository->findOneByOrganization($organization);

        $samlConfig = $this->samlConfigRepository->findOneByOrganization($organization);

        $authConfig = $this->authConfigRepository->findOneByOrganization($organization);

        if ($authConfig) {
            $authConfigDto->setOrganization($organization);
            $authConfigDto->setLdapStaffEnabled($authConfig->getLdapStaffEnabled());
            $authConfigDto->setLdapStudentEnabled($authConfig->getLdapStudentEnabled());
            $authConfigDto->setSamlStaffEnabled($authConfig->getSamlStaffEnabled());
            $authConfigDto->setSamlStudentEnabled($authConfig->getSamlStudentEnabled());
            $authConfigDto->setCampusPortalLoginUrl($authConfig->getCampusPortalLoginUrl());
            $authConfigDto->setCampusPortalLogoutUrl($authConfig->getCampusPortalLogoutUrl());
            $authConfigDto->setCampusPortalStaffEnabled($authConfig->getCampusPortalStaffEnabled());
            if ($authConfig->getCampusPortalStaffEnabled()) {
                $authConfigDto->setCampusPortalStaffKey(
                    $this->ebiConfigService->get('System_API_URL')
                        . 'api/v1/portal/sso?orgToken=' . $authConfig->getCampusPortalStaffKey()
                        . '&personToken='
                );
            }
            $authConfigDto->setCampusPortalStudentEnabled($authConfig->getCampusPortalStudentEnabled());
            if ($authConfig->getCampusPortalStudentEnabled()) {
                $authConfigDto->setCampusPortalStudentKey(
                    $this->ebiConfigService->get('System_API_URL')
                        . 'api/v1/portal/sso?orgToken=' . $authConfig->getCampusPortalStudentKey()
                        . '&personToken='
                );
            }

            if ($ldapConfig) {
                $ldapConfigDto = new LdapConfigDto;
                $ldapConfigDto->setType($ldapConfig->getType());
                $ldapConfigDto->setStaffHostname($ldapConfig->getStaffHostname());
                $ldapConfigDto->setStudentHostname($ldapConfig->getStudentHostname());
                $ldapConfigDto->setStaffInitialUser($ldapConfig->getStaffInitialUser());
                $ldapConfigDto->setStaffInitialPassword($ldapConfig->getStaffInitialPassword());
                $ldapConfigDto->setStaffUserBaseDomain($ldapConfig->getStaffUserBaseDomain());
                $ldapConfigDto->setStaffUsernameAttribute($ldapConfig->getStaffUsernameAttribute());
                $ldapConfigDto->setStudentInitialUser($ldapConfig->getStudentInitialUser());
                $ldapConfigDto->setStudentInitialPassword($ldapConfig->getStudentInitialPassword());
                $ldapConfigDto->setStudentUserBaseDomain($ldapConfig->getStudentUserBaseDomain());
                $ldapConfigDto->setStudentUsernameAttribute($ldapConfig->getStudentUsernameAttribute());
                $authConfigDto->setLdapConfig($ldapConfigDto);
            }

            if ($samlConfig) {
                $samlConfigDto = new SamlConfigDto;
                $samlConfigDto->setEntityId($samlConfig->getEntityId());
                $samlConfigDto->setFederationMetadata($samlConfig->getFederationMetadata());
                $samlConfigDto->setSsoUrl($samlConfig->getSsoUrl());
                $samlConfigDto->setPublicKeyFile($samlConfig->getPublicKeyFile());
                $authConfigDto->setSamlConfig($samlConfigDto);
            }

            return $authConfigDto;
        }

        return false;
    }

    public function saveConfig(AuthConfigDto $authConfigDto)
    {
        $organization = $this->organizationService->find($authConfigDto->getOrganization());

        $ldapConfig = $this->ldapConfigRepository->findOneByOrganization($organization);

        $samlConfig = $this->samlConfigRepository->findOneByOrganization($organization);

        $authConfig = $this->authConfigRepository->findOneByOrganization($organization);

        if ($authConfigDto->getLdapConfig()) {
            $ldapConfig = $ldapConfig ? $ldapConfig : new OrgLdapConfig;
            $ldapConfig->setOrganization($organization);
            $ldapConfig->setType($authConfigDto->getLdapConfig()->getType());
            $ldapConfig->setStaffHostname($authConfigDto->getLdapConfig()->getStaffHostname());
            $ldapConfig->setStudentHostname($authConfigDto->getLdapConfig()->getStudentHostname());
            $ldapConfig->setStaffInitialUser($authConfigDto->getLdapConfig()->getStaffInitialUser());
            $ldapConfig->setStaffInitialPassword($authConfigDto->getLdapConfig()->getStaffInitialPassword());
            $ldapConfig->setStaffUserBaseDomain($authConfigDto->getLdapConfig()->getStaffUserBaseDomain());
            $ldapConfig->setStaffUsernameAttribute($authConfigDto->getLdapConfig()->getStaffUsernameAttribute());
            $ldapConfig->setStudentInitialUser($authConfigDto->getLdapConfig()->getStudentInitialUser());
            $ldapConfig->setStudentInitialPassword($authConfigDto->getLdapConfig()->getStudentInitialPassword());
            $ldapConfig->setStudentUserBaseDomain($authConfigDto->getLdapConfig()->getStudentUserBaseDomain());
            $ldapConfig->setStudentUsernameAttribute($authConfigDto->getLdapConfig()->getStudentUsernameAttribute());
            $this->ldapConfigRepository->persist($ldapConfig);
        }

        if ($authConfigDto->getSamlConfig()) {
            $samlConfig = $samlConfig ? $samlConfig : new OrgSamlConfig;
            $samlConfig->setOrganization($organization);
            $samlConfig->setEntityId($authConfigDto->getSamlConfig()->getEntityId());
            $samlConfig->setFederationMetadata($authConfigDto->getSamlConfig()->getFederationMetadata());
            $samlConfig->setSsoUrl($authConfigDto->getSamlConfig()->getSsoUrl());
            $samlConfig->setPublicKeyFile($authConfigDto->getSamlConfig()->getPublicKeyFile());
            $this->samlConfigRepository->persist($samlConfig);
        }

        $authConfig = $authConfig ? $authConfig : new OrgAuthConfig;
        $authConfig->setOrganization($organization);
        $authConfig->setCampusPortalStudentEnabled($authConfigDto->getCampusPortalStudentEnabled());
        if ($authConfigDto->getCampusPortalStudentEnabled()) {
            $authConfig->setCampusPortalStudentKey(base64_encode(Helper::encrypt($organization->getId())));
        }
        $authConfig->setCampusPortalStaffEnabled($authConfigDto->getCampusPortalStaffEnabled());
        if ($authConfigDto->getCampusPortalStaffEnabled()) {
            $authConfig->setCampusPortalStaffKey(base64_encode(Helper::encrypt($organization->getId())));
        }
        $authConfig->setLdapStudentEnabled($authConfigDto->getLdapStudentEnabled());
        $authConfig->setLdapStaffEnabled($authConfigDto->getLdapStaffEnabled());
        $authConfig->setSamlStudentEnabled($authConfigDto->getSamlStudentEnabled());
        $authConfig->setSamlStaffEnabled($authConfigDto->getSamlStaffEnabled());
        $this->authConfigRepository->persist($authConfig);

        $authConfigDto->setOrganization($organization->getId());

        return $authConfigDto;
    }

    /**
     * Gets Auth config object for a given organization
     *
     * @param $organizationId
     * @return OrgAuthConfig
     */
    public function getAuthConfigForOrganization($organizationId){

        $orgAuthConfigObject = $this->authConfigRepository->findOneBy(['organization' => $organizationId]);
        return $orgAuthConfigObject;
    }
}