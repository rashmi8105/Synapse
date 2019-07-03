<?php

namespace Synapse\PdfBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Symfony\Component\Process\Process;
use Synapse\CoreBundle\Exception\PhantomJsException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EbiMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgMetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\OrgProfileService;
use Synapse\CoreBundle\Service\Impl\ProfileService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\DataBundle\DAO\InformationSchemaDAO;
use Synapse\PdfBundle\Repository\EbiTemplateLangRepository;
use Synapse\UploadBundle\Repository\UploadColumnHeaderDownloadMapRepository;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;


/**
 * @DI\Service("pdf_service")
 */
class PdfDetailsService extends AbstractService
{

    const SERVICE_KEY = 'pdf_service';

    // Member Variables

    const STUDENT_SURVEY_REPORT_MIN_VALID_PDF_FILE_SIZE = 153600; //150 Kilobytes

    const EBI_PROFILE_TEXT_LENGTH = 255;

    const ORG_PROFILE_TEXT_LENGTH = 1024;

    const NUMBER_DEFAULT_MIN_VALUE = 0;

    const CREDIT_HOURS_MAX_VALUE = 40;

    const STRING_DEFAULT_LENGTH = 45;

    const TEXT_TYPE_DEFAULT_LENGTH = 100;

    const DEFAULT_LENGTH = 10;

    const ABSENCE_MAX_VALUE = 99;

    const RISK_GROUP_ID_LENGTH = 1;

    private $failureRiskLevel = [
        'Low',
        'High'
    ];

    private $grade = [
        'A',
        'B',
        'C',
        'D',
        'F/No Pass',
        'Pass'
    ];

    private $finalGrade = [
        'A',
        'A-',
        'B+',
        'B',
        'B-',
        'C+',
        'C',
        'C-',
        'D+',
        'D',
        'D-',
        'F/No Pass',
        'Pass',
        'Withdraw',
        'Incomplete',
        'In Progress',
        'Not for Credit'
    ];

    private $sendToStudent = [
        'No',
        'Yes'
    ];

    private $calendarAssignment = array(
        'Y' => "Year",
        'T' => "Term",
        'N' => "None"

    );

    private $receiveSurveyColumnArray = [
        'TransitionOneReceiveSurvey',
        'CheckupOneReceiveSurvey',
        'TransitionTwoReceiveSurvey',
        'CheckupTwoReceiveSurvey'
    ];


    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    protected $rbacManager;


    // Services
    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var OrgProfileService
     */
    private $orgProfileService;

    /**
     * @var ProfileService
     */
    private $profileService;

    // DAO
    /**
     * @var InformationSchemaDAO
     */
    private $informationSchemaDAO;


    // Repositories
    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EbiMetadataListValuesRepository
     */
    private $ebiMetaDataListValuesRepository;

    /**
     * @var EbiTemplateLangRepository
     */
    private $ebiTemplateLangRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var OrgMetadataListValuesRepository
     */
    private $orgMetaDataListValuesRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionSetRepository;

    /**
     * @var SurveyLangRepository
     */
    private $surveyLangRepository;

    /**
     * @var UploadColumnHeaderDownloadMapRepository
     */
    private $uploadColumnHeaderDownloadMapRepository;



    /**
     * PdfDetailsService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        //Services
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->orgProfileService = $this->container->get(OrgProfileService::SERVICE_KEY);
        $this->profileService = $this->container->get(ProfileService::SERVICE_KEY);

        // DAO
        $this->informationSchemaDAO = $this->container->get(InformationSchemaDAO::DAO_KEY);

        //Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->ebiMetaDataListValuesRepository = $this->repositoryResolver->getRepository(EbiMetadataListValuesRepository::REPOSITORY_KEY);
        $this->ebiTemplateLangRepository = $this->repositoryResolver->getRepository(EbiTemplateLangRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->orgMetaDataListValuesRepository = $this->repositoryResolver->getRepository(OrgMetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgPermissionSetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->surveyLangRepository = $this->repositoryResolver->getRepository(SurveyLangRepository::REPOSITORY_KEY);
        $this->uploadColumnHeaderDownloadMapRepository = $this->repositoryResolver->getRepository(UploadColumnHeaderDownloadMapRepository::REPOSITORY_KEY);

    }

    /**
     * Returns the Faculty Upload Data Definitions Details in HTML format
     *
     * @return string $htmlTemplate
     */
    public function getFacultyUploadPdfDetails()
    {
        $this->logger->info(" Get Faculty Upload Pdf Details ");
        // Person and Contact Info Pdf Details.
        $personUploadPdfDetails = $this->getPersonAndContactDetailsForPdf();

        // Person Status (isActive) Field Details.
        $statusArray = [
            'fieldName' => "IsActive",
            'value_list' => '<li>0 (Is Not Active)</li><li>1 (Is Active)</li>',
            "data_type" => 'Category',
            'scale' => 0,
            'length' => 1,
            'unique' => '',
            'nullable' => 1,
            'precision' => 0,
            "column_name" => 'IsActive'
        ];
        $statusArray["required"] = '';
        $statusArray["description"] = '';
        $statusArray['valid_values'] = $this->getNumberTypeValidValues($statusArray);

        $personUploadPdfDetails[] = $statusArray;
        $headerKey = 'Pdf_Faculty_Header_Template';
        $footerKey = 'Pdf_Student_Footer_Template';


        $headersTemplate = $this->uploadColumnHeaderDownloadMapRepository->getUploadHeaders('faculty', 'data_definition_file', 'upload_column_display_name');
        $personUploadPdfDetails = $this->dataProcessingUtilityService->sortBasedOnSortKey($personUploadPdfDetails, $headersTemplate, 'column_name');

        $htmlTemplate = $this->getHtmlFromTemplate($personUploadPdfDetails, $headerKey, $footerKey);
        return $htmlTemplate;
    }

    /**
     * Returns the Student Upload Data Definitions Details in HTML format
     *
     * @param int $organizationId
     * @return string
     */
    public function getStudentUploadPdfDetails($organizationId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);
        $this->logger->debug(" Get Student Upload Pdf Details for Organization Id " . $organizationId);

        $personUploadPdfDetails = $this->getPersonAndContactDetailsForPdf(true);
        $profileItems = $this->profileService->getProfiles('active');

        $orgProfileItems = $this->orgProfileService->getInstitutionSpecificProfileBlockItems($organizationId, false, 'active', false);

        // Merged ebi specific profile items and organization specific profile items to reduce for loop.
        $ebiAndOrganizationProfileItemDetails = array_merge($profileItems['profile_items'], $orgProfileItems['profile_items']);

        $profileItemsDetailsArray = [];
        foreach ($ebiAndOrganizationProfileItemDetails as $ebiAndOrganizationProfileItemDetail) {

            $pdfContentArray = [];
            $pdfContentArray["column_name"] = $ebiAndOrganizationProfileItemDetail["item_label"];
            $profileItemDescription = (!empty($ebiAndOrganizationProfileItemDetail["item_subtext"])) ? $ebiAndOrganizationProfileItemDetail["item_subtext"] : '';
            $pdfContentArray["description"] = $profileItemDescription;
            $pdfContentArray["required"] = '';
            $pdfContentArray["length"] = '';

            // Calender assignment for organization specific profile items.
            $calendarAssignment = '';
            if ($ebiAndOrganizationProfileItemDetail["definition_type"] == 'O') {
                if (isset($orgProfile['calendar_assignment']) && trim($orgProfile['calendar_assignment']) != "") {
                    $calendarAssignment = " <br/><br/>Calendar Assignment : <br/>" . $this->calendarAssignment[$orgProfile['calendar_assignment']];
                }
            }
            $pdfContentArray['additionalData'] = $calendarAssignment;

            switch ($ebiAndOrganizationProfileItemDetail["item_data_type"]) {
                case "D":
                    $pdfContentArray["data_type"] = 'Date';
                    break;

                case "T":
                    $pdfContentArray["data_type"] = 'String';
                    ($ebiAndOrganizationProfileItemDetail["definition_type"] == 'E') ? $pdfContentArray["length"] = self::EBI_PROFILE_TEXT_LENGTH : $pdfContentArray["length"] = self::ORG_PROFILE_TEXT_LENGTH;
                    break;

                case "N":
                    $pdfContentArray["data_type"] = 'Number';
                    $pdfContentArray = $this->getMinAndMaxValuesForNumericProfileItem($ebiAndOrganizationProfileItemDetail, $pdfContentArray);
                    break;

                case "S":
                    $pdfContentArray["data_type"] = 'Category';
                    $pdfContentArray['value_list'] = ($ebiAndOrganizationProfileItemDetail["definition_type"] == 'E') ? $this->getProfilesCategoryValues('E', $ebiAndOrganizationProfileItemDetail) : $this->getProfilesCategoryValues('O', $ebiAndOrganizationProfileItemDetail);
                    break;

                default:
                    $pdfContentArray["data_type"] = '';
                    $pdfContentArray["column_name"] = '';
                    $pdfContentArray["description"] = '';
                    break;


            }
            $profileItemsDetailsArray[] = $pdfContentArray;
        }
        // Student details including IsActive, SurveyCohort, StudentPhoto, PrimaryCampusConnection column Details.
        $orgStudentDetails = $this->getStudentPhotoAndStatusDetailsForPdf();

        // Get Student Receive Survey Details
        $receiveSurveyDetails = $this->getStudentReceiveSurveyDetails();
        $yearDetails = $this->getAcademicYearDetails();
        $termDetails = $this->getAcademicTermDetails();
        $yearDetails[] = $termDetails[0];

        $uploadDetails = array_merge($personUploadPdfDetails, $yearDetails, $orgStudentDetails, $receiveSurveyDetails, $profileItemsDetailsArray);

        $headerKey = 'Pdf_Student_Header_Template';
        $footerKey = 'Pdf_Student_Footer_Template';

        $headersTemplate = $this->uploadColumnHeaderDownloadMapRepository->getUploadHeaders('student', 'data_definition_file', 'upload_column_display_name');
        $uploadDetails = $this->dataProcessingUtilityService->sortBasedOnSortKey($uploadDetails, $headersTemplate, 'column_name');

        $htmlTemplate = $this->getHtmlFromTemplate($uploadDetails, $headerKey, $footerKey);
        $this->logger->info(" Get Student Upload Pdf Details for Organization Id");
        return $htmlTemplate;
    }



    /**
     * Returns the Course Upload Data Definitions Details in HTML format
     *
     * @return array
     */
    public function getCourseUploadPdfDetails()
    {
        $this->logger->debug(" Get Course Upload Pdf Details");

        // Course related column details for PDF.
        $courseIncludedColumnsArray = ["college_code", "dept_code", "subject_code", "course_number", "course_name", "section_number", "days_times", "location", "credit_hours", "externalId"];
        $courseIncludedColumnsDetailsArray = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('org_courses', $courseIncludedColumnsArray, false);

        $optionalCourseFields = ["location"];

        $coursePdfDetails = [];
        foreach ($courseIncludedColumnsDetailsArray as $courseIncludedColumnsDetails) {
            $pdfContentArray = [];
            $pdfContentArray["length"] = "";
            $pdfContentArray["description"] = "";
            $pdfContentArray["required"] = "";
            switch ($courseIncludedColumnsDetails["columnName"]) {
                case "college_code":
                case "dept_code":
                case "subject_code":
                case "course_number":
                case "course_name":
                case "section_number":
                case "location":
                    $pdfContentArray["column_name"] = "";
                    $fieldNameArray = preg_split('/(?=[_])/', $courseIncludedColumnsDetails["columnName"]);
                    foreach ($fieldNameArray as $fieldNamePart) {
                        $pdfContentArray["column_name"] .= ucfirst(str_replace("_", "", $fieldNamePart));
                    }
                    $pdfContentArray["data_type"] = "string";
                    $pdfContentArray["required"] = (!in_array($courseIncludedColumnsDetails["columnName"], $optionalCourseFields)) ? "(Required)" : "";
                    $pdfContentArray["length"] = $courseIncludedColumnsDetails["length"];
                    break;

                case "credit_hours":
                    $pdfContentArray["column_name"] = "CreditHours";
                    $pdfContentArray["data_type"] = "Number";
                    $pdfContentArray["min_value"] = self::NUMBER_DEFAULT_MIN_VALUE;
                    $pdfContentArray["max_value"] = self::CREDIT_HOURS_MAX_VALUE;
                    $pdfContentArray["valid_values"] = $this->getNumberTypeValidValues($pdfContentArray);
                    break;

                case "days_times":
                    $pdfContentArray["column_name"] = "Days/Times";
                    $pdfContentArray["data_type"] = "string";
                    $pdfContentArray["length"] = self::STRING_DEFAULT_LENGTH;
                    break;

                case "externalId":
                    $pdfContentArray["column_name"] = "UniqueCourseSectionId";
                    $pdfContentArray["data_type"] = "string";
                    $pdfContentArray["length"] = $courseIncludedColumnsDetails["length"];
                    $pdfContentArray["required"] = "(Required)";
                    break;

                default:
                    $pdfContentArray["column_name"] = "";
                    $pdfContentArray["data_type"] = "";
                    break;
            }
            $coursePdfDetails[] = $pdfContentArray;

        }
        $yearDetails = $this->getAcademicYearDetails(true);
        $termDetails = $this->getAcademicTermDetails(true);
        $uploadDetails = array_merge($yearDetails, $termDetails, $coursePdfDetails);
        $headerKey = 'Pdf_Courses_Header_Template';
        $footerKey = 'Pdf_Course_Footer_Template';
        $htmlTemplate = $this->getHtmlFromTemplate($uploadDetails, $headerKey, $footerKey);
        return $htmlTemplate;
    }

    /**
     * Returns the Course Faculty Upload Data Definitions Details in HTML format
     *
     * @return string
     */
    public function getCourseFacultyUploadPdfDetails()
    {
        $this->logger->info(" Get Faculty Upload Pdf Details for Organization Id");

        // Course related column details for PDF.
        $courseIncludedColumnsArray = ["externalId"];
        $courseIncludedColumnsDetailsArray = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('org_courses', $courseIncludedColumnsArray);

        $courseFacultyPdfDetails = [];
        foreach ($courseIncludedColumnsDetailsArray as $courseIncludedColumnsDetails) {
            $pdfContentArray = [];
            $pdfContentArray["column_name"] = "UniqueCourseSectionId";
            $pdfContentArray["data_type"] = "string";
            $pdfContentArray["length"] = $courseIncludedColumnsDetails["length"];
            $pdfContentArray["required"] = "(Required)";
            $pdfContentArray["description"] = "";
            $courseFacultyPdfDetails[] = $pdfContentArray;
        }

        // Course Faculty Id column Details
        $courseFacultyIdPdfDetails = [];
        $courseFacultyIdPdfDetails["column_name"] = 'FacultyID';
        $courseFacultyIdPdfDetails["data_type"] = "string";
        $courseFacultyIdPdfDetails["length"] = self::STRING_DEFAULT_LENGTH;
        $courseFacultyIdPdfDetails["required"] = '(Required)';
        $courseFacultyIdPdfDetails["description"] = '';
        $courseFacultyPdfDetails[] = $courseFacultyIdPdfDetails;

        // Course Faculty Permission column Details
        $courseFacultyPermissionPdfDetails = [];
        $courseFacultyPermissionPdfDetails["column_name"] = 'PermissionSet';
        $courseFacultyPermissionPdfDetails["data_type"] = "string";
        $courseFacultyPermissionPdfDetails["length"] = self::TEXT_TYPE_DEFAULT_LENGTH;
        $courseFacultyPermissionPdfDetails["required"] = '(Required)';
        $courseFacultyPermissionPdfDetails["description"] = '';

        $courseFacultyPdfDetails[] = $courseFacultyPermissionPdfDetails;

        // Course Faculty Remove column Details
        $courseFacultyRemovePdfDetails = [];
        $removeColumnName = $this->ebiConfigRepository->findOneByKey('Course_Upload_Remove_Definition_ColumnName');
        $removeColumnType = $this->ebiConfigRepository->findOneByKey('Course_Upload_Remove_Definition_Type');
        $removeColumnDesc = $this->ebiConfigRepository->findOneByKey('Course_Upload_Remove_Definition_Desc');
        $courseFacultyRemovePdfDetails["column_name"] = $removeColumnName->getValue();
        $courseFacultyRemovePdfDetails["data_type"] = $removeColumnType->getValue();
        $courseFacultyRemovePdfDetails["description"] = $removeColumnDesc->getValue();
        $courseFacultyRemovePdfDetails["required"] = '';
        $courseFacultyRemovePdfDetails["length"] = self::DEFAULT_LENGTH;

        $courseFacultyPdfDetails[] = $courseFacultyRemovePdfDetails;

        $headerKey = 'Pdf_CoursesFaculty_Header_Template';
        $footerKey = 'Pdf_Course_Footer_Template';
        $htmlTemplate = $this->getHtmlFromTemplate($courseFacultyPdfDetails, $headerKey, $footerKey);
        return $htmlTemplate;
    }

    /**
     * Returns the Course Student Upload
     *Data Definitions Details in HTML format
     * @return array
     */
    public function getCourseStudentsUploadPdfDetails()
    {
        $this->logger->info(" Get Course Students Upload Pdf Details");

        // Course related column details for PDF.
        $courseIncludedColumnsArray = ["externalId"];
        $courseIncludedColumnsDetailsArray = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('org_courses', $courseIncludedColumnsArray);

        $courseStudentPdfDetails = [];
        foreach ($courseIncludedColumnsDetailsArray as $courseIncludedColumnsDetails) {
            $pdfContentArray = [];
            $pdfContentArray["column_name"] = "UniqueCourseSectionId";
            $pdfContentArray["data_type"] = "string";
            $pdfContentArray["length"] = $courseIncludedColumnsDetails["length"];
            $pdfContentArray["required"] = "(Required)";
            $pdfContentArray["description"] = "";
            $courseStudentPdfDetails[] = $pdfContentArray;
        }

        // Course Student Id Column Details.
        $courseStudentIdPdfDetails = [];
        $courseStudentIdPdfDetails["column_name"] = 'StudentId';
        $courseStudentIdPdfDetails["data_type"] = "string";
        $courseStudentIdPdfDetails["length"] = self::STRING_DEFAULT_LENGTH;
        $courseStudentIdPdfDetails["required"] = '(Required)';
        $courseStudentIdPdfDetails["description"] = '';

        // Course Student Remove Column Details.
        $courseStudentRemovePdfDetails = [];
        $removeColName = $this->ebiConfigRepository->findOneByKey('Course_Upload_Remove_Definition_ColumnName');
        $removeColType = $this->ebiConfigRepository->findOneByKey('Course_Upload_Remove_Definition_Type');
        $removeColDesc = $this->ebiConfigRepository->findOneByKey('Course_Upload_Remove_Definition_Desc');
        $courseStudentRemovePdfDetails["column_name"] = $removeColName->getValue();
        $courseStudentRemovePdfDetails["data_type"] = $removeColType->getValue();
        $courseStudentRemovePdfDetails["description"] = $removeColDesc->getValue();
        $courseStudentRemovePdfDetails["length"] = self::DEFAULT_LENGTH;
        $courseStudentRemovePdfDetails["required"] = '';
        $courseStudentPdfDetails[] = $courseStudentIdPdfDetails;
        $courseStudentPdfDetails[] = $courseStudentRemovePdfDetails;

        $headerKey = 'Pdf_CoursesStudents_Header_Template';
        $footerKey = 'Pdf_CourseStudent_Footer_Template';
        $htmlTemplate = $this->getHtmlFromTemplate($courseStudentPdfDetails, $headerKey, $footerKey);
        return $htmlTemplate;
    }

    /**
     * Returns the Academic Update Upload Data Definitions Details in HTML format
     *
     * @return string
     */
    public function getAcademicUpdateUploadPdfDetails()
    {
        $this->logger->info(" Get Academic Update Upload Pdf Details");

        // Course related column details for PDF.
        $courseIncludedColumnsArray = ["externalId"];
        $courseIncludedColumnsDetailsArray = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('org_courses', $courseIncludedColumnsArray);

        // Academic Update Pdf Column Details.
        $academicUpdateIncludedColumnsArray = ["id", "failure_risk_level", "grade", "absence", "comment", "send_to_student", "final_grade"];
        $academicUpdateIncludedColumnsDetailsArray = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('academic_update', $academicUpdateIncludedColumnsArray, false);

        $academicUpdateColumnDetailsArray = array_merge($courseIncludedColumnsDetailsArray, $academicUpdateIncludedColumnsDetailsArray);

        $academicUpdatePdfDetails = [];
        foreach ($academicUpdateColumnDetailsArray as $academicUpdateColumnDetails) {
            $pdfContentArray = [];
            $pdfContentArray["required"] = "";
            $pdfContentArray["description"] = "";
            switch ($academicUpdateColumnDetails["columnName"]) {
                case "externalId":
                    $pdfContentArray["column_name"] = "UniqueCourseSectionId";
                    $pdfContentArray["data_type"] = "string";
                    $pdfContentArray["length"] = $academicUpdateColumnDetails["length"];
                    $pdfContentArray["required"] = "(Required)";
                    break;

                case "id":
                    $pdfContentArray["column_name"] = "StudentId";
                    $pdfContentArray["data_type"] = "string";
                    $pdfContentArray["length"] = self::STRING_DEFAULT_LENGTH;
                    $pdfContentArray["required"] = "(Required)";
                    break;

                case "failure_risk_level":
                case "grade":
                case "final_grade":
                case "send_to_student":
                    $pdfContentArray["column_name"] = $this->getAcademicUpdateColumnName($academicUpdateColumnDetails["columnName"]);
                    $pdfContentArray["data_type"] = "Category";
                    $pdfContentArray["value_list"] = $this->getAcademicUpdateCategoryValuesByColumnName($academicUpdateColumnDetails["columnName"]);
                    break;

                case "absence":
                    $pdfContentArray["column_name"] = "Absences";
                    $pdfContentArray["data_type"] = "integer";
                    $pdfContentArray["min_value"] = self::NUMBER_DEFAULT_MIN_VALUE;
                    $pdfContentArray["max_value"] = self::ABSENCE_MAX_VALUE;
                    $pdfContentArray["valid_values"] = $this->getNumberTypeValidValues($pdfContentArray);
                    break;

                case "comment":
                    $pdfContentArray["column_name"] = "Comment";
                    $pdfContentArray["data_type"] = "string";
                    $pdfContentArray["length"] = $academicUpdateColumnDetails["length"];
                    break;

                default:
                    $pdfContentArray["column_name"] = "";
                    $pdfContentArray["data_type"] = "";
                    break;
            }
            $academicUpdatePdfDetails[] = $pdfContentArray;
        }

        $headerKey = 'Pdf_AcademicUpdates_Header_Template';
        $footerKey = 'Pdf_CourseStudent_Footer_Template';
        $htmlTemplate = $this->getHtmlFromTemplate($academicUpdatePdfDetails, $headerKey, $footerKey);
        return $htmlTemplate;
    }

    /**
     * Returns HTML data with OptionalData for Columns.
     *
     * @param array $htmlData
     * @return mixed
     */
    private function addOptionalDataBelowColumnName($htmlData)
    {

        $replaceData = '<span class="boldStyler">$$column_name$$</span>';
        $replaceDataWith = '<span class="boldStyler">$$column_name$$</span>$$additionalData$$';
        $htmlData[0]['body'] = str_replace($replaceData, $replaceDataWith, $htmlData[0]['body']);
        return $htmlData;
    }

    /**
     * Replaces the placeholders in the HTML templates with the corresponding values.
     *
     * @param array $dataDefinitionPdfDetails
     * @param string $headerKey
     * @param string $footerKey
     * @param string $explanatoryNotesKey
     * @return string
     * @throws SynapseValidationException
     */
    private function getHtmlFromTemplate($dataDefinitionPdfDetails, $headerKey, $footerKey, $explanatoryNotesKey = '')
    {
        try {


            $headerTemplate = $this->ebiTemplateLangRepository->getTemplateByKey($headerKey);
            $textTypeBodyTemplate = $this->ebiTemplateLangRepository->getTemplateByKey('Pdf_TextType_Body_Template');
            $dateTypeBodyTemplate = $this->ebiTemplateLangRepository->getTemplateByKey('Pdf_DateType_Body_Template');
            $stringTypeBodyTemplate = $this->ebiTemplateLangRepository->getTemplateByKey('Pdf_StringType_Body_Template');
            $numberTypeBodyTemplate = $this->ebiTemplateLangRepository->getTemplateByKey('Pdf_NumberType_Body_Template');
            $categoryTypeBodyTemplate = $this->ebiTemplateLangRepository->getTemplateByKey('Pdf_CategoryType_Body_Template');
            $pdfHtmlString = $headerTemplate[0]['body'];

            $textTemplate = $this->addOptionalDataBelowColumnName($textTypeBodyTemplate);
            $dateTemplate = $this->addOptionalDataBelowColumnName($dateTypeBodyTemplate);
            $stringTemplate = $this->addOptionalDataBelowColumnName($stringTypeBodyTemplate);
            $numberTemplate = $this->addOptionalDataBelowColumnName($numberTypeBodyTemplate);
            $categoryTemplate = $this->addOptionalDataBelowColumnName($categoryTypeBodyTemplate);

            foreach ($dataDefinitionPdfDetails as $uploadDetail) {

                if (!isset($uploadDetail['optionalTitle'])) {
                    $uploadDetail['optionalTitle'] = '';
                }

                if (!isset($uploadDetail['optional'])) {
                    $uploadDetail['optional'] = '';
                }

                if (!isset($uploadDetail['additionalData'])) {
                    $uploadDetail['additionalData'] = '';
                }

                $dataTypeStringTextArray = ["string", 'text'];
                $columnNameArray = ['facultyid', 'studentid', 'uniquecoursesectionid', "externalId"];
                $dataTypeNumericArray = ['integer', 'number', 'boolean', 'decimal'];

                if (in_array(strtolower($uploadDetail["data_type"]), $dataTypeStringTextArray)) {

                    if (in_array(strtolower($uploadDetail["column_name"]), $columnNameArray)) {
                        $pdfHtmlString .= str_replace("$$", "", strtr($stringTemplate[0]['body'], $uploadDetail));
                    } else {

                        if (isset($uploadDetail["groupName"]) || $uploadDetail['column_name'] == 'FullPathNames' || $uploadDetail['column_name'] == 'FullPathGroupIDs') {
                            $groupStudentTemplate = $this->ebiTemplateLangRepository->getTemplateByKey('Pdf_GroupName_Body_Template');
                            $groupStudentTemplate = $this->addOptionalDataBelowColumnName($groupStudentTemplate);
                            $pdfHtmlString .= str_replace("$$", "", strtr($groupStudentTemplate[0]['body'], $uploadDetail));
                        } else {
                            $pdfHtmlString .= str_replace("$$", "", strtr($textTemplate[0]['body'], $uploadDetail));
                        }
                    }
                } else if (in_array(strtolower($uploadDetail["data_type"]), $dataTypeNumericArray)) {
                    $pdfHtmlString .= str_replace("$$", "", strtr($numberTemplate[0]['body'], $uploadDetail));
                } else if (strtolower($uploadDetail["data_type"]) == 'date') {
                    $pdfHtmlString .= str_replace("$$", "", strtr($dateTemplate[0]['body'], $uploadDetail));
                } else {
                    $pdfHtmlString .= str_replace("$$", "", strtr($categoryTemplate[0]['body'], $uploadDetail));
                }
            }
            if ($explanatoryNotesKey) {
                $explanatoryNotesTemplate = $this->ebiTemplateLangRepository->getTemplateByKey($explanatoryNotesKey);
                $explanatoryNotes = $explanatoryNotesTemplate[0]['body'];
                $pdfHtmlString .= $explanatoryNotes;
            }
            $footerTemplate = $this->ebiTemplateLangRepository->getTemplateByKey($footerKey);
            $footer = $footerTemplate[0]['body'];
            $pdfHtmlString .= $footer;
        } catch (\Exception $e) {
            throw new SynapseValidationException("PDF cannot be generated");
        }
        return $pdfHtmlString;
    }

    /**
     * Get Academic Year Details
     *
     * @param boolean $isRequiredField
     * @return array
     */
    private function getAcademicYearDetails($isRequiredField = false)
    {
        // Academic Year Column Details.
        $orgAcademicYearPdfDetails = [];
        $pdfContentArray = [];
        $pdfContentArray["column_name"] = "YearId";
        $pdfContentArray["data_type"] = "integer";
        $pdfContentArray["description"] = "";
        $pdfContentArray["valid_values"] = "";

        if ($isRequiredField) {
            $pdfContentArray["required"] = "(Required)";
        } else {
            $pdfContentArray["required"] = "";
        }
        $orgAcademicYearPdfDetails[] = $pdfContentArray;
        return $orgAcademicYearPdfDetails;
    }

    /**
     * Get Academic Term Details
     *
     * @param boolean $isRequiredField
     * @return array
     */
    private function getAcademicTermDetails($isRequiredField = false)
    {
        // Academic Year Column Details.
        $orgAcademicTermPdfDetails = [];
        $pdfContentArray = [];
        $pdfContentArray["column_name"] = "TermId";
        $pdfContentArray["data_type"] = "Text";
        $pdfContentArray["length"] = self::DEFAULT_LENGTH;
        $pdfContentArray["description"] = "";

        if ($isRequiredField) {
            $pdfContentArray["required"] = "(Required)";
        } else {
            $pdfContentArray["required"] = "";
        }
        $orgAcademicTermPdfDetails[] = $pdfContentArray;
        return $orgAcademicTermPdfDetails;
    }

    /**
     * Get Student Details for the Student Upload data Definitions
     *
     * @return array
     */
    private function getStudentPhotoAndStatusDetailsForPdf()
    {
        // Get Student Photo and Status Details
        $studentPhotoUrlAndStatusColumns = ['photo_url', 'status'];
        $studentPhotoUrlAndStatusColumnDetails = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('org_person_student', $studentPhotoUrlAndStatusColumns);

        // Get Student Cohort details
        $studentCohortColumn = ['cohort'];
        $studentCohortColumnDetails = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('org_person_student_cohort', $studentCohortColumn);

        // Student Participation column Details.
        $participatingColumnDetails = [["columnName" => "participating", "length" => ""]];

        // Student RetentionTrack column Details.
        $retentionTrackColumnDetails = [["columnName" => "RetentionTrack", "length" => ""]];

        // Student EnrolledAtBeginningOfAcademicYear, EnrolledAtMidpointOfAcademicYear and CompletedADegree Details.
        $enrolledAcademicYearColumnDetails = [
            ["columnName" => "EnrolledAtBeginningOfAcademicYear", "length" => ""],
            ["columnName" => "EnrolledAtMidpointOfAcademicYear", "length" => ""],
            ["columnName" => "CompletedADegree", "length" => ""]
        ];

        // Merge both array details to have common looping
        $organizationStudentDetails = array_merge($studentPhotoUrlAndStatusColumnDetails, $participatingColumnDetails, $retentionTrackColumnDetails, $enrolledAcademicYearColumnDetails, $studentCohortColumnDetails);
        $studentUploadDetails = [];
        foreach ($organizationStudentDetails as $organizationStudentDetail) {
            $pdfDataArray = [];
            $pdfDataArray["required"] = "";
            $pdfDataArray["description"] = "";
            $pdfDataArray["valid_values"] = "";
            $pdfDataArray["length"] = "";
            switch ($organizationStudentDetail["columnName"]) {
                case "photo_url":
                    $pdfDataArray["column_name"] = "StudentPhoto";
                    $pdfDataArray["length"] = $organizationStudentDetail["length"];
                    $pdfDataArray["data_type"] = "string";
                    break;

                case "status":
                    $pdfDataArray["column_name"] = "IsActive (Year Specific)";
                    $pdfDataArray["data_type"] = "Category";
                    $pdfDataArray["length"] = $organizationStudentDetail["length"];
                    $pdfDataArray["value_list"] = "<li>0 (Is Not Active)</li><li>1 (Is Active)</li>";
                    break;

                case "participating":
                    $pdfDataArray["column_name"] = 'Participating (Year Specific)';
                    $pdfDataArray["data_type"] = "Category";
                    $pdfDataArray['value_list'] = '<li>0 (Non Participant)</li> <li>1 (Participant)</li>';
                    break;

                case "RetentionTrack":
                    $pdfDataArray["column_name"] = 'RetentionTrack (Year Specific)';
                    $pdfDataArray["data_type"] = "Category";
                    $pdfDataArray['value_list'] = '<li>0 (No)</li> <li>1 (Yes)</li>';
                    break;

                case "EnrolledAtBeginningOfAcademicYear":
                    $pdfDataArray["column_name"] = 'EnrolledAtBeginningOfAcademicYear';
                    $pdfDataArray["data_type"] = "Category";
                    $pdfDataArray['value_list'] = '<li>0 (No)</li> <li>1 (Yes)</li>';
                    break;

                case "EnrolledAtMidpointOfAcademicYear":
                    $pdfDataArray["column_name"] = 'EnrolledAtMidpointOfAcademicYear';
                    $pdfDataArray["data_type"] = "Category";
                    $pdfDataArray['value_list'] = '<li>0 (No)</li> <li>1 (Yes)</li>';
                    break;

                case "CompletedADegree":
                    $pdfDataArray["column_name"] = 'CompletedADegree';
                    $pdfDataArray["data_type"] = "Category";
                    $pdfDataArray['value_list'] = '<li>0 (No)</li> <li>1 (Yes)</li>';
                    break;

                case "cohort":
                    $pdfDataArray["column_name"] = "SurveyCohort";
                    $pdfDataArray["data_type"] = "Category";
                    $pdfDataArray["value_list"] = "<li>1 (Survey Cohort 1)</li><li>2 (Survey Cohort 2)</li><li>3 (Survey Cohort 3)</li><li>4 (Survey Cohort 4)</li>";
                    break;

                default:
                    $pdfDataArray["column_name"] = "";
                    $pdfDataArray["data_type"] = "";
                    $pdfDataArray["value_list"] = "";
                    break;
            }
            $studentUploadDetails[] = $pdfDataArray;

        }

        $primaryConnectionColumnDetails = [];
        $primaryConnectionColumnDetails["column_name"] = $this->ebiConfigRepository->findOneByKey('Student_Upload_PrimaryConn_Definition_ColumnName')->getValue();
        $primaryConnectionColumnDetails["data_type"] = $this->ebiConfigRepository->findOneByKey('Student_Upload_PrimaryConn_Definition_Type')->getValue();
        $primaryConnectionColumnDetails["description"] = $this->ebiConfigRepository->findOneByKey('Student_Upload_PrimaryConn_Definition_Desc')->getValue();
        $primaryConnectionColumnDetails["length"] = self::STRING_DEFAULT_LENGTH;
        $primaryConnectionColumnDetails["required"] = '';


        $riskGroupColumnDetails = [];
        $riskGroupColumnDetails["column_name"] = 'RiskGroupId';
        $riskGroupColumnDetails["data_type"] = $this->ebiConfigRepository->findOneByKey('Student_Upload_RiskGroup_Definition_Type')->getValue();
        $riskGroupColumnDetails["description"] = $this->ebiConfigRepository->findOneByKey('Student_Upload_RiskGroup_Definition_Desc')->getValue();
        $riskGroupColumnDetails["length"] = self::RISK_GROUP_ID_LENGTH;
        $riskGroupColumnDetails["required"] = '';
        $riskGroupColumnDetails["min_value"] = "";
        $riskGroupColumnDetails["max_value"] = "";
        $riskGroupColumnDetails['valid_values'] = $this->getNumberTypeValidValues($riskGroupColumnDetails);
        $studentUploadDetails[] = $primaryConnectionColumnDetails;
        $studentUploadDetails[] = $riskGroupColumnDetails;
        return $studentUploadDetails;
    }

    /**
     * Arranges the Categorical Profile Item values in a list.
     *
     * @param string $definitionType
     * @param array $profileItemArray
     * @return string
     */
    private function getProfilesCategoryValues($definitionType, $profileItemArray)
    {
        if ($definitionType == 'E') {
            $metaValueListArray = $this->ebiMetaDataListValuesRepository->findByebiMetadata($profileItemArray['id']);
        } else {
            $metaValueListArray = $this->orgMetaDataListValuesRepository->findByOrgMetadata($profileItemArray['id']);
        }
        $list = '';
        foreach ($metaValueListArray as $MetaValue) {
            $list .= '<li>' . $MetaValue->getListValue() . '(' . $MetaValue->getListName() . ')</li>';
        }

        return $list;
    }

    /**
     * Returns the Academic Update Column name based on the $fieldName
     *
     * @param string $fieldName
     * @return string
     */
    private function getAcademicUpdateColumnName($fieldName)
    {
        switch ($fieldName) {
            case "failure_risk_level";
                $columnName = 'FailureRisk';
                break;

            case "grade";
                $columnName = 'InProgressGrade';
                break;

            case "final_grade";
                $columnName = 'FinalGrade';
                break;

            case "absence";
                $columnName = 'Absences';
                break;

            case "send_to_student";
                $columnName = 'SentToStudent';
                break;

            default:
                $columnName = ucfirst($fieldName);
                break;

        }
        return $columnName;
    }

    /**
     * Returns the Sub Groups Upload Data Definitions Details in HTML format
     *
     * @return string
     */
    public function getSubGroupsUploadPdfDetails()
    {
        // Set the upload details for Sub group
        $subGroupDetails = $this->getGroupItemDetails(["id"], true);

        // Parent Group Data Definition Field Details.
        $parentGroupFieldDetails = [];
        $parentGroupFieldDetails["column_name"] = 'ParentGroupId';
        $parentGroupFieldDetails["data_type"] = $this->ebiConfigRepository->findOneByKey('GroupFaculty_Upload_PermissionSet_ColumnType')->getValue();
        $parentGroupFieldDetails["length"] = self::TEXT_TYPE_DEFAULT_LENGTH;
        $parentGroupFieldDetails["required"] = '';
        $parentGroupFieldDetails["description"] = '';
        $parentGroupFieldDetails["min_value"] = "";
        $parentGroupFieldDetails["max_value"] = "";
        $parentGroupFieldDetails['valid_values'] = $this->getNumberTypeValidValues($parentGroupFieldDetails);
        $subGroupDetails[] = $parentGroupFieldDetails;

        // Get the FullPathGroupName and FullPathGroupIds field details and merge with uploadDetails.
        $uploadDetails = array_merge($subGroupDetails, $this->getGroupFullPathDetails());

        // Create HTML for Pdf Generation
        $headerKey = 'Pdf_SubGroup_Header_Template';
        $footerKey = 'Pdf_SubGroup_Footer_Template';
        $htmlTemplate = $this->getHtmlFromTemplate($uploadDetails, $headerKey, $footerKey);

        return $htmlTemplate;
    }

    /**
     * Returns the Groups Faculty Upload Data Definitions Details in HTML format
     *
     * @param int $organizationId
     * @return string
     */
    public function getGroupsFacultyUploadPdfDetails($organizationId = null)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);
        // Group Related data definition fields.
        $groupDetails = $this->getGroupItemDetails(['external_id', 'id']);

        // Person Related data definition fields.
        $includedPersonColumns = ["firstname", "lastname", "username"];
        $personColumnDetails = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('person', $includedPersonColumns);

        $personRelatedFieldDetails = [];
        foreach ($personColumnDetails as $detail) {
            $pdfContent = [];
            $pdfContent["required"] = "";
            $pdfContent["description"] = "";
            switch ($detail['columnName']) {
                case "firstname":
                    $pdfContent["column_name"] = "Firstname";
                    $pdfContent["data_type"] = "text";
                    $pdfContent["length"] = self::STRING_DEFAULT_LENGTH;
                    break;

                case "lastname":
                    $pdfContent["column_name"] = "Lastname";
                    $pdfContent["data_type"] = "text";
                    $pdfContent["length"] = self::STRING_DEFAULT_LENGTH;
                    break;

                case "username":
                    $pdfContent["column_name"] = "PrimaryEmail";
                    $pdfContent["data_type"] = "text";
                    $pdfContent["length"] = $detail['length'];
                    break;

                default:
                    $pdfContent["column_name"] = "";
                    $pdfContent["data_type"] = "";
                    $pdfContent["length"] = "";
            }

            $personRelatedFieldDetails[] = $pdfContent;
        }

        // Merge the faculty field details with the overall group upload details
        //Get the FullPathGroupName and FullPathGroupIds field details and merge with uploadDetails.
        $uploadDetails = array_merge($groupDetails, $personRelatedFieldDetails, $this->getGroupFullPathDetails());

        $permissionSetFieldDetails = [];
        // Added for listing the permission Set
        $permissionSetList = "";
        if ($organizationId) {
            $orgPermissions = $this->orgPermissionSetRepository->findByOrganization($organizationId);
            foreach ($orgPermissions as $perm) {
                $permissionSetList .= "<li>" . $perm->getPermissionsetName() . "</li>";
            }
        }
        $permissionSetDetails = '<div class="validvalues align1"> <ul class="valueslist">' . $permissionSetList . '</ul></div>';
        $permissionSetFieldDetails["column_name"] = 'PermissionSet';
        $permissionSetFieldDetails["data_type"] = $this->ebiConfigRepository->findOneByKey('GroupFaculty_Upload_PermissionSet_ColumnType')->getValue();
        $permissionSetFieldDetails["length"] = $this->ebiConfigRepository->findOneByKey('GroupFaculty_Upload_PermissionSet_ColumnLength')->getValue();
        $permissionSetFieldDetails["description"] = '#clear to be added to remove the permission';
        $permissionSetFieldDetails["required"] = '';
        $permissionSetFieldDetails["min_value"] = "";
        $permissionSetFieldDetails["max_value"] = "";
        $permissionSetFieldDetails['valid_values'] = $this->getNumberTypeValidValues($permissionSetFieldDetails);
        $permissionSetFieldDetails['optional'] = $permissionSetDetails;
        $uploadDetails[] = $permissionSetFieldDetails;

        // Invisible Data definition field details.
        $isInvisibleFieldDetails = [];
        $isInvisibleFieldDetails["column_name"] = "Invisible";
        $isInvisibleFieldDetails["data_type"] = "Category";
        $isInvisibleFieldDetails["value_list"] = "<li> 0 or Null (Visible)</li><li> 1 (Invisible)";
        $isInvisibleFieldDetails["required"] = "";
        $isInvisibleFieldDetails["description"] = "";

        // "Remove" Data definition field details.
        $removeFieldDetails = [];
        $removeFieldDetails["column_name"] = $this->ebiConfigRepository->findOneByKey('Course_Upload_Remove_Definition_ColumnName')->getValue();
        $removeFieldDetails["data_type"] = $this->ebiConfigRepository->findOneByKey('Course_Upload_Remove_Definition_Type')->getValue();
        $removeFieldDetails["description"] = $this->ebiConfigRepository->findOneByKey('Course_Upload_Remove_Definition_Desc')->getValue();
        $removeFieldDetails["length"] = self::DEFAULT_LENGTH;
        $removeFieldDetails["required"] = '';

        $uploadDetails[] = $isInvisibleFieldDetails;
        $uploadDetails[] = $removeFieldDetails;

        $headerKey = 'Pdf_GroupFaculty_Header_Template';
        $footerKey = 'Pdf_SubGroup_Footer_Template';

        $htmlTemplate = $this->getHtmlFromTemplate($uploadDetails, $headerKey, $footerKey);

        return $htmlTemplate;
    }

    /**
     * Returns the Groups Students Upload Data Definitions Details in HTML format
     *
     * @param int $organizationId
     * @return string
     */
    public function getGroupStudentsUploadPdfDetails($organizationId)
    {
        $includedPersonColumns = array('external_id', 'firstname', 'lastname', 'username');
        $personDetails = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('person', $includedPersonColumns);

        $groupStudentPdfDetails = [];
        foreach ($personDetails as $personDetail) {
            $pdfContentArray = [];
            if ($personDetail['columnName'] == 'external_id') {

                $pdfContentArray["column_name"] = 'ExternalId';
                $pdfContentArray["required"] = '(Required)';
                $pdfContentArray["length"] = self::STRING_DEFAULT_LENGTH;
                $pdfContentArray["data_type"] = 'Text';
                $pdfContentArray["description"] = '';

            } else if ($personDetail['columnName'] == 'firstname') {

                $pdfContentArray["column_name"] = 'Firstname';
                $pdfContentArray["required"] = '';
                $pdfContentArray["length"] = self::STRING_DEFAULT_LENGTH;
                $pdfContentArray["data_type"] = 'Text';
                $pdfContentArray["description"] = '';

            } else if ($personDetail['columnName'] == 'lastname') {

                $pdfContentArray["column_name"] = 'Lastname';
                $pdfContentArray["required"] = '';
                $pdfContentArray["length"] = self::STRING_DEFAULT_LENGTH;
                $pdfContentArray["data_type"] = 'Text';
                $pdfContentArray["description"] = '';

            } else if ($personDetail['columnName'] == 'username') {

                $pdfContentArray["column_name"] = 'PrimaryEmail';
                $pdfContentArray["required"] = '';
                $pdfContentArray["length"] = $personDetail['length'];
                $pdfContentArray["data_type"] = 'Text';
                $pdfContentArray["description"] = '';
            }
            if (!empty($pdfContentArray)) {
                $groupStudentPdfDetails[] = $pdfContentArray;
            }

        }

        $topLevelGroups = $this->orgGroupRepository->getTopLevelGroups($organizationId, false);
        $topLevelGroupNames = array_column($topLevelGroups, 'group_name');

        $topLevelGroupDetails = array();
        foreach ($topLevelGroupNames as $topLevelGroupName) {
            $pdfContentArray = [];
            $pdfContentArray["column_name"] = $topLevelGroupName;
            $pdfContentArray["required"] = '';
            $pdfContentArray["data_type"] = 'Text';
            $pdfContentArray["length"] = self::TEXT_TYPE_DEFAULT_LENGTH;
            $pdfContentArray["description"] = "Semicolon-delimited list of group ID's.  Valid ID's are this top-level group or any of its subgroups.  Can also contain #clear.  See explanation at bottom of this file.";
            $topLevelGroupDetails[] = $pdfContentArray;
        }

        $groupStudentPdfDetails = array_merge($groupStudentPdfDetails, $topLevelGroupDetails);
        $headerKey = 'Pdf_GroupStudent_Header_Template';
        $footerKey = 'Pdf_SubGroup_Footer_Template';
        $explanatoryNotesKey = 'Pdf_GroupStudent_ExplanatoryNotes_Template';
        $htmlTemplate = $this->getHtmlFromTemplate($groupStudentPdfDetails, $headerKey, $footerKey, $explanatoryNotesKey);
        return $htmlTemplate;
    }

    /**
     * Returns the Group Item Details by excluding the fields mentioned in $exclude.
     *
     * @param array $includedOrgGroupColumns
     * @param boolean $isGroupNameRequired
     * @return array
     */
    private function getGroupItemDetails($includedOrgGroupColumns = [], $isGroupNameRequired = false)
    {
        $groupDetails = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('org_group', $includedOrgGroupColumns);

        // Fetch group name details separately to align the occurrence of the column definitions.
        $groupNameDetails = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('org_group', ["group_name"]);
        $orgGroupItemDetails = array_merge($groupDetails, $groupNameDetails);

        $uploadDetails = [];
        foreach ($orgGroupItemDetails as $detail) {
            $pdfContent = [];
            $pdfContent["description"] = "";
            switch ($detail['columnName']) {
                case "external_id":
                    $pdfContent["column_name"] = "ExternalID";
                    $pdfContent["data_type"] = "text";
                    $pdfContent["length"] = self::STRING_DEFAULT_LENGTH;
                    $pdfContent["required"] = "(Required)";
                    break;

                case "id":
                    $pdfContent["column_name"] = "GroupID";
                    $pdfContent["data_type"] = "text";
                    $pdfContent["length"] = self::TEXT_TYPE_DEFAULT_LENGTH;
                    $pdfContent["required"] = "(Required)";
                    break;

                case "group_name":
                    $pdfContent["column_name"] = "GroupName";
                    $pdfContent["data_type"] = "text";
                    $pdfContent["length"] = self::TEXT_TYPE_DEFAULT_LENGTH;
                    // For Subgroups the Group Name is Required.
                    // Adding condition:
                    if ($isGroupNameRequired) {
                        $pdfContent["required"] = "(Required)";
                    } else {
                        $pdfContent["required"] = "";
                    }
                    break;

                default:
                    $pdfContent["column_name"] = "";
                    $pdfContent["data_type"] = "";
                    $pdfContent["length"] = "";
                    $pdfContent["required"] = "";
                    break;
            }
            $uploadDetails[] = $pdfContent;
        }
        return $uploadDetails;
    }

    /**
     * Arranges the AcademicUpdate Categorical Item values in a list.
     *
     * @param string $fieldName
     * @return string
     */
    private function getAcademicUpdateCategoryValuesByColumnName($fieldName)
    {
        switch ($fieldName) {
            case "failure_risk_level":
                $fieldValuesArray = $this->failureRiskLevel;
                break;

            case "grade":
                $fieldValuesArray = $this->grade;
                break;

            case "final_grade":
                $fieldValuesArray = $this->finalGrade;
                break;

            default:
                $fieldValuesArray = $this->sendToStudent;
                break;
        }

        $list = '';
        $i = 0;
        foreach ($fieldValuesArray as $fieldValue) {
            $list .= '<li>' . ($i) . ' ' . '(' . $fieldValue . ')</li>';
            $i++;
        }
        return $list;
    }


    /**
     * Returns decimal String by removing white spaces.
     *
     * @param $profile
     * @param $decimalString
     * @return array
     */
    private function trimDecimalString($profile, $decimalString)
    {
        if (isset($profile["decimal_points"]) && ($decimalAt = strpos($decimalString, '.'))) {
            $numberOfDecimals = intval($profile["decimal_points"]);
            if ($numberOfDecimals > 0) {  //If it is zero, then we don't increment because we don't want the dot
                $numberOfDecimals++;
            }
            $decimalString = substr($decimalString, 0, $decimalAt + $numberOfDecimals);
        }
        return $decimalString;
    }

    /**
     * Rearranges the Number Type minimum and maximum values as per the format.
     *
     * @param array $pdfContentArray
     * @return string
     */
    private function getNumberTypeValidValues($pdfContentArray)
    {
        $validValues = '';
        if (array_key_exists("min_value", $pdfContentArray) && array_key_exists("max_value", $pdfContentArray)) {
            if (is_numeric($pdfContentArray["min_value"]) && is_numeric($pdfContentArray["max_value"])) {
                $validValues = ' Valid Values: Minimum : ' . $pdfContentArray["min_value"] . '  Maximum : ' . $pdfContentArray["max_value"];
            } elseif (is_numeric($pdfContentArray["min_value"])) {
                $validValues = ' Valid Values: Minimum : ' . $pdfContentArray["min_value"];
            } elseif (is_numeric($pdfContentArray["max_value"])) {
                $validValues = ' Valid Values: Maximum : ' . $pdfContentArray["max_value"];
            }
        }
        return $validValues;
    }

    /**
     * Get Student Receive Survey Details
     *
     * @return array
     */
    private function getStudentReceiveSurveyDetails()
    {
        $surveyPdfDetails = [];
        foreach ($this->receiveSurveyColumnArray as $receiveSurveyColumn) {

            $pdfContent = [];
            $pdfContent["column_name"] = $receiveSurveyColumn;
            $pdfContent["data_type"] = 'Category';
            $pdfContent['value_list'] = "<li> 0 (Don't Receive Survey)</li><li> 1 (Receive Survey)</li>";
            $pdfContent["length"] = '';
            $pdfContent["required"] = '';
            $pdfContent["description"] = '';

            $pdfContent['valid_values'] = $this->getNumberTypeValidValues($pdfContent);
            $surveyPdfDetails[] = $pdfContent;
        }
        return $surveyPdfDetails;
    }

    /**
     * Returns the Group FullPathNames and FullPathGroupId Details for Data Definitions.
     *
     * @return array
     */
    private function getGroupFullPathDetails()
    {
        $fullPathDetailsArray = [];
        //FullPathNames field Details
        $groupFullPathNames["column_name"] = 'FullPathNames';
        $groupFullPathNames["data_type"] = "Text";
        $groupFullPathNames["description"] = 'A pipe-delimited (|) list of the group NAMES which lead to this group, starting with the root group. It is for information, and is ignored on upload.';
        $groupFullPathNames["required"] = '';
        $fullPathDetailsArray[] = $groupFullPathNames;

        //FullPathGroupIDs field Details
        $groupFullPathGroupIDs["column_name"] = 'FullPathGroupIDs';
        $groupFullPathGroupIDs["data_type"] = "Text";
        $groupFullPathGroupIDs["description"] = "A pipe-delimited (|) list of the group external IDs which lead to this group, starting with the root group. It is for information, and is ignored on upload.";
        $groupFullPathGroupIDs["required"] = '';
        $fullPathDetailsArray[] = $groupFullPathGroupIDs;

        return $fullPathDetailsArray;
    }

    /**
     * Returns Person and Contact Info table Column Details for PDF generation.
     *
     * @param boolean $includeHomePhoneVsOfficePhone
     * @return array
     */
    private function getPersonAndContactDetailsForPdf($includeHomePhoneVsOfficePhone = false)
    {
        // Fetch externalId and authUserName column details separately to maintain the column detail sequence in the PDF.
        $requiredPersonEmailAndAuthNameColumns = ["external_id", "auth_username"];
        $personEmailAndAuthNameDetails = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('person', $requiredPersonEmailAndAuthNameColumns, false);

        // Person Column Details.
        $requiredPersonColumns = ["firstname", "lastname"];
        // Title is not to be apart of student uploads. We accept the column but don't advertise it in the upload.
        if (!$includeHomePhoneVsOfficePhone) {
            $requiredPersonColumns[] = "title";
        }
        $personFullNameDetails = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('person', $requiredPersonColumns, false);

        $personDetails = array_merge($personFullNameDetails, $personEmailAndAuthNameDetails);

        if ($includeHomePhoneVsOfficePhone) {
            $phoneColumn = "home_phone";
        } else {
            $phoneColumn = "office_phone";
        }
        // Contact Column details.
        $requiredContactsColumns = ["address_1", "address_2", "city", "zip", "state", "country", "primary_mobile", "alternate_mobile", $phoneColumn, "primary_email", "alternate_email", "primary_mobile_provider", "alternate_mobile_provider"];
        $contactsDetails = $this->informationSchemaDAO->getCharacterLengthForColumnsInTable('contact_info', $requiredContactsColumns, false);

        $personFieldDetails = array_merge($personDetails, $contactsDetails);

        $personUploadPdfDetails = [];
        foreach ($personFieldDetails as $details) {
            $pdfContent = [];
            $pdfContent["required"] = "";
            $pdfContent["length"] = $details["length"];
            $pdfContent["data_type"] = "string";
            switch ($details["columnName"]) {
                case "external_id":
                    $pdfContent["column_name"] = "ExternalId";
                    $pdfContent["length"] = self::STRING_DEFAULT_LENGTH;
                    $pdfContent["required"] = "(Required)";
                    break;

                case "primary_mobile":
                case "alternate_mobile":
                case "home_phone":
                case "office_phone":
                case "primary_email":
                case "alternate_email":
                case "primary_mobile_provider":
                case "alternate_mobile_provider":
                    $fieldNameArray = preg_split('/(?=[_])/', $details["columnName"]);
                    $pdfContent["column_name"] = "";
                    foreach ($fieldNameArray as $fieldNameParts) {
                        $pdfContent["column_name"] .= ucfirst(str_replace("_", "", $fieldNameParts));
                    }
                    if ($details["columnName"] == "primary_email") {
                        $pdfContent["required"] = "(Required)";
                    }
                    break;

                case "firstname":
                case "lastname":
                    $pdfContent["column_name"] = ucfirst($details["columnName"]);
                    $pdfContent["length"] = self::STRING_DEFAULT_LENGTH;
                    $pdfContent["required"] = "(Required)";
                    break;

                case "auth_username":
                    $pdfContent["column_name"] = "AuthUsername";
                    break;

                case "title":
                case "address_1":
                case "address_2":
                case "city":
                case "zip":
                case "state":
                case "country":
                    if (substr($details["columnName"], 0, -2) === "address") {
                        $pdfContent["column_name"] = "Address" . substr($details["columnName"], -1);
                    } else {
                        $pdfContent["column_name"] = ucfirst($details["columnName"]);
                    }
                    break;

                default:
                    $pdfContent["column_name"] = "";
                    $pdfContent["data_type"] = "";
                    $pdfContent["required"] = "";
                    break;
            }
            $pdfContent["description"] = "";
            $personUploadPdfDetails[] = $pdfContent;
        }

        return $personUploadPdfDetails;
    }

    /**
     * Returns Min and max range values for numeric type profile item
     *
     * @param array $ebiAndOrganizationProfileItemDetail
     * @param array $pdfContentArray
     * @return array
     */
    private function getMinAndMaxValuesForNumericProfileItem($ebiAndOrganizationProfileItemDetail, $pdfContentArray)
    {
        $minRange = '';
        $maxRange = '';
        if ($ebiAndOrganizationProfileItemDetail["definition_type"] == 'E') {

            $minRange = (!empty($ebiAndOrganizationProfileItemDetail["min_range"])) ? $ebiAndOrganizationProfileItemDetail["min_range"] : '';
            $maxRange = (!empty($ebiAndOrganizationProfileItemDetail["max_range"])) ? $ebiAndOrganizationProfileItemDetail["max_range"] : '';

            $ebiAndOrganizationProfileItemDetail['decimal_points'] = (int)$ebiAndOrganizationProfileItemDetail['decimal_points'];
            if (is_numeric($minRange)) {
                $minRange = number_format($minRange, $ebiAndOrganizationProfileItemDetail['decimal_points'], ".", "");
            }

            if (is_numeric($maxRange)) {
                $maxRange = number_format($maxRange, $ebiAndOrganizationProfileItemDetail['decimal_points'], ".", "");
            }

            $pdfContentArray["min_value"] = $this->trimDecimalString($ebiAndOrganizationProfileItemDetail, $minRange);
            $pdfContentArray["max_value"] = $this->trimDecimalString($ebiAndOrganizationProfileItemDetail, $maxRange);
            $pdfContentArray['valid_values'] = $this->getNumberTypeValidValues($pdfContentArray);
        } else {

            if (array_key_exists("number_type", $ebiAndOrganizationProfileItemDetail)) {
                $minRange = (!empty($ebiAndOrganizationProfileItemDetail["number_type"]['min_digits'])) ? $ebiAndOrganizationProfileItemDetail["number_type"]['min_digits'] : '';
                $maxRange = (!empty($ebiAndOrganizationProfileItemDetail["number_type"]['max_digits'])) ? $ebiAndOrganizationProfileItemDetail["number_type"]['max_digits'] : '';

                $ebiAndOrganizationProfileItemDetail["number_type"]['decimal_points'] = (int)$ebiAndOrganizationProfileItemDetail["number_type"]['decimal_points'];
                if (is_numeric($minRange)) {
                    $minRange = number_format($minRange, $ebiAndOrganizationProfileItemDetail["number_type"]['decimal_points'], ".", "");
                }

                if (is_numeric($maxRange)) {
                    $maxRange = number_format($maxRange,
                        $ebiAndOrganizationProfileItemDetail["number_type"]['decimal_points'], ".", "");
                }
            }

            $pdfContentArray["min_value"] = $this->trimDecimalString($ebiAndOrganizationProfileItemDetail, $minRange);
            $pdfContentArray["max_value"] = $this->trimDecimalString($ebiAndOrganizationProfileItemDetail, $maxRange);
            $pdfContentArray['valid_values'] = $this->getNumberTypeValidValues($pdfContentArray);
        }
        return $pdfContentArray;
    }

    /**
     * Generates a PDF using PhantomJS off a given URL and places it in the given location
     *
     * @param string $urlToBeGeneratedIntoPDF
     * @param string $PDFStorageLocation
     * @param string $zoom
     * @return bool|string
     * @throws PhantomJSException|SynapseValidationException
     */
    public function generatePDFUsingPhantomJS($urlToBeGeneratedIntoPDF, $PDFStorageLocation, $zoom)
    {
        $phantomJSresponse = false;
        $phantomJSpath = $this->ebiConfigRepository->findOneBy(['key' => 'PHANTOM_JS_PATH'], new SynapseValidationException('Phantom JS path does not exist.'));

        $rootDir = $this->container->getParameter(SynapseConstant::KERNEL_ROOT_DIRECTORY);
        $pdfifyJs = $this->ebiConfigRepository->findOneBy(['key' => 'PDFIFY_JS'], new SynapseValidationException('PDFIFY JS path does not exist.'));
        $pdfInverse = $this->ebiConfigRepository->findOneBy(['key' => 'PDF_INVERSE'], new SynapseValidationException('PDF Inverse does not exist.'));
        $pdfDpi = $this->ebiConfigRepository->findOneBy(['key' => 'PDF_DPI'], new SynapseValidationException('PDF DPI value does not exist.'));

        $processCommand = $phantomJSpath . " " . $rootDir . $pdfifyJs . " '" . $urlToBeGeneratedIntoPDF . "' " . $PDFStorageLocation . " " . $pdfInverse . " " . $pdfDpi . " " . $zoom;
        $process = new Process($processCommand);
        $error = $process->run();

        if ($error != 0) {
            $this->logger->addCritical("PhantomJS threw an error code of " . $error . " with processCommand: " . $processCommand);
            throw new PhantomJSException($error);
        } else {
            $phantomJSresponse = true;
        }
        return $phantomJSresponse;
    }
}