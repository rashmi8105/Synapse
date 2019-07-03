<?php

/**
 * Class PdfDetailsServiceTest
 */

use Codeception\TestCase\Test;

class PdfDetailsServiceTest extends Test
{
    use Codeception\Specify;

    private $receiveSurveyCol = [
        'TransitionOneReceiveSurvey',
        'CheckupOneReceiveSurvey',
        'TransitionTwoReceiveSurvey',
        'CheckupTwoReceiveSurvey'
    ];

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Service\Impl\GroupService
     */
    private $groupService;

    /**
     * @var \Synapse\CoreBundle\Repository\OrgGroupTreeRepository
     */
    private $orgGroupTreeRepository;

    /**
     * @var \Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac
     */
    private $rbacManager;

    /**
     * @var \Synapse\CoreBundle\Service\Impl\ProfileService
     */
    private $profileService;

    /**
     * @var \Synapse\CoreBundle\Service\Impl\OrgProfileService
     */
    private $orgProfileService;

    /**
     * @var \Synapse\PdfBundle\Service\Impl\PdfDetailsService
     */
    private $pdfDetailsService;

    public function testGetStudentUploadPdfDetails()
    {
        $this->markTestSkipped('Failing Test case');
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->rbacManager = $this->container->get('tinyrbac.manager');
            $this->logger = $this->container->get('logger');
            $this->profileService = $this->container->get('profile_service');
            $this->orgProfileService = $this->container->get('orgprofile_service');
            $this->pdfDetailsService = $this->container->get('pdf_service');
        });

        $this->specify("Verify the functionality of the method getStudentUploadPdfDetails", function ($orgId, $personId) {
            $this->rbacManager->initializeForUser($personId); // so that user has the proper access
            $results = $this->pdfDetailsService->getStudentUploadPdfDetails($orgId);
            verify($results)->notEmpty();
            verify($results)->internalType('string');
            $results = preg_replace('/\s+/', '', $results);
            foreach ($this->receiveSurveyCol as $receiveSurvey) {
                $receiveSureyTemp = preg_replace('/\s+/', '', $this->getPdfDataForSurvey($receiveSurvey));
                verify($results)->contains($receiveSureyTemp);
                break;
            }
        }, ["examples" =>
            [
                [203, 4878750],
                [190, 4559001]
            ]
        ]);
    }


    public function testGetGroupsFacultyUploadPdfDetails()
    {
        $this->markTestSkipped('Failing Test case');
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->rbacManager = $this->container->get('tinyrbac.manager');
            $this->logger = $this->container->get('logger');
            $this->profileService = $this->container->get('profile_service');
            $this->orgProfileService = $this->container->get('orgprofile_service');
            $this->pdfDetailsService = $this->container->get('pdf_service');
        });

        $this->specify("Verify the functionality of the method getGroupsFacultyUploadPdfDetails", function ($orgId, $personId, $fieldTemplate) {
            $this->rbacManager->initializeForUser($personId); // so that user has the proper access
            $results = $this->pdfDetailsService->getGroupsFacultyUploadPdfDetails($orgId);
            verify($results)->internalType('string');
            $results = preg_replace('/\s+/', '', $results);
            $groupTemplate = preg_replace('/\s+/', '',$fieldTemplate);
            $this->assertContains($groupTemplate, $results);
        }, ["examples" =>
            [
                [203, 4878750, $this->getFieldTemplate1()],
                [190, 4559001, $this->getFieldTemplate2()]
            ]
        ]);
    }


    private function getPdfDataForSurvey($receiveSurvey)
    {
        return '<div id="outerContainer">
                        <div class="align1 subHeadingDiv">
                            <div class="columnNameContainer details">
                                <p class="idHeading">' . $receiveSurvey . ' &nbsp;
                                    <span style="font-style:italic;color:#666;font-size:16px;"> </span>
                                </p>
                            </div>
                            <div class="columnNameContainer dataTypeContainer">
                                <p></p>
                            </div>

                        </div>
                        <div class="align1 userInfo">
                            <p class="userInfoHeading">Upload Information</p>
                            <div class="horizontalDottedLine"></div>

                        </div>
                        <div class="align1 userInfoDetails">
                            <div class="columnNameContainer columnNameContainer2">
                                <p>
                                    <span class="italicStyler">Column Name:</span>
                                    <span class="boldStyler">' . $receiveSurvey . '</span>
                                </p>
                            </div>
                            <div class="columnNameContainer dataTypeContainer">
                                <p>
                                    <span class="italicStyler">Data Type:</span>Category
                                </p>

                            </div>

                        </div>
                        <div class="validvalues align1">
                            <p>Valid Values:</p>
                            <ul class="valueslist">
                                                        <li>0(Don' . "'" . 't Receive Survey)</li>
                    <li>1(Receive Survey)</li>
                                                </ul>

                        </div>

                    </div>';
    }

    private function getFieldTemplate1()
    {
        return  '<!doctypehtml>
                <html>
                
                <head>
                    <title></title>
                    <style>
                        .container {
                            padding: 60px 50px;
                        }
                        p,
                        body {
                            margin: 0;
                            color: #003366;
                        }
                        #outerContainer {
                            float: left;
                            width: 100%;
                            box-sizing: border-box;
                        }
                        #outerContainer.align1 {
                            float: left;
                            width: 100%;
                        }
                        #outerContainer.columnNameContainer {
                            float: left;
                            display: inline-block;
                        }
                        #outerContainer.heading {
                            font-weight: bold;
                            font-size: 22px;
                        }
                        #outerContainer.headingDiv {
                            margin-bottom: 30px;
                        }
                        #outerContainer.subHeadingDiv {
                            margin-bottom: 15px;
                            float: left;
                            width: 100%;
                        }
                        #outerContainer.userInfo {
                            margin-bo ttom: 5px;
                        }
                        #outerContainer.userInfoDetails {
                            height: auto;
                            margin-bottom: 15px;
                        }
                        #outerContainer.subHeading {
                            font-weight: bold;
                            font-size: 18px;
                        }
                        #outerContainer.idHeading {
                            font-weight: bold;
                            font-size: 18px;
                        }
                        #outerContainer.horizontalLine {
                            background-color: #ccc;
                            width: 100%;
                            height: 2px;
                        }
                        #outerContainer.horizontalDottedLine {
                            border-bottom: dotted;
                            border-width: 4px;
                            color: #ccc;
                        }
                        #outerContainer.boldStyler {
                            font-weight: bold;
                        }
                        #outerContainer.columnNameContainer2 {
                            min-width: 30%;
                            width: auto;
                            height: auto;
                            padding: 0px 10px;
                        }
                        #outerContainer.details {
                            min-width: 30%;
                            width: auto;
                            height: auto;
                        }
                        #outerContainer.dataTypeContainer {
                            width: 68%;
                            height: auto;
                        }
                        #outerContainer.userInfoHeading {
                            margin-bottom: 3px;
                        }
                        #outerContainer.validvalues {
                            padding: 0px 10px;
                        }
                        #outerContainer.italicStyler {
                            font-style: italic;
                        }
                
                    </style>
                </head>
                
                <body>
                <div class="container">
                    <div id="outerContainer">
                        <div class="align1headingDiv">
                            <p class="heading">Mapworks:GroupMembership-Faculty/Stafffiledefinitions<span><imgsrc =""/></span>
                            </p>
                        </div>
                        <div class="subHeadingDiv">
                            <p class="subHeading">GeneralGroupsFaculty/StaffInformation
                            </p>
                            <div class="horizontalLine">
                            </div>
                        </div>
                    </div>
                
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails">
                                <p class="idHeading">ExternalId&nbsp;
                                    <span style="font-style:italic;color:#666;font-size:16px;">(Required)</span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p></p>
                            </div>
                        </div>
                        <div class="align1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine">
                            </div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                    <span class="italicStyler">ColumnName:</span>
                                    <span class="boldStyler">ExternalId</span>
                                </p>
                            </div>
                            <br>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    <span class="italicStyler">DataType:</span>Text
                
                                </p>
                                <p>(MaxLength:45characters)</p>
                            </div>
                        </div>
                        <div class="validvaluesalign1">
                            <p></p>
                            <ul class="valueslist">
                            </ul>
                        </div>
                    </div>
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails">
                                <p class="idHeading">GroupId&nbsp;
                                    <span style="font-style:italic;color:#666;font-size:16px;">(Required)</span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p></p>
                            </div>
                        </div>
                        <div class="align1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine">
                            </div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                    <span class="italicStyler">ColumnName:</span>
                                    <span class="boldStyler">GroupId</span>
                                </p>
                            </div>
                            <br>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    <span class="italicStyler">DataType:</span>Text
                
                                </p>
                                <p>(MaxLengt h:100characters)</p>
                            </div>
                        </div>
                        <div class="validvaluesalign1">
                            <p></p>
                            <ul class="valueslist">
                            </ul>
                        </div>
                    </div>
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails">
                                <p class="idHeading">GroupName&nbsp;
                                  <span style="font-style:italic;color:#666;font-size:16px;">
                    </span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p></p>
                            </div>
                        </div>
                        <div class="align1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine"></div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                    <span class="italicStyler">ColumnName:</span>
                                    <span class="boldStyler">GroupName</span>
                                </p>
                            </div>
                            <br>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    <span class="italicStyler"> DataType:</span>Text
                
                                </p>
                                <p>(MaxLength:100characters)</p>
                            </div>
                        </div>
                        <div class="validvaluesalign1">
                            <p></p>
                            <ul class="valueslist">
                            </ul>
                        </div>
                    </div>
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails">
                                <p class="idHeading">Firstname&nbsp;
                                  <span style="font-style:italic;color:#666;font-size:16px;">
                    </span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p></p>
                            </div>
                        </div>
                        <div class="align1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine">
                            </div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                    <span class="italicStyler">ColumnName:</span>
                                    <span class="boldStyler">Firstname</span>
                                </p>
                            </div>
                            <br>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    <span class="italicStyler">DataType:</span>Text
                
                                </p>
                                <p>(MaxLength:45characters)</p>
                            </div>
                        </div>
                        <div class="validvaluesalign1">
                            <p></p>
                            <ul class="valueslist">
                            </ul>
                        </div>
                    </div>
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails">
                                <p class="idHeading">Lastname&nbs p;
                                  <span style="font-style:italic;color:#666;font-size:16px;">
                    </span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p></p>
                            </div>
                        </div>
                        <div class="align1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine">
                            </div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                    <span class="italicStyler">ColumnName:</span>
                                    <span class="boldStyler">Lastname</span>
                                </p>
                            </div>
                            <br>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    <span class="italicStyler">DataType:</span>Text
                
                                </p>
                                <p>(MaxLength:45characters)</p>
                            </div>
                        </div>
                        <div class="validvaluesalign1">
                            <p></p>
                            <ul class="valueslist">
                            </ul>
                        </div>
                    </div>
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails">
                                <p class="idHeading">PrimaryEmail&nbsp;
                                  <span style="font-style:italic;color:#666;font-size:16px;">
                            </span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p></p>
                            </div>
                        </div>
                        <div class="align1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine">
                            </div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                    <span class="italicStyler">ColumnName:</span>
                                  <span class="boldStyler">P rimaryEmail
                                    </span>
                                </p>
                            </div>
                            <br>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    <span class="italicStyler">DataType:</span>Text
                
                                </p>
                                <p>(MaxLength:100ch aracters)</p>
                            </div>
                        </div>
                        <div class="validvaluesalign1">
                            <p></p>
                            <ul class="valueslist">
                            </ul>
                        </div>
                    </div>
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails">
                                <p class="idHeading">FullPathNames&nbsp;
                                  <span style="font-style:italic;color:#666;font-size:16px;">
                                </span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    Apipe-delimited(|)listofthegroupNAMESwhichleadtothisgroup,startingwiththerootgroup</p>
                            </div>
                        </div>
                        <div class="align1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine">
                            </div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                    <span class="italicStyler">ColumnName:</span>
                                    <span class="boldStyler">FullPathNames</span>
                                </p>
                            </div>
                            <br>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    <span class="italicStyler">DataType:</span>Text
                
                                </p>
                                <p>(MaxLength:255characters)</p>
                            </div>
                        </div>
                        <div class="validvaluesalign1">
                            <p></p>
                            <ul class="valueslist">
                            </ul>
                        </div>
                    </div>
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails">
                                <p class="idHeading">FullPathGroupIDs&nbsp;
                                     <span style="font-style:italic;color:#666;font-size:16px;">
                                </span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    Apipe-delimited(|)listofthegroupexternalID\'swhichleadtothisgroup,startingwiththerootgroup</p>
                            </div>
                        </div>
                        <div class="align
                                1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine">
                            </div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                    <span class="italicStyler">ColumnName:</span>
                                    <span class="boldStyler">FullPathGroupIDs</span>
                                </p>
                            </div>
                            <br>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    <span class="italicStyler">DataType:</span>Text
                
                                </p>
                                <p>(MaxLength:255characters)</p>
                            </div>
                        </div>
                        <div class="validvaluesalign1">
                            <p></p>
                            <ul class="valueslist">
                            </ul>
                        </div>
                    </div>
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails
                                ">
                                <p class="idHeading">PermissionSet&nbsp;
                                     <span style="font-style:italic;color:#666;font-size:16px;">
                                </span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>#cleartobeaddedtoremovethepermission</p>
                            </div>
                        </div>
                        <div class="align1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine">
                            </div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                     <span class="italicStyler">Colum nName:
                                    </span>
                                    <span class="boldStyler">PermissionSet</span>
                                </p>
                            </div>
                            <br>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                     <span class="italicStyler">Data Type:
                                    </span>Text
                                </p>
                                <p>(MaxLength:100characters)</p>
                            </div>
                        </div>
                        <div class="validvaluesalign1">
                            <p></p>
                            <ul class="valueslist">
                                <div class="validvaluesalign1">
                                    <ul class="valueslist">
                                        <li>All</li>
                                        <li>CourseOnly</li>
                                    </ul>
                                </div>
                            </ul>
                        </div>
                    </div>
                    <div id="outerContainer">
                        <div class="align1subHeadingDiv">
                            <div class="columnNameContainerdetails">
                                <p class="idHeading">Invisible&nbsp;
                                     <span style="font-style:italic;color:#666;font-size:16px;">
                                </span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p></p>
                            </div>
                        </div>
                        <div class="align1userInfo">
                            <p class="userInfoHeading">UploadInformation
                            </p>
                            <div class="horizontalDottedLine
                                ">
                            </div>
                        </div>
                        <div class="align1userInfoDetails">
                            <div class="columnNameContainercolumnNameContainer2">
                                <p>
                                    <span class="italicStyler">ColumnName:</span>
                                    <span class="boldStyler">Invisible</span>
                                </p>
                            </div>
                            <div class="columnNameContainerdataTypeContainer">
                                <p>
                                    <span class="italicStyler">DataType:</span>Category
                
                                </p>
                                </d iv>
                            </div>
                            <div class="validvaluesalign1">
                                <p>ValidValues:</p>
                                <ul class="valueslist">
                                    <li>0orNull(Visible)</li>
                                    <li>1(Invisible)
                                </ul>
                            </div>
                        </div>
                        <div id="outerContainer">
                            <div class="align1subHeadingDiv">
                                <div class="columnNameContainerdetails">
                                    <p class="idHeading">Remove&nbsp;
                                        <span style="font-style:italic;color:#666;
                                font-size:16px;">
                                </span>
                                    </p>
                                </div>
                                <div class="columnNameContainerdataTypeContainer">
                                    <p>"Remove"tobeaddedtoremovetherecord</p>
                                </div>
                            </div>
                            <div class="align1userInfo">
                                <p class="userInfoHeading">UploadInformation
                                </p>
                                <div class="horizontalDottedLine">
                                </div>
                            </div>
                            <div class="align1userInfoDetails">
                                <div class="columnNameContainercolumnNameContainer2">
                                    <p>
                                        <span class="italicStyler">ColumnName:</span>
                                        <span class="boldStyler">Remove</span>
                                    </p>
                                </div>
                                <br>
                                <div class="columnNameContainerdataTypeContainer">
                                    <p>
                                        <span class="italicStyler">DataType:</span>Text
                
                                    </p>
                                    <p>(MaxLength:10characters)</p>
                                </div>
                            </div>
                            <div class="validvaluesalign1
                                ">
                                <p></p>
                                <ul class="valueslist">
                                </ul>
                            </div>
                        </div>
                        <p></p>
                    </div>
                </body>
                </html>';
 }


    private function getFieldTemplate2()
    {
        return  '<!doctype html>
                 <html>
                    <head>
                        <title></title>
                        <style>
                            .container{
                                padding:60px 50px;
                            }
                            p,body{
                                margin:0;
                                color:#003366;
                            }
                            #outerContainer{
                                float:left;
                                width:100%;
                                box-sizing:border-box;
                            }
                            #outerContainer .align1{
                                float:left;
                                width:100%;
                            }
                            #outerContainer .columnNameContainer{
                                float:left;
                                display:inline-block;
                            }
                            #outerContainer .heading{
                                font-weight:bold;
                                font-size:22px;
                            }
                            #outerContainer .headingDiv{
                                margin-bottom:30px;
                            }
                            #outerContainer .subHeadingDiv{
                                margin-bottom:15px;
                                float: left;
                                width: 100%;
                            }
                            #outerContainer .userInfo{
                                margin-bottom:5px;
                            }
                            #outerContainer .userInfoDetails{
                                height:auto;
                                margin-bottom:15px;
                            }
                            #outerContainer .subHeading{
                                font-weight:bold;
                                font-size:18px;
                            }
                            #outerContainer .idHeading{
                                font-weight:bold;
                                font-size:18px;
                            }
                            #outerContainer .horizontalLine{
                                background-color: #ccc;
                                width:100%;
                                height:2px;
                            }
                            #outerContainer .horizontalDottedLine{
                                border-bottom: dotted;
                                border-width: 4px; 
                                color:#ccc;
                            }
                            #outerContainer .boldStyler{
                                font-weight:bold;
                            }
                            #outerContainer .columnNameContainer2{
                                min-width:30%;
                                width:auto;
                                height:auto;
                                padding:0px 10px;
                            }
                            #outerContainer .details{
                                min-width:30%;
                                width:auto;
                                height:auto;
                            }
                            #outerContainer .dataTypeContainer{
                                width:68%;
                                height:auto;
                            }
                            #outerContainer .userInfoHeading{
                                margin-bottom:3px;
                            }
                            #outerContainer .validvalues{
                                padding:0px 10px;
                            }
                            #outerContainer .italicStyler{
                                font-style:italic;
                            }
                        </style>
                    </head>
                    <body>
                    <div class="container">
                        <div id="outerContainer">
                            <div class="align1 headingDiv">
                                <p class="heading">Mapworks: Group Membership - Faculty/Staff file definitions <span><img src="" /></span></p>
                            </div>
                            <div class="subHeadingDiv">
                                <p class="subHeading">General Groups Faculty/Staff Information</p>
                                <div class="horizontalLine"></div>
                            </div>
                        </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">ExternalId &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> (Required)</span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p></p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">ExternalId</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 45 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> </ul>
                                </div>
                            </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">GroupId &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> (Required)</span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p></p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">GroupId</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 100 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> </ul>
                                </div>
                            </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">GroupName &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> </span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p></p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">GroupName</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 100 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> </ul>
                                </div>
                            </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">Firstname &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> </span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p></p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">Firstname</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 45 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> </ul>
                                </div>
                            </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">Lastname &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> </span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p></p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">Lastname</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 45 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> </ul>
                                </div>
                            </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">PrimaryEmail &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> </span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p></p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">PrimaryEmail</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 100 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> </ul>
                                </div>
                            </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">FullPathNames &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> </span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p>A pipe-delimited (|) list of the group NAMES which lead to this group, starting with the root group</p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">FullPathNames</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 255 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> </ul>
                                </div>
                            </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">FullPathGroupIDs &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> </span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p>A pipe-delimited (|) list of the group external ID\'s which lead to this group, starting with the root group</p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">FullPathGroupIDs</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 255 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> </ul>
                                </div>
                            </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">PermissionSet &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> </span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p>#clear to be added to remove the permission</p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">PermissionSet</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 100 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> <div class="validvalues align1"> <ul class="valueslist"><li>Professional Advisor All Access</li><li>Resident Director Partial Access</li><li>LimitedAccess</li><li>nonRM Faculty/Staff Input Only Access</li><li>AggregateOnly</li><li>Faculty Course Only Access</li><li>Institutional Effectiveness Access</li><li>Center for International Education Access</li><li>Royal Advisor All Access</li></ul></div></ul>
                                </div>
                            </div><div id="outerContainer">
                            <div class="align1 subHeadingDiv">
                                <div class="columnNameContainer details"><p class="idHeading">Invisible &nbsp;<span style="font-style:italic;color:#666;font-size:16px;"> </span></p></div>
                                <div class="columnNameContainer dataTypeContainer"> <p></p></div>
                            </div>
                            <div class="align1 userInfo">
                                <p class="userInfoHeading">Upload Information</p>
                                <div class="horizontalDottedLine"></div>
                            </div>
                            <div class="align1 userInfoDetails">
                                <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span class="boldStyler">Invisible</span></p></div>
                                <div class="columnNameContainer dataTypeContainer">
                                    <p><span class="italicStyler">Data Type:</span>Category</p>
                                </div>
                            </div>
                            <div class="validvalues align1">
                                <p>Valid Values:</p>
                                <ul class="valueslist">
                                    <li> 0 or Null (Visible)</li><li> 1 (Invisible)
                                </ul>
                            </div>
                            </div><div id="outerContainer">
                                <div class="align1 subHeadingDiv">
                                    <div class="columnNameContainer details"><p class="idHeading">Remove &nbsp;<span
                                            style="font-style:italic;color:#666;font-size:16px;"> </span></p></div>
                                    <div class="columnNameContainer dataTypeContainer"><p>"Remove" to be added to remove the record</p></div>
                                </div>
                                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                                    <div class="horizontalDottedLine"></div>
                                </div>
                                <div class="align1 userInfoDetails">
                                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span
                                            class="boldStyler">Remove</span></p></div>
                                    <br>
                                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p>
                                        <p>(Max Length: 10 characters)</p></div>
                                </div>
                                <div class="validvalues align1"><p></p>
                                    <ul class="valueslist"> </ul>
                                </div>
                            </div><p></p>
                    </div>  
                   </body>
                </html>';
    }


}