<?php

use App\Utils\RawMigration;

class CreateVulnerabilitiesVulnRefAndSystemInfoTables extends RawMigration
{
    /**
     * @inheritdoc
     * @return string
     */
    function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `vulnerabilities` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(255) NOT NULL COMMENT '',
  `severity` ENUM('Critical', 'High', 'Medium', 'Low', 'None') NOT NULL DEFAULT 'None' COMMENT '',
  `exploit_available` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT '',
  `impact` VARCHAR(255) NULL DEFAULT NULL COMMENT '',
  `cvss_score` DOUBLE(2,2) NULL DEFAULT NULL COMMENT '',
  `description` TEXT NULL DEFAULT NULL COMMENT '',
  `solution` TEXT NULL DEFAULT NULL COMMENT '',
  `generic_output` TEXT NULL DEFAULT NULL COMMENT '',
  `attack_type` ENUM('Remote', 'Local') NULL DEFAULT NULL COMMENT '',
  `poc` TEXT NULL DEFAULT NULL COMMENT '',
  `asset_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `http_port` INT(6) UNSIGNED NULL DEFAULT NULL COMMENT '',
  `http_method` ENUM('GET', 'OPTIONS', 'HEAD', 'POST', 'PUT', 'DELETE', 'TRACE', 'CONNECT') NULL DEFAULT NULL COMMENT '',
  `http_banner` VARCHAR(45) NULL DEFAULT NULL COMMENT '',
  `http_status_code` INT(4) NULL DEFAULT NULL COMMENT '',
  `http_uri` TEXT NULL DEFAULT NULL COMMENT '',
  `http_test_parameter` VARCHAR(45) NULL DEFAULT NULL COMMENT '',
  `http_raw_request` TEXT NULL DEFAULT NULL COMMENT '',
  `http_raw_response` TEXT NULL DEFAULT NULL COMMENT '',
  `http_attack_pattern` TEXT NULL DEFAULT NULL COMMENT '',
  `http_attack_response` TEXT NULL DEFAULT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `vulnerabilities_asset_fk_idx` (`asset_id` ASC)  COMMENT '',
  CONSTRAINT `vulnerabilities_asset_fk`
    FOREIGN KEY (`asset_id`)
    REFERENCES `assets` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `vulnerability_reference_codes` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `reference_type` ENUM('cve', 'bid', 'cert', 'pci3.1', 'pci3.2', 'owasp', 'capec', 'wasc', 'osvdbid', 'hipaa', 'online_other') NOT NULL COMMENT '',
  `value` VARCHAR(45) NOT NULL COMMENT '',
  `vulnerability_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `vulnerability_reference_codes_vulnerability_fk_idx` (`vulnerability_id` ASC)  COMMENT '',
  CONSTRAINT `vulnerability_reference_codes_vulnerability_fk`
    FOREIGN KEY (`vulnerability_id`)
    REFERENCES `vulnerabilities` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `system_information` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `open_port` INT(6) UNSIGNED NULL DEFAULT NULL COMMENT '',
  `port_protocol` VARCHAR(45) NULL DEFAULT NULL COMMENT '',
  `port_service` VARCHAR(45) NULL DEFAULT NULL COMMENT '',
  `port_srv_information` TEXT NULL DEFAULT NULL COMMENT '',
  `port_srv_banner` VARCHAR(150) NULL DEFAULT NULL COMMENT '',
  `uptime` VARCHAR(30) NULL DEFAULT NULL COMMENT '',
  `last_boot` DATETIME NULL DEFAULT NULL COMMENT '',
  `asset_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `system_information_asset_fk_idx` (`asset_id` ASC)  COMMENT '',
  CONSTRAINT `system_information_asset_fk`
    FOREIGN KEY (`asset_id`)
    REFERENCES `assets` (`id`)
    ON DELETE NO ACTION
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
