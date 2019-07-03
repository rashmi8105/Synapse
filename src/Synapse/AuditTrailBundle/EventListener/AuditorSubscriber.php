<?php
namespace Synapse\AuditTrailBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Synapse\AuditTrailBundle\Entity\AuditTrail;
use Synapse\AuditTrailBundle\Job\WriteAuditTrail;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class AuditorSubscriber implements EventSubscriber
{

    const REQUEST = 'request';

    /**
     * Symfony Container
     * 
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return [
            'preUpdate'
        ];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        
        // added by dibya
        $request = Request::createFromGlobals();
        $switchUser = $request->headers->get('switch-user');
        if (trim($switchUser) != "") {
            $ebiUser = $this->container->get('ebi_user')->getId();
            $proxyUser = $this->container->get('proxy_user');
            $this->container->get(UploadConstant::SECURITY_CONTEXT)
                ->getToken()
                ->setUser($proxyUser);
        } else {
            $ebiUser = '';
        }
        // added by dibya
        
        $auditEntities = json_decode($this->container->get('ebi_config_service')->get('Audit_Entities'));
        
        if (is_array($auditEntities) && in_array(get_class($entity), $auditEntities)) {
            
            $controller = $this->container->get(self::REQUEST)->attributes->get('_controller');
            $controllerParts = explode('::', $controller);
            
            $person = null;
            
            if ($this->container->get(UploadConstant::SECURITY_CONTEXT)->getToken()) {
                $person = $this->container->get(UploadConstant::SECURITY_CONTEXT)
                    ->getToken()
                    ->getUser()
                    ->getId();
            }
            
            $job = new WriteAuditTrail();
            $job->args = [
                'route' => $this->container->get(self::REQUEST)->get('_route'),
                'class' => $controllerParts[0],
                'method' => $controllerParts[1],
                self::REQUEST => [
                    'GET' => $this->container->get(self::REQUEST)->query->all(),
                    'POST' => $this->container->get(self::REQUEST)->request->all()
                ],
                'unitOfWork' => $args->getEntityChangeSet(),
                'time' => time(),
                'person' => $person,
                'ebiUser' => $ebiUser
            ];
            
            $this->container->get('bcc_resque.resque')->enqueue($job);
        }
    }
}