DELIMITER **

DROP EVENT IF EXISTS `Survey_Risk_Event` **

CREATE DEFINER = `synapsemaster`@`%` EVENT `Survey_Risk_Event` ON SCHEDULE EVERY 15 MINUTE STARTS '2015-10-13 00:01:30' ENABLE DO
BEGIN
	SET @startTime = NOW();
	CALL Academic_Update_Grade_Fixer();
	CALL survey_data_transfer();
	CALL isq_data_transfer();
	CALL Factor_Calc(DATE_ADD(NOW(), INTERVAL 140 second), 60);
	CALL Report_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 60);
	CALL Intent_Leave_Calc();
	CALL Talking_Point_Calc(DATE_ADD(NOW(), INTERVAL 50 second), 100);
	CALL risk_calculation_V2(DATE_ADD(@startTime, INTERVAL 14 minute), 30);
END**
