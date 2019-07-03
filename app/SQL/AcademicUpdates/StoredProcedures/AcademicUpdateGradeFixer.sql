DROP PROCEDURE IF EXISTS `Academic_Update_Grade_Fixer`;
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Academic_Update_Grade_Fixer`()
  BEGIN
    UPDATE academic_update SET grade = 'F', modified_by = -25 WHERE grade = 'F/No Pass';
    UPDATE academic_update SET grade = 'P', modified_by = -25 WHERE grade = 'Pass';
  END