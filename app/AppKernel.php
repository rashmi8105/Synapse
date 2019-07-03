<?php
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\AopBundle\JMSAopBundle(),
            new Synapse\CoreBundle\SynapseCoreBundle(),
            new Synapse\RestBundle\SynapseRestBundle(),
            new Synapse\UploadBundle\SynapseUploadBundle(),
            // new PHPExperts\DoctrineDetectiveBundle\DoctrineDetectiveBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new BCC\ResqueBundle\BCCResqueBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Synapse\GatewayBundle\SynapseGatewayBundle(),
            new Synapse\SearchBundle\SynapseSearchBundle(),
            new Synapse\AcademicBundle\SynapseAcademicBundle(),
            new Synapse\DataBundle\SynapseDataBundle(),
            new Synapse\AcademicUpdateBundle\SynapseAcademicUpdateBundle(),
            new Synapse\SurveyBundle\SynapseSurveyBundle(),
            new Synapse\HelpBundle\SynapseHelpBundle(),
			new Synapse\CampusResourceBundle\SynapseCampusResourceBundle(),
            new Synapse\StorageBundle\SynapseStorageBundle(),
            new Synapse\MultiCampusBundle\SynapseMultiCampusBundle(),
            new Synapse\RiskBundle\SynapseRiskBundle(),
            new Synapse\StaticListBundle\SynapseStaticListBundle(),
            new Synapse\StudentViewBundle\SynapseStudentViewBundle(),
            new Synapse\AuditTrailBundle\SynapseAuditTrailBundle(),
            new Synapse\CampusConnectionBundle\SynapseCampusConnectionBundle(),
            new Synapse\AuthenticationBundle\SynapseAuthenticationBundle(),
            new Synapse\CalendarBundle\SynapseCalendarBundle(),
            new Synapse\PdfBundle\SynapsePdfBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Synapse\StudentBulkActionsBundle\SynapseStudentBulkActionsBundle(),
            new Synapse\ReportsBundle\SynapseReportsBundle(),
            new Snc\RedisBundle\SncRedisBundle(),
            new Synapse\PersonBundle\SynapsePersonBundle(),
            new Synapse\MapworksToolBundle\SynapseMapworksToolBundle(),
            new Synapse\GroupBundle\SynapseGroupBundle(),
            new Synapse\JobBundle\SynapseJobBundle()
        );

        if (in_array($this->getEnvironment(), array(
            'dev',
            'test'
        ))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Bazinga\Bundle\FakerBundle\BazingaFakerBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
