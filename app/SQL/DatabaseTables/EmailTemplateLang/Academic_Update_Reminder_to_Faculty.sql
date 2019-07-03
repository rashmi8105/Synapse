SET @emtid := (SELECT id FROM email_template WHERE email_key="Academic_Update_Reminder_to_Faculty");

UPDATE `email_template_lang` 
SET 
    `body` = '<html>
              <head>
                  <style>body {
                      background: none repeat scroll 0 0 #f4f4f4;
                  }

                  table {
                      padding: 21px;
                      width: 799px;
                      font-family: helvetica, arial, verdana, san-serif;
                      font-size: 13px;
                      color: #333;
                  }</style>
              </head>
              <body>
              <table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
                  <tbody>
                  <tr style="background:#fff;border-collapse:collapse;">
                      <td>Dear $$faculty_name$$,</td>
                  </tr>
                  <tr style="background:#fff;border-collapse:collapse;">
                      <td>Please submit your academic updates for this request:</td>
                  </tr>
                  <tr>
                      <td style=" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse">
                          <p style="font-weight: bold; font-size: 14px; color:#fff;"><a href="$$faculty_au_submission_page$$">View and
                              complete this academic update request on Mapworks </a></p></td>
                  </tr>
                  <tr>
                      <td style="background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;">
                          <p style="font-size:14px; font-weight: bold;   margin: 0px !important;"> $$request_name$$ (due <span> $$due_date$$ </span>)
                          </p>
                          <p style="font-size:14px;   margin: 0px !important;"> $$request_description$$ </p>
                          <p style="font-size:14px;   margin: 0px !important;">Requestor:
                              <span> $$requestor_name$$ </span>&nbsp;<span> $$requestor_email$$ </span></p>
                          <p><span style="display:inline-block; float:left; font-size:14px;   margin: 0px !important;">Student Updates: </span><span> $$student_update_count$$</span>
                              <span style="float:right;"> <a
                                      style="width: 65px; height:20px; background-color:#ccc; text-align: center; display:inline-block; float: right; border: 1px solid #ccc; margin: 2px; text-decoration: none; color:#000;"
                                      href="$$faculty_au_submission_page$$">Update</a></span>
                          </p>

                      </td>
                  </tr>
                  <tr style="background:#fff;border-collapse:collapse;">
                      <td>$$custom_message$$</td>
                  </tr>
                  <tr style="background:#fff;border-collapse:collapse;">
                      <td><p>Thank you.</br></p>
                          <p><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$$Skyfactor_Mapworks_logo$$"/><br/></p>
                      </td>
                  </tr>
                  <tr style="background:#fff;border-collapse:collapse;">
                      <td>This email is an auto-generated message.

                          Replies to automated messages are not monitored.
                      </td>
                  </tr>
                  </tbody>
              </table>
              </body>
              </html>'
WHERE
    `email_template_id` = @emtid;