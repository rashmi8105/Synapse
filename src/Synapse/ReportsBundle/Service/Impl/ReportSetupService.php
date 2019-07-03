<?php
namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\ReportsBundle\EntityDto\SectionDto;
use Synapse\ReportsBundle\Entity\ReportSections;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\ReportsBundle\EntityDto\ReportSectionsDto;
use Synapse\ReportsBundle\Entity\ReportSectionElements;
use Synapse\ReportsBundle\EntityDto\ElementDto;
use Synapse\ReportsBundle\EntityDto\ElementBucketDto;
use Synapse\ReportsBundle\EntityDto\TipsDto;
use Synapse\ReportsBundle\Entity\ReportElementBuckets;
use Synapse\ReportsBundle\Entity\ReportTips;

/**
 * @DI\Service("reportsetup_service")
 */
class ReportSetupService extends AbstractService
{

	const SERVICE_KEY = 'reportsetup_service';

	/**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
    }
	
	public function createReportSection(SectionDto $sectionDto)
	{
		$this->reportSectionRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:ReportSections');
		$this->reportsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);
		$sectionName = $sectionDto->getSectionName();
		$uniqueSection = $this->reportSectionRepository->checkSectionName($sectionName);
		if($uniqueSection)
		{
			$this->isObjectExist(null, ReportsConstants::SECTION_EXIST, ReportsConstants::SECTION_EXIST_KEY);
		} else {
			$sequence = $this->reportSectionRepository->getSequenceOrder();
			$sequence++;
			$reportSection = new ReportSections();
			$reports = $this->reportsRepository->find($sectionDto->getReportId());
			$this->isObjectExist($reports, ReportsConstants::ERROR_REPORT_NOT_FOUND, ReportsConstants::ERROR_KEY_REPORT_NOT_FOUND);
			$reportSection->setReports($reports);
			$reportSection->setTitle($sectionName);
			$reportSection->setSequence($sequence);
			$reportSection = $this->reportSectionRepository->persist($reportSection);
			$sectionDto->setId($reportSection->getId());
			return $sectionDto;	
		}
	}
	
	public function updateSection(SectionDto $sectionDto)
	{
		if(empty($sectionDto->getReorderDirection()))
		{
			$section = $this->editSection($sectionDto);
		} else {
			$section = $this->reorderSectionSequence($sectionDto);
		}
		return $section;
	}
	
	private function editSection($sectionDto)
    {
        $sectionId = $sectionDto->getSectionId();
        $this->logger->info("Edit Section - " . $sectionId);
        $this->reportSectionRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_REPO);
        $this->reportsRepo = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);
        $sectionObj = $this->reportSectionRepository->find($sectionId);
        $reports = $this->reportsRepo->find($sectionDto->getReportId());
        $this->isObjectExist($reports, ReportsConstants::ERROR_REPORT_NOT_FOUND, ReportsConstants::ERROR_KEY_REPORT_NOT_FOUND);
        $this->isObjectExist($sectionObj, ReportsConstants::ERROR_SECTION_NOT_FOUND, ReportsConstants::ERROR_KEY_SECTION_NOT_FOUND);
        $sectionName = $sectionDto->getSectionName();
		$uniqueSection = $this->reportSectionRepository->checkSectionName($sectionName, $sectionId);
		if($uniqueSection)
		{
			$this->isObjectExist(null, ReportsConstants::SECTION_EXIST, ReportsConstants::SECTION_EXIST_KEY);
		} else {		
			$sectionObj->setTitle($sectionName);
			$this->reportSectionRepository->flush();
		}
        $this->logger->info("Section - " . $sectionId . " updated successfully");
        return $sectionDto;
    }
	
	
	public function deleteSection($sectionId)
    {
        $this->sectionRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_REPO);
        $this->sectionElementRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_ELEMENT_REPO);
        $this->sectionElementBucketRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_ELEMENT_BUCKET_REPO);
        $this->tipsRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:ReportTips');
        $this->logger->info("Delete Section - " . $sectionId);
        $sectionObj = $this->sectionRepository->find($sectionId);
        $this->isObjectExist($sectionObj, ReportsConstants::ERROR_SECTION_NOT_FOUND, ReportsConstants::ERROR_KEY_SECTION_NOT_FOUND);
        $this->logger->info("Deleting the tip for that section");
        $tipsObj = $this->tipsRepository->deleteTipsBySectionId($sectionId);
        $sectionElements = $this->sectionElementRepository->findElementIdForSection($sectionId);
        $this->logger->info("Deleting the bucket element based on section element");
        if (count($sectionElements) > 0) {
            foreach ($sectionElements as $sectionElement) {
                $sectionElementsID[] = $sectionElement['id'];
            }
            $this->sectionElementBucketRepository->deleteSectionElementsBucket($sectionElementsID);
        }
        $this->sectionElementRepository->deleteSectionElements($sectionId);
        $this->sectionRepository->remove($sectionObj);
        $this->sectionRepository->flush();
        $this->logger->info("Deleted the Section - " . $sectionId);
    }
	
	
	public function sectionDetails($sectionId, $reportId)
	{
		if(!empty($sectionId))
		{
			$section = $this->getSection($sectionId);
		} else {
			$section = $this->listSection($reportId);
		}
		return $section;
	}
	
	public function elementDetails($elementId, $reportId)
	{
		if(!empty($elementId))
		{
			$elements = $this->getElement($elementId);
		} else {
			$elements = $this->listElements($reportId);
		}
		return $elements;
	}
	
	public function tipDetails($tipId, $reportId)
	{
		if(!empty($tipId))
		{
			$elements = $this->getTip($tipId, $reportId);
		} else {
			$elements = $this->listTips($reportId);
		}
		return $elements;
	}
	
	public function createElements(ElementDto $elementDto)
	{	
		
        $this->sectionRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_REPO);
        $this->sectionElementRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_ELEMENT_REPO);
		$this->sectionElementBucketRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_ELEMENT_BUCKET_REPO);        
		$this->factorRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:Factor');
		$this->surveyQuestionRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:SurveyQuestions');
		$sectionId = $elementDto->getSectionId();
		$sectionObj = $this->sectionRepository->find($sectionId);
        $this->isObjectExist($sectionObj, ReportsConstants::ERROR_SECTION_NOT_FOUND, 'Section');        
        $reportElement = new ReportSectionElements();
		$reportElement->setSectionId($sectionObj);
		$reportElement->setTitle($elementDto->getElementName());		
		if($elementDto->getSourceType() == 'Factor')
		{
			$factor = $this->factorRepository->find($elementDto->getFactorId());
			$this->isObjectExist($factor, "Factor Not Found", "Factor");
			$reportElement->setFactorId($factor);
			$reportElement->setSourceType('F'); 
		} else if($elementDto->getSourceType() == 'QuestionBank'){
			$surveyQuestion = $this->surveyQuestionRepository->find($elementDto->getSurveyQuestionId());
			$this->isObjectExist($surveyQuestion, "Survey Question Not Found", "Survey Question");
			$reportElement->setSurveyQuestionId($surveyQuestion);
			$reportElement->setSourceType('Q'); 
		}        
		$this->sectionElementRepository->createElements($reportElement);					
		$elementsBuckets = $elementDto->getBuckets();				
		if(!empty($elementsBuckets))
		{
			foreach($elementsBuckets as $elementsBucket)
			{					
				$elementBucket = new ReportElementBuckets();
				$elementBucket->setElementId($reportElement);
				$elementBucket->setBucketName($elementsBucket->getBucketName());
				$elementBucket->setBucketText($elementsBucket->getBucketText());
				$elementBucket->setRangeMin($elementsBucket->getRangeMin());
				$elementBucket->setRangeMax($elementsBucket->getRangeMax());
				$this->sectionElementBucketRepository->createBucket($elementBucket);
				
			}
		}	
        $this->sectionElementRepository->flush();
		return $elementDto;
	}
	
	
	public function editElement(ElementDto $elementDto)
    {
        $sectionId = $elementDto->getSectionId();
        $elementId = $elementDto->getElementId();				
        $this->logger->info("Edit Element - " . $elementId);
        $this->sectionRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_REPO);
        $this->sectionElementRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_ELEMENT_REPO);
        $this->sectionElementBucketRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_ELEMENT_BUCKET_REPO);
        $this->reportsRepo = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);
        $reports = $this->reportsRepo->find($elementDto->getReportId());
        $this->isObjectExist($reports, ReportsConstants::ERROR_REPORT_NOT_FOUND, ReportsConstants::ERROR_KEY_REPORT_NOT_FOUND);
        $sectionObj = $this->sectionRepository->find($sectionId);
        $this->isObjectExist($sectionObj, ReportsConstants::ERROR_SECTION_NOT_FOUND, ReportsConstants::ERROR_KEY_SECTION_NOT_FOUND);
        $elementObj = $this->sectionElementRepository->find($elementId);
        $this->isObjectExist($elementObj, ReportsConstants::ERROR_ELEMENT_NOT_FOUND, ReportsConstants::ERROR_KEY_ELEMENT_NOT_FOUND);
        $elementObj->setTitle($elementDto->getElementName());
        if($elementDto->getImageChanges() == true)
        {              
            $imageName = (!empty($elementDto->getElementIcon())) ? $elementDto->getElementIcon() : NULL;            
            $elementObj->setIconFileName($imageName);
        }
        $elementBuckets = $elementDto->getBuckets();
        if(!empty($elementBuckets))
        {
            foreach($elementBuckets as $elementBucket)
            {
                $bucketName = $elementBucket->getBucketName();
                $bucketText = $elementBucket->getBucketText();
                $elementBucketObj = $this->sectionElementBucketRepository->findOneBy([
                                        'elementId' => $elementId,
                                        'bucketName' => $bucketName
                                    ]);                
                if(!empty($elementBucketObj))
                {
                    $elementBucketObj->setBucketText($bucketText);
                } else {
                    $elementBucket = new ReportElementBuckets();
                    $elementBucket->setElementId($elementObj);
                    $elementBucket->setBucketName($bucketName);
                    $elementBucket->setBucketText($bucketText);
                    //$elementBucket->setRangeMin($elementsBucket->getRangeMin());
                    //$elementBucket->setRangeMax($elementsBucket->getRangeMax());
                    $this->sectionElementBucketRepository->createBucket($elementBucket);
                }
            }
            $this->sectionElementRepository->flush();
        }
        $this->logger->info("Element - " . $elementId . " updated successfully");
        return $elementDto;
    }
	
	
	public function deleteElement($elementId)
    {
        $this->sectionElementRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_ELEMENT_REPO);
        $this->sectionElementBucketRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_ELEMENT_BUCKET_REPO);
        $this->logger->info("Delete Element - " . $elementId);
        $elementObj = $this->sectionElementRepository->find($elementId);
        $this->isObjectExist($elementObj, ReportsConstants::ERROR_ELEMENT_NOT_FOUND, ReportsConstants::ERROR_KEY_ELEMENT_NOT_FOUND);
        $this->sectionElementRepository->remove($elementObj);
        $elementBucketObj = $this->sectionElementBucketRepository->findOneBy([
            'elementId' => $elementId
        ]);
        if (! empty($elementBucketObj)) {
            $this->sectionElementBucketRepository->remove($elementBucketObj);
        }
        $this->sectionElementRepository->flush();
        $this->logger->info("Deleted the Element - " . $elementId);
    }
	
	public function editTips(TipsDto $tipsDto)
    {
        $tipsId = $tipsDto->getTipId();
        $this->logger->info("Edit Tips - " . $tipsId);
        $this->reportsRepo = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);
        $this->tipsRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:ReportTips');
        $reports = $this->reportsRepo->find($tipsDto->getReportId());
        $this->isObjectExist($reports, ReportsConstants::ERROR_REPORT_NOT_FOUND, ReportsConstants::ERROR_KEY_REPORT_NOT_FOUND);
        $tipsObj = $this->tipsRepository->find($tipsId);
        $this->isObjectExist($tipsObj, "Tips Not Found", "tips_not_found");
        $tipsObj->setTitle($tipsDto->getTipName());
        $tipsObj->setDescription($tipsDto->getTipText());
        $this->tipsRepository->flush();
        $this->logger->info("Tips - " . $tipsId . " updated successfully");
        return $tipsDto;
    }

    public function deleteTips($tipsId)
    {
        $this->tipsRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:ReportTips');
        $this->logger->info("Delete Tips - " . $tipsId);
        $tipsObj = $this->tipsRepository->find($tipsId);
        $this->isObjectExist($tipsObj, "Tips Not Found", "tips_not_found");
        $this->tipsRepository->remove($tipsObj);
        $this->tipsRepository->flush();
        $this->logger->info("Deleted the Tips - " . $tipsId);
    }
	
	public function createTip(TipsDto $tipsDto)
    {
        $tipsId = $tipsDto->getTipId();
        $this->logger->info("Create Tip");
        $this->reportsRepo = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);
        $this->tipsRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:ReportTips');
		$this->sectionRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_REPO);
		$sectionObj = $this->sectionRepository->find($tipsDto->getSectionId());
		$this->isObjectExist($sectionObj, ReportsConstants::ERROR_SECTION_NOT_FOUND, ReportsConstants::ERROR_KEY_SECTION_NOT_FOUND);
        $tipsObj = new ReportTips();  
		$tipsObj->setSectionId($sectionObj);
        $tipsObj->setTitle($tipsDto->getTipName());
        $tipsObj->setDescription($tipsDto->getTipText());
        $tipsObj->setSequence($tipsDto->getTipOrder());
		$this->tipsRepository->createTip($tipsObj);
        $this->tipsRepository->flush();
        $this->logger->info("Tip created successfully");        
    }
	
	
	private function listElements($reportId)
	{
		$this->sectionElementsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_ELEMENT_REPO);
		$elements = $this->sectionElementsRepository->getSectionElementsReportId($reportId);		
		$sectionDto = new SectionDto();
		$sectionDto->setReportId($reportId);
		$sectionDto->setSectionElementsCount(count($elements));
		$elementsArr = [];
		if(!empty($elements))
		{
			foreach($elements as $element)
			{
				$elementsDto = new ElementDto();
				$elementsDto->setsectionId($element['sectionId']);
				$elementsDto->setSectionName($element['sectionName']);
				$elementsDto->setElementId($element['elementId']);
				$elementsDto->setElementName($element['elementName']);
				$elementsDto->setElementIcon($element['element_icon']);
				$elementsArr[] = $elementsDto;
			}
		}
		$sectionDto->setSectionElements($elementsArr);
		return $sectionDto;
	}
	
	private function listTips($reportId)
	{
		$this->reportTipsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_TIPS_REPO);
		$tips = $this->reportTipsRepository->getSectionTipsByReportId($reportId);		
		$sectionDto = new SectionDto();
		$sectionDto->setReportId($reportId);
		$sectionDto->setTipsCount(count($tips));
		$tipsArr = [];
		if(!empty($tips))
		{
			foreach($tips as $tip)
			{				
				$tipsDto = new TipsDto();
				$tipsDto->setsectionId($tip['sectionId']);
				$tipsDto->setSectionName($tip['sectionName']);
				$tipsDto->setTipId($tip['tipId']);
				$tipsDto->setTipName($tip['tipName']);				
				$tipsArr[] = $tipsDto;
			}
		}
		$sectionDto->setTips($tipsArr);
		return $sectionDto;
	}
	
	private function getElement($elementId)
	{
		$this->sectionElementsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_ELEMENT_REPO);
		$this->elementBucketRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_ELEMENT_BUCKET_REPO);
		$elementObj = $this->sectionElementsRepository->find($elementId);
        $this->isObjectExist($elementObj, ReportsConstants::SECTION_ELEMENT_NOT_FOUND, ReportsConstants::SECTION_ELEMENT_NOT_FOUND_KEY);
		$elementDto = new ElementDto();
		$elementDto->setReportId($elementObj->getSectionId()->getReports()->getId());
		$elementDto->setSectionId($elementObj->getSectionId()->getId());
		$elementDto->setElementName($elementObj->getTitle());
        $elementDto->setElementId($elementId);
		$elementDto->setElementIcon($elementObj->getIconFileName());		
		$buckets = $this->elementBucketRepository->findBy([ 'elementId' => $elementId ]);
		$bucketArr = [];
		if(!empty($buckets))
		{
			foreach($buckets as $bucket)
			{
				$elementBucketDto = new ElementBucketDto();
				$elementBucketDto->setBucketName($bucket->getBucketName());
				$elementBucketDto->setBucketText($bucket->getBucketText());
				$bucketArr[] = $elementBucketDto;
			}			
		}
		$elementDto->setBuckets($bucketArr);
		return $elementDto;
	}
	
	private function getTip($tipId, $reportId)
	{
		$this->reportTipsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_TIPS_REPO);		
		$tipObj = $this->reportTipsRepository->find($tipId);
        $this->isObjectExist($tipObj, ReportsConstants::TIP_NOT_FOUND, ReportsConstants::TIP_NOT_FOUND_KEY);
		$tipsDto = new TipsDto();
		$tipsDto->setReportId($reportId);
		$tipsDto->setSectionId($tipObj->getSectionId()->getId());
		$tipsDto->setSectionName($tipObj->getSectionId()->getTitle());
		$tipsDto->setTipId($tipObj->getId());
		$tipsDto->setTipName($tipObj->getTitle());		
		$tipsDto->setTipText($tipObj->getDescription());
		return $tipsDto;
	}
	
	private function reorderSectionSequence($sectionDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($sectionDto);
        $this->logger->debug(" Reorder section sequence  -  " . $logContent);
        $this->reportSectionRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_REPO);
        $sectionId = $sectionDto->getSectionId();
		$section = $this->reportSectionRepository->find($sectionId);
        $this->isObjectExist($section, ReportsConstants::ERROR_SECTION_NOT_FOUND, ReportsConstants::ERROR_KEY_SECTION_NOT_FOUND);
        $maxSeq = $this->reportSectionRepository->getSequenceOrder();
		if($sectionDto->getReorderDirection() == 'up')
		{
			$newSeq = ($sectionDto->getSectionOrder() <= 1 ) ? 1 : $sectionDto->getSectionOrder() - 1;
		} else {
			$newSeq = ($sectionDto->getSectionOrder() >= $maxSeq ) ? $maxSeq : $sectionDto->getSectionOrder() + 1;			
		}		
        $oldSeq = $section->getSequence();                
        if ($newSeq > $maxSeq) {
            $newSeq = $maxSeq;
        }
		elseif ($oldSeq < $newSeq) {
            for ($i = $oldSeq + 1; $i <= $newSeq; $i ++) {
                $sectionSequence = $this->getSectionSequence($i);
                if ($sectionSequence) {
                    $j = $i - 1;
                    $sectionSequence->setSequence($j);
                }
            }
        }        
		else {
            for ($i = $oldSeq - 1; $i >= $newSeq; $i --) {
                $sectionSequence = $this->getSectionSequence($i);
                if ($sectionSequence) {
                    $j = $i + 1;
                    $sectionSequence->setSequence($j);
                }
            }
        }
        $section->setSequence($newSeq);
        $this->reportSectionRepository->flush();
        return $sectionDto;
    }
	
	private function getSectionSequence($i)
    {
        $section = $this->reportSectionRepository->findOneBy(array(
            'sequence' => $i
        ));
        return $section;
    }
	
	private function getSection($sectionId)
	{
		$this->sectionRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_REPO);
		$sectionObj = $this->sectionRepository->find($sectionId);
        $this->isObjectExist($sectionObj, ReportsConstants::ERROR_SECTION_NOT_FOUND, ReportsConstants::ERROR_KEY_SECTION_NOT_FOUND);
		$sectionDto = new SectionDto();
		$sectionDto->setReportId($sectionObj->getReports()->getId());
		$sectionDto->setSectionId($sectionObj->getId());
		$sectionDto->setSectionName($sectionObj->getTitle());
		$sectionDto->setSectionOrder($sectionObj->getSequence());
		return $sectionDto;		
	}
	
	private function listSection($reportId)
	{
		$this->reportsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);
		$this->sectionRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_REPO);
        $this->sectionElementsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_SECTION_ELEMENT_REPO);
        $this->reportTipsRepository = $this->repositoryResolver->getRepository(ReportsConstants::REPORT_TIPS_REPO);
		$reports = $this->reportsRepository->find($reportId);
        $this->isObjectExist($reports, ReportsConstants::ERROR_REPORT_NOT_FOUND, ReportsConstants::ERROR_KEY_REPORT_NOT_FOUND);
		$sections = $this->sectionRepository->findBy(['reports' => $reportId ]);
		$reportSectionsDto = new ReportSectionsDto();
		$reportSectionsDto->setReportId($reportId);
		$reportSectionsDto->setSectionsCount(count($sections));
		$sectionArr = [];
		if(!empty($sections))
		{			
			foreach($sections as $section)
			{
				$sectionDto = new SectionDto();
                $sectionId = $section->getId();
				$sectionDto->setSectionId($sectionId);
				$sectionDto->setSectionName($section->getTitle());
				$sectionDto->setSectionOrder($section->getSequence());
                $sectionElements = $this->sectionElementsRepository->findBy(['sectionId' => $sectionId ]);
                $sectionTips = $this->reportTipsRepository->findBy(['sectionId' => $sectionId ]);
                $sectionDto->setSectionElementsCount(count($sectionElements));
                $sectionDto->setSectionTipsCount(count($sectionTips));
				$sectionArr[] = $sectionDto;
			}
		}
		$reportSectionsDto->setSections($sectionArr);
		return $reportSectionsDto;
	}
	
	private function isObjectExist($object, $message, $key)
    {
        if (! isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }
}