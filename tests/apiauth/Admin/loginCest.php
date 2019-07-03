<?php
require_once(dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class loginCest extends SynapseRestfulTestBase
{
    private $admin = [
        'email' => 'david.warner@gmail.com',
        'password' => 'ramesh@1974',
        'id' => 99706,
        'orgId' => -1,
        'langId' => 1,
        'type' => 'admin'
    ];

    private $adminAccountData =[
        [
            'errors' => [],
            'data' => [
                'id' => 99706,
                'type' => "Coordinator",
                'firstname' => "David",
                'lastname' => "Warner",
                'email' => "david.warner@gmail.com",
                'mobile' => 9591900663,
                'external_id' => "David123",
                'organization_id' => -1,
                'organization_name' => "Ebi User Org",
                'lang_id' => 1,
                'lang_code' => "en_US",
                'can_act_as_proxy' => false,
                'org_features' => [],
                'user_feature_permissions' => [],
                'risk_indicator' => false,
                'intent_to_leave' => false,
                'is_multicampus_user' => false,
                'tier_level' => "",
                'proxy' => [
                    'is_proxy_user' => false
                ],
                'permissions' => [
                    'permission' => "Coordinator",
                    'templates' => [],
                ],
                'course_tab_enable' => false,
                'access_level' => [
                    'individual_and_aggregate' => false,
                    'aggregate_only' => false
                ],
                'courses_access' => [
                    'view_courses' => false,
                    'create_view_academic_update' => false,
                    'view_all_academic_update_courses' => false,
                    'view_all_final_grades' => false
                ],
                'academic_update_notification' => false,
                'refer_for_academic_assistance' => false,
                'send_to_student' => false
            ],
            'sideLoaded' => []
        ]
    ];


    //New Primary Tier
    public function testLoginAsAdmin(ApiAuthTester $I)
    {
        $I->wantTo('Login as an admin');
        $this->_getAPITestRunner($I, $this->admin, 'myaccount', [], 200, $this->adminAccountData);
    }

}
