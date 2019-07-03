<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150722131130 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
      
        $query = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<!doctype html>
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
 				width:58%;
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
 				<p class="heading">MAP-Works: Academic Updates File Data Definitions<span><img src="" /></span></p>
 			</div>
 			<div class="subHeadingDiv">
 				<p class="subHeading">General Academic Updates Information</p>
 				<div class="horizontalLine"></div>
 			</div>
 		</div>'
WHERE `ebi_template_key` = 'Pdf_AcademicUpdates_Header_Template';
CDATA;
        $this->addSql($query);
        
        
        
        $pdfCourseFacultyQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<!doctype html>
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
 				width:58%;
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
 				<p class="heading">MAP-Works: Course Faculty/Staff File Data Definitions<span><img src="" /></span></p>
 			</div>
 			<div class="subHeadingDiv">
 				<p class="subHeading">General Course Faculty/Staff Information</p>
 				<div class="horizontalLine"></div>
 			</div>
 		</div>'
WHERE `ebi_template_key` = 'Pdf_CoursesFaculty_Header_Template';
CDATA;
        $this->addSql($pdfCourseFacultyQuery);
        
        
        $pdfCourseStudentQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<!doctype html>
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
 				width:58%;
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
 				<p class="heading">MAP-Works: Course Students File Data Definitions<span><img src="" /></span></p>
 			</div>
 			<div class="subHeadingDiv">
 				<p class="subHeading">General Course Students Information</p>
 				<div class="horizontalLine"></div>
 			</div>
 		</div>'
WHERE `ebi_template_key` = 'Pdf_CoursesStudents_Header_Template';
CDATA;
        $this->addSql($pdfCourseStudentQuery);
        
        
        $pdfCourseHeaderQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<!doctype html>
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
 				width:58%;
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
 				<p class="heading">MAP-Works: Courses and Sections File Data Definitions<span><img src="" /></span></p>
 			</div>
 			<div class="subHeadingDiv">
 				<p class="subHeading">General Courses and Sections Information</p>
 				<div class="horizontalLine"></div>
 			</div>
 		</div>'
WHERE `ebi_template_key` = 'Pdf_Courses_Header_Template';
CDATA;
        $this->addSql($pdfCourseHeaderQuery);
        

        
        $pdfFacultyHeaderQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<!doctype html>
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
 				width:58%;
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
 				<p class="heading">MAP-Works: Faculty/Staff File Data Definitions<span><img src="" /></span></p>
 			</div>
 			<div class="subHeadingDiv">
 				<p class="subHeading">General Faculty/Staff Information</p>
 				<div class="horizontalLine"></div>
 			</div>
 		</div>'
WHERE `ebi_template_key` = 'Pdf_Faculty_Header_Template';
CDATA;
        $this->addSql($pdfFacultyHeaderQuery);
        
       
        $pdfGroupFacultyHeaderQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<!doctype html>
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
 				<p class="heading">MAP-Works: Group Membership - Faculty/Staff file definitions <span><img src="" /></span></p>
 			</div>
 			<div class="subHeadingDiv">
 				<p class="subHeading">General Groups Faculty/Staff Information</p>
 				<div class="horizontalLine"></div>
 			</div>
 		</div>'
WHERE `ebi_template_key` = 'Pdf_GroupFaculty_Header_Template';
CDATA;
        $this->addSql($pdfGroupFacultyHeaderQuery);
        
        
        $pdfGroupStudentHeaderQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<!doctype html>
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
 				<p class="heading">MAP-Works: Group Membership - Student file definitions <span><img src="" /></span></p>
 			</div>
 			<div class="subHeadingDiv">
 				<p class="subHeading">General Groups Students Information</p>
 				<div class="horizontalLine"></div>
 			</div>
 		</div>'
WHERE `ebi_template_key` = 'Pdf_GroupStudent_Header_Template';
CDATA;
        $this->addSql($pdfGroupStudentHeaderQuery);
        
        $pdfStudentHeaderSQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<!doctype html>
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
 				width:58%;
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
 			#outerContainer .valueslist{
 				list-style-type: none;
 			}
 		</style>
 	</head>
 	<body>
 	<div class="container">
 		<div id="outerContainer">
 			<div class="align1 headingDiv">
 				<p class="heading">MAP-Works: Student File Data Definitions<span><img src="" /></span></p>
 			</div>
 			<div class="subHeadingDiv">
 				<p class="subHeading">General Student Information</p>
 				<div class="horizontalLine"></div>
 			</div>
 		</div>'
WHERE `ebi_template_key` = 'Pdf_Student_Header_Template';
CDATA;
        $this->addSql($pdfStudentHeaderSQuery);
        
        
        $pdfSubGroupHeaderQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<!doctype html>
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
 				<p class="heading">MAP-Works: Sub-Groups Creation File Definitions<span><img src="" /></span></p>
 			</div>
 			<div class="subHeadingDiv">
 				<p class="subHeading">General Sub-Groups Information</p>
 				<div class="horizontalLine"></div>
 			</div>
 		</div>'
WHERE `ebi_template_key` = 'Pdf_SubGroup_Header_Template';
CDATA;
        $this->addSql($pdfSubGroupHeaderQuery);
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       
    }
}
