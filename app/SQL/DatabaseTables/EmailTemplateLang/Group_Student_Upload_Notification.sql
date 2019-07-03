SET @templateId := (select id from email_template where email_key = 'Group_Student_Upload_Notification');

UPDATE email_template_lang
SET body = '
<!DOCTYPE html>
    <head>
        <style>
            body {
                background: none repeat scroll 0 0 #f4f4f4;
            }
            table {
                padding: 21px;
                width: 799px;
                font-family: helvetica, arial, verdana, san-serif;
                font-size: 13px;
                color: #333;
            }
        </style>
    </head>
    <body>
        <table cellpadding="10" style="background:#eeeeee;" cellspacing="0">
            <tbody>
                <tr style="background:#fff;border-collapse:collapse;">
                    <td>Dear $userFirstName :</td>
                </tr>
                <tr style="background:#fff;border-collapse:collapse;">
                    <td>Your student group upload has finished importing. $downloadFailedLogFile </td>
                </tr>
                <tr style="background:#fff;border-collapse:collapse;">
                    <td>Thank you from the Skyfactor team.</td>
                </tr>
                <tr>
                    <td><img width="307" height="89" alt="Skyfactor Mapworks Logo" src="$skyfactorMapworksLogo"/></td>
                </tr>
                <tr style="background:#fff;border-collapse:collapse;">
                    <td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
'
WHERE email_template_id = @templateId;