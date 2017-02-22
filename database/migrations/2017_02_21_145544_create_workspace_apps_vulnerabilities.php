<?php

use App\Utils\RawMigration;

class CreateWorkspaceAppsVulnerabilities extends RawMigration
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

CREATE TABLE IF NOT EXISTS `workspace_apps_vulnerabilities` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `workspace_app_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `vulnerability_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  CONSTRAINT `workspace_apps_vulnerabilities_workspace_app_fk` FOREIGN KEY (`workspace_app_id`)
    REFERENCES `workspace_apps` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `workspace_apps_vulnerabilities_vulnerability_fk` FOREIGN KEY (`vulnerability_id`)
    REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }
}
