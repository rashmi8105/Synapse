<?php
use Codeception\Util\Stub;
class ActivityCategoryServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Service\Impl\PermissionSetService
     */
    private $activityCategoryService;
    
    private $org = 1;
    
    private $invalidOrg = -2;
    
    private $groupId = 1;
    
    private $invalidGroupId = -1;

    /**
     * {@inheritDoc}
     */

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->activityCategoryService = $this->container->get('activity_category_service');
    }

    public function  testGetReasonCategories(){
        $result = $this->activityCategoryService->getActivityCategory();
        $this->assertInternalType('array', $result);
        $this->assertNotNull($result['category_groups']);
        $this->assertArrayHasKey('group_item_key', $result['category_groups'][0]);
        $this->assertArrayHasKey('group_item_value', $result['category_groups'][0]);
        $this->assertArrayHasKey('subitems', $result['category_groups'][0]);
        $this->assertArrayHasKey('group_item_key', $result['category_groups'][1]);
        $this->assertArrayHasKey('group_item_value', $result['category_groups'][1]);
        $this->assertArrayHasKey('subitem_key', $result['category_groups'][0]['subitems'][0]);
        $this->assertArrayHasKey('subitem_value', $result['category_groups'][0]['subitems'][0]);
        $this->assertArrayHasKey('subitem_key', $result['category_groups'][1]['subitems'][0]);
        $this->assertArrayHasKey('subitem_value', $result['category_groups'][1]['subitems'][0]);
    }
}