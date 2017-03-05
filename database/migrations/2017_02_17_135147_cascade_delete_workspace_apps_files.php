<?php

use App\Utils\RawMigration;

class CascadeDeleteWorkspaceAppsFiles extends RawMigration
{
    /**
     * @inheritdoc
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `files` 
DROP FOREIGN KEY `files_workspace_apps_fk`;

ALTER TABLE `files` 
ADD CONSTRAINT `files_workspace_apps_fk`
  FOREIGN KEY (`workspace_apps_id`)
  REFERENCES `workspace_apps` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `workspace_apps` 
DROP FOREIGN KEY `workspace_apps_workspace_fk`;

ALTER TABLE `workspace_apps`
ADD CONSTRAINT `workspace_apps_workspace_fk`
  FOREIGN KEY (`workspace_id`)
  REFERENCES `workspaces` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `assets` 
DROP FOREIGN KEY `assets_file_fk`;

ALTER TABLE `assets` 
ADD CONSTRAINT `assets_file_fk`
  FOREIGN KEY (`file_id`)
  REFERENCES `files` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `open_ports` 
DROP FOREIGN KEY `open_ports_asset_fk`;

ALTER TABLE `open_ports` 
ADD CONSTRAINT `open_ports_asset_fk`
  FOREIGN KEY (`asset_id`)
  REFERENCES `assets` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `vulnerability_reference_codes` 
DROP FOREIGN KEY `vulnerability_reference_codes_vulnerability_fk`;

ALTER TABLE `vulnerability_reference_codes` 
ADD CONSTRAINT `vulnerability_reference_codes_vulnerability_fk`
  FOREIGN KEY (`vulnerability_id`)
  REFERENCES `vulnerabilities` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `asset_software_information`
    DROP FOREIGN KEY `asset_fk`,
    DROP FOREIGN KEY `software_information_fk`;

ALTER TABLE `asset_software_information`
  ADD CONSTRAINT `asset_software_information_asset_fk`
  FOREIGN KEY (`asset_id`)
  REFERENCES `assets` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `asset_software_information_software_information_fk`
  FOREIGN KEY (`software_information_id`)
  REFERENCES `software_information` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `assets_audits`
    DROP FOREIGN KEY `assets_audits_asset_fk`,
    DROP FOREIGN KEY `assets_audits_audit_fk`;

ALTER TABLE `assets_audits`
  ADD CONSTRAINT `assets_audits_asset_fk`
  FOREIGN KEY (`asset_id`)
  REFERENCES `assets` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `assets_audits_audit_fk`
  FOREIGN KEY (`audit_id`)
  REFERENCES `audits` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `assets_vulnerabilities`
    DROP FOREIGN KEY `assets_vulnerabilities_asset_fk`,
    DROP FOREIGN KEY `assets_vulnerabilities_vulnerability_fk`;

ALTER TABLE `assets_vulnerabilities`
  ADD CONSTRAINT `assets_vulnerabilities_asset_fk`
  FOREIGN KEY (`asset_id`)
  REFERENCES `assets` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `assets_vulnerabilities_vulnerability_fk`
  FOREIGN KEY (`vulnerability_id`)
  REFERENCES `vulnerabilities` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `files_audits`
    DROP FOREIGN KEY `files_audits_file_fk`,
    DROP FOREIGN KEY `files_audits_audit_fk`;

ALTER TABLE `files_audits`
  ADD CONSTRAINT `files_audits_file_fk`
  FOREIGN KEY (`file_id`)
  REFERENCES `files` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `files_audits_audit_fk`
  FOREIGN KEY (`audit_id`)
  REFERENCES `audits` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `files_open_ports`
    DROP FOREIGN KEY `file_open_ports_file_fk`,
    DROP FOREIGN KEY `file_open_ports_open_port_fk`;

ALTER TABLE `files_open_ports`
  ADD CONSTRAINT `files_open_ports_file_fk`
  FOREIGN KEY (`file_id`)
  REFERENCES `files` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `files_open_ports_open_port_fk`
  FOREIGN KEY (`open_port_id`)
  REFERENCES `open_ports` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `files_software_information`
    DROP FOREIGN KEY `file_software_information_file_fk`,
    DROP FOREIGN KEY `file_software_information_si_fk`;

ALTER TABLE `files_software_information`
  ADD CONSTRAINT `files_software_information_file_fk`
  FOREIGN KEY (`file_id`)
  REFERENCES `files` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `file_software_information_si_fk`
  FOREIGN KEY (`software_information_id`)
  REFERENCES `software_information` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `files_vulnerabilities`
    DROP FOREIGN KEY `file_vulnerabilities_file_fk`,
    DROP FOREIGN KEY `file_vulnerabilities_vulnerability_id`;

ALTER TABLE `files_vulnerabilities`
  ADD CONSTRAINT `files_vulnerabilities_file_fk`
  FOREIGN KEY (`file_id`)
  REFERENCES `files` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `files_vulnerabilities_vulnerability_fk`
  FOREIGN KEY (`vulnerability_id`)
  REFERENCES `vulnerabilities` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `folders_vulnerabilities`
    DROP FOREIGN KEY `folders_vulnerabilities_file_fk`,
    DROP FOREIGN KEY `folders_vulnerabilities_vulnerability_id`;

ALTER TABLE `folders_vulnerabilities`
  ADD CONSTRAINT `folders_vulnerabilities_file_fk`
  FOREIGN KEY (`folder_id`)
  REFERENCES `folders` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `folders_vulnerabilities_vulnerability_fk`
  FOREIGN KEY (`vulnerability_id`)
  REFERENCES `vulnerabilities` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
ALTER TABLE `vulnerabilities_exploits`
    DROP FOREIGN KEY `vulnerabilities_exploits_vulnerability_fk`,
    DROP FOREIGN KEY `vulnerabilities_exploits_exploit_fk`;

ALTER TABLE `vulnerabilities_exploits`
  ADD CONSTRAINT `vulnerabilities_exploits_vulnerability_fk`
  FOREIGN KEY (`vulnerability_id`)
  REFERENCES `vulnerabilities` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `vulnerabilities_exploits_exploit_fk`
  FOREIGN KEY (`exploit_id`)
  REFERENCES `exploits` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
  
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
