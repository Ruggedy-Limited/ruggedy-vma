<?php

use App\Utils\RawMigration;

class CascadeDeleteForeignKeys extends RawMigration
{
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

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
  
ALTER TABLE `folders_vulnerabilities`
  DROP FOREIGN KEY `folders_vulnerabilities_folder_fk`,
  DROP FOREIGN KEY `folders_vulnerabilities_vulnerability_id`;
  
ALTER TABLE `folders_vulnerabilities`
  ADD CONSTRAINT `folders_vulnerabilities_folder_fk`
  FOREIGN KEY (`folder_id`)
  REFERENCES `folders` (`id`)
  ON DELETE CASCADE 
  ON UPDATE NO ACTION,
  ADD CONSTRAINT `folders_vulnerabilities_vulnerability_fk`
  FOREIGN KEY (`vulnerability_id`)
  REFERENCES `vulnerabilities` (`id`)
  ON DELETE CASCADE 
  ON UPDATE NO ACTION;
  
ALTER TABLE `folders`
  DROP FOREIGN KEY `folder_workspace_fk`;
  
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_workspace_fk`
  FOREIGN KEY (`workspace_id`)
  REFERENCES `workspaces` (`id`)
  ON DELETE CASCADE 
  ON UPDATE NO ACTION;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }
}
