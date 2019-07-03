<?php

namespace unit\Synapse\CoreBundle\Security\Authorization\TinyRbac;

use Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac;

class RbacTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /** @var Rbac **/
    private $rbac;
    
    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->rbac = new Rbac();
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container');
    }
    
    public function testCanGetAccessTree()
    {
        $expected = ['/' => []];
        $this->assertEquals($expected, $this->rbac->getAccessTree());
    }

    protected function getSampleAccessList()
    {
        return [
            '/' => [
                'group1' => [
                    'granted-perm' => Rbac::ACCESS_GRANTED,
                    'denied-perm' => Rbac::ACCESS_DENIED,
                    '_this' => Rbac::ACCESS_GRANTED,
                    ],
                'group2' => [
                    'granted-perm2' => Rbac::ACCESS_GRANTED,
                    'abstained-perm' => Rbac::ACCESS_ABSTAIN,
                    '_this' => Rbac::ACCESS_DENIED,
                    ]
                ]
            ];
    }

    /**
     * @depends testCanGetAccessTree
     */
    public function testCanSetAccessTree()
    {
        $expected = $this->getSampleAccessList();
        $this->rbac->setAccessTree($expected);
        $this->assertEquals($expected, $this->rbac->getAccessTree());
    }

    /**
     * @depends testCanSetAccessTree
     */
    public function testCanGetFlattenedPermissionsList()
    {
        $expected = [
            'granted-perm' => Rbac::ACCESS_GRANTED,
            'denied-perm' => Rbac::ACCESS_DENIED,
            'granted-perm2' => Rbac::ACCESS_GRANTED,
            'abstained-perm' => Rbac::ACCESS_ABSTAIN,
        ];
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $this->assertEquals($expected, $this->rbac->getPermissions());
    }

    public function testCanDetermineSimpleAccess()
    {
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $this->assertFalse($this->rbac->hasAccess('non-existant'), 'Could not properly validate a non-existant permission');
        $this->assertTrue($this->rbac->hasAccess('granted-perm'), 'Could not properly validate a Granted permission');
        $this->assertTrue($this->rbac->hasAccess('granted-perm2'), 'Could not properly validate a Granted permission');
        $this->assertFalse($this->rbac->hasAccess('denied-perm'), 'Could not properly validate a Denied permission');
        $this->assertFalse($this->rbac->hasAccess('abstained-perm'), 'Could not properly validate an Abstained permission');
    }

    public function testCanDetermineComplexAccess()
    {
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $this->assertFalse($this->rbac->hasComplexAccess('/group2/non-existant-perm'), 'Could not properly validate a Non-existant complex permission');
        $this->assertFalse($this->rbac->hasComplexAccess('/no-group/granted-perm'), 'Could not properly validate a Non-existant complex permission');
        $this->assertTrue($this->rbac->hasComplexAccess('/group1/granted-perm'), 'Could not properly validate a Granted complex permission');
        $this->assertFalse($this->rbac->hasComplexAccess('/group1/denied-perm'), 'Could not properly validate a Denied complex permission');
        $this->assertFalse($this->rbac->hasComplexAccess('/group2/abstained-perm'), 'Could not properly validate an Abstained complex permission');
    }

    public function testComplexAccessCanGrantOrDenyEveryPermissionInAGroup()
    {
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $this->assertTrue($this->rbac->hasComplexAccess('/group1'), 'Could not properly validate a Granted complex permission');
        $this->assertFalse($this->rbac->hasComplexAccess('/group2'), 'Could not properly validate a Denied complex permission');
    }

    public function testCanAddGroup()
    {
        $expected = [
            '/' => [
                'group1' => [
                    'granted-perm' => Rbac::ACCESS_GRANTED,
                    'denied-perm' => Rbac::ACCESS_DENIED,
                    '_this' => Rbac::ACCESS_GRANTED,
                    ],
                'group2' => [
                    'granted-perm2' => Rbac::ACCESS_GRANTED,
                    'abstained-perm' => Rbac::ACCESS_ABSTAIN,
                    '_this' => Rbac::ACCESS_DENIED,
                    ],
                'new-group' => []
                ]
            ];
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $this->rbac->addGroup('new-group');
        $this->assertEquals($expected, $this->rbac->getAccessTree(), 'Could not add a group.');

        $expected['/']['new-group']['new-child-group'] = [];

        $this->rbac->addGroup('new-child-group', 'new-group');
        $this->assertEquals($expected, $this->rbac->getAccessTree(), 'Could not add a child group to an existing group.');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanOnlyAddStringGroups()
    {
        $this->rbac->addGroup(666);
        $this->rbac->addGroup(array('foo'));
    }

    public function testCanAddGroups()
    {
        $expected = [
            '/' => [
                'group1' => [
                    'granted-perm' => Rbac::ACCESS_GRANTED,
                    'denied-perm' => Rbac::ACCESS_DENIED,
                    '_this' => Rbac::ACCESS_GRANTED,
                    ],
                'group2' => [
                    'granted-perm2' => Rbac::ACCESS_GRANTED,
                    'abstained-perm' => Rbac::ACCESS_ABSTAIN,
                    '_this' => Rbac::ACCESS_DENIED,
                    ],
                'new-group1' => [],
                'new-group2' => [],
                ]
            ];
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $this->rbac->addGroups(['new-group1', 'new-group2']);
        $this->assertEquals($expected, $this->rbac->getAccessTree());

    }

    public function testAddPermission()
    {
        $expected = [
            '/' => [
                'group1' => [
                    'granted-perm' => Rbac::ACCESS_GRANTED,
                    'denied-perm' => Rbac::ACCESS_DENIED,
                    'new-perm2' => Rbac::ACCESS_DENIED,
                    '_this' => Rbac::ACCESS_GRANTED,
                    ],
                'group2' => [
                    'granted-perm2' => Rbac::ACCESS_GRANTED,
                    'abstained-perm' => Rbac::ACCESS_ABSTAIN,
                    'new-perm1' => Rbac::ACCESS_GRANTED,
                    '_this' => Rbac::ACCESS_DENIED,
                ],
                ]
            ];
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $this->rbac->addPerm('new-perm1', 'group2', Rbac::ACCESS_GRANTED);
        $this->rbac->addPerm('new-perm2', 'group1', Rbac::ACCESS_DENIED);
        $this->assertEquals($expected, $this->rbac->getAccessTree());
    }

    /**
     * @depends testAddPermission
     */
    public function testCanAddPermissions()
    {
        $expected = [
            '/' => [
                'group1' => [
                    'granted-perm' => Rbac::ACCESS_GRANTED,
                    'denied-perm' => Rbac::ACCESS_DENIED,
                    'new-perm1' => Rbac::ACCESS_GRANTED,
                    'new-perm2' => Rbac::ACCESS_GRANTED,
                    '_this' => Rbac::ACCESS_GRANTED,
                    ],
                'group2' => [
                    'granted-perm2' => Rbac::ACCESS_GRANTED,
                    'abstained-perm' => Rbac::ACCESS_ABSTAIN,
                    '_this' => Rbac::ACCESS_DENIED,
                    ],
                ]
            ];
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $perms = [
            'new-perm1',
            'new-perm2',
        ];
        $this->rbac->addPerms($perms, 'group1', '*');
        $this->assertEquals($expected, $this->rbac->getAccessTree());        
    }

    /**
     * @depends testAddPermission
     * @expectedException \RuntimeException
     */
    public function testCantAddPermissionToNonexistantGroup()
    {
        $this->rbac->addPerm('new-perm', Rbac::ACCESS_GRANTED, 'nonexistant group');
    }

    public function testCanGetAUsersAccessTree()
    {
        $expected1 = [
            '/' => [
                'group2' => [
                    'granted-perm2' => Rbac::ACCESS_GRANTED,
                    'abstained-perm' => Rbac::ACCESS_ABSTAIN,
                    '_this' => Rbac::ACCESS_DENIED,
                ],
            ]
        ];
        $expected2 = [
            '/' => [
                'group1' => [
                    'granted-perm' => Rbac::ACCESS_GRANTED,
                    'denied-perm' => Rbac::ACCESS_DENIED,
                    '_this' => Rbac::ACCESS_GRANTED,
                ],
                'group2' => [
                    'granted-perm2' => Rbac::ACCESS_GRANTED,
                    'abstained-perm' => Rbac::ACCESS_ABSTAIN,
                    '_this' => Rbac::ACCESS_DENIED,
                ],
            ]
        ];
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $this->rbac->attachUser(1, 'group2');
        $this->assertFalse($this->rbac->getUserAccessTree(3), 'Tried to grab a non-existant user access tree.');
        $this->assertEquals($expected1, $this->rbac->getUserAccessTree(1), 'Could not get the correct user access tree.');

        $this->rbac->attachUser(1, 'group1');
        $this->assertEquals($expected2, $this->rbac->getUserAccessTree(1));
    }

    public function testCanGetAUsersPermissions()
    {
        $expected = [
            'granted-perm' => Rbac::ACCESS_GRANTED,
            'denied-perm' => Rbac::ACCESS_DENIED,
            'granted-perm2' => Rbac::ACCESS_GRANTED,
            'abstained-perm' => Rbac::ACCESS_ABSTAIN,
        ];
        $this->rbac->setAccessTree($this->getSampleAccessList());
        $this->rbac->attachUser(1, 'group2');
        $this->rbac->attachUser(1, 'group1');
        $this->assertEquals($expected, $this->rbac->getUserPermissions(1));
    }

    /**
     * {@inheritDoc}
     */
    protected function _after()
    {

    }
}
