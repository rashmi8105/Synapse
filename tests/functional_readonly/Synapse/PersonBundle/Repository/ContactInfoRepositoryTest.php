<?php
use Codeception\TestCase\Test;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\CoreBundle\Exception\SynapseValidationException;

/**
 * Class ContactInfoRepositoryTest
 */
class ContactInfoRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(\Synapse\CoreBundle\SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
    }

    public function testGetPersonMobileAndHomePhoneNumbers()
    {
        $this->specify("Verify the functionality of the method getPersonMobileAndHomePhoneNumbers", function ($personIds, $expectedHomePhoneNumbers) {
            $results = $this->contactInfoRepository->getPersonMobileAndHomePhoneNumbers($personIds);
            $homePhoneNumbers = array_column($results, 'home_phone');
            $this->assertEquals($homePhoneNumbers, $expectedHomePhoneNumbers);
        }, [
            "examples" => [
                // List of person Id's ,only 4883148 and 4878750 have phone numbers,ordering is done by lastname and firstname
                [[4883150,4883148,4883079,4878751,4883169,4891668,4878750],[
                                                                                0 => null,
                                                                                1 => null,
                                                                                2 => null,
                                                                                3 => '555-555-2251',
                                                                                4 => null,
                                                                                5 => null,
                                                                                6 => '555-555-2250'
                                                                            ]
                ],
                // List of person Id's ,only 4883148 and 4878750 have phone numbers,ordering is done by lastname and firstname
                [[4883174,4883079,4878751,4883148,4883175,4891668,4878750], [
                                                                                0 => null,
                                                                                1 => null,
                                                                                2 => null,
                                                                                3 => '555-555-2251',
                                                                                4 => null,
                                                                                5 => null,
                                                                                6 => '555-555-2250'
                                                                            ]
                ],
                // List of person Id's  only 4883148 and 4878750 have phone numbers,ordering is done by lastname and firstname
                [[4883150,4883160,4883079,4878751,4883148,4883162,4891668,4878750,4883165],[
                                                                                0 => null,
                                                                                1 => null,
                                                                                2 => null,
                                                                                3 => null,
                                                                                4 => '555-555-2251',
                                                                                5 => null,
                                                                                6 => null,
                                                                                7 => '555-555-2250',
                                                                                8 => null
                                                                            ]
                ],
                // Only one person id passed  with phone number 555-555-2795
                [[4879295],[0 => '555-555-2795']]
            ]
        ]);
    }

    public function testGetPersonIdsBasedOnContactInfoFilters()
    {
        $this->specify("Verify the functionality of the method GetPersonIdsBasedOnContactInfoFilters", function ($organizationId, $personFilter, $contactFilter, $contactFilterType, $expectedResult) {
            $results = $this->contactInfoRepository->getPersonIdsBasedOnContactInfoFilters($organizationId, $personFilter, $contactFilter, $contactFilterType);
            $this->assertEquals($results, $expectedResult);
        }, [
            "examples" => [
                // get person id with no filters
                [
                    184, null, null, null, [256046, 256035, 256033]
                ],
                // get the person  with externalId  = 245711  for organization Id  =  141
                [
                    141, 245711, null, null, [245711]
                ],
                // get the person  with  name =  DilanWinters  for organization Id =  141
                [
                    141, "DilanWinters", null, null, [245711]
                ],
                // get the person  with  name like  DilanWi  for organization 141
                [
                    141, "DilanWi", null, null, [245711]
                ],
                // get the person  with  name is like  DilanWin   and  contact number is 555-555-5711 for organization 141
                [
                    141, "DilanWinters", "555-555-5711", "phone", [245711]
                ],
                // get the person  with  name is like  DilanWin   and  contact number is 555-555-6000 for organization 141
                [
                    141, "DilanWinters", "555-555-6000", "phone", []
                ],
                // get the person  with  name is like  DilanWin   and  stays at Some CityTX12345 for organization 141
                [
                    141, "DilanWinters", "Some CityTX12345", "address", [245711]
                ],
                // get the person  with  name is like  DilanWin   and  stays at TX for organization 141
                [
                    141, "DilanWinters", "TX", "address", [245711]
                ],
                // get the person  with  name is like  DilanWin   and  stays at place where postal code is 12345 for organization 141
                [
                    141, "DilanWinters", "12345", "address", [245711]
                ],
                // get the person  with  name is like  Dilan  and  stays at place where postal code is 68790 for organization 141
                [
                    141, "Dilan", "1234567899", "address", []
                ],
                // get the person  with  name is like  nonexistent  and  stays at place where postal code is 68790 for organization 141
                [
                    141, "nonexistent", null, null, []
                ],
                // get the person  with  name is like  Dilan  for an non-existent organization id -1
                [
                    -1, "Dilan", null, null, []
                ],
                // searching for a valid user in a wrong organization
                [
                    183, "DilanWinters", "12345", "address", []
                ],
            ]
        ]);
    }
    public function testGetCoalescedContactInfo()
    {
        $this->specify("Verify the functionality of the method getCoalescedContactInfo", function ($contacts, $expectedPrimaryEmail, $expectedHomePhone) {

            $results = $this->contactInfoRepository->getCoalescedContactInfo($contacts);

            $this->assertEquals($results->getPrimaryEmail(), $expectedPrimaryEmail);
            $this->assertEquals($results->getHomePhone(), $expectedHomePhone);
            $this->assertEquals($results->getAddress1(), null);
            $this->assertEquals($results->getAlternateEmail(), null);
            $this->assertEquals($results->getCity(), null);

        }, [
            "examples" => [
                //Test Case 0: Setting contact info and verifying for valid email 'test1@mailinator.com' and phone '111-555-4397'
                [
                    $this->getContacts('test1@mailinator.com', '111-555-4397'), 'test1@mailinator.com', '111-555-4397'
                ],
                //Test Case 1: Setting contact info and verifying for valid email 'test2@mailinator.com' and phone '222-555-4397'
                [
                    $this->getContacts('test2@mailinator.com', '222-555-4397'), 'test2@mailinator.com', '222-555-4397'
                ],
                //Test Case 2: Setting contact info and verifying for valid email 'test3@mailinator.com' and phone '333-555-4397'
                [
                    $this->getContacts('test3@mailinator.com', '333-555-4397'), 'test3@mailinator.com', '333-555-4397'
                ]

            ]
        ]);
    }
    public function testGetUsersContactInfo()
    {
        $this->specify("Verify the functionality of the method GetPersonIdsBasedOnContactInfoFilters", function ($organizationId, $personIdArr, $offset, $recordsPerPage, $expectedResult) {
            $results = $this->contactInfoRepository->getUsersContactInfo($organizationId, $personIdArr, $offset, $recordsPerPage);
            $this->assertEquals($results, $expectedResult);
        }, [
            "examples" => [
                // get contact info for  245711  for organization id = 141
                [
                    141, [245711], null, null,
                    [
                        [
                            "external_id" => "245711",
                            "firstname" => "Dilan",
                            "lastname" => "Winters",
                            "primary_email" => "MapworksBetaUser00245711@mailinator.com",
                            "address_one" => "1234 Somewhere Street",
                            "address_two" => " ",
                            "city" => "Some City",
                            "state" => "TX",
                            "zip" => "12345",
                            "country" => " ",
                            "primary_mobile" => "555-555-5711",
                            "alternate_mobile" => null,
                            "home_phone" => "555-555-5711",
                            "office_phone" => "555-555-5711",
                            "alternate_email" => "MapworksTestingUserAlternate00245711@mailinator.com",
                            "primary_mobile_provider" => null,
                            "alternate_mobile_provider" => null
                        ]
                    ]
                ],
                // get contact info for  for all there person  for organization id = 184  without any pagination
                [
                    184, [256046, 256035, 256033], null, null,
                    [
                        [
                            "external_id" => "256046",
                            "firstname" => "Valeria",
                            "lastname" => "Phillips",
                            "primary_email" => "MapworksBetaUser00256046@mailinator.com",
                            "address_one" => null,
                            "address_two" => null,
                            "city" => null,
                            "state" => null,
                            "zip" => null,
                            "country" => null,
                            "primary_mobile" => null,
                            "alternate_mobile" => null,
                            "home_phone" => null,
                            "office_phone" => null,
                            "alternate_email" => null,
                            "primary_mobile_provider" => null,
                            "alternate_mobile_provider" => null,
                        ],
                        [
                            "external_id" => "256035",
                            "firstname" => "Emilia",
                            "lastname" => "Scott",
                            "primary_email" => "MapworksBetaUser00256035@mailinator.com",
                            "address_one" => null,
                            "address_two" => null,
                            "city" => null,
                            "state" => null,
                            "zip" => null,
                            "country" => null,
                            "primary_mobile" => null,
                            "alternate_mobile" => null,
                            "home_phone" => "555-555-6034",
                            "office_phone" => null,
                            "alternate_email" => null,
                            "primary_mobile_provider" => null,
                            "alternate_mobile_provider" => null,
                        ],
                        [
                            "external_id" => "256033",
                            "firstname" => "Gracie",
                            "lastname" => "Wright",
                            "primary_email" => "MapworksBetaUser00256033@mailinator.com",
                            "address_one" => null,
                            "address_two" => null,
                            "city" => null,
                            "state" => null,
                            "zip" => null,
                            "country" => null,
                            "primary_mobile" => null,
                            "alternate_mobile" => null,
                            "home_phone" => "555-555-6032",
                            "office_phone" => null,
                            "alternate_email" => null,
                            "primary_mobile_provider" => null,
                            "alternate_mobile_provider" => null,
                        ]
                    ]
                ],
                // get contact info for  for all there person  for organization id = 184  with offset 0 and number of records 2 . will result in first two records out of 3
                [
                    184, [256046, 256035, 256033], 0, 2,
                    [
                        [
                            "external_id" => "256046",
                            "firstname" => "Valeria",
                            "lastname" => "Phillips",
                            "primary_email" => "MapworksBetaUser00256046@mailinator.com",
                            "address_one" => null,
                            "address_two" => null,
                            "city" => null,
                            "state" => null,
                            "zip" => null,
                            "country" => null,
                            "primary_mobile" => null,
                            "alternate_mobile" => null,
                            "home_phone" => null,
                            "office_phone" => null,
                            "alternate_email" => null,
                            "primary_mobile_provider" => null,
                            "alternate_mobile_provider" => null,
                        ],
                        [
                            "external_id" => "256035",
                            "firstname" => "Emilia",
                            "lastname" => "Scott",
                            "primary_email" => "MapworksBetaUser00256035@mailinator.com",
                            "address_one" => null,
                            "address_two" => null,
                            "city" => null,
                            "state" => null,
                            "zip" => null,
                            "country" => null,
                            "primary_mobile" => null,
                            "alternate_mobile" => null,
                            "home_phone" => "555-555-6034",
                            "office_phone" => null,
                            "alternate_email" => null,
                            "primary_mobile_provider" => null,
                            "alternate_mobile_provider" => null,
                        ]
                    ]
                ],
                // get contact info for  for all there person  for organization id = 184  with offset 1 and number of records 2 . will result in last two records out of 3
                [
                    184, [256046, 256035, 256033], 1, 2,
                    [
                        [
                            "external_id" => "256035",
                            "firstname" => "Emilia",
                            "lastname" => "Scott",
                            "primary_email" => "MapworksBetaUser00256035@mailinator.com",
                            "address_one" => null,
                            "address_two" => null,
                            "city" => null,
                            "state" => null,
                            "zip" => null,
                            "country" => null,
                            "primary_mobile" => null,
                            "alternate_mobile" => null,
                            "home_phone" => "555-555-6034",
                            "office_phone" => null,
                            "alternate_email" => null,
                            "primary_mobile_provider" => null,
                            "alternate_mobile_provider" => null,
                        ],
                        [
                            "external_id" => "256033",
                            "firstname" => "Gracie",
                            "lastname" => "Wright",
                            "primary_email" => "MapworksBetaUser00256033@mailinator.com",
                            "address_one" => null,
                            "address_two" => null,
                            "city" => null,
                            "state" => null,
                            "zip" => null,
                            "country" => null,
                            "primary_mobile" => null,
                            "alternate_mobile" => null,
                            "home_phone" => "555-555-6032",
                            "office_phone" => null,
                            "alternate_email" => null,
                            "primary_mobile_provider" => null,
                            "alternate_mobile_provider" => null,
                        ]
                    ]
                ],
                // get contact info for  for all there person  for organization id = 184  with offset 2 and number of records 2 . will result in last  record out of 3
                [
                    184, [256046, 256035, 256033], 2, 1,
                    [
                        [
                            "external_id" => "256033",
                            "firstname" => "Gracie",
                            "lastname" => "Wright",
                            "primary_email" => "MapworksBetaUser00256033@mailinator.com",
                            "address_one" => null,
                            "address_two" => null,
                            "city" => null,
                            "state" => null,
                            "zip" => null,
                            "country" => null,
                            "primary_mobile" => null,
                            "alternate_mobile" => null,
                            "home_phone" => "555-555-6032",
                            "office_phone" => null,
                            "alternate_email" => null,
                            "primary_mobile_provider" => null,
                            "alternate_mobile_provider" => null,
                        ]
                    ]
                ],
                // valid students for organization id = 183, but organizationid passed is 141, would return empty array
                [141, [256046, 256035, 256033], null, null, []]
            ]
        ]);
    }

    private function getContacts($primaryEmail, $homePhone)
    {
        $contacts = new \Synapse\CoreBundle\Entity\ContactInfo();
        $contacts->setAddress1(null);
        $contacts->setAlternateEmail(null);
        $contacts->setPrimaryEmail($primaryEmail);
        $contacts->setHomePhone($homePhone);
        $personContact[] = $contacts;
        return $personContact;

    }
}