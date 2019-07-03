<?php

namespace Synapse\CoreBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\NoResultException;

use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Repository\RepositoryResolver;

class UserProvider implements UserProviderInterface
{

    private $personRepository;

  

    public function __construct(RepositoryResolver $repositoryResolver)
    {
        $this->personRepository = $repositoryResolver->getRepository("SynapseCoreBundle:Person");
       
    }

    public function loadUserByUsername($username)
    {
        try {
            $user = $this->personRepository->findOneBy([
                'username' => $username
            ]);
           
        } catch (NoResultException $e) {
            $message = sprintf('Unable to find an active admin SynapseCoreBundle:Person object identified by "%s".', $username);
            throw new UsernameNotFoundException($message, 0, $e);
        }
        
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->personRepository->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->personRepository->getClassName() === $class
        || is_subclass_of($class, $this->personRepository->getClassName());
    }
}