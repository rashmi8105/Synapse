<?php

/**
 * Class AccessTokenRepositoryTest
 */

use Codeception\TestCase\Test;

class AccessTokenRepositoryTest extends Test
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
     * @var \Synapse\CoreBundle\Repository\AccessTokenRepository
     */
    private $accessTokenRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->accessTokenRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:AccessToken');
    }

    public function testGetAccessTokenExpireTime()
    {
        $this->specify("Verify the functionality of the method getAccessTokenExpireTime", function ($personId, $accessToken, $expectedValue) {

            $results = $this->accessTokenRepository->getAccessTokenExpireTime($personId, $accessToken);

            verify($results)->notEmpty();
            verify($results['expires_at'])->notEmpty();
            verify($results['expires_at'])->equals($expectedValue);

        }, ["examples" =>
            [
                [4891668, 'Y2NkODg4NWVkN2E1NmE5Njg4MThmNTkwNzQyN2ZmMzljY2NkNGZiMjlhMmQ1ODUxMDEzZWUyYjIxN2Y1YjFkZg', '1459799451'],
                [4891668, 'MTM3MmZiNjdlMGM0ZDI5ZjhmMTIzZTcxNTUxM2NjMzk4OWI0ZDJmZTliODdmODRhY2ExYzQwZDhhNzE4YTQ5Ng', '1459799970']
            ]
        ]);
    }

}