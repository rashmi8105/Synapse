<?php

/**
 * Class GroupServiceTest
 */

use Codeception\TestCase\Test;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\RestBundle\Entity\OrgGroupDto;

class GroupServiceTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Service\Impl\GroupService
     */
    private $groupService;

    /**
     * @var \Synapse\CoreBundle\Repository\OrgGroupTreeRepository
     */
    private $orgGroupTreeRepository;
    
    /**
     * @var int
     */
    private $orgId = 214;

    /**
     * @var int
     */
    private $personId = 4878750;

    public function testGetStudentGroupsDetails()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->groupService = $this->container->get('group_service');
            $this->orgGroupTreeRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupTree');
            $rbacMan = $this->container->get('tinyrbac.manager');
            $rbacMan->initializeForUser(4891025); // so that user has the proper access

        });

        $this->specify("Verify the functionality of the method creategroup  and remove group", function ($orgId, $parentGroupName, $childGroupName, $grandChildGroupName) {

            // for testing group name and group externalId are the same.
            $createParentGroupDto = $this->createGroupDto($orgId, null, $parentGroupName);
            $parentGroup = $this->groupService->createGroup($createParentGroupDto);

            $parentGroupId = $parentGroup->getGroupId();

            $childGroupDto = $this->createGroupDto($orgId, $parentGroupId, $childGroupName);
            $childGroup = $this->groupService->createGroup($childGroupDto);
            $childGroupId = $childGroup->getGroupId();


            $grandChildGroupDto = $this->createGroupDto($orgId, $childGroupId, $grandChildGroupName);
            $grandChildGroup = $this->groupService->createGroup($grandChildGroupDto);
            $grandChildGroupId = $grandChildGroup->getGroupId();

            $checkParentGroup = $this->orgGroupTreeRepository->findOneBy([
                'ancestorGroupId' => $parentGroupId,
                'descendantGroupId' => $parentGroupId,
                'pathLength' => 0
            ]);

            $checkChildGroup = $this->orgGroupTreeRepository->findOneBy([
                'ancestorGroupId' => $childGroupId,
                'descendantGroupId' => $childGroupId,
                'pathLength' => 0
            ]);

            $checkGroupTree = $this->orgGroupTreeRepository->findBy([
                'ancestorGroupId' => $parentGroupId,
                'descendantGroupId' => $childGroupId,
                'pathLength' => 1
            ]);

            $checkGrandChildGroup = $this->orgGroupTreeRepository->findOneBy([
                'ancestorGroupId' => $grandChildGroupId,
                'descendantGroupId' => $grandChildGroupId,
                'pathLength' => 0
            ]);

            $checkGrandChildWithChildGroupTree = $this->orgGroupTreeRepository->findOneBy([
                'ancestorGroupId' => $childGroupId,
                'descendantGroupId' => $grandChildGroupId,
                'pathLength' => 1
            ]);

            $checkGrandChildWithParentGroupTree = $this->orgGroupTreeRepository->findOneBy([
                'ancestorGroupId' => $parentGroupId,
                'descendantGroupId' => $grandChildGroupId,
                'pathLength' => 2
            ]);


            verify($checkParentGroup)->notEmpty();
            verify($checkChildGroup)->notEmpty();
            verify($checkGroupTree)->notEmpty();
            verify($checkGrandChildGroup)->notEmpty();
            verify($checkGrandChildWithChildGroupTree)->notEmpty();
            verify($checkGrandChildWithParentGroupTree)->notEmpty();

            $this->groupService->deleteGroup($orgId, $childGroupId);

            $checkChildGroup = $this->orgGroupTreeRepository->findOneBy([
                'ancestorGroupId' => $childGroupId,
                'descendantGroupId' => $childGroupId,
                'pathLength' => 0
            ]);

            $checkGroupTree = $this->orgGroupTreeRepository->findBy([
                'ancestorGroupId' => $parentGroupId,
                'descendantGroupId' => $childGroupId,
                'pathLength' => 1
            ]);
            $checkGrandChildGroup = $this->orgGroupTreeRepository->findOneBy([
                'ancestorGroupId' => $grandChildGroupId,
                'descendantGroupId' => $grandChildGroupId,
                'pathLength' => 0
            ]);

            $checkGrandChildWithChildGroupTree = $this->orgGroupTreeRepository->findOneBy([
                'ancestorGroupId' => $childGroupId,
                'descendantGroupId' => $grandChildGroupId,
                'pathLength' => 1
            ]);

            $checkGrandChildWithParentGroupTree = $this->orgGroupTreeRepository->findOneBy([
                'ancestorGroupId' => $parentGroupId,
                'descendantGroupId' => $grandChildGroupId,
                'pathLength' => 2
            ]);

            verify($checkChildGroup)->isEmpty();
            verify($checkGroupTree)->isEmpty();
            verify($checkGrandChildGroup)->isEmpty();
            verify($checkGrandChildWithChildGroupTree)->isEmpty();
            verify($checkGrandChildWithParentGroupTree)->isEmpty();


        }, ["examples" =>
            [
                [214, "ParentGroup", "ChildGroup", "GrandChildGroup"]

            ]
        ]);

    }

    public function testGetGroupById()

    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->groupService = $this->container->get('group_service');

        });

        $this->specify("Verify the functionality of the method getGroupById", function ($expectedResultsSize, $expectedGid, $groupId) {
            $results = $this->groupService->getGroupById($this->orgId, $groupId);
            verify(count($results))->equals($expectedResultsSize);
            verify($results['group_id'])->notEmpty();
            verify($results['group_id'])->equals($expectedGid);

        }, ["examples" =>
            [
                [13, 370421, 370421],
                [13, 370427, 370427],
                [13, 370427, 370427]
            ]
        ]);
    }


    private function createGroupDto($orgId, $parentGroupId, $groupName)
    {

        $group = new OrgGroupDto();
        $group->setOrganizationId($orgId);
        $group->setParentGroupId($parentGroupId);
        $group->setGroupName($groupName);
        $group->setExternalId($groupName);
        return $group;
    }

    public function testGetListGroups()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->groupService = $this->container->get('group_service');
            $rbacMan = $this->container->get('tinyrbac.manager');
            // user of org Id - 203
            $rbacMan->initializeForUser($this->personId);
        });

        $this->specify("Verify the functionality of the method getListGroups", function ($loggedUserId , $expectedGrName)
        {
            $resultSet = $this->groupService->getListGroups($this->personId);
            verify($resultSet)->isInstanceOf("Synapse\SearchBundle\EntityDto\GroupsArrayDto");
            for ($i = 0; $i < count($resultSet->getGroups()); $i++) {
                verify($resultSet->getGroups()[$i])->isInstanceOf("Synapse\SearchBundle\EntityDto\GroupsDto");
                verify($resultSet->getGroups()[$i]->getGroupId())->notEmpty();
                verify($resultSet->getGroups()[$i]->getGroupName())->equals($expectedGrName);
            }

        }, ["examples" =>
            [
                [$this->personId, 'All Students']
            ]
        ]);
    }

}