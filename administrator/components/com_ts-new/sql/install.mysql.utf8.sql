CREATE TABLE IF NOT EXISTS `#__ts_tournament` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`tournament_name` VARCHAR(255)  NOT NULL ,
`tournament_start_date` DATE NULL  DEFAULT NULL,
`tournament_end_date` DATE NULL  DEFAULT NULL,
`tournament_description` TEXT NULL ,
`teams_registered` TEXT NULL ,
`season_id` INT NULL  DEFAULT 0,
`tournament_notes` TEXT NULL ,
`is_deleted` TINYINT(4)  NULL  DEFAULT 0,
`tournament_complete` VARCHAR(255)  NULL  DEFAULT "0",
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ts_games` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`home_seed` INT(11)  NULL  DEFAULT 0,
`visitor_seed` INT(11)  NULL  DEFAULT 0,
`home_team` VARCHAR(255)  NOT NULL ,
`visitor_team` VARCHAR(255)  NULL  DEFAULT NULL,
`home_pool` VARCHAR(255)  NULL  DEFAULT "",
`game_date` DATETIME NULL  DEFAULT NULL ,
`game_type` VARCHAR(255)  NULL  DEFAULT "",
`field_location` TEXT NULL ,
`home_score` INT(11)  NULL  DEFAULT 0,
`visitor_score` INT(11)  NULL  DEFAULT 0,
`notes` TEXT NULL ,
`age_id` TEXT NULL ,
`tournament_id` TEXT NULL ,
`visitor_pool` VARCHAR(255)  NULL  DEFAULT "",
`game_time` DATETIME NULL  DEFAULT NULL ,
`game_order` INT(11)  NULL  DEFAULT 0,
`game_active` VARCHAR(255)  NULL  DEFAULT "0",
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ts_tournament_age_cost` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`tournament_cost` DECIMAL(10,2)  NULL  DEFAULT 0,
`tourn_capacity` INT(11)  NULL  DEFAULT 0,
`field_location_description` TEXT NULL ,
`tournament_results` TEXT NULL ,
`age_id` TEXT NULL ,
`tournament_id` TEXT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__ts_register` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`team_manager_1` VARCHAR(255)  NULL  DEFAULT NULL,
`team_address` TEXT NULL ,
`level_play` VARCHAR(255)  NULL  DEFAULT NULL,
`registration_number` TEXT NULL ,
`team_name` TEXT NULL ,
`team_manager_2` VARCHAR(255)  NULL  DEFAULT NULL,
`team_city` VARCHAR(255)  NULL  DEFAULT NULL,
`team_state` VARCHAR(255)  NULL  DEFAULT NULL,
`team_zip` VARCHAR(255)  NULL  DEFAULT NULL,
`home_phone` VARCHAR(255)  NULL  DEFAULT NULL,
`cell_phone_2` VARCHAR(255)  NULL  DEFAULT NULL,
`email_1` VARCHAR(255)  NULL  DEFAULT NULL,
`season_id` INT(11)  NULL  DEFAULT 0,
`reg_status` VARCHAR(255)  NULL  DEFAULT NULL,
`date_submitted` DATETIME NULL  DEFAULT NULL ,
`league_affiliation` VARCHAR(255)  NULL  DEFAULT NULL,
`email_2` VARCHAR(255)  NULL  DEFAULT NULL,
`comments` VARCHAR(1000)  NULL  DEFAULT NULL,
`cell_phone_1` VARCHAR(255)  NULL  DEFAULT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;
