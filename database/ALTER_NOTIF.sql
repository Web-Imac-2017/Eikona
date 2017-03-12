ALTER TABLE `Roger`.`notifications`
	CHANGE COLUMN `profile_id` `profile_target_id` INT NOT NULL;

ALTER TABLE `Roger`.`notifications`
	ADD `profile_id` INT(11) NOT NULL;