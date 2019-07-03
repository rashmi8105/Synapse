<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\FeatureDTO;

class FeatureServiceTest extends \Codeception\TestCase\Test
{

    /**
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\CoreBundle\Service\FeatureService
     */
    private $featureService;

    private $organization = 1;


    private $key = "Referral";

    private $name = "Referral_Feature";

    private $langId = 1;

    private $invalidLangId = -1;

    private $invalidOrg = -200;
    
    private $userId = 1;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->featureService = $this->container->get('feature_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->userId);
    }
    public function testUpdateFeatures()
    {
        $this->markTestSkipped("Errored");
        $this->initializeRbac();
        $createdFeature = $this->createFeature();
        $feature = $this->featureService->updateFeatures($createdFeature);

        $this->assertEquals($createdFeature->getOrganizationId(), $feature['organization_id']);
        $this->assertEquals($createdFeature->getOrganizationId(), $feature["organization_id"]);
        $this->assertEquals($createdFeature->getReferralFeatureId(), $feature["referral_feature_id"]);
        $this->assertEquals($createdFeature->getReferralOrgId(), $feature["referral_org_id"]);
        $this->assertEquals($createdFeature->getIsReferralEnabled(), $feature["is_referral_enabled"]);
        $this->assertEquals($createdFeature->getNotesFeatureId(), $feature["notes_feature_id"]);
        $this->assertEquals($createdFeature->getNotesOrgId(), $feature["notes_org_id"]);
        $this->assertEquals($createdFeature->getIsNotesEnabled(), $feature["is_notes_enabled"]);
        $this->assertEquals($createdFeature->getLogContactsFeatureId(), $feature["log_contacts_feature_id"]);
        $this->assertEquals($createdFeature->getLogContactsOrgId(), $feature["log_contacts_org_id"]);
        $this->assertEquals($createdFeature->getIsLogContactsEnabled(), $feature["is_log_contacts_enabled"]);
        $this->assertEquals($createdFeature->getBookingFeatureId(), $feature["booking_feature_id"]);
        $this->assertEquals($createdFeature->getBookingOrgId(), $feature["booking_org_id"]);
        $this->assertEquals($createdFeature->getIsBookingEnabled(), $feature["is_booking_enabled"]);
        $this->assertEquals($createdFeature->getStudentReferralNotificationFeatureId(), $feature["student_referral_notification_feature_id"]);
        $this->assertEquals($createdFeature->getStudentReferralNotificationOrgId(), $feature["student_referral_notification_org_id"]);
        $this->assertEquals($createdFeature->getIsStudentReferralNotificationEnabled(), $feature["is_student_referral_notification_enabled"]);
        $this->assertEquals($createdFeature->getReasonRoutingFeatureId(), $feature["reason_routing_feature_id"]);
        $this->assertEquals($createdFeature->getReasonRoutingOrgId(), $feature["reason_routing_org_id"]);
        $this->assertEquals($createdFeature->getIsReasonRoutingEnabled(), $feature["is_reason_routing_enabled"]);
        
        $this->assertEquals($createdFeature->getEmailFeatureId(), $feature["email_feature_id"]);
        $this->assertEquals($createdFeature->getEmailOrgId(), $feature["email_org_id"]);
        $this->assertEquals($createdFeature->getIsEmailEnabled(), $feature["is_email_enabled"]);
        $this->assertEquals($createdFeature->getPrimaryCampusConnectionReferralRoutingFeatureId(), $feature["primary_campus_connection_referral_routing_feature_id"]);
        $this->assertEquals($createdFeature->getPrimaryCampusConnectionReferralRoutingOrgId(), $feature["primary_campus_connection_referral_routing_org_id"]);
        $this->assertEquals($createdFeature->getIsPrimaryCampusConnectionReferralRoutingEnabled(), $feature["is_primary_campus_connection_referral_routing_enabled"]);
    }



    public function testListFeature()
    {
        //$this->markTestSkipped("Errored");
        $this->initializeRbac();
        $createdFeature = $this->createFeature();
        //$feature = $this->featureService->updateFeatures($createdFeature);
        $feature = $this->featureService->getListFeatures($this->organization, $this->langId);
        $this->assertEquals($createdFeature->getOrganizationId(), $feature["organization_id"]);
        $this->assertEquals($createdFeature->getReferralFeatureId(), $feature["referral_feature_id"]);
        $this->assertEquals($createdFeature->getReferralOrgId(), $feature["referral_org_id"]);
        $this->assertEquals($createdFeature->getIsReferralEnabled(), $feature["is_referral_enabled"]);
        $this->assertEquals($createdFeature->getNotesFeatureId(), $feature["notes_feature_id"]);
        $this->assertEquals($createdFeature->getNotesOrgId(), $feature["notes_org_id"]);
        $this->assertEquals($createdFeature->getIsNotesEnabled(), $feature["is_notes_enabled"]);
        $this->assertEquals($createdFeature->getLogContactsFeatureId(), $feature["log_contacts_feature_id"]);
        $this->assertEquals($createdFeature->getLogContactsOrgId(), $feature["log_contacts_org_id"]);
        $this->assertEquals($createdFeature->getIsLogContactsEnabled(), $feature["is_log_contacts_enabled"]);
        $this->assertEquals($createdFeature->getBookingFeatureId(), $feature["booking_feature_id"]);
        $this->assertEquals($createdFeature->getBookingOrgId(), $feature["booking_org_id"]);
        $this->assertEquals($createdFeature->getIsBookingEnabled(), $feature["is_booking_enabled"]);
        $this->assertEquals($createdFeature->getStudentReferralNotificationFeatureId(), $feature["student_referral_notification_feature_id"]);
        $this->assertEquals($createdFeature->getStudentReferralNotificationOrgId(), $feature["student_referral_notification_org_id"]);
        $this->assertEquals($createdFeature->getIsStudentReferralNotificationEnabled(), $feature["is_student_referral_notification_enabled"]);
        $this->assertEquals($createdFeature->getReasonRoutingFeatureId(), $feature["reason_routing_feature_id"]);
        $this->assertEquals($createdFeature->getReasonRoutingOrgId(), $feature["reason_routing_org_id"]);
        $this->assertEquals($createdFeature->getIsReasonRoutingEnabled(), $feature["is_reason_routing_enabled"]);
        
        $this->assertEquals($createdFeature->getEmailFeatureId(), $feature["email_feature_id"]);
        $this->assertEquals($createdFeature->getEmailOrgId(), $feature["email_org_id"]);
        $this->assertEquals($createdFeature->getIsEmailEnabled(), $feature["is_email_enabled"]);
        $this->assertEquals($createdFeature->getPrimaryCampusConnectionReferralRoutingFeatureId(), $feature["primary_campus_connection_referral_routing_feature_id"]);
        $this->assertEquals($createdFeature->getPrimaryCampusConnectionReferralRoutingOrgId(), $feature["primary_campus_connection_referral_routing_org_id"]);
        $this->assertEquals($createdFeature->getIsPrimaryCampusConnectionReferralRoutingEnabled(), $feature["is_primary_campus_connection_referral_routing_enabled"]);
        
    }



    public function testGetListMasterFeaturesStatus()
    {
        //$this->markTestSkipped("Errored");
        $this->initializeRbac();
    	$featureStatus = $this->featureService->getListMasterFeaturesStatus($this->organization);
     	$this->assertInternalType('array', $featureStatus);
     	$this->assertEquals($this->organization, $featureStatus['organization_id']);
     	$this->assertContains('is_referral_enabled', $featureStatus);
     	$this->assertContains('referral_feature_id', $featureStatus);
     	$this->assertContains('notes_feature_id', $featureStatus);
     	$this->assertContains('notes_feature_id', $featureStatus);
     	$this->assertContains('log_contacts_feature_id', $featureStatus);
     	$this->assertContains('is_log_contacts_enabled', $featureStatus);
     	$this->assertContains('booking_feature_id', $featureStatus);
     	$this->assertContains('is_booking_enabled', $featureStatus);
     	$this->assertContains('student_referral_notification_feature_id', $featureStatus);
     	$this->assertContains('is_student_referral_notification_enabled', $featureStatus);
     	$this->assertContains('reason_routing_feature_id', $featureStatus);
     	$this->assertContains('is_reason_routing_enabled', $featureStatus);
     	
     	$this->assertContains('email_feature_id', $featureStatus);
     	$this->assertContains('is_email_enabled', $featureStatus);
     	$this->assertContains('primary_campus_connection_referral_routing_feature_id', $featureStatus);
     	$this->assertContains('is_primary_campus_connection_referral_routing_enabled', $featureStatus);
    }


    public function testGetListFeatures()
    {
       // $this->markTestSkipped("Errored");
        $this->initializeRbac();
    	$feature = $this->featureService->getListFeatures($this->organization,$this->langId);
    	$this->assertInternalType('array',$feature);

    	$this->assertEquals($this->organization, $feature['organization_id']);
    	$this->assertArrayHasKey('reason_routing_list', $feature);
    	$this->assertArrayHasKey('referral_feature_id', $feature);
    	$this->assertArrayHasKey('referral_org_id', $feature);
    	$this->assertArrayHasKey('notes_feature_id', $feature);
    	$this->assertArrayHasKey('notes_org_id', $feature);
    	$this->assertArrayHasKey('log_contacts_feature_id', $feature);
    	$this->assertArrayHasKey('log_contacts_org_id', $feature);
    	$this->assertArrayHasKey('booking_feature_id', $feature);
    	$this->assertArrayHasKey('booking_org_id', $feature);
    	$this->assertArrayHasKey('student_referral_notification_feature_id', $feature);
    	$this->assertArrayHasKey('student_referral_notification_org_id', $feature);
    	$this->assertNotNull($feature['referral_feature_id']);
    }


    public function createFeature()
    {
        $feature = new FeatureDTO();
        $feature->setOrganizationId(1);

        $feature->setReferralFeatureId(1);
        $feature->setReferralOrgId(1);
        $feature->setIsReferralEnabled(1);

        $feature->setNotesFeatureId(2);
        $feature->setNotesOrgId(2);
        $feature->setIsNotesEnabled(1);

        $feature->setLogContactsFeatureId(3);
        $feature->setLogContactsOrgId(3);
        $feature->setIsLogContactsEnabled(1);

        $feature->setBookingFeatureId(4);
        $feature->setBookingOrgId(4);
        $feature->setIsBookingEnabled(1);

        $feature->setStudentreferralNotificationFeatureId(5);
        $feature->setStudentreferralNotificationOrgId(5);
        $feature->setIsStudentreferralNotificationEnabled(1);
        $feature->setReasonRoutingFeatureId(6);
        $feature->setReasonRoutingOrgId(6);
        $feature->setIsReasonRoutingEnabled(1);
        $feature->setEmailFeatureId(7);
        $feature->setEmailOrgId(7);
        $feature->setIsEmailEnabled(1);
        $feature->setPrimaryCampusConnectionReferralRoutingFeatureId(8);
        $feature->setPrimaryCampusConnectionReferralRoutingOrgId(8);
        $feature->setIsPrimaryCampusConnectionReferralRoutingEnabled(1); 
        /* $subcategory['sub_categories'][] = array(
            "reason_id" => 1,
            "is_primary_coordinator" => true,
            "person_id" => 0
        );
        $subcategory['sub_categories'][] = array(
            "reason_id" => 2,
            "is_primary_coordinator" => true,
            "person_id" => 0
        );
        $subcategory['sub_categories'][] = array(
            "reason_id" => 3,
            "is_primary_coordinator" => true,
            "person_id" => 0
        );
        $reasonRoutingList[] = $subcategory; */
        
        $reasonRoutingList = [
        [
        	"reason_id"=> 1,
        	"reason_name"=> "Academic Issues",
        	"sub_categories"=> [
        	[
        		"reason_id"=> 19,
        		"reason_name"=> "Class attendance concern ",
        		"is_primary_coordinator"=> false,
        		"is_primary_campus_connection"=> false,
        		"person_id"=> 1
        	],
        	[
        		"reason_id"=> 20,
        		"reason_name"=> "Class attendance positive",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> false,
        		"is_primary_campus_connection"=> true
        	],
        	[
        		"reason_id"=> 21,
        		"reason_name"=> "Academic performance concern ",
        		"is_primary_coordinator"=> false,
        		"is_primary_campus_connection"=> false,
        		"person_id"=> 1
        	],
        	[
        		"reason_id"=> 22,
        		"reason_name"=> "Academic performance positive",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 23,
        		"reason_name"=> "Registration positive ",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 24,
        		"reason_name"=> "Registration concern",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 25,
        		"reason_name"=> "Academic skills",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 26,
        		"reason_name"=> "Academic major exploration/selection",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 27,
        		"reason_name"=> "Academic action meeting",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 28,
        		"reason_name"=> "Academic success planning",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 29,
        		"reason_name"=> "Missing required meetings / activities",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 30,
        		"reason_name"=> "Attended meeting / activities",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 31,
        		"reason_name"=> "Other academic concerns",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> false,
        		"is_primary_campus_connection"=> true
        	]
        	]
        ],
        [
        	"reason_id"=> 2,
        	"reason_name"=> "Personal Issues",
        	"sub_categories"=> [
        	[
        		"reason_id"=> 32,
        		"reason_name"=> "Living environment concern",
        		"is_primary_coordinator"=> false,
        		"is_primary_campus_connection"=> false,
        		"person_id"=> 1
        	],
        	[
        		"reason_id"=> 33,
        		"reason_name"=> "Living environment positive",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 34,
        		"reason_name"=> "Relationships concern",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 35,
        		"reason_name"=> "Relationships positive",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 36,
        		"reason_name"=> "Social connections concern",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 37,
        		"reason_name"=> "Social connections positive",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 38,
        		"reason_name"=> "Medical / mental health",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	]
        ],
        [
        	"reason_id"=> 3,
        	"reason_name"=> "Financial Issues",
        	"sub_categories"=> [
        	[
        		"reason_id"=> 39,
        		"reason_name"=> "Short term",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 40,
        		"reason_name"=> "Long term",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	],
        	[
        		"reason_id"=> 41,
        		"reason_name"=> "Positive financial",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	]
        	]
        ],
        [
        	"reason_id"=> 4,
        	"reason_name"=> "Mapworks issues",
        	"sub_categories"=> [
        	[
        		"reason_id"=> 42,
        		"reason_name"=> "Mapworks related issues",
        		"person_id"=> 0,
        		"is_primary_coordinator"=> true,
        		"is_primary_campus_connection"=> false
        	]
        	]
        ]
        ];
        $feature->setReasonRoutingList($reasonRoutingList);

        return $feature;
    }
    /**
     * {@inheritDoc}
     */
    protected function _after()
    {}
}
