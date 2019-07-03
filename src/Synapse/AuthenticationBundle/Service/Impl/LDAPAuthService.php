<?php
namespace Synapse\AuthenticationBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("ldap_auth_service")
 */
class LDAPAuthService extends AbstractService
{

    const SERVICE_KEY = 'ldap_auth_service';

    private $ldapConfigRepository;

    /**
     *
     * @param $repositoryResolver
     * @param $logger @DI\InjectParams({
     *        "repositoryResolver" = @DI\Inject("repository_resolver"),
     *        "logger" = @DI\Inject("logger"),
     *        "container" = @DI\Inject("service_container"),
     *        "encoderFactory" = @DI\Inject("security.encoder_factory")
     * })
     */
    public function __construct($repositoryResolver, $logger, $container, $encoderFactory)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->encoderFactory = $encoderFactory;
        $this->ldapConfigRepository = $repositoryResolver->getRepository('SynapseAuthenticationBundle:OrgLdapConfig');
    }

    public function ldapAuth($person, $roles, $inputData)
    {
        $ldapConfig = $this->ldapConfigRepository->findOneByOrganization($person->getOrganization());

        $username = $inputData['username'];
        if ($person->getAuthUsername() && $person->getAuthUsername() !== $username) {
            $username = $person->getAuthUsername();
        }

        if (in_array('Primary coordinator', $roles) || in_array('Technical coordinator', $roles) ||
            in_array('Non Technical coordinator', $roles) || in_array('Staff', $roles)
        ) {
            $entity = 'Staff';
        } else {
            $entity = 'Student';
        }

        if (call_user_func([$ldapConfig, 'get'.$entity.'InitialUser'])) {
            $authBind = $this->searchBind($ldapConfig, $entity, $username, $inputData['password']);
        } else {
            $authBind = $this->bind($ldapConfig, $entity, $username, $inputData['password']);
        }


        // verify binding
        if ($authBind) {
            $personRepository = $this->container->get('repository_resolver')->getRepository('SynapseCoreBundle:Person');
            $encoder = $this->encoderFactory->getEncoder($person);
            $encryptPassword = $encoder->encodePassword($inputData['password'], $person->getSalt());
            $person->setPassword($encryptPassword);
            $personRepository->update($person);
            return true;
        }


        return false;
    }

    public function searchBind($ldapConfig, $entity, $username, $password)
    {
        $ldapconns = [];

        foreach (explode("\n", call_user_func([$ldapConfig, 'get'.$entity.'Hostname'])) as $key => $hostname) {
            $ldapconn = ldap_connect($hostname);

            if ($ldapconn) {
                $ldapconns[] = $ldapconn;
                if ($ldapConfig->getType() == 'AD') {
                    ldap_set_option($ldapconns[$key], LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($ldapconn[$key], LDAP_OPT_REFERRALS, 0);
                }
            }
        }


        if (count($ldapconns)) {
            foreach ($ldapconns as $ldapconn) {
                $success = $this->multiDnBind($ldapconn, explode("\n", call_user_func([$ldapConfig, 'get'.$entity.'UserBaseDomain'])), $username, $password, $ldapConfig, $entity);
                if ($success) {
                    return true;
                }
            }
        }

        return false;
    }

    private function multiDnBind($ldapconn, $dnArray, $username, $password, $ldapConfig, $entity)
    {
        //  $ldapconn = $ldapconn[0];
        if(($searchBind=ldap_bind($ldapconn,
            call_user_func([$ldapConfig, 'get'.$entity.'InitialUser']),
            call_user_func([$ldapConfig, 'get'.$entity.'InitialPassword']))) == false){
          return false;
        }

        $baseDomains = explode("\n", call_user_func([$ldapConfig, 'get'.$entity.'UserBaseDomain']));

        $ldapconns = [];

        foreach ($baseDomains as $baseDomain) {
            $ldapconns[] = $ldapconn;
        }

        // search for user
        $resIds = ldap_search($ldapconns,$baseDomains,call_user_func([$ldapConfig, 'get'.$entity.'UsernameAttribute']) . '=' . $username, ['dn']);

        $resId = false;

        foreach ($resIds as $value) {
            if (ldap_count_entries($ldapconn,$value) > 0) {
                $resId = $value;
                break;
            }
        }

        if (!$resId) {
            return false;
        }

        if (ldap_count_entries($ldapconn, $resId) != 1) {
          return false;
        }

        if (($entryId = ldap_first_entry($ldapconn, $resId))== false) {
          return false;
        }

        if (($userDn = ldap_get_dn($ldapconn, $entryId)) == false) {
          return false;
        }

        if (($linkId = ldap_bind($ldapconn, $userDn, $password)) == false) {
          return false;
        }

        @ldap_close($ldapconn);

        return true;
    }

    public function bind($ldapConfig, $entity, $username, $password)
    {

        $ldapconn = ldap_connect($ldapConfig->getHostname());

        if ($ldapConfig->getType() == 'AD') {
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        }

        if ($ldapconn) {
            $rdn = $username;

            if (call_user_func([$ldapConfig, 'get'.$entity.'UserBaseDomain'])) {
                $rdn = $rdn . ',' . $baseDn;
            }

            if ($usernameAttribute = call_user_func([$ldapConfig, 'get'.$entity.'UsernameAttribute'])) {
                $rdn = $usernameAttribute . '=' . $rdn;
            }

            $authBind = ldap_bind($ldapconn, $rdn, $password);

            if ($authBind) {
                return true;
            }
        }
        return false;
    }
}