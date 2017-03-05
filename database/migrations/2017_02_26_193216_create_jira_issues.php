<?php

use App\Utils\RawMigration;

class CreateJiraIssues extends RawMigration
{
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `jira_issues` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `project_key` VARCHAR(255) NOT NULL COMMENT '',
  `issue_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '',
  `issue_key` VARCHAR(255) NULL DEFAULT NULL COMMENT '',
  `issue_status` ENUM('open', 'in progress', 'resolved', 'closed', 'reopened') NULL DEFAULT 'open' COMMENT '',
  `issue_type` VARCHAR(100) NOT NULL DEFAULT 'Bug' COMMENT '',
  `summary` VARCHAR(255) NULL DEFAULT NULL COMMENT '',
  `description` TEXT NULL DEFAULT NULL COMMENT '',
  `request_type` ENUM('search', 'create', 'update') NOT NULL COMMENT '',
  `request_status` ENUM('pending', 'in progress', 'failed', 'success') NOT NULL COMMENT '',
  `host` VARCHAR(255) NULL DEFAULT NULL COMMENT '',
  `port` INT(6) NULL DEFAULT NULL COMMENT '',
  `retries` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '',
  `failure_reason` VARCHAR(255) NULL DEFAULT NULL COMMENT '',
  `file_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `vulnerability_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `jira_issues_file_fk_idx` (`file_id` ASC)  COMMENT '',
  INDEX `jira_issues_vulnerability_fk_idx` (`vulnerability_id` ASC)  COMMENT '',
  CONSTRAINT `jira_issues_file_fk`
    FOREIGN KEY (`file_id`)
    REFERENCES `files` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `jira_issues_vulnerability_fk`
    FOREIGN KEY (`vulnerability_id`)
    REFERENCES `vulnerabilities` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
