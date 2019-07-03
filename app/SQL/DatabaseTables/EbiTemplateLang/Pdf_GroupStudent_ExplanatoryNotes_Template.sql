INSERT INTO ebi_template VALUES ('Pdf_GroupStudent_ExplanatoryNotes_Template','y');
INSERT INTO ebi_template_lang
		VALUES ('Pdf_GroupStudent_ExplanatoryNotes_Template',1,NULL,'<!DOCTYPE HTML>
					<html>
					<head>
						<title></title>
						<style>
							.container {
								padding: 60px 50px;
							}
					
							p, body {
								margin: 0;
								color: #003366;
							}
					
							#outerContainer {
								float: left;
								width: 100%;
								box-sizing: border-box;
							}
					
							#outerContainer .align1 {
								float: left;
								width: 100%;
							}
					
							#outerContainer .columnNameContainer {
								float: left;
								display: inline-block;
							}
					
							#outerContainer .heading {
								font-weight: bold;
								font-size: 22px;
							}
					
							#outerContainer .headingDiv {
								margin-bottom: 30px;
							}
					
							#outerContainer .subHeadingDiv {
								margin-bottom: 15px;
								float: left;
								width: 100%;
							}
					
							#outerContainer .userInfo {
								margin-bottom: 5px;
							}
					
							#outerContainer .userInfoDetails {
								height: auto;
								margin-bottom: 15px;
							}
					
							#outerContainer .subHeading {
								font-weight: bold;
								font-size: 18px;
							}
					
							#outerContainer .idHeading {
								font-weight: bold;
								font-size: 18px;
							}
					
							#outerContainer .horizontalLine {
								background-color: #ccc;
								width: 100%;
								height: 2px;
							}
					
							#outerContainer .horizontalDottedLine {
								border-bottom: dotted;
								border-width: 4px;
								color: #ccc;
							}
					
							#outerContainer .boldStyler {
								font-weight: bold;
							}
					
							#outerContainer .columnNameContainer2 {
								min-width: 30%;
								width: auto;
								height: auto;
								padding: 0px 10px;
							}
					
							#outerContainer .details {
								min-width: 30%;
								width: auto;
								height: auto;
							}
					
							#outerContainer .dataTypeContainer {
								width: 68%;
								height: auto;
							}
					
							#outerContainer .userInfoHeading {
								margin-bottom: 3px;
							}
					
							#outerContainer .validvalues {
								padding: 0px 10px;
							}
					
							#outerContainer .italicStyler {
								font-style: italic;
							}
					
							.wrapper {
								width: 100%;
								box-sizing: border-box;
								padding-top: 30px;
							}
					
							.wrapper .header {
								padding-bottom: 10px;
							}
					
							.inner-content {
								margin-bottom: 15px;
							}
					
							.content ul {
								padding: 0px 40px 0px 40px;
							}
					
							.table-data {
								margin: 30px 0px 15px 0px;
								width: 100%;
							}
					
							.table-data th {
								color: #003366;
								text-align: left;
							}
					
							.table-data th {
								padding: 5px;
							}
					
							.table-data td {
								padding: 5px;
							}
					
							.table-data td a {
								color: #003366;
							}
					
							.table-data tr:nth-child(even) {
								background: #e0f3ff;
							}
					
							.table-data tr:nth-child(odd) {
								background: #FFF;
							}
							table {
									page-break-inside: avoid;
								}
					
							@media print {
								.table-data th:nth-child(5) {
									min-width: 130px;
								}
					
								p {
									page-break-inside: avoid;
								}
					
								table {
									page-break-inside: avoid;
								}
							}
						</style>
					</head>
					<body>
					<hr/>
					<div class="validvalues align1"><p></p>
						<ul class="valueslist"></ul>
					</div>
					<div id="outerContainer">
						<div class="columnNameContainer details"><p class="idHeading">Explanatory Notes</p></div>
					</div>
					<div class="wrapper">
						<div class="content">
							<div class="inner-content">Mapworks 3.3 introduced this new format for uploading students into groups. The new
								Format applies to both FTP and files uploaded through the setup webpage for groups.
								In the new format, each student should appear on only one row. First, there are four columns to identify the
								student:
							</div>
							<ul>
								<li>ExternalID (required)</li>
								<li>Firstname (optional, for readability)</li>
								<li>Lastname (optional, for readability)</li>
								<li>PrimaryEmail (optional, for readability)</li>
							</ul>
							<p>These are followed one column per top-level group, for as many top-level groups as you have defined. Each
								column name contains the group ID of the top-level group. The ALLSTUDENTS group is not included, since that
								is automatically maintained by the system.</p>
						</div>
						<div class="content">
							<div class="inner-content">For each student row, here is what can be in each cell under a Top-Level Group
								column:
							</div>
							<ul>
								<li>The cell can be
									<b>empty</b>. This means there is no change to the student\'s membership in any of the groups under this
									top-level group.
								</li>
								<li>The cell can include
									<b>one or more group ID\'s</b>. If there is more than one, they need to be separated by semicolons. These
									can be the group ID of the top-level group or any of its subgroups. This will add the student to the
									groups. The effect is cumulative &#45; it does not remove students from other groups they are in under
									this hierarchy.
								</li>
								<li>The cell can also include
									<b>#clear</b>, either by itself, or along with the group names. If #clear is present, then the student
									will be removed from all groups under the top-level group, as well as added to any groups named in the
									cell.
								</li>
							</ul>
							<p>The example below illustrates a campus with three top-level groups: ResLife, Major and Athletics. </p>
							<p>The ResLife group hierarchy consists of ResLife at the top, with areas, halls and floors underneath.</p>
						</div>
						<table class="table-data" border="1">
							<tr>
								<th>ExternalID</th>
								<th>FirstName</th>
								<th>LastName</th>
								<th>PrimaryEmail</th>
								<th style="min-width:130px;">ResLife</th>
								<th>Major</th>
								<th>Athletics</th>
							</tr>
							<tr>
								<td>A091873</td>
								<td>John</td>
								<td>Smith</td>
								<td>
									<a href="#">Smith@northstate.edu</a>
								</td>
								<td>#clear;Jones 1</td>
								<td>#clear;Voice;Painting</td>
								<td>Baseball</td>
							</tr>
							<tr>
								<td>A091874</td>
								<td>Tulsi</td>
								<td>Able</td>
								<td>
									<a href="#">Able@northstate.edu</a>
								</td>
								<td>#clear; Smith 1</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>A091875</td>
								<td>LaDeitra</td>
								<td>Baker</td>
								<td>
									<a href="#">Baker@northstate.edu</a>
								</td>
								<td>#clear</td>
								<td>#clear;CompSci</td>
								<td>Baseball</td>
							</tr>
							<tr>
								<td>A091876</td>
								<td>James</td>
								<td>Charlie</td>
								<td>
									<a href="#">Charlie@northstate.edu</a>
								</td>
								<td>#clear;Smith 2</td>
								<td>#clear;Civil Eng</td>
								<td></td>
							</tr>
							<tr>
								<td>A091877</td>
								<td>Sudhakar</td>
								<td>Delta</td>
								<td>
									<a href="#">Delta@northstate.edu</a>
								</td>
								<td></td>
								<td></td>
								<td>#clear;Quidditch</td>
							</tr>
						</table>
						<div class="content">
							<div class="inner-content">In our example,
							</div>
							<ul>
								<li>Student John Smith is cleared from any ResLife groups he is in, then added to Jones 1, which is a
									subgroup under ResLife. Jones 1 can be at any level under ResLife.
								</li>
								<li>Student John Smith is cleared from any Major groups he is in, then added to both the Voice group and
									Painting group, which are subgroups of Major.
								</li>
								<li>Student John Smith is added to the Baseball subgroup of Athletics. This is in addition to any other
									athletic groups he is a member of.
								</li>
								<li>Student Tulsi Able is cleared from any ResLife groups she is in, then added to Smith 1. Since the Major
									and Athletics columns are blank, there is no change to her group membership for Major, Athletics, or
									their subgroups.
								</li>
							</ul>
						</div>
					</div>
					</body>
					</html>');