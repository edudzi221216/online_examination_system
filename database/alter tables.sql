ALTER TABLE `tbl_users` CHANGE `email` `email` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `contact` `contact` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `last_login` `last_login` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `login_device` `login_device` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `tbl_assessment_records` ADD `user_answers` JSON NULL DEFAULT NULL AFTER `score`;

-- TRUNCATE TABLE `tbl_examinations`;
ALTER TABLE `tbl_examinations` CHANGE `date` `date` DATE NOT NULL, CHANGE `end_exam_date` `end_exam_date` DATE NULL DEFAULT NULL;
ALTER TABLE `tbl_teacher` ADD `contact` VARCHAR(30) NULL AFTER `email`;