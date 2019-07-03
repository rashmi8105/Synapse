INSERT INTO `synapse`.`question_type` (`id`) VALUES ('1');
INSERT INTO `synapse`.`question_type` (`id`) VALUES ('2');
INSERT INTO `synapse`.`question_type` (`id`) VALUES ('3');


INSERT INTO `synapse`.`question_type_lang` (`question_type_id`, `lang_id`, `description`) VALUES ('1', '1', 'Question Type 1');
INSERT INTO `synapse`.`question_type_lang` (`question_type_id`, `lang_id`, `description`) VALUES ('2', '1', 'Question Type 2');
INSERT INTO `synapse`.`question_type_lang` (`question_type_id`, `lang_id`, `description`) VALUES ('3', '1', 'Question Type 3');


INSERT INTO `synapse`.`question_category` (`id`) VALUES ('1');

INSERT INTO `synapse`.`question_category_lang` (`question_category_id`, `lang_id`, `description`) VALUES ('1', '1', 'Question Category 1');


INSERT INTO `synapse`.`org_question` (`organization_id`, `question_type_id`, `question_category_id`, `question_key`, `question_text`) VALUES ('1', '1', '1', 'q1', 'What is your name ?');
INSERT INTO `synapse`.`org_question` (`organization_id`, `question_type_id`, `question_category_id`, `question_key`, `question_text`) VALUES ('1', '2', '1', 'q1', 'What is your favorite color ?');
