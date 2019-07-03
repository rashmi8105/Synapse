<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150706105528 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $columnName = '$$column_name$$';
        $description = '$$description$$';
        $valueList = '$$value_list$$';
        $query = <<<CDATA
        update ebi_template_lang set body = '<div id="outerContainer">
			<div class="align1 subHeadingDiv">
				<div class="columnNameContainer details"><p class="idHeading">$columnName</p></div>
				<div class="columnNameContainer dataTypeContainer"> <p>$description</p></div>
			</div>
			<div class="align1 userInfo">
				<p class="userInfoHeading">Upload Information</p>
				<div class="horizontalDottedLine"></div>
			</div>
			<div class="align1 userInfoDetails">
				<div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span class="boldStyler">$columnName</span></p></div>
				<div class="columnNameContainer dataTypeContainer">
					<p><span class="italicStyler">Data Type:</span>Category</p>
				</div>
			</div>
			<div class="validvalues align1">
				<p>Valid Values:</p>
				<ul class="valueslist">
					$valueList
				</ul>
			</div>
			</div>'
where ebi_template_key = 'Pdf_CategoryType_Body_Template';
CDATA;
        $this->addSql($query);
        
        $query1 = <<<CDATA
        update ebi_template_lang set body = '<!doctype html>
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
where ebi_template_key = 'Pdf_Student_Header_Template';
CDATA;
        
        $this->addSql($query1);
        
        $minValue = '$$min_value$$';
        $maxValue = '$$max_value$$';
        $query2 = <<<CDATA
        update ebi_template_lang set body = '<div id="outerContainer">
			<div class="align1 subHeadingDiv">
				<div class="columnNameContainer details"><p class="idHeading">$columnName</p></div>
				<div class="columnNameContainer dataTypeContainer"> <p>$description</p></div>
			</div>
			<div class="align1 userInfo">
				<p class="userInfoHeading">Upload Information</p>
				<div class="horizontalDottedLine"></div>
			</div>
			<div class="align1 userInfoDetails">
				<div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span class="boldStyler">$columnName</span></p></div>
				<div class="columnNameContainer dataTypeContainer">
					<p><span class="italicStyler">Data Type:</span>Numbers Only</p>
				</div>
			</div>
			<div class="validvalues align1">
				<p style="margin-bottom : 15px;">Valid Values: Minimum : $minValue Maximum : $maxValue</p>
			</div>
			</div>'
where ebi_template_key = 'Pdf_NumberType_Body_Template';
CDATA;
        $this->addSql($query2);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
    }
}
