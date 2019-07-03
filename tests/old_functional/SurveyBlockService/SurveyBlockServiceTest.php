<?php
use Codeception\Util\Stub;
use Synapse\SurveyBundle\EntityDto\SurveyBlockDto;

class SurveyBlockServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\SurveyBundle\Service\Impl\SurveyMarkerService
     */
    private $surveyMarkerService;

    private $langId = 1;

    private $invalidLangId = - 1;

    private $blockId = 7;

    private $invalidBlockId = - 2;

    private $qtype = 'bank';

    private $surveyId = 1;

    private $dataid = 7;
    
    private $delDataid = 8;

    private $qid = 4;
    
    private $delQid = 3;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->surveyBlockService = $this->container->get('surveyblock_service');
    }

    private function createSurveyBlockDto()
    {
        $blockDto = new SurveyBlockDto();
        $blockDto->setLang($this->langId);
        $blockDto->setSurveyBlockName(uniqid("Block_", true));
        
        return $blockDto;
    }

    private function createSurveyBlockDtoWithSameName()
    {
        $blockDto = new SurveyBlockDto();
        $blockDto->setLang($this->langId);
        $blockDto->setSurveyBlockName('Test Block');
        
        return $blockDto;
    }

    public function testCreateSurveyBlockUniqueName()
    {
        $blockDto = $this->createSurveyBlockDto();
        $block = $this->surveyBlockService->createSurveyBlock($blockDto);
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveyBlockDto", $block);
        $this->assertEquals($this->langId, $block->getLang());
        $this->assertNotEmpty($block->getId());
        $this->assertNotEmpty($block->getSurveyBlockName());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateSurveyBlockSameName()
    {
        $blockDto = $this->createSurveyBlockDto();
        $blockDto->setLang($this->langId);
        $blockDto->setSurveyBlockName("Survey Block Test");
        $block = $this->surveyBlockService->createSurveyBlock($blockDto);
        $blockDto->setSurveyBlockName("Survey Block Test");
        $block = $this->surveyBlockService->createSurveyBlock($blockDto);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateSurveyBlockInvalidLang()
    {
        $blockDto = $this->createSurveyBlockDto();
        $blockDto->setLang($this->invalidLangId);
        $block = $this->surveyBlockService->createSurveyBlock($blockDto);
    }

    public function testGetSurveyBlocks()
    {
        $blockDto = $this->createSurveyBlockDto();
        $block = $this->surveyBlockService->createSurveyBlock($blockDto);
        $blockList = $this->surveyBlockService->getSurveyBlocks();
        $this->assertInternalType('array', $blockList);
        $this->assertNotEmpty($block->getSurveyBlockName());
    }

    public function testGetAllSurveys()
    {
        $surveyList = $this->surveyBlockService->getAllSurveys();
        $this->assertInstanceOf("Synapse\RestBundle\Entity\SurveyDto", $surveyList);
    }

    public function testGetSurveyBlockDetails()
    {
        $block = $this->surveyBlockService->getSurveyBlockDetails($this->blockId);
        $this->assertEquals($this->blockId, $block->getId());
        $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\SurveyBlockDetailsResponseDto", $block);
        $this->assertEquals("", $block->getSurveyBlockName());
        $this->assertInternalType('array', $block->getBlockData());
        foreach ($block->getBlockData() as $data) {
            $this->assertNotEmpty($data->getId());
            $this->assertNotEmpty($data->getType());
            $this->assertNotEmpty($data->getText());
        }
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetSurveyBlockDetailsInvalid()
    {
        $block = $this->surveyBlockService->getSurveyBlockDetails($this->invalidBlockId);
    }

    public function testDeleteSurveyBlock()
    {
        $block = $this->surveyBlockService->deleteSurveyBlock($this->blockId);
        $this->assertEquals($this->blockId, $block);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteSurveyBlockInvalid()
    {
        $block = $this->surveyBlockService->deleteSurveyBlock($this->invalidBlockId);
    }

    public function testGetDataForBlocks()
    {
        $blocksData = $this->surveyBlockService->getDataForBlocks($this->qtype, $this->surveyId);
        $this->assertEquals($this->qtype, $blocksData['type']);
        $this->assertEquals("First question", $blocksData['questions'][0]->getText());
        $this->assertInternalType('array', $blocksData);
    }

    public function testDeleteSurveyBlockQuestion()
    {
        $block = $this->surveyBlockService->deleteSurveyBlockQuestion($this->delDataid, $this->delQid);
        $this->assertEquals($this->delQid, $block);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteSurveyBlockQuestionInvalid()
    {
        $block = $this->surveyBlockService->deleteSurveyBlockQuestion($this->invalidBlockId, $this->qid);
    }

    public function testEditSurveyBlock()
    {
        $blockDto = $this->createSurveyBlockDto();
        $block = $this->surveyBlockService->createSurveyBlock($blockDto);
        $blockDto = $this->createSurveyBlockDto();
        $blockDto->setId($block->getId());
        $blockDto->setSurveyBlockName('Edit survey block test');
        $editBlock = $this->surveyBlockService->editSurveyBlock($blockDto);
        $this->assertEquals($blockDto->getId(), $editBlock->getId());
        $this->assertEquals($this->langId, $editBlock->getLang());
        $this->assertNotEmpty($editBlock->getSurveyBlockName());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditSurveyBlockSameName()
    {
        $blockDto = $this->createSurveyBlockDtoWithSameName();
        $block = $this->surveyBlockService->createSurveyBlock($blockDto);
        $block = $this->surveyBlockService->createSurveyBlock($blockDto);
        $editBlock = $this->surveyBlockService->editSurveyBlock($block);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditSurveyBlockInvalidLang()
    {
        $blockDto = $this->createSurveyBlockDtoWithSameName();
        $block = $this->surveyBlockService->createSurveyBlock($blockDto);
        $block->setLang($this->invalidLangId);
        $editBlock = $this->surveyBlockService->editSurveyBlock($block);
    }
}