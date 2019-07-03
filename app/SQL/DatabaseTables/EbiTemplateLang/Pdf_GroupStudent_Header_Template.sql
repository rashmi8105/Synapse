UPDATE ebi_template_lang SET body = '<!doctype html>
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
                </style>
            </head>
            <body>
            <div class="container">
                <div id="outerContainer">
                    <div class="align1 headingDiv">
                        <p class="heading">Mapworks: Group-Student Data Definitions<span><img src=""/></span></p>
                    </div>
                    <div class="subHeadingDiv">
                        <p class="subHeading">General Groups Students Information</p>
                        <div class="horizontalLine"></div>
                    </div>
                </div>'
        WHERE ebi_template_key = 'Pdf_GroupStudent_Header_Template';