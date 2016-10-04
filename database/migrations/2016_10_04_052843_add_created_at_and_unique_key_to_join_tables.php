<?php

use App\Utils\RawMigration;

class AddCreatedAtAndUniqueKeyToJoinTables extends RawMigration
{
    /**
     * @inheritdoc
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
ALTER TABLE `asset_software_information`
  ADD UNIQUE KEY `asset_software_information_idx` (`asset_id`, `software_information_id`),
  ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT ''
    AFTER `software_information_id`;

ALTER TABLE `files_assets`
  ADD UNIQUE KEY `file_asset_idx` (`file_id`, `asset_id`),
  ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT ''
    AFTER `asset_id`;

ALTER TABLE `files_open_ports`
  ADD UNIQUE KEY `file_open_port_idx` (`file_id`, `open_port_id`),
  ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT ''
    AFTER `open_port_id`;

ALTER TABLE `files_software_information`
  ADD UNIQUE KEY `file_software_information_idx` (`file_id`, `software_information_id`),
  ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT ''
    AFTER `software_information_id`;

ALTER TABLE `files_vulnerabilities`
  ADD UNIQUE KEY `file_vulnerability_idx` (`file_id`, `vulnerability_id`),
  ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT ''
    AFTER `vulnerability_id`;

ALTER TABLE `files_vulnerability_reference_codes`
  ADD UNIQUE KEY `file_vulnerability_reference_code_idx` (`file_id`, `vulnerability_reference_code_id`),
  ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT ''
    AFTER `vulnerability_reference_code_id`;
SQL;
    }
}
