<?php


namespace Synapse\MapworksToolBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use Flow\Exception;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\PhantomJsException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\PdfBundle\Service\Impl\PdfDetailsService;

class PdfDetailsServiceTest extends Unit
{
    use Specify;

    // Scaffolding

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    // Repositories
    /**
     * @var string
     */
    private $kernalRootDir = '';
    /**
     * @var string
     */
    private $pdfStorageLocation = '/tmp/sample.pdf';
    /**
     * @var int
     */
    private $pdfZoom = 1;

    public function _before()
    {
        $this->container = $this->getMock('container', ['get', 'getParameter']);
        $this->logger = $this->getMock('logger', ['debug', 'error', 'addCritical']);
        $this->repositoryResolver = $this->getMock('repositoryResolver', ['getRepository']);
    }

    private function validateConfigKeys($configKeys = [])
    {
        if (!in_array('PHANTOM_JS_PATH', $configKeys)) {
            throw new SynapseValidationException('Phantom JS path does not exist.');
        } else if (!in_array('PDFIFY_JS', $configKeys)) {
            throw new SynapseValidationException('PDFIFY JS path does not exist.');
        } else if (!in_array('PDF_INVERSE', $configKeys)) {
            throw new SynapseValidationException('PDF Inverse does not exist.');
        } else if (!in_array('PDF_DPI', $configKeys)) {
            throw  new SynapseValidationException('PDF DPI value does not exist.');
        }
    }

    public function testGeneratePDFUsingPhantomJS()
    {
        $this->specify('test method generatePDFUsingPhantomJS', function ($configKeys, $urlToBeGeneratedIntoPDF, $expectedResult) {
            try {
                $this->container->method('getParameter')->with(SynapseConstant::KERNEL_ROOT_DIRECTORY)->willReturn($this->kernalRootDir);
                $mockEbiConfigRepository = $this->getMock("EbiConfigRepository", ["findOneBy"]);

                $this->validateConfigKeys($configKeys);
                $mockEbiConfigRepository->method("findOneBy")->withConsecutive([['key' => 'PHANTOM_JS_PATH']], [['key' => 'PDFIFY_JS']], [['key' => 'PDF_INVERSE']], [['key' => 'PDF_DPI']])
                    ->willReturnOnConsecutiveCalls('/usr/local/bin/phantomjs --web-security=false --ssl-protocol=tlsv12', 'pdfify.js', '4A', 72);

                $this->repositoryResolver->method('getRepository')->willReturnMap([
                    [
                        EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ]
                ]);

                $pdfDetailsService = new PdfDetailsService($this->repositoryResolver, $this->logger, $this->container);
                $result = $pdfDetailsService->generatePDFUsingPhantomJS($urlToBeGeneratedIntoPDF, $this->pdfStorageLocation, $this->pdfZoom);
                $this->assertEquals($result, $expectedResult);
            } catch (PhantomJsException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            } catch (SynapseValidationException $validationException) {
                $this->assertEquals($validationException->getMessage(), $expectedResult);
            }
        }, [
            'examples' => [
                // invalid url exception will be thrown
                [
                    ['PDFIFY_JS', 'PDF_INVERSE', 'PDF_DPI'],  // missing key PHANTOM_JS_PATH , validation exception occured,
                    'http://mapworks-qa.skyfactor.com',
                    'Phantom JS path does not exist.' // error message expected
                ],
                [
                    ['PHANTOM_JS_PATH', 'PDF_INVERSE', 'PDF_DPI'], // missing key PDFIFY_JS , validation exception occured,
                    'http://mapworks-qa.skyfactor.com',
                    'PDFIFY JS path does not exist.' // error message expected
                ],
                [
                    ['PHANTOM_JS_PATH', 'PDFIFY_JS', 'PDF_DPI'], // missing key PDF_INVERSE , validation exception occured,
                    'http://mapworks-qa.skyfactor.com',
                    'PDF Inverse does not exist.' // error message expected

                ],
                [
                    ['PHANTOM_JS_PATH', 'PDFIFY_JS', 'PDF_INVERSE'], // missing key PDF_DPI , validation exception occured,
                    'http://mapworks-qa.skyfactor.com',
                    'PDF DPI value does not exist.' // error message expected
                ]
            ]
        ]);
    }
}