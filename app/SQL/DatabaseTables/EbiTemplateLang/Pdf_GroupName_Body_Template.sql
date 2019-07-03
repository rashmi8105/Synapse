INSERT INTO ebi_template VALUES ('Pdf_GroupName_Body_Template','y');


INSERT INTO ebi_template_lang (ebi_template_key, lang_id, description, body) VALUES ('Pdf_GroupName_Body_Template', @langid, NULL, '<div id="outerContainer">
                <div class="align1 subHeadingDiv">
                    <div class="columnNameContainer details"><p class="idHeading">$$column_name$$</p></div>
                    <div class="columnNameContainer dataTypeContainer"><p>$description</p></div>
                </div>
                <div class="align1 userInfo"><p class="userInfoHeading">Upload Information</p>
                    <div class="horizontalDottedLine"></div>
                </div>
                <div class="align1 userInfoDetails">
                    <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span>
                    <span class="boldStyler">$$column_name$$</span></p></div>
                    <div class="columnNameContainer dataTypeContainer"><p><span class="italicStyler">Data Type:</span>Text</p></div>
                </div>
            </div>');