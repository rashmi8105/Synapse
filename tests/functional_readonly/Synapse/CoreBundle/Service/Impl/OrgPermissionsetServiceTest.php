<?php

use Codeception\TestCase\Test;

class OrgPermissionsetServiceTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Service\Impl\OrgPermissionsetService
     */
    private $orgPermissionsetService;

    /**
     * @var \Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac
     */
    private $rbacManager;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->orgPermissionsetService = $this->container->get('orgpermissionset_service');
        $this->rbacManager = $this->container->get('tinyrbac.manager');
    }


    public function testBuildAggregatedFeatureTree()
    {
        $this->markTestSkipped('Skipping this test case for deployment, documented in ESPRJ-14997');
        $this->specify("Verify the functionality of the method buildAggregatedFeatureTree", function ($permissionSetIds, $personId, $expectedResult) {
            $this->rbacManager->initializeForUser($personId); // so that user has the proper access
            $result = $this->orgPermissionsetService->buildAggregatedFeatureTree($permissionSetIds);
            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                [[1452], 4893139, [
                                    'viewCourses' => true,
                                    'createViewAcademicUpdate' => true,
                                    'viewAllAcademicUpdateCourses' => true,
                                    'viewAllFinalGrades' => true,
                                    'referrals-public-create' => false,
                                    'referrals-public-view' => false,
                                    'referrals-private-create' => false,
                                    'referrals-private-view' => false,
                                    'referrals-teams-create' => false,
                                    'referrals-teams-view' => false,
                                    'reason-routed-referrals-public-create' => false,
                                    'reason-routed-referrals-public-view' => false,
                                    'reason-routed-referrals-private-create' => false,
                                    'reason-routed-referrals-private-view' => false,
                                    'reason-routed-referrals-teams-create' => false,
                                    'reason-routed-referrals-teams-view' => false,
                                    'receive_referrals' => false,
                                    'notes-public-create' => false,
                                    'notes-public-view' => false,
                                    'notes-private-create' => false,
                                    'notes-private-view' => false,
                                    'notes-teams-create' => false,
                                    'notes-teams-view' => false,
                                    'log_contacts-public-create' => false,
                                    'log_contacts-public-view' => false,
                                    'log_contacts-private-create' => false,
                                    'log_contacts-private-view' => false,
                                    'log_contacts-teams-create' => false,
                                    'log_contacts-teams-view' => false,
                                    'booking-public-create' => false,
                                    'booking-public-view' => false,
                                    'booking-private-create' => false,
                                    'booking-private-view' => false,
                                    'booking-teams-create' => false,
                                    'booking-teams-view' => false,
                                    'email-public-create' => false,
                                    'email-public-view' => false,
                                    'email-private-create' => false,
                                    'email-private-view' => false,
                                    'email-teams-create' => false,
                                    'email-teams-view' => false
                                ]
                ],
                [[399], 202267, [
                                    'viewCourses' => false,
                                    'createViewAcademicUpdate' => true,
                                    'viewAllAcademicUpdateCourses' => false,
                                    'viewAllFinalGrades' => false,
                                    'referrals-public-create' => true,
                                    'referrals-public-view' => false,
                                    'referrals-private-create' => false,
                                    'referrals-private-view' => false,
                                    'referrals-teams-create' => true,
                                    'referrals-teams-view' => false,
                                    'reason-routed-referrals-public-create' => true,
                                    'reason-routed-referrals-public-view' => false,
                                    'reason-routed-referrals-private-create' => false,
                                    'reason-routed-referrals-private-view' => false,
                                    'reason-routed-referrals-teams-create' => true,
                                    'reason-routed-referrals-teams-view' => false,
                                    'receive_referrals' => false,
                                    'notes-public-create' => true,
                                    'notes-public-view' => false,
                                    'notes-private-create' => false,
                                    'notes-private-view' => false,
                                    'notes-teams-create' => true,
                                    'notes-teams-view' => false,
                                    'log_contacts-public-create' => true,
                                    'log_contacts-public-view' => false,
                                    'log_contacts-private-create' => false,
                                    'log_contacts-private-view' => false,
                                    'log_contacts-teams-create' => true,
                                    'log_contacts-teams-view' => false,
                                    'booking-public-create' => true,
                                    'booking-public-view' => false,
                                    'booking-private-create' => false,
                                    'booking-private-view' => false,
                                    'booking-teams-create' => true,
                                    'booking-teams-view' => false,
                                    'student_referrals-public-create' => false,
                                    'student_referrals-public-view' => false,
                                    'student_referrals-private-create' => false,
                                    'student_referrals-private-view' => false,
                                    'student_referrals-teams-create' => false,
                                    'student_referrals-teams-view' => false,
                                    'reason_routing-public-create' => false,
                                    'reason_routing-public-view' => false,
                                    'reason_routing-private-create' => false,
                                    'reason_routing-private-view' => false,
                                    'reason_routing-teams-create' => false,
                                    'reason_routing-teams-view' => false,
                                    'email-public-create' => true,
                                    'email-public-view' => true,
                                    'email-private-create' => false,
                                    'email-private-view' => false,
                                    'email-teams-create' => false,
                                    'email-teams-view' => false
                                ]
                ]
            ]
        ]);
    }

}