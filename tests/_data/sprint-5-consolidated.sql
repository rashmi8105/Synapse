INSERT INTO `feature_master` (id,created_by,created_at,modified_by,modified_at,deleted_by,deleted_at) VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL),(2,NULL,NULL,NULL,NULL,NULL,NULL),(3,NULL,NULL,NULL,NULL,NULL,NULL),(4,NULL,NULL,NULL,NULL,NULL,NULL),(5,NULL,NULL,NULL,NULL,NULL,NULL),(6,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT INTO `feature_master_lang` (id,feature_master_id,lang_id,feature_name,created_by,created_at,modified_by,modified_at,deleted_by,deleted_at) VALUES (1,1,1,'Referrals',NULL,NULL,NULL,NULL,NULL,NULL),(2,2,1,'Notes',NULL,NULL,NULL,NULL,NULL,NULL),(3,3,1,'Log Contacts',NULL,NULL,NULL,NULL,NULL,NULL),(4,4,1,'Booking',NULL,NULL,NULL,NULL,NULL,NULL),(5,5,1,'Student Referrals',NULL,NULL,NULL,NULL,NULL,NULL),(6,6,1,'Reason Routing',NULL,NULL,NULL,NULL,NULL,NULL);


/*
--  activity_category
*/
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic Issues',NULL,NULL,NULL);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (2,NULL,NULL,NULL,NULL,NULL,NULL,'Personal Issues',NULL,NULL,NULL);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (3,NULL,NULL,NULL,NULL,NULL,NULL,'Financial Issues',NULL,NULL,NULL);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (4,NULL,NULL,NULL,NULL,NULL,NULL,'MAP-Works Issues',NULL,NULL,NULL);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (19,NULL,NULL,NULL,NULL,NULL,NULL,'Class attendance concern',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (20,NULL,NULL,NULL,NULL,NULL,NULL,'Class attendance positive',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (21,NULL,NULL,NULL,NULL,NULL,NULL,'Academic performance concern',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (22,NULL,NULL,NULL,NULL,NULL,NULL,'Academic performance positive',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (23,NULL,NULL,NULL,NULL,NULL,NULL,'Registration positive',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (24,NULL,NULL,NULL,NULL,NULL,NULL,'Registration concern',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (25,NULL,NULL,NULL,NULL,NULL,NULL,'Academic skills',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (26,NULL,NULL,NULL,NULL,NULL,NULL,'Academic major exploration/selection',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (27,NULL,NULL,NULL,NULL,NULL,NULL,'Academic action meeting',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (28,NULL,NULL,NULL,NULL,NULL,NULL,'Academic success planning',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (29,NULL,NULL,NULL,NULL,NULL,NULL,'Missing required meetings / activities',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (30,NULL,NULL,NULL,NULL,NULL,NULL,'Attended meeting / activities',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (31,NULL,NULL,NULL,NULL,NULL,NULL,'Other academic concerns',NULL,NULL,1);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (32,NULL,NULL,NULL,NULL,NULL,NULL,'Living environment concern',NULL,NULL,2);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (33,NULL,NULL,NULL,NULL,NULL,NULL,'Living environment positive',NULL,NULL,2);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (34,NULL,NULL,NULL,NULL,NULL,NULL,'Relationships concern',NULL,NULL,2);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (35,NULL,NULL,NULL,NULL,NULL,NULL,'Relationships positive',NULL,NULL,2);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (36,NULL,NULL,NULL,NULL,NULL,NULL,'Social connections concern',NULL,NULL,2);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (37,NULL,NULL,NULL,NULL,NULL,NULL,'Social connections positive',NULL,NULL,2);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (38,NULL,NULL,NULL,NULL,NULL,NULL,'Medical / mental health',NULL,NULL,2);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (39,NULL,NULL,NULL,NULL,NULL,NULL,'Short term',NULL,NULL,3);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (40,NULL,NULL,NULL,NULL,NULL,NULL,'Long term',NULL,NULL,3);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (41,NULL,NULL,NULL,NULL,NULL,NULL,'Positive financial',NULL,NULL,3);
INSERT INTO `activity_category` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`short_name`,`is_active`,`display_seq`,`parent_activity_category_id`) VALUES (42,NULL,NULL,NULL,NULL,NULL,NULL,'MAP-Works related issues',NULL,NULL,4);

/*
-- activity_category_lang
*/
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic Issues');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Personal Issues');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'Financial Issues');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (4,4,1,NULL,NULL,NULL,NULL,NULL,NULL,'MAP-Works Issues');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (5,19,1,NULL,NULL,NULL,NULL,NULL,NULL,'Class attendance concern ');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (6,20,1,NULL,NULL,NULL,NULL,NULL,NULL,'Class attendance positive');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (7,21,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic performance concern ');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (8,22,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic performance positive');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (9,23,1,NULL,NULL,NULL,NULL,NULL,NULL,'Registration positive ');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (10,24,1,NULL,NULL,NULL,NULL,NULL,NULL,'Registration concern');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (11,25,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic skills');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (12,26,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic major exploration/selection');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (13,27,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic action meeting');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (14,28,1,NULL,NULL,NULL,NULL,NULL,NULL,'Academic success planning');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (15,29,1,NULL,NULL,NULL,NULL,NULL,NULL,'Missing required meetings / activities');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (16,30,1,NULL,NULL,NULL,NULL,NULL,NULL,'Attended meeting / activities');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (17,31,1,NULL,NULL,NULL,NULL,NULL,NULL,'Other academic concerns');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (18,32,1,NULL,NULL,NULL,NULL,NULL,NULL,'Living environment concern');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (19,33,1,NULL,NULL,NULL,NULL,NULL,NULL,'Living environment positive');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (20,34,1,NULL,NULL,NULL,NULL,NULL,NULL,'Relationships concern');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (21,35,1,NULL,NULL,NULL,NULL,NULL,NULL,'Relationships positive');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (22,36,1,NULL,NULL,NULL,NULL,NULL,NULL,'Social connections concern');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (23,37,1,NULL,NULL,NULL,NULL,NULL,NULL,'Social connections positive');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (24,38,1,NULL,NULL,NULL,NULL,NULL,NULL,'Medical / mental health');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (25,39,1,NULL,NULL,NULL,NULL,NULL,NULL,'Short term');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (26,40,1,NULL,NULL,NULL,NULL,NULL,NULL,'Long term');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (27,41,1,NULL,NULL,NULL,NULL,NULL,NULL,'Positive financial');
INSERT INTO `activity_category_lang` (`id`,`activity_category_id`,`language_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (28,42,1,NULL,NULL,NULL,NULL,NULL,NULL,'MAP-Works related issues');


/*
-- contact_types
*/
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,1,1,NULL);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (2,NULL,NULL,NULL,NULL,NULL,NULL,1,2,NULL);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (3,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (4,NULL,NULL,NULL,NULL,NULL,NULL,1,2,1);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (5,NULL,NULL,NULL,NULL,NULL,NULL,1,3,1);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (6,NULL,NULL,NULL,NULL,NULL,NULL,1,4,1);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (7,NULL,NULL,NULL,NULL,NULL,NULL,1,5,1);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (8,NULL,NULL,NULL,NULL,NULL,NULL,1,6,1);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (9,NULL,NULL,NULL,NULL,NULL,NULL,1,7,1);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (10,NULL,NULL,NULL,NULL,NULL,NULL,1,8,1);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (11,NULL,NULL,NULL,NULL,NULL,NULL,1,9,1);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (12,NULL,NULL,NULL,NULL,NULL,NULL,1,1,2);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (13,NULL,NULL,NULL,NULL,NULL,NULL,1,2,2);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (14,NULL,NULL,NULL,NULL,NULL,NULL,1,3,2);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (15,NULL,NULL,NULL,NULL,NULL,NULL,1,4,2);
INSERT INTO `contact_types` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`is_active`,`display_seq`,`parent_contact_types_id`) VALUES (16,NULL,NULL,NULL,NULL,NULL,NULL,1,5,2);

/*
-- contact_types_lang
*/
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Interaction');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (2,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Non-interaction');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (3,3,1,NULL,NULL,NULL,NULL,NULL,NULL,'In person meeting');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (4,4,1,NULL,NULL,NULL,NULL,NULL,NULL,'Phone conversation');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (5,5,1,NULL,NULL,NULL,NULL,NULL,NULL,'Email from student');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (6,6,1,NULL,NULL,NULL,NULL,NULL,NULL,'Phone message received from student');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (7,7,1,NULL,NULL,NULL,NULL,NULL,NULL,'Message received via social networking site');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (8,8,1,NULL,NULL,NULL,NULL,NULL,NULL,'Written communication from student');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (9,9,1,NULL,NULL,NULL,NULL,NULL,NULL,'Group meeting');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (10,10,1,NULL,NULL,NULL,NULL,NULL,NULL,'Appointment');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (11,11,1,NULL,NULL,NULL,NULL,NULL,NULL,'Other interaction');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (12,12,1,NULL,NULL,NULL,NULL,NULL,NULL,'Email to student');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (13,13,1,NULL,NULL,NULL,NULL,NULL,NULL,'Phone message left for student');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (14,14,1,NULL,NULL,NULL,NULL,NULL,NULL,'Message sent via social networking site');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (15,15,1,NULL,NULL,NULL,NULL,NULL,NULL,'Written communication to student');
INSERT INTO `contact_types_lang` (`id`,`contact_types_id`,`language_master_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`description`) VALUES (16,16,1,NULL,NULL,NULL,NULL,NULL,NULL,'Other noninteraction');

/*
-- datablock_ui

*/
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,'ProfileBlockUI','ProfileBlockUI');
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (11,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `datablock_ui` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`ui_feature_name`) VALUES (12,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*
-- datablock_master
*/
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,'profile');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (2,1,NULL,NULL,NULL,NULL,NULL,NULL,'profile');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (3,1,NULL,NULL,NULL,NULL,NULL,NULL,'profile');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (4,1,NULL,NULL,NULL,NULL,NULL,NULL,'profile');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (5,1,NULL,NULL,NULL,NULL,NULL,NULL,'profile');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (6,1,NULL,NULL,NULL,NULL,NULL,NULL,'profile');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (7,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (8,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (9,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (10,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey');
INSERT INTO `datablock_master` (`id`,`datablock_ui_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`block_type`) VALUES (11,1,NULL,NULL,NULL,NULL,NULL,NULL,'survey');


/*
-- datablock_master_lang
*/
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Demographic');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (2,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Financial');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (3,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'High School Grades');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (4,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Contact Information');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (5,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Personal Information');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (6,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Student Name');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (7,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic - Behaviors');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (8,8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic - Content Skills');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (9,9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic - Education Plan');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (10,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Academic - Mindset');
INSERT INTO `datablock_master_lang` (`id`,`datablock_id`,`lang_id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`datablock_desc`) VALUES (11,11,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Intent to Leave');





/*
-- ebi_config
*/
INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (13,NULL,NULL,NULL,NULL,NULL,NULL,'StaffDashboard_AppointmentPage','http://synapse-uat.mnv-tech.com/#/stff');


INSERT INTO `ebi_config` (`id`,`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (14,NULL,NULL,NULL,NULL,NULL,NULL,'StudentDashboard_AppointmentPage','http://synapse-uat.mnv-tech.com/#/viewstudentcalendars');