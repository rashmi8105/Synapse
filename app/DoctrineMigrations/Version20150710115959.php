<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150710115959 extends AbstractMigration
{

    /**
     *
     * @param Schema $schema            
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $query = <<<CDATA
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'SubGroup_Upload_ParentGroup_ColumnName','Parent_Group_Id');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'SubGroup_Upload_ParentGroup_ColumnType','Integer');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'GroupFaculty_Upload_PermissionSet_ColumnName','Permission_Set');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'GroupFaculty_Upload_PermissionSet_ColumnType','String');
INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'GroupFaculty_Upload_PermissionSet_ColumnLength','100');
CDATA;
        $this->addSql($query);
        
        $query1 = <<<CDATA
INSERT INTO `ebi_template` (`key`,`is_active`) values ('Pdf_SubGroup_Header_Template','y');
INSERT INTO `ebi_template` (`key`,`is_active`) values ('Pdf_SubGroup_Footer_Template','y');
INSERT INTO `ebi_template` (`key`,`is_active`) values ('Pdf_GroupStudent_Header_Template','y');
INSERT INTO `ebi_template` (`key`,`is_active`) values ('Pdf_GroupFaculty_Header_Template','y');
CDATA;
        $this->addSql($query1);
        
        $query2 = <<<CDATA
SET @langId = (select id from language_master where langcode = 'en_US');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_SubGroup_Header_Template', @langId, null, '<!doctype html>
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
				width:30%;
				height:auto;
				padding:0px 10px;
			}
			#outerContainer .details{
				width:30%;
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
		</div>');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_SubGroup_Footer_Template', @langId, null, '<p></p>
	</div>  
   </body>
</html>');   
CDATA;
        $this->addSql($query2);
        
        $query3 = <<<CDATA
SET @langId = (select id from language_master where langcode = 'en_US');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_GroupFaculty_Header_Template', @langId, null, '<!doctype html>
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
				width:30%;
				height:auto;
				padding:0px 10px;
			}
			#outerContainer .details{
				width:30%;
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
		</div>');
CDATA;
        $this->addSql($query3);
        
        $query4 = <<<CDATA
SET @langId = (select id from language_master where langcode = 'en_US');
INSERT INTO `ebi_template_lang` (`ebi_template_key`,`lang_id`,`description`,`body`) VALUES ('Pdf_GroupStudent_Header_Template', @langId, null, '<!doctype html>
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
				width:30%;
				height:auto;
				padding:0px 10px;
			}
			#outerContainer .details{
				width:30%;
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
		</div>');
CDATA;
        $this->addSql($query4);
    }

    /**
     *
     * @param Schema $schema            
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
    }
}
