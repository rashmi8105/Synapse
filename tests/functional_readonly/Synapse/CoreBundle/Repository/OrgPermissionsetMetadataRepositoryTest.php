<?php
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\OrgPermissionsetMetadataRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;


class OrgPermissionsetMetadataRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     *
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var OrgPermissionsetMetadataRepository
     */
    private $orgPermissionsetMetadataRepository;


    public function testGetIspsByPermissionSet()
    {

        $this->beforeSpecify(function () {
            $this->container = $this->getModule(\Synapse\CoreBundle\SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
            $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
            $this->orgPermissionsetMetadataRepository = $this->repositoryResolver->getRepository(OrgPermissionsetMetadataRepository::REPOSITORY_KEY);
        });
        $this->specify("Verify the functionality of the method getIspsByPermissionSet", function ($organizationId, $permissionSetId, $expectedResultsSize, $expectedResults) {
            $results = $this->orgPermissionsetMetadataRepository->getIspsByPermissionSet($permissionSetId, $organizationId);
            verify(count($results))->equals($expectedResultsSize);
            if (count($results) > 0) {
                verify($results)->equals($expectedResults);
            }
        }, [
            "examples" => [
                [ // Standard Test Case, permissionset exists in organization
                    203, 1411, 2,
                    [
                        [
                            'ispId' => "7326",
                            'metaKey' => "ORG203META007326",
                            'ispLabel' => "Organization: 203 Metadata ID: 007326",
                            'ispDescription' => "Organization: 203 Metadata ID: 007326",
                            'ispSelection' => "1",
                            'modifiedAt' => "2016-01-26 19:48:26"
                        ],
                        [
                            'ispId' => "7327",
                            'metaKey' => "ORG203META007327",
                            'ispLabel' => "Organization: 203 Metadata ID: 007327",
                            'ispDescription' => "Organization: 203 Metadata ID: 007327",
                            'ispSelection' => "1",
                            'modifiedAt' => "2016-01-26 19:48:26"
                        ]
                    ],
                ],
                [ // Permissionset does not exist in the organization
                    203, 2, 0, null
                ],
                [ // organization does not exist
                    867, 5309, 0, null
                ],
                [ // Organization/Permissionset is null
                    null, null, 0, null
                ],
            ]
        ]);
    }

    public function testgetAllISPsByPersonIdWithRelationToStudentAccess()
    {

        $this->beforeSpecify(function () {
            $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
            $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
            $this->orgPermissionsetMetadataRepository = $this->repositoryResolver->getRepository(OrgPermissionsetMetadataRepository::REPOSITORY_KEY);
        });
        $this->specify("Verify the functionality of the method getAllISPsByPersonIdWithRelationToStudentAccess", function ($organizationId, $personId, $expectedResultsSize, $expectedResults) {
            $results = $this->orgPermissionsetMetadataRepository->getAllISPsByPersonIdWithRelationToStudentAccess($personId, $organizationId);
            verify(count($results))->equals($expectedResultsSize);
            if (count($results) > 0) {
                verify($results)->contains($expectedResults);
            } else {
                verify($expectedResults)->equals(null);
            }
        }, [
            "examples" => [
                [ // Case 1: the person exists within the organization
                    2, 2, 3, 3
                ],
                [ // Case 2: the person does not exist within the organization
                    203, 2, 0, null
                ],
                [ // Case 3: the organization does not exist
                    867, 5309, 0, null
                ],
                [ // Case 4: the person in the organization does not have acess to any ISP
                    203, 4883099, 0, null
                ],
            ]
        ]);
    }
}