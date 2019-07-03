SET @emailtemplateId = (SELECT id FROM email_template WHERE email_key = "Referral_Closed_Creator");
UPDATE `email_template_lang`
SET
  `body` = '
<html>
<head>
    <style>
        body {
            background: none repeat scroll 0 0 #f4f4f4;
            font-family: helvetica, arial, verdana, sans-serif;
            font-size: 13px;
            color: #333;
        }

        .outer-div {
            padding: 21px;
            width: 799px;
            background-color: #EEEEEE;
        }

        .inner-div {
            background-color: #FFFFFF;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="outer-div">
    <div class="inner-div">
        <p>$$creator_first_name$$ $$creator_last_name$$,</p>
        <p>The referral you created for $$student_first_name$$ $$student_last_name$$ on $$date_of_creation$$ has been
            closed.</p>
        <p>If you have any questions, please contact $$coordinator_first_name$$ $$coordinator_last_name$$,
            $$coordinator_title$$
            ($$coordinator_email_address$$).</p>
        <p>Best regards,</p>
        <p>Skyfactor Mapworks Team</p>
        <p><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/></p>
        <p>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</p>
    </div>
</div>
</body>
</html>'
WHERE
`email_template_id` = @emailtemplateId;
