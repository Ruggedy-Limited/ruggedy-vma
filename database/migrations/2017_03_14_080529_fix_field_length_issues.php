<?php

use App\Utils\RawMigration;

class FixFieldLengthIssues extends RawMigration
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

ALTER TABLE `audits`
  CHANGE `policy_value` `policy_value` TEXT NULL DEFAULT NULL COMMENT '';

ALTER TABLE `open_ports`
  CHANGE `service_extra_info` `service_extra_info` TEXT NULL DEFAULT NULL COMMENT '';

ALTER TABLE `vulnerabilities`
  CHANGE `http_raw_request` `http_raw_request` LONGTEXT NULL DEFAULT NULL COMMENT '',
  CHANGE `http_raw_response` `http_raw_response` LONGTEXT NULL DEFAULT NULL COMMENT '';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }
}
