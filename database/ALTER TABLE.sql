ALTER TABLE `Roger`.`profiles` 
    ADD `profile_picture` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'default.jpg';

ALTER TABLE `Roger`.`followings`
	ADD follow_confirmed BOOL NOT NULL DEFAULT 1
;

