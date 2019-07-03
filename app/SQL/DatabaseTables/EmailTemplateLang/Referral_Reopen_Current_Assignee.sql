INSERT INTO email_template (email_key, from_email_address, is_active, bcc_recipient_list) VALUES ('referral_reopen_current_assignee','no-reply@mapworks.com',1,null);
INSERT INTO email_template_lang (email_template_id, language_id, body, subject)
SELECT id,
  1,
  '<html>
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

        .inner-div{
          background-color: #FFFFFF;
          padding: 10px;
        }
      </style>
    </head>
    <body>
      <div class="outer-div">
        <div class="inner-div">
          <p>Hi $$current_assignee_first_name$$,</p>
          <p>The referral created for $$student_first_name$$ $$student_last_name$$ on $$date_of_creation$$ has been reopened and is assigned to you.</p>
          <p>If you have any questions, please contact $$coordinator_first_name$$ $$coordinator_last_name$$ at $$coordinator_email_address$$.</p>
          <p>Best regards,</p>
          <p>Skyfactor Mapworks Team</p>
          <p><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/></p>
          <p>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.
          </p>
        </div>
      </div>
    </body>
  </html>',
  'Mapworks Referral Reopened'
FROM email_template
WHERE email_key='referral_reopen_current_assignee';