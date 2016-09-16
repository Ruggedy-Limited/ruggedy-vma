<?php

use App\Utils\RawMigration;

class CreateManyToManyFileJoinTables extends RawMigration
{
    function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


ALTER TABLE `files`
  DROP FOREIGN KEY `files_asset_fk`,
  DROP `asset_id`;

ALTER TABLE `vulnerabilities`
  DROP FOREIGN KEY `vulnerabilities_file_fk`,
  DROP `file_id`;
  
ALTER TABLE `open_ports`
  DROP FOREIGN KEY `system_information_file_fk`,
  DROP `file_id`;


CREATE TABLE `files_assets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `asset_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_assets_file_fk` (`file_id`),
  KEY `files_assets_asset_fk` (`asset_id`),
  CONSTRAINT `files_assets_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `files_assets_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `files_vulnerabilities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `vulnerability_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_vulnerabilities_file_fk` (`file_id`),
  KEY `files_vulnerabilities_vulnerability_id` (`vulnerability_id`),
  CONSTRAINT `files_vulnerabilities_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `files_vulnerabilities_vulnerability_id` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `files_open_ports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `open_port_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_open_ports_file_fk` (`file_id`),
  KEY `files_open_ports_open_port_fk` (`open_port_id`),
  CONSTRAINT `files_open_ports_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `files_open_ports_open_port_fk` FOREIGN KEY (`open_port_id`) REFERENCES `open_ports` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `files_vulnerability_reference_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `vulnerability_reference_code_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_vulnerability_reference_codes_file_fk` (`file_id`),
  KEY `files_vulnerability_reference_codes_vrc_fk` (`vulnerability_reference_code_id`),
  CONSTRAINT `files_vulnerability_reference_codes_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `files_vulnerability_reference_codes_vrc_fk` FOREIGN KEY (`vulnerability_reference_code_id`) REFERENCES `vulnerability_reference_codes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `files_software_information` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `software_information_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `files_software_information_file_fk` (`file_id`),
  KEY `files_software_information_si_fk` (`software_information_id`),
  CONSTRAINT `files_software_information_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `files_software_information_si_fk` FOREIGN KEY (`software_information_id`) REFERENCES `software_information` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
