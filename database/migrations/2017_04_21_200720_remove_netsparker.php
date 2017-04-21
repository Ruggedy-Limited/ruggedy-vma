<?php

use App\Utils\RawMigration;

class RemoveNetsparker extends RawMigration
{
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DELETE FROM `vulnerabilities` WHERE `file_id` IN (
  SELECT `files`.`id` FROM `files`
    LEFT JOIN `workspace_apps` ON `files`.`workspace_app_id` = `workspace_apps`.`id`
    LEFT JOIN `scanner_apps` ON `workspace_apps`.`scanner_app_id` = `scanner_apps`.`id`
    WHERE `scanner_apps`.`name` = 'netsparker'
);

DELETE FROM `files` WHERE `workspace_app_id` IN (
  SELECT `workspace_apps`.`id` FROM `workspace_apps`
    LEFT JOIN `scanner_apps` ON `workspace_apps`.`scanner_app_id` = `scanner_apps`.`id`
    WHERE `scanner_apps`.`name` = 'netsparker'
);

DELETE FROM `workspace_apps` WHERE `scanner_app_id` IN (
  SELECT `scanner_apps`.`id` FROM `scanner_apps`
    WHERE `scanner_apps`.`name` = 'netsparker'
);

DELETE FROM `scanner_apps` WHERE `name` = 'netsparker';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }
}
