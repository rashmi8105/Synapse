<?php

namespace unit\Synapse\CoreBundle\Security\Authorization\TinyRbac;

//include '../../../../../Mocks/RedisCacheMock.php';

use Codeception\Util\Stub;
//use unit\Mocks\RedisCacheMock;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Common\Cache\RedisCache;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;

class RedisCacheMock
{
    protected $isEnabled = true;
    protected $cache;

    public function turnOn()
    {
        $this->isEnabled = true;
    }

    public function turnOff()
    {
        $this->isEnabled = false;
    }

    public function get($key)
    {
        if (!$this->isEnabled) {
            return null;
        }
        if (isset($this->cache[$key])) {
            if (empty($cache[$key]['expires']) || $cache[$key]['expires'] > time())
            {
                return $this->cache[$key]['value'];
            } else {
                return null;
            }
        }

        return null;
    }

    public function set($key, $value, $ttl = null)
    {
        $expires = null;
        if (!$ttl) {
            $expires = time() + $ttl;
        }
        $this->cache[$key] = [
            'value' => $value,
            'ttl' => $expires
        ];
    }
}

class ManagerTest extends \Codeception\Test\Unit
{
    /** @var Manager **/
    private $manager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /** @var EntityManager **/
    protected $em;
    /** @var Session **/
    protected $session;
    /** @var SecurityContext */
    protected $securityContext;
    /** @var \Redis */
    protected $cache;
    /** @var Rbac */
    protected $rbac;
    /** @var OrgPermissionsetService */
    private $permissionsetService;

    /** @var Person */
    private $personMock;
    /** @var RedisCacheMock */
    protected $redisMock;
	protected $userId = 1;
    /**
     * Allows for the testing of private and protected methods.
     *
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new \ReflectionClass(Manager::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface',[]);
        // em, session, security.context, @synapse_redis_cache, @orgpermissionset_service, @tinyrbac, %tinyrbac_options%

        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                ->disableOriginalConstructor()
                ->setMethods(['getRepository', 'getTeamsByAsset', 'getTeams', 'getUserCoordinatorRole'])
                ->getMock();
        $this->em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnSelf());

        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
                ->disableOriginalConstructor()
                ->getMock();
        $this->session->expects($this->any())
        ->method('getId')
        ->will($this->returnValue(uniqid()));
        
        $this->organizationMock = $this->getMockBuilder('Synapse\CoreBundle\Entity\Organization')
        ->disableOriginalConstructor()
        ->getMock();
        $this->organizationMock->expects($this->any())
        ->method('getId')
        ->will($this->returnValue(1));
        
        $this->personMock = $this->getMockBuilder('Synapse\CoreBundle\Entity\Person')
            ->disableOriginalConstructor()
            ->getMock();
        $this->personMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
         $this->personMock->expects($this->any())
         ->method('getOrganization')
         ->will($this->returnValue($this->organizationMock));

        $this->securityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->setMethods(['getToken', 'isAuthenticated', 'getUser'])
            ->getMock();
        $this->securityContext->expects($this->any())
            ->method('getToken')
            ->will($this->returnSelf());
        $this->securityContext->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true));
        $this->securityContext->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->personMock));

        $this->redisMock = new RedisCacheMock();
        $this->cache = $this->getMockBuilder('Doctrine\Common\Cache\RedisCache')
                ->disableOriginalConstructor()
                ->getMock();
        $this->cache->expects($this->any())
                ->method('getRedis')
                ->will($this->returnValue($this->redisMock));

        $this->permissionsetService = $this->getMockBuilder('Synapse\CoreBundle\Service\Impl\OrgPermissionsetService')
                ->disableOriginalConstructor()
                ->getMock();
        $this->permissionsetService->expects($this->any())
                ->method('getPermissionSetByUser')
                ->will($this->returnValue($this->getPermissionsInfo()));
        
       $this->permissionsetService->expects($this->any())
        ->method('getOrgFeatures')
        ->will($this->returnValue($this->getOrgFeaturePerimissions()));
        
        

        $this->rbac = new Rbac;
        $rbacOptions = [
            'enable_auth' => true
        ];

        $this->manager = new Manager(
                $this->em,
                $this->session,
                $this->securityContext,
                $this->cache,
                $this->permissionsetService,
                $this->container,
                $this->rbac,
                $rbacOptions
            );
    }
    
    public function getOrgFeaturePerimissions(){
      
        $arr =  array(
            array
            (
                'id' => 1,
                'connected' => 1,
                'featureName' => "Referrals",
                'feature_id' => 1,
                'organization_id' => 1
            ),
            array
            (
                'id' => 2,
                'connected' => 1 ,
                'featureName' => "Notes",
                'feature_id' => 2,
                'organization_id' => 1,
            ),
            array
            (
                'id' => 3,
                'connected' => 1,
                'featureName' => "Log Contacts",
                'feature_id' => 3,
                'organization_id' => 1
            ),
            array
            (
                'id' => 4 ,
                'connected' => 1,
                'featureName' => "Booking",
                'feature_id' => 4,
                'organization_id' => 1
            ),
            array
            (
                'id' => 6,
                'connected' => 1,
                'featureName' => "Reason Routing",
                'feature_id' => 6,
                'organization_id' => 1,
            )
        );
     
        return $arr;
    }

    public function testCanGetRbac()
    {
        $expected = spl_object_hash($this->rbac);
        $this->assertEquals($expected, spl_object_hash($this->manager->getRbac()));
    }

    public function testCanGetAccessTree()
    {
        $expected = ['/' => []];
        $this->manager->getRbac()->setAccessTree($expected);
        $this->assertEquals($expected, $this->manager->getRbac()->getAccessTree());
    }

    public function testCanGetCurrentLoggedInUser()
    {
        $method = self::getMethod('grabCurrentUser');
        /** @var Person $user */
        $user = $method->invoke($this->manager);

        $this->assertSame($this->personMock, $user, 'Did not properly fetch the currently authenticated user.');
    }

    /**
     * @depends testCanGetCurrentLoggedInUser
     */
    public function testWillStillWorkWithVistors()
    {
        $visitorSecurityContextMock = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->setMethods(['getToken', 'isAuthenticated', 'getUser'])
            ->getMock();
        $visitorSecurityContextMock->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(null));
        
       

        $manager = new Manager(
            $this->em,
            $this->session,
            $visitorSecurityContextMock,
            $this->cache,
            $this->permissionsetService,
            $this->container,
            $this->rbac
        );


        $method = self::getMethod('grabCurrentUser');
        /** @var Person $user */
        $user = $method->invoke($manager);

        $this->assertNull($user, 'Did not properly handle unauthenticated visitors.');
//        $this->assertFalse($manager->hasAccess('booking-team-view2'), 'Improperly granted access to unauthenticated visitors.');
 //       $this->assertFalse($manager->hasCoordinatorAccess(), 'Improperly granted coordinator access to unauthenticated visitors.');

        $method = self::getMethod('grabCurrentUser');
        $user = $method->invoke($manager);
        $this->assertNull($user, 'Did not properly handle a bugged security context.');

    }

    /**
     * @depends testCanGetCurrentLoggedInUser
     */
    public function testWillStillWorkWithAuthenticatedAnonymousUsers()
    {
        $anonymousUserSecurityContextMock = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->setMethods(['getToken', 'isAuthenticated', 'getUser'])
            ->getMock();
        $this->securityContext->expects($this->any())
            ->method('getToken')
            ->will($this->returnSelf());
        $this->securityContext->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true));
        $this->securityContext->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue('anon'));

        $method = self::getMethod('grabCurrentUser');

        $manager = new Manager(
            $this->em,
            $this->session,
            $anonymousUserSecurityContextMock,
            $this->cache,
            $this->permissionsetService,
            $this->container,
            $this->rbac
        );

        $asset = $this->getMockBuilder('Synapse\CoreBundle\Entity\Contacts')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Person $user */
        $user = $method->invoke($manager);
        $this->assertNull($user, 'Did not properly handle authenticated anonymous visitors.');
//        $this->assertFalse($manager->hasAccess('booking-team-view2'), 'Improperly granted access to authenticated anonymous visitors.');
//        $this->assertFalse($manager->hasCoordinatorAccess(), 'Improperly granted coordinator access to authenticated anonymous visitors.');
//        $this->assertFalse($manager->hasPrivateAccess($asset), 'Improperly granted private access to authenticated anonymous visitors.');
//        $this->assertFalse($manager->hasTeamAccess($asset), 'Improperly granted team access to authenticated anonymous visitors.');
//        $this->assertFalse($manager->hasTeamAccess($asset), 'Improperly granted high-level asset access to authenticated anonymous visitors.');

    }
	
    protected function getStudents(){
    	return $students = array("0"=>"1","1"=>"2","2"=>"3");
    }
    
    protected function getPermissionsInfo()
    {
        $info = <<<INFO
{
    "permission_templates": [
        {
            "groups": {
                "403": "ATOne",
                "433": "Automation",
                "472": "SSWWEEWW"
            },
            "organizationId": 1,
            "permissionTemplateName": "Permission Alpha",
            "profileBlocks": [
                {
                    "blockId": 1,
                    "blockSelection": false,
                    "blockName": "Demographic",
                    "lastUpdated": null
                },
                {
                    "blockId": 2,
                    "blockSelection": false,
                    "blockName": "Financial",
                    "lastUpdated": null
                },
                {
                    "blockId": 4,
                    "blockSelection": false,
                    "blockName": "HighSchool",
                    "lastUpdated": null
                },
                {
                    "blockId": 5,
                    "blockSelection": false,
                    "blockName": "HighSchool-Testscores",
                    "lastUpdated": null
                }
            ],
            "accessLevel": {
                "aggregateOnly": false,
                "individualAndAggregate": true
            },
            "isp": [
                {
                    "blockSelection": false,
                    "id": 1,
                    "itemLabel": "Age"
                },
                {
                    "blockSelection": false,
                    "id": 2,
                    "itemLabel": "isp-test-101"
                },
                {
                    "blockSelection": true,
                    "id": 2,
                    "questionLabel": "q1",
                    "sequenceNo": null
                }
            ],
            "surveyBlocks": [
                {
                    "blockId": 7,
                    "blockSelection": false,
                    "blockName": "Academic - Behaviors",
                    "lastUpdated": null
                },
                {
                    "blockId": 8,
                    "blockSelection": true,
                    "blockName": "Academic - Content Skills",
                    "lastUpdated": null
                },
                {
                    "blockId": 12,
                    "blockSelection": true,
                    "blockName": "Academic - Transition",
                    "lastUpdated": null
                }
            ],
            "features": [
                {
                    "id": 1,
                    "name": "Referrals",
                    "privateShare": {
                        "create": true,
                        "view": null
                    },
                    "publicShare": {
                        "create": true,
                        "view": true
                    },
                    "receiveReferrals": true,
                    "lastUpdated": null,
                    "teamsShare": {
                        "create": true,
                        "view": false
                    }
                },
                {
                    "id": 3,
                    "name": "Log Contacts",
                    "privateShare": {
                        "create": true,
                        "view": null
                    },
                    "publicShare": {
                        "create": true,
                        "view": false
                    },
                    "receiveReferrals": null,
                    "lastUpdated": null,
                    "teamsShare": {
                        "create": false,
                        "view": false
                    }
                },
                {
                    "id": 4,
                    "name": "Booking",
                    "privateShare": {
                        "create": true,
                        "view": null
                    },
                    "publicShare": {
                        "create": true,
                        "view": true
                    },
                    "receiveReferrals": null,
                    "lastUpdated": null,
                    "teamsShare": {
                        "create": true,
                        "view": false
                    }
                }
            ],
            "permissionTemplateId": 1,
            "lastUpdated": null,
            "intentToLeave": true,
            "riskIndicator": true
        },
        {
            "groups": {
                "604": "hha"
            },
            "organizationId": 1,
            "permissionTemplateName": "My Second Permission Template123",
            "profileBlocks": [
                {
                    "blockId": 5,
                    "blockSelection": true,
                    "blockName": "HighSchool-Testscores",
                    "lastUpdated": {
                        "lastErrors": {
                            "warning_count": 0,
                            "warnings": [

                            ],
                            "error_count": 0,
                            "errors": [

                            ]
                        },
                        "timezone": {
                            "name": "America\/New_York",
                            "location": {
                                "country_code": "US",
                                "latitude": 40.71417,
                                "longitude": -74.00639,
                                "comments": "Eastern Time"
                            }
                        },
                        "offset": -18000,
                        "timestamp": 1415375285
                    }
                },
                {
                    "blockId": 13,
                    "blockSelection": true,
                    "blockName": "Military Background",
                    "lastUpdated": null
                }
            ],
            "accessLevel": {
                "aggregateOnly": false,
                "individualAndAggregate": true
            },
            "isp": [
                {
                    "blockSelection": true,
                    "id": 1,
                    "itemLabel": "Age"
                },
                {
                    "blockSelection": false,
                    "id": 2,
                    "itemLabel": "isp-test-101"
                },
                {
                    "blockSelection": true,
                    "id": 12,
                    "itemLabel": "u"
                }
            ],
            "isq": [
                {
                    "blockSelection": true,
                    "id": 1,
                    "questionLabel": "q1",
                    "sequenceNo": null
                },
                {
                    "blockSelection": true,
                    "id": 2,
                    "questionLabel": "q1",
                    "sequenceNo": null
                }
            ],
            "surveyBlocks": [
                {
                    "blockId": 7,
                    "blockSelection": true,
                    "blockName": "Academic - Behaviors",
                    "lastUpdated": {
                        "lastErrors": {
                            "warning_count": 0,
                            "warnings": [

                            ],
                            "error_count": 0,
                            "errors": [

                            ]
                        },
                        "timezone": {
                            "name": "America\/New_York",
                            "location": {
                                "country_code": "US",
                                "latitude": 40.71417,
                                "longitude": -74.00639,
                                "comments": "Eastern Time"
                            }
                        },
                        "offset": -18000,
                        "timestamp": 1415375285
                    }
                },
                {
                    "blockId": 12,
                    "blockSelection": true,
                    "blockName": "Academic - Transition",
                    "lastUpdated": {
                        "lastErrors": {
                            "warning_count": 0,
                            "warnings": [

                            ],
                            "error_count": 0,
                            "errors": [

                            ]
                        },
                        "timezone": {
                            "name": "America\/New_York",
                            "location": {
                                "country_code": "US",
                                "latitude": 40.71417,
                                "longitude": -74.00639,
                                "comments": "Eastern Time"
                            }
                        },
                        "offset": -18000,
                        "timestamp": 1415375285
                    }
                }
            ],
            "features": [
                {
                    "id": 1,
                    "name": "Referrals",
                    "privateShare": {
                        "create": true,
                        "view": null
                    },
                    "publicShare": {
                        "create": true,
                        "view": false
                    },
                    "receiveReferrals": true,
                    "lastUpdated": null,
                    "teamsShare": {
                        "create": true,
                        "view": false
                    }
                },
                {
                    "id": 3,
                    "name": "Log Contacts",
                    "privateShare": {
                        "create": true,
                        "view": null
                    },
                    "publicShare": {
                        "create": true,
                        "view": false
                    },
                    "receiveReferrals": null,
                    "lastUpdated": null,
                    "teamsShare": {
                        "create": false,
                        "view": false
                    }
                },
                {
                    "id": 4,
                    "name": "Booking",
                    "privateShare": {
                        "create": true,
                        "view": null
                    },
                    "publicShare": {
                        "create": true,
                        "view": true
                    },
                    "receiveReferrals": null,
                    "lastUpdated": null,
                    "teamsShare": {
                        "create": true,
                        "view": false
                    }
                }
            ],
            "permissionTemplateId": 8,
            "lastUpdated": null,
            "intentToLeave": false,
            "riskIndicator": false
        }
    ]
}
INFO;
        $permInfo = json_decode($info, true);
        return $permInfo;
    }

    public function getAccessTree()
    {
        $json = <<<JSON
 {
    "\/": {
        "organizations": {
            "1": "*"
        },		
        "accessLevel": {
            "individualAndAggregate": "*"
        },
        "riskIndicator": "*",
        "intentToLeave": "*",
        "profileBlocks": {
            "profileBlocks-1": "",
            "profileBlocks-2": "",
            "profileBlocks-4": "",
            "profileBlocks-5": "*",
            "profileBlocks-13": "*"
        },
        "isp": {
            "isp-1": "*",
            "isp-2": "*",
            "isp-12": "*"
        },
        "surveyBlocks": {
            "surveyBlocks-7": "*",
            "surveyBlocks-8": "*",
            "surveyBlocks-12": "*"
        },
        "features": {
            "referrals-private-create": "*",
            "referrals-private-view": "*",
            "referrals-public-create": "*",
            "referrals-public-view": "*",
            "receive_referrals": "*",
            "referrals-teams-create": "*",
            "referrals-teams-view": "*",
            "log_contacts-private-create": "*",
            "log_contacts-private-view": "*",
            "log_contacts-public-create": "*",
            "log_contacts-public-view": "*",
            "log_contacts-teams-create": "",
            "log_contacts-teams-view": "",
            "booking-private-create": "*",
            "booking-private-view": "*",
            "booking-public-create": "*",
            "booking-public-view": "*",
            "booking-teams-create": "*",
            "booking-teams-view": "*"
        },
        "groups": {
            "ATOne": "*",
            "Automation": "*",
            "SSWWEEWW": "*",
            "hha": "*"
        },
        "isq": {
            "isq-1": "*",
            "isq-2": "*"
        },
        "students": {
            "0": "1",
            "1": "2",
        	"2": "3"	
        },
        "org_features": {
            "0": "Referrals",
            "1": "Notes",
            "2": "Log Contacts",
            "3":"Booking",
            "4":"Reason Routing"  
        }    
    }
}
JSON;
        return json_decode($json, true);
    }

   /* public function testCanCreateAccessTree()
    {
        $expected = $this->getAccessTree();        
        $accessTree = $this->manager->createAccessTree($this->getPermissionsInfo(), $this->userId);
        $accessTree['/']['students'] = $this->getStudents();
        $this->assertEquals($expected, $accessTree);
    }*/

    /**
     * @depends testCanCreateAccessTree
     */
    public function testCanBuildUserAccessTree()
    {
        $expected = $this->getAccessTree();
        $userId = 1;
        $this->manager->initializeForUser($userId);
        $accessTree = $this->manager->getRbac()->getAccessTree();
        $accessTree['/']['students'] = $this->getStudents();        
        $this->assertEquals($expected, $accessTree, 'Could not properly fetch a user access tree.');
    }

    /**
     * @depends testCanGetCurrentLoggedInUser
     */
   /* public function testCanBuildUserAccessTreeForCurrentUser()
    {
        $expected = $this->getAccessTree();
        $this->manager->initializeForUser();
        $accessTree = $this->manager->getRbac()->getAccessTree();
        $accessTree['/']['students'] = $this->getStudents();
        $this->assertEquals($expected, $accessTree, 'Could not properly fetch a user access tree.');
    }*/

    protected function initializedManager()
    {
        $userId = 1;
        $this->manager->initializeForUser($userId);
    }

    /*
     * @depends testCanBuildUserAccessTree
     */
   /* public function testCanDeterminViewAccess()
    {
        $this->getCoordinatorFromMock();
        $this->initializedManager();
       
        $this->assertTrue($this->manager->hasAccess('booking-public-view'), 'Did not grant access to a Granted permission.');
        $this->assertFalse($this->manager->hasAccess('booking-team-view'), 'Did grant access to an Abstained permission.');
        $this->assertFalse($this->manager->hasAccess('profileBlocks-4'), 'Did grant access to a Denied permission.');
    }*/

    /*
     * @depends testCanBuildUserAccessTree
     */
   /* public function testCanChainPermissions()
    {   
        $this->getCoordinatorFromMock();
        $this->initializedManager();
        $this->assertTrue($this->manager->hasAccess(['booking-public-view', 'booking-private-view']), 'Did not grant access to two Granted permissions.');
        $this->assertTrue($this->manager->hasAccess(['booking-public-view', 'booking-team-view']), 'Did not dency access with one Granted permission and one Abstained.');
        $this->assertTrue($this->manager->hasAccess(['booking-public-view', 'profileBlocks-4']), 'Did not dency access with one Granted permission and one Denied.');
    }*/
    
    /**
     * @depends testCanDeterminViewAccess
     */
    public function testCanToggleAuthOnAndOff()
    {        
        // Toggle auth on.
        $this->manager = new Manager(
                $this->em,
                $this->session,
                $this->securityContext,
                $this->cache,
                $this->permissionsetService,
                $this->rbac,
                ['enable_auth' => true]
            );
        $this->getCoordinatorFromMock();
        $this->initializedManager();

        $this->assertFalse($this->manager->hasAccess('booking-team-view'), 'Auth is still disabled: Granted access on Denied permission.');
        $this->assertTrue($this->manager->hasAccess('booking-public-view'), 'Auth is still disabled: Denied access on Granted permission.');
        
        // Toggle auth off.
        $this->manager = new Manager(
                $this->em,
                $this->session,
                $this->securityContext,
                $this->cache,
                $this->permissionsetService,
                $this->rbac,
                ['enable_auth' => false]
            );
        $this->initializedManager();

        $this->assertTrue($this->manager->hasAccess('booking-team-view'), 'Auth is still enabled: Denied access on Denied permission.');
        $this->assertTrue($this->manager->hasAccess('booking-public-view'), 'Auth is still enabled: Denied access on Granted permission.');

        $asset = $this->getMockBuilder('Synapse\CoreBundle\Entity\Contacts')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($this->manager->hasCoordinatorAccess());
        $this->assertTrue($this->manager->hasPrivateAccess($asset));
        $this->assertTrue($this->manager->hasTeamAccess($asset));
        $this->assertTrue($this->manager->hasAssetAccess(['anything'], $asset));

    }

    public function testCanConfigureCacheSettings()
    {
        $cacheTTL = 100;
        $longerCacheTTL = 1000;
        $manager = new Manager(
            $this->em,
            $this->session,
            $this->securityContext,
            $this->cache,
            $this->permissionsetService,
            $this->container,
            $this->rbac,
            [
                'cache_ttl' => $cacheTTL,
                'longer_cache_ttl' => $longerCacheTTL,
            ]
        );

        $mirror = new \ReflectionClass(Manager::class);

        $prop = $mirror->getProperty('cacheTTL');
        $prop->setAccessible(true);
        $newCacheTTL = $prop->getValue();
        $this->assertEquals($cacheTTL, $newCacheTTL, 'Did not set cacheTTL according to config.');

        $prop = $mirror->getProperty('longerCacheTTL');
        $prop->setAccessible(true);
        $newLongerCacheTTL = $prop->getValue();
        $this->assertEquals($longerCacheTTL, $newLongerCacheTTL, 'Did not set longerCacheTTL according to config.');
    }

    protected function getCoordinatorMock()
    {
        $orgMock = $this->getMockBuilder('Synapse\CoreBundle\Entity\Organization')
            ->disableOriginalConstructor()
            ->getMock();
        $orgMock->expects($this->any())
            ->method('getId')
            ->willReturn($this->returnValue(1));

        $person = $this->getMockBuilder('Synapse\CoreBundle\Entity\Person')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'getOrganization'])
            ->getMock();
        $person->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $person->expects($this->any())
            ->method('getOrganization')
            ->willReturn($orgMock);

        return $person;

    }
    protected function getCoordinatorFromMock() {
        
        $coordinator = $this->getCoordinatorMock();                
        $securityContextMock = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
        ->disableOriginalConstructor()
        ->setMethods(['getToken', 'isAuthenticated', 'getUser'])
        ->getMock();
        $securityContextMock->expects($this->any())
        ->method('getToken')
        ->will($this->returnSelf());
        $securityContextMock->expects($this->any())
        ->method('isAuthenticated')
        ->will($this->returnValue(true));
        $securityContextMock->expects($this->any())
        ->method('getUser')
        ->will($this->returnValue($coordinator));
        
        $this->em->expects($this->any())
        ->method('getUserCoordinatorRole')
        //            ->will($this->returnValue(true));
        ->will($this->onConsecutiveCalls(null, true));
        
        $this->manager = new Manager(
            $this->em,
            $this->session,
            $securityContextMock,
            $this->cache,
            $this->permissionsetService,
            $this->rbac
        );
    }
    
    protected function getStudentMock()
    {
        $person = $this->getMockBuilder('Synapse\CoreBundle\Entity\Person')
                ->disableOriginalConstructor()
                ->getMock();
        $person->expects($this->any())
                ->method('getId')
                ->will($this->returnValue(1));
        return $person;
    }

    protected function getFacultyMock()
    {
        $person = $this->getMockBuilder('Synapse\CoreBundle\Entity\Person')
                ->disableOriginalConstructor()
                ->getMock();
        $person->expects($this->any())
                ->method('getId')
                ->will($this->returnValue(2));
        return $person;
    }
    protected function getContactsPrivateAccessMock()
    {
        $contacts = $this->getMockBuilder('Synapse\CoreBundle\Entity\Contacts')
        ->disableOriginalConstructor()
        ->getMock();
        $contacts->expects($this->any())
        ->method('getAccessPrivate')
        ->will($this->returnValue(1));
        return $contacts;
    }

    /*public function testCanDetermineCoordinatorAccess()
    {
        $coordinator = $this->getCoordinatorMock();

        $securityContextMock = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->setMethods(['getToken', 'isAuthenticated', 'getUser'])
            ->getMock();
        $securityContextMock->expects($this->any())
            ->method('getToken')
            ->will($this->returnSelf());
        $securityContextMock->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true));
        $securityContextMock->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($coordinator));

        $this->em->expects($this->any())
            ->method('getUserCoordinatorRole')
//            ->will($this->returnValue(true));
            ->will($this->onConsecutiveCalls(null, true));

        $this->manager = new Manager(
            $this->em,
            $this->session,
            $securityContextMock,
            $this->cache,
            $this->permissionsetService,
            $this->rbac
        );


        $this->initializedManager();
        $this->assertFalse($this->manager->hasCoordinatorAccess(), 'Improperly granted coordinator access.');
        $this->assertFalse($this->manager->hasCoordinatorAccess(), 'Did not properly cache coordinator access.');

        $this->redisMock->turnOff();
        $this->assertTrue($this->manager->hasCoordinatorAccess(), 'Improperly did not grant coordinator access.');

//        $this->assertFalse($this->manager->hasCoordinatorAccess(), 'Improperly granted coordinator access.');
    }*/

   /* public function testCanDeterminePrivateAssetAccess()
    {
        $student = $this->getStudentMock();
        $faculty = $this->getFacultyMock();
        $contactsPrivate = $this->getContactsPrivateAccessMock();
            
        $asset = $this->getMockBuilder('Synapse\CoreBundle\Entity\Contacts')
                ->disableOriginalConstructor()
                ->getMock();
        $asset->expects($this->any())
                ->method('getPersonIdStudent')
                ->will($this->returnValue($student));
        $asset->expects($this->any())
                ->method('getPersonIdFaculty')
                ->will($this->returnValue($faculty));        
        $asset->expects($this->any())              
              ->method('getAccessPrivate')
              ->will($this->returnValue(1));        

        $this->assertFalse($this->manager->hasPrivateAccess($asset, 3), 'Improperly granted private access to the owning student.');
        $this->initializedManager();
        $this->assertFalse($this->manager->hasPrivateAccess($asset, $student->getId()), 'Did not grant private access to the owning student.');
        $this->assertFalse($this->manager->hasPrivateAccess($asset, $faculty->getId()), 'Did not grant private access to the owning faculty.');
        
        $this->assertFalse($this->manager->hasPrivateAccess($asset, 3), 'Improperly granted private access to the owning student.');
        $this->assertFalse($this->manager->hasPrivateAccess($asset, 4), 'Improperly granted private access to the owning faculty.');
    }*/

   /* public function testCanDetermineTeamAccess()
    {
        $assetTeams = [
            5 => [
                'id' => 5,
                'name' => 'Team 5'
            ]
        ];
        $assetTeams2 = [
            7 => [
                'id' => 7,
                'name' => 'Team 7'
            ]
        ];
        $userTeams = [
            [
                'team_id' => 5,
                'team_name' => 'Team 5'
            ],
            [
                'team_id' => 6,
                'team_team' => 'Team 6'
            ]
        ];

        $this->em->expects($this->any())
            ->method('getTeamsByAsset')
            ->will($this->onConsecutiveCalls([], $assetTeams, $assetTeams, $assetTeams2));
        $this->em->expects($this->any())
            ->method('getTeams')
            ->will($this->onConsecutiveCalls([], $userTeams, $userTeams, $userTeams));

        $asset = $this->getMockBuilder('Synapse\CoreBundle\Entity\Contacts')
            ->disableOriginalConstructor()
            ->getMock();
        $asset->expects($this->any())
        ->method('getAccessTeam')
        ->will($this->returnValue(1));
        
        $manager = new Manager(
            $this->em,
            $this->session,
            $this->securityContext,
            $this->cache,
            $this->permissionsetService,
            $this->rbac
        );

        $this->initializedManager();
//@TODO - needs to be fixed.... temp. commenting out		
//        $this->assertFalse($manager->hasTeamAccess($asset), 'Improperly granted team access when asset had no teams.');
//        $this->assertFalse($manager->hasTeamAccess($asset), 'Improperly granted team access when user had no teams.');
//        $this->assertTrue($manager->hasTeamAccess($asset), 'Improperly denied team access.');
//        $this->assertFalse($manager->hasTeamAccess($asset), 'Improperly granted team access to a member not part of the team.');
    }*/

    /*public function testCanGetAnAccessMap()
    {
        $expected = <<<JSON
{"profileBlocks":[{"id":5,"name":"HighSchool-Testscores","value":"*"},{"id":13,"name":"Military Background","value":"*"},{"id":4,"name":"HighSchool","value":""},{"id":5,"name":"HighSchool-Testscores","value":""}],"isp":[{"id":1,"name":"Age","value":"*"},{"id":2,"name":"isp-test-101","value":"*"},{"id":12,"name":"u","value":"*"}],"surveyBlocks":[{"id":7,"name":"Academic - Behaviors","value":"*"},{"id":12,"name":"Academic - Transition","value":"*"},{"id":12,"name":"Academic - Transition","value":"*"}],"isq":[{"id":1,"name":"","value":"*"},{"id":2,"name":"","value":"*"}],"features":[{"id":1,"name":"Referrals","private_share":{"view":true,"create":true},"public_share":{"view":true,"create":true},"receiveReferrals":true,"teams_share":{"view":true,"create":true}},{"id":3,"name":"Log Contacts","private_share":{"view":true,"create":true},"public_share":{"view":true,"create":true},"receiveReferrals":null,"teams_share":{"view":false,"create":false}},{"id":4,"name":"Booking","private_share":{"view":true,"create":true},"public_share":{"view":true,"create":true},"receiveReferrals":null,"teams_share":{"view":true,"create":true}}]}
JSON;

        $this->initializedManager();
        $accessmap = $this->manager->getAccessMap();
        unset($accessmap['students'], $accessmap['groups'], $accessmap['org_features']);        
        $this->assertEquals($expected, json_encode($accessmap));
    }*/

    /**
     * {@inheritDoc}
     */
    protected function _after()
    {

    }
}
