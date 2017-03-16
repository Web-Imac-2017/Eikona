-- MySQL Script generated by MySQL Workbench
-- Thu Mar 16 16:14:27 2017
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema Roger
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema Roger
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `Roger` DEFAULT CHARACTER SET utf8 ;
USE `Roger` ;

-- -----------------------------------------------------
-- Table `Roger`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`users` ;

CREATE TABLE IF NOT EXISTS `Roger`.`users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(45) NOT NULL,
  `user_email` VARCHAR(100) NOT NULL,
  `user_passwd` VARCHAR(256) NOT NULL COMMENT '				',
  `user_register_time` INT NOT NULL,
  `user_last_activity` INT NOT NULL,
  `user_moderator` TINYINT(1) NOT NULL DEFAULT 0,
  `user_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `user_activated` TINYINT(1) NOT NULL DEFAULT 0,
  `user_code` VARCHAR(20) NULL,
  `user_key` VARCHAR(45) NULL,
  `userscol` VARCHAR(45) NULL DEFAULT NULL COMMENT '« UUID() »',
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `user_email_UNIQUE` (`user_email` ASC),
  UNIQUE INDEX `userscol_UNIQUE` (`userscol` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`profiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`profiles` ;

CREATE TABLE IF NOT EXISTS `Roger`.`profiles` (
  `profile_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `profile_name` VARCHAR(45) NOT NULL,
  `profile_desc` VARCHAR(255) NULL DEFAULT NULL,
  `profile_create_time` INT NOT NULL,
  `profile_views` INT NOT NULL DEFAULT 0,
  `profile_private` TINYINT(1) NOT NULL DEFAULT 0,
  `profile_picture` VARCHAR(150) NOT NULL DEFAULT 'default.jpg',
  `profile_key` VARCHAR(36) NULL COMMENT '« UUID() » ',
  PRIMARY KEY (`profile_id`, `user_id`),
  INDEX `USER_ID` (`user_id` ASC),
  UNIQUE INDEX `profile_name_UNIQUE` (`profile_name` ASC),
  CONSTRAINT `USER_ID`
    FOREIGN KEY (`user_id`)
    REFERENCES `Roger`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`posts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`posts` ;

CREATE TABLE IF NOT EXISTS `Roger`.`posts` (
  `post_id` INT NOT NULL AUTO_INCREMENT,
  `profile_id` INT NOT NULL,
  `post_type` VARCHAR(5) NOT NULL COMMENT 'photo ou video',
  `post_extension` VARCHAR(5) NOT NULL,
  `post_description` VARCHAR(255) NULL DEFAULT NULL,
  `post_publish_time` INT NOT NULL,
  `post_edit_time` INT NOT NULL,
  `post_state` INT NOT NULL DEFAULT 1 COMMENT '1 - Post publié, pas de soucis\n2 - Post en modération, n’est pas visible',
  `post_filter` VARCHAR(10) NULL DEFAULT NULL,
  `post_geo_lat` VARCHAR(45) NULL DEFAULT NULL,
  `post_geo_lng` VARCHAR(45) NULL DEFAULT NULL,
  `post_geo_name` VARCHAR(100) NULL DEFAULT NULL,
  `post_allow_comments` TINYINT(1) NOT NULL DEFAULT 1,
  `post_approved` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 - En attente de première validation\n1 - Post approuvé lors de la première validation',
  PRIMARY KEY (`post_id`, `profile_id`),
  INDEX `PROFILE_OWNER_idx` (`profile_id` ASC),
  CONSTRAINT `PROFILE_OWNER`
    FOREIGN KEY (`profile_id`)
    REFERENCES `Roger`.`profiles` (`profile_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`tags` ;

CREATE TABLE IF NOT EXISTS `Roger`.`tags` (
  `tag_name` VARCHAR(40) NOT NULL,
  `post_id` INT NOT NULL,
  `use_time` INT NOT NULL,
  PRIMARY KEY (`tag_name`, `post_id`),
  UNIQUE INDEX `UNIQUE_TAG` (`tag_name` ASC, `post_id` ASC)  COMMENT 'This unique key is used to make sure a post cannot receive the same tag multiple times.',
  INDEX `POST_TAGGED_idx` (`post_id` ASC),
  CONSTRAINT `POST_TAGGED`
    FOREIGN KEY (`post_id`)
    REFERENCES `Roger`.`posts` (`post_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`post_views`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`post_views` ;

CREATE TABLE IF NOT EXISTS `Roger`.`post_views` (
  `profile_id` INT NOT NULL,
  `post_id` INT NOT NULL,
  `view_time` INT NOT NULL,
  PRIMARY KEY (`profile_id`, `post_id`, `view_time`),
  INDEX `POST_idx` (`post_id` ASC),
  CONSTRAINT `POST`
    FOREIGN KEY (`post_id`)
    REFERENCES `Roger`.`posts` (`post_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `VIEWER_ID`
    FOREIGN KEY (`profile_id`)
    REFERENCES `Roger`.`profiles` (`profile_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`post_likes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`post_likes` ;

CREATE TABLE IF NOT EXISTS `Roger`.`post_likes` (
  `profile_id` INT NOT NULL,
  `post_id` INT NOT NULL,
  `like_time` INT NOT NULL,
  PRIMARY KEY (`profile_id`, `post_id`),
  INDEX `POST_LIKED_idx` (`post_id` ASC),
  UNIQUE INDEX `ONLY_ONE_LIKE` (`profile_id` ASC, `post_id` ASC)  COMMENT 'This key ensure a same profile cannot like a post multiple times',
  CONSTRAINT `LIKER_ID`
    FOREIGN KEY (`profile_id`)
    REFERENCES `Roger`.`profiles` (`profile_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `POST_LIKED`
    FOREIGN KEY (`post_id`)
    REFERENCES `Roger`.`posts` (`post_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`comments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`comments` ;

CREATE TABLE IF NOT EXISTS `Roger`.`comments` (
  `comment_id` INT NOT NULL AUTO_INCREMENT,
  `profile_id` INT NOT NULL,
  `post_id` INT NOT NULL,
  `comment_text` TEXT NOT NULL,
  `comment_time` INT NOT NULL,
  PRIMARY KEY (`comment_id`, `profile_id`, `post_id`),
  INDEX `POST_COMMENTED_idx` (`post_id` ASC),
  INDEX `COMMENTER_ID_idx` (`profile_id` ASC),
  CONSTRAINT `COMMENTER_ID`
    FOREIGN KEY (`profile_id`)
    REFERENCES `Roger`.`profiles` (`profile_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `POST_COMMENTED`
    FOREIGN KEY (`post_id`)
    REFERENCES `Roger`.`posts` (`post_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`comment_likes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`comment_likes` ;

CREATE TABLE IF NOT EXISTS `Roger`.`comment_likes` (
  `profile_id` INT NOT NULL,
  `comment_id` INT NOT NULL,
  `like_time` INT NOT NULL,
  PRIMARY KEY (`profile_id`, `comment_id`),
  UNIQUE INDEX `ONLY_ONE_LIKE` (`profile_id` ASC, `comment_id` ASC)  COMMENT 'This key is here to ensure that nobody is linking the same comment twice.',
  INDEX `COMMENT_LIKED_idx` (`comment_id` ASC),
  CONSTRAINT `COMMENT_LIKED`
    FOREIGN KEY (`comment_id`)
    REFERENCES `Roger`.`comments` (`comment_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `COMMENT_LIKER_ID`
    FOREIGN KEY (`profile_id`)
    REFERENCES `Roger`.`profiles` (`profile_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`followings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`followings` ;

CREATE TABLE IF NOT EXISTS `Roger`.`followings` (
  `follower_id` INT NOT NULL,
  `followed_id` INT NOT NULL,
  `following_time` INT NOT NULL,
  `follower_subscribed` TINYINT(1) NOT NULL DEFAULT 0,
  `follow_confirmed` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`follower_id`, `followed_id`),
  UNIQUE INDEX `FOLLOW_JUST_ONE_TIME` (`follower_id` ASC, `followed_id` ASC),
  INDEX `FOLLOWED_ID_idx` (`followed_id` ASC),
  CONSTRAINT `FOLLOWER_ID`
    FOREIGN KEY (`follower_id`)
    REFERENCES `Roger`.`profiles` (`profile_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FOLLOWED_ID`
    FOREIGN KEY (`followed_id`)
    REFERENCES `Roger`.`profiles` (`profile_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`blocked`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`blocked` ;

CREATE TABLE IF NOT EXISTS `Roger`.`blocked` (
  `blocker_id` INT NOT NULL,
  `blocked_id` INT NOT NULL,
  `block_time` INT NOT NULL,
  PRIMARY KEY (`blocker_id`, `blocked_id`),
  INDEX `BLOCKED_ID_idx` (`blocked_id` ASC),
  UNIQUE INDEX `ONLY_ONE_BLOCK` (`blocker_id` ASC, `blocked_id` ASC),
  CONSTRAINT `BLOCKER_ID`
    FOREIGN KEY (`blocker_id`)
    REFERENCES `Roger`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `BLOCKED_ID`
    FOREIGN KEY (`blocked_id`)
    REFERENCES `Roger`.`users` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`reports`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`reports` ;

CREATE TABLE IF NOT EXISTS `Roger`.`reports` (
  `report_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `post_id` INT NOT NULL,
  `report_comment` TEXT NULL DEFAULT NULL,
  `report_status` INT NOT NULL DEFAULT 0,
  `report_handler` INT NULL DEFAULT NULL,
  `report_result` TEXT NULL DEFAULT NULL,
  `time_state_change` INT NULL DEFAULT NULL,
  PRIMARY KEY (`report_id`, `user_id`, `post_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`banned_emails`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`banned_emails` ;

CREATE TABLE IF NOT EXISTS `Roger`.`banned_emails` (
  `banned_email` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`banned_email`),
  UNIQUE INDEX `banned_email_UNIQUE` (`banned_email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`PARAMS`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`PARAMS` ;

CREATE TABLE IF NOT EXISTS `Roger`.`PARAMS` (
  `PARAM_NAME` VARCHAR(255) NOT NULL COMMENT 'Paramètre à utiliser sur le site\n	- Nombre maximum de profils\n	- Messages par défaut\n	- etc.',
  `PARAM_VALUE` TEXT NULL DEFAULT NULL,
  `PARAM_edit_time` INT NOT NULL COMMENT '	',
  `PARAM_edit_user_id` INT NULL,
  PRIMARY KEY (`PARAM_NAME`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`notifications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`notifications` ;

CREATE TABLE IF NOT EXISTS `Roger`.`notifications` (
  `notif_id` INT NOT NULL AUTO_INCREMENT,
  `profile_target_id` INT NOT NULL,
  `notif_type` VARCHAR(5) NOT NULL COMMENT 'Type de notification\n- Nouvel abonné\n- Like\n- …',
  `notif_target` INT NULL,
  `notif_time` INT NOT NULL,
  `notif_seen` TINYINT(1) NOT NULL DEFAULT 0,
  `profile_id` INT NULL,
  PRIMARY KEY (`notif_id`, `profile_target_id`),
  INDEX `PROFILE_NOTIFIED_idx` (`profile_target_id` ASC),
  CONSTRAINT `PROFILE_NOTIFIED`
    FOREIGN KEY (`profile_target_id`)
    REFERENCES `Roger`.`profiles` (`profile_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Roger`.`banned_words`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Roger`.`banned_words` ;

CREATE TABLE IF NOT EXISTS `Roger`.`banned_words` (
  `word` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`word`),
  UNIQUE INDEX `word_UNIQUE` (`word` ASC))
ENGINE = InnoDB;

USE `Roger` ;

-- -----------------------------------------------------
-- Placeholder table for view `Roger`.`posts_bonus`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Roger`.`posts_bonus` (`post_id` INT, `post_bonus` INT);

-- -----------------------------------------------------
-- Placeholder table for view `Roger`.`comments_score`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Roger`.`comments_score` (`post_id` INT, `comment_id` INT, `comment_score` INT);

-- -----------------------------------------------------
-- Placeholder table for view `Roger`.`likes_score`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Roger`.`likes_score` (`post_id` INT, `like_score` INT);

-- -----------------------------------------------------
-- Placeholder table for view `Roger`.`views_score`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Roger`.`views_score` (`post_id` INT, `view_score` INT);

-- -----------------------------------------------------
-- Placeholder table for view `Roger`.`pop_score`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Roger`.`pop_score` (`post_id` INT, `post_score` INT);

-- -----------------------------------------------------
-- View `Roger`.`posts_bonus`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `Roger`.`posts_bonus` ;
DROP TABLE IF EXISTS `Roger`.`posts_bonus`;
USE `Roger`;
CREATE  OR REPLACE VIEW `posts_bonus` AS
SELECT
	post_id,
    IF (UNIX_TIMESTAMP() - post_publish_time < 24*3600, 
        TRUNCATE(((24*3600 - (UNIX_TIMESTAMP() - post_publish_time)) / LOG(UNIX_TIMESTAMP() - post_publish_time)) * 0.0155, 2)
        , 0)
		 AS post_bonus
FROM
	posts
WHERE 
	post_publish_time > 0 AND
	post_state = 1
;

-- -----------------------------------------------------
-- View `Roger`.`comments_score`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `Roger`.`comments_score` ;
DROP TABLE IF EXISTS `Roger`.`comments_score`;
USE `Roger`;
CREATE  OR REPLACE VIEW `comments_score` AS
SELECT
	comments.post_id AS post_id,
    comments.comment_id AS comment_id,
	IF (UNIX_TIMESTAMP() - comment_time < 96*3600, 
        TRUNCATE(((96*3600 - (UNIX_TIMESTAMP() - post_publish_time)) / LOG(UNIX_TIMESTAMP() - post_publish_time)) * 0.0155, 2)
        , 0)
		 AS comment_score
FROM
	comments
JOIN posts ON
	posts.post_id = comments.post_id
WHERE 
	post_publish_time > 0 AND
	post_state = 1
;

-- -----------------------------------------------------
-- View `Roger`.`likes_score`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `Roger`.`likes_score` ;
DROP TABLE IF EXISTS `Roger`.`likes_score`;
USE `Roger`;
CREATE  OR REPLACE VIEW `likes_score` AS
SELECT
	post_likes.post_id AS post_id,
	IF (UNIX_TIMESTAMP() - like_time < 72*3600, 
        TRUNCATE(((72*3600 - (UNIX_TIMESTAMP() - post_publish_time)) / LOG(UNIX_TIMESTAMP() - post_publish_time)) * 0.0155, 2)
        , 0)
		 AS like_score
FROM
	post_likes
JOIN posts ON
	posts.post_id = post_likes.post_id
WHERE 
	post_publish_time > 0 AND
	post_state = 1
;

-- -----------------------------------------------------
-- View `Roger`.`views_score`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `Roger`.`views_score` ;
DROP TABLE IF EXISTS `Roger`.`views_score`;
USE `Roger`;
CREATE  OR REPLACE VIEW `views_score` AS
SELECT
	post_views.post_id AS post_id,
	IF (UNIX_TIMESTAMP() - view_time < 48*3600, 
        TRUNCATE(((48*3600 - (UNIX_TIMESTAMP() - post_publish_time)) / LOG(UNIX_TIMESTAMP() - post_publish_time)) * 0.0155, 2)
        , 0)
		 AS view_score
FROM
	post_views
JOIN posts ON
	posts.post_id = post_views.post_id
WHERE 
	post_publish_time > 0 AND
	post_state = 1
;

-- -----------------------------------------------------
-- View `Roger`.`pop_score`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `Roger`.`pop_score` ;
DROP TABLE IF EXISTS `Roger`.`pop_score`;
USE `Roger`;
CREATE  OR REPLACE VIEW `pop_score` AS
SELECT
	posts.post_id AS post_id,
	(
        (IF ((SELECT COUNT(*) FROM comments_score WHERE comments_score.post_id = posts.post_id) > 0, (SELECT SUM(comment_score) FROM comments_score WHERE comments_score.post_id = posts.post_id), 0))
      + (IF ((SELECT COUNT(*) FROM likes_score WHERE likes_score.post_id = posts.post_id) > 0, (SELECT SUM(like_score) FROM likes_score WHERE likes_score.post_id = posts.post_id), 0))
      + (IF ((SELECT COUNT(*) FROM views_score WHERE views_score.post_id = posts.post_id) > 0, (SELECT SUM(view_score) FROM views_score WHERE views_score.post_id = posts.post_id), 0))
      + (SELECT post_bonus FROM posts_bonus WHERE posts_bonus.post_id = posts.post_id LIMIT 1)
    ) AS post_score
FROM
    posts
WHERE 
	post_publish_time > 0 AND
	post_state = 1
;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
