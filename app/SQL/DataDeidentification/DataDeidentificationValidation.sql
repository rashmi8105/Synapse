#--Date: 2017-10-27
#--Author: Hai Deng
#--This is validation script for de-identifying one organization data.

SELECT
    COUNT(DISTINCT organization_id) organization_count,
    Concat('person:', COUNT(DISTINCT p.id)) record_count
FROM
    person p
        JOIN
    organization o ON o.id = p.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND INSTR(p.username, 'mailinator') > 0
UNION ALL SELECT
    COUNT(DISTINCT organization_id) organization_count,
    Concat('contact_info:', COUNT(DISTINCT ci.id)) record_count
FROM
    synapse.contact_info ci
        JOIN
    person_contact_info pci ON ci.id = pci.contact_id
        JOIN
    person p ON p.id = pci.person_id
        JOIN
    organization o ON o.id = p.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND (INSTR(ci.home_phone, '555-555-') > 0 OR INSTR(ci.office_phone, '555-555-') > 0
        OR INSTR(ci.alternate_mobile, '555-555-') > 0 OR INSTR(ci.alternate_mobile, '555-555-') > 0
        OR INSTR(ci.primary_email, 'MapworksTestingUser') > 0 OR INSTR(ci.address_1, '1234 Somewhere Street') > 0
        OR INSTR(ci.city, 'Some City') > 0 OR INSTR(ci.zip, '12345') > 0)
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('organization-logoFileNameNull:', COUNT(DISTINCT o.id)) record_count
FROM
    organization o
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND logo_file_name IS NULL
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('organization_lang-name(Beta Org):', COUNT(DISTINCT o.id)) record_count
FROM
    organization o
        JOIN
    organization_lang ol ON ol.organization_id = o.id
        JOIN
    person p ON o.id = p.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND INSTR(organization_name, 'Synapse Beta Org') > 0
UNION ALL SELECT
    COUNT(DISTINCT o.id), Concat('organization_lang-nickname(Beta):', COUNT(DISTINCT o.id)) record_count
FROM
    organization o
        JOIN
    organization_lang ol ON ol.organization_id = o.id
        JOIN
    person p ON o.id = p.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND INSTR(nick_name, 'Beta') > 0
UNION ALL SELECT
    COUNT(DISTINCT o.id), Concat('org_person_student-photoUrlNull:', COUNT(DISTINCT ops.person_id)) record_count
FROM
    organization o
        JOIN
    org_person_student ops ON ops.organization_id = o.id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND photo_url
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('person-externalID=id:',COUNT(DISTINCT p.id) )record_count
FROM
    organization o
        JOIN
    person p ON o.id = p.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND p.external_id = concat('EXID',p.id)
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('contacts:',COUNT(DISTINCT c.id)) record_count
FROM
    organization o
        JOIN
    contacts c ON o.id = c.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(note, 7) = 'This is a note'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('referrals:',COUNT(DISTINCT r.id)) record_count
FROM
    organization o
        JOIN
    referrals r ON o.id = r.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(note, 7) = 'This is a note'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('note:',COUNT(DISTINCT n.id)) record_count
FROM
    organization o
        JOIN
    note n ON o.id = n.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(note, 7) = 'This is a note'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('academic_update_request:',COUNT(DISTINCT aur.id)) record_count
FROM
    organization o
        JOIN
    academic_update_request aur ON o.id = aur.org_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(name, 7) = 'Some AU'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('org_announcements_lang:',COUNT(DISTINCT oal.id)) record_count
FROM
    organization o
        JOIN
    org_announcements oa ON o.id = oa.org_id
        JOIN
    org_announcements_lang oal ON oal.org_announcements_id = oa.id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND INSTR(message, 'I have an announcement') > 0
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('survey_response-charValue:',COUNT(DISTINCT sr.id)) record_count
FROM
    organization o
        JOIN
    survey_response sr ON o.id = sr.org_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(char_value, 4) = 'Boba'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('survey_response-charMaxValue:',COUNT(DISTINCT sr.id)) record_count
FROM
    organization o
        JOIN
    survey_response sr ON o.id = sr.org_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(charmax_value, 4) = 'Boba'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('organization-externalId:',COUNT(DISTINCT og.id)) record_count
FROM
    organization o
        JOIN
    org_group og ON o.id = og.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(og.external_id, 4) = 'EXID'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('Teams-name:',COUNT(DISTINCT T.id)) record_count
FROM
    organization o
        JOIN
    Teams T ON o.id = T.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(team_name, 9) = 'Test Team'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('org_courses-location:',COUNT(DISTINCT oc.id)) record_count
FROM
    organization o
        JOIN
    org_courses oc ON o.id = oc.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND location = 'Dagobah'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('org_metadata:',COUNT(DISTINCT om.id)) record_count
FROM
    organization o
        JOIN
    org_metadata om ON o.id = om.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(meta_key, 3) = 'ORG'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('org_question:',COUNT(DISTINCT oq.id)) record_count
FROM
    organization o
        JOIN
    org_question oq ON o.id = oq.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND LEFT(question_text, 4) = 'Orga'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('person-passwordPassword$1:',COUNT(DISTINCT p.id)) record_count
FROM
    organization o
        JOIN
    person p ON o.id = p.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND password = '$2a$10$it/3J8lIQeih9xDzxXbViO9Oq4xMN0ka8BngIgGsupPCNDbAzIgVG'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('wess_link-wessAdminLinkNull:',COUNT(DISTINCT wl.id)) record_count
FROM
    organization o
        JOIN
    wess_link wl ON o.id = wl.org_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND wess_admin_link IS NULL
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('organization-ftp:',COUNT(DISTINCT o.id)) record_count
FROM
    organization o
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND RIGHT(ftp_password, 1) = '1' > 0
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('person_org_metadata:',COUNT(DISTINCT pom.id)) record_count
FROM
	organization o 
    JOIN
	person p ON p.organization_id = o.id
    JOIN
    person_org_metadata pom ON p.id = pom.person_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND metadata_value = 'secure_info'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('org_campus_resource:',COUNT(DISTINCT ocr.id)) record_count
FROM
	organization o
    JOIN
    org_campus_resource ocr ON o.id = ocr.org_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND phone = '555-5555' AND name = 'name'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('org_person_student_survey_link:',COUNT(opss.id)) record_count
FROM
	organization o
    JOIN
    org_person_student_survey_link opss ON o.id = opss.org_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND survey_link = 'www.google.com'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('org_question_response:',COUNT(oqr.id)) record_count
FROM
	organization o
    JOIN
    org_question_response oqr ON o.id = oqr.org_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND (char_value = 'secure info' OR charmax_value = 'secure info')
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('person-title:',COUNT(p.id)) record_count
FROM
	organization o
    JOIN
    person p ON o.id = p.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND title = 'Title Masked'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('org_question_options:',COUNT(oqp.id)) record_count
FROM
	organization o
    JOIN
    org_question_options oqp ON o.id = oqp.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND right(option_name, 7) = ' option'
UNION ALL SELECT
    COUNT(DISTINCT o.id) organization_count, Concat('org_static_list:',COUNT(osl.id)) record_count
FROM
	organization o
    JOIN
    org_static_list osl ON o.id = osl.organization_id
WHERE
    o.status = 'I' AND is_mock = 'n'
        AND (right(name, 5) = ' name' OR right(description, 12) = ' description')
;
        
