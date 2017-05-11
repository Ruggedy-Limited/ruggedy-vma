<?php

use App\Utils\RawMigration;

class RuggedySetup extends RawMigration
{
    public function getRawSqlMigration()
    {
        return <<<SQL
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

# Dump of table asset_software_information
# ------------------------------------------------------------

CREATE TABLE `asset_software_information` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL,
  `software_information_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_software_information_idx` (`asset_id`,`software_information_id`),
  KEY `asset_fk_idx` (`asset_id`),
  KEY `software_information_fk_idx` (`software_information_id`),
  CONSTRAINT `asset_software_information_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `asset_software_information_software_information_fk` FOREIGN KEY (`software_information_id`) REFERENCES `software_information` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table assets
# ------------------------------------------------------------

CREATE TABLE `assets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `cpe` varchar(255) DEFAULT NULL,
  `vendor` varchar(45) DEFAULT NULL,
  `ip_address_v4` varchar(15) DEFAULT NULL,
  `ip_address_v6` varchar(45) DEFAULT NULL,
  `hostname` varchar(255) DEFAULT NULL,
  `mac_address` varchar(25) DEFAULT NULL,
  `mac_vendor` varchar(100) DEFAULT NULL,
  `os_version` varchar(255) DEFAULT NULL,
  `netbios` varchar(255) DEFAULT NULL,
  `uptime` int(10) unsigned DEFAULT NULL,
  `last_boot` datetime DEFAULT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `suppressed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `assets_user_fk_idx` (`user_id`),
  KEY `assets_file_fk` (`file_id`),
  CONSTRAINT `assets_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `assets_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table assets_audits
# ------------------------------------------------------------

CREATE TABLE `assets_audits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL,
  `audit_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assets_audits_idx` (`asset_id`,`audit_id`),
  KEY `assets_audits_audit_fk` (`audit_id`),
  CONSTRAINT `assets_audits_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `assets_audits_audit_fk` FOREIGN KEY (`audit_id`) REFERENCES `audits` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table assets_vulnerabilities
# ------------------------------------------------------------

CREATE TABLE `assets_vulnerabilities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL,
  `vulnerability_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_vulnerability_idx` (`asset_id`,`vulnerability_id`),
  KEY `assets_vulnerabilities_vulnerability_fk` (`vulnerability_id`),
  CONSTRAINT `assets_vulnerabilities_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `assets_vulnerabilities_vulnerability_fk` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table audits
# ------------------------------------------------------------

CREATE TABLE `audits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `audit_file` varchar(150) DEFAULT NULL,
  `compliance_check_name` text,
  `compliance_check_id` varchar(45) DEFAULT NULL,
  `actual_value` text,
  `policy_value` text,
  `info` text,
  `result` enum('PASSED','FAILED','ERROR','WARNING') DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `see_also` varchar(255) DEFAULT NULL,
  `description` text,
  `solution` text,
  `agent` varchar(45) DEFAULT NULL,
  `uname` varchar(255) DEFAULT NULL,
  `output` text,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table comments
# ------------------------------------------------------------

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `user_id` int(10) unsigned NOT NULL,
  `vulnerability_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `comments_user_fk_idx` (`user_id`),
  KEY `comments_vulnerability_fk_idx` (`vulnerability_id`),
  CONSTRAINT `comments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `comments_vulnerability_fk` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table component_permissions
# ------------------------------------------------------------

CREATE TABLE `component_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `component_id` int(10) unsigned NOT NULL,
  `instance_id` int(10) unsigned NOT NULL COMMENT 'The id of the instance of the relevant component',
  `permission` enum('r','rw') NOT NULL DEFAULT 'r',
  `user_id` int(10) unsigned DEFAULT NULL,
  `granted_by` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `component_permissions_component_fk_idx` (`component_id`),
  KEY `component_permissions_user_fk_idx` (`user_id`),
  KEY `component_permissions_user_granted_fk_idx` (`granted_by`),
  CONSTRAINT `component_permissions_component_fk` FOREIGN KEY (`component_id`) REFERENCES `components` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `component_permissions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `component_permissions_user_granted_fk` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table components
# ------------------------------------------------------------

CREATE TABLE `components` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `class_name` varchar(100) NOT NULL COMMENT 'The class used to store row instances in the application',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `components` WRITE;
/*!40000 ALTER TABLE `components` DISABLE KEYS */;

INSERT INTO `components` (`id`, `name`, `class_name`, `created_at`, `updated_at`)
VALUES
	(1,'User Account','User',NOW(),NOW()),
	(2,'Project','Project',NOW(),NOW()),
	(3,'Workspace','Workspace',NOW(),NOW()),
	(4,'Workspace App','WorkspaceApp',NOW(),NOW()),
	(5,'File','File',NOW(),NOW()),
	(6,'Asset','Asset',NOW(),NOW()),
	(7,'Scanner App','ScannerApp',NOW(),NOW()),
	(8,'Event','Event',NOW(),NOW()),
	(9,'Rule','Rule',NOW(),NOW());

/*!40000 ALTER TABLE `components` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table exploits
# ------------------------------------------------------------

CREATE TABLE `exploits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url_reference` varchar(255) DEFAULT NULL,
  `skill_level` enum('Novice','Intermediate','Expert') DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table files
# ------------------------------------------------------------

CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `path` varchar(255) NOT NULL,
  `format` enum('xml','csv','json') NOT NULL,
  `size` int(16) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `workspace_app_id` int(10) unsigned NOT NULL,
  `processed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `files_user_fk_idx` (`user_id`),
  KEY `files_workspace_apps_fk_idx` (`workspace_app_id`),
  CONSTRAINT `files_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `files_workspace_app_fk` FOREIGN KEY (`workspace_app_id`) REFERENCES `workspace_apps` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table files_audits
# ------------------------------------------------------------

CREATE TABLE `files_audits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `audit_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `files_audits_idx` (`file_id`,`audit_id`),
  KEY `files_audits_audit_fk` (`audit_id`),
  CONSTRAINT `files_audits_audit_fk` FOREIGN KEY (`audit_id`) REFERENCES `audits` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `files_audits_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table files_open_ports
# ------------------------------------------------------------

CREATE TABLE `files_open_ports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `open_port_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_open_port_idx` (`file_id`,`open_port_id`),
  KEY `files_open_ports_open_port_fk` (`open_port_id`),
  CONSTRAINT `files_open_ports_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `files_open_ports_open_port_fk` FOREIGN KEY (`open_port_id`) REFERENCES `open_ports` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table files_software_information
# ------------------------------------------------------------

CREATE TABLE `files_software_information` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `software_information_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_software_information_idx` (`file_id`,`software_information_id`),
  KEY `files_software_information_si_fk` (`software_information_id`),
  CONSTRAINT `files_software_information_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `files_software_information_si_fk` FOREIGN KEY (`software_information_id`) REFERENCES `software_information` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table folders
# ------------------------------------------------------------

CREATE TABLE `folders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `workspace_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `folder_workspace_fk_idx` (`workspace_id`),
  KEY `folder_user_fk_idx` (`user_id`),
  CONSTRAINT `folder_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `folders_workspace_fk` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table folders_vulnerabilities
# ------------------------------------------------------------

CREATE TABLE `folders_vulnerabilities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` int(10) unsigned NOT NULL,
  `vulnerability_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `folders_vulnerabilities_folder_fk` (`folder_id`),
  KEY `folders_vulnerabilities_vulnerability_fk` (`vulnerability_id`),
  CONSTRAINT `folders_vulnerabilities_folder_fk` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `folders_vulnerabilities_vulnerability_fk` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table jira_issues
# ------------------------------------------------------------

CREATE TABLE `jira_issues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_key` varchar(255) NOT NULL,
  `issue_id` int(10) unsigned DEFAULT NULL,
  `issue_key` varchar(255) DEFAULT NULL,
  `issue_status` enum('open','in progress','resolved','closed','reopened') DEFAULT 'open',
  `issue_type` varchar(100) NOT NULL DEFAULT 'Bug',
  `summary` varchar(255) DEFAULT NULL,
  `description` text,
  `request_type` enum('search','create','update') NOT NULL,
  `request_status` enum('pending','in progress','failed','success') NOT NULL,
  `host` varchar(255) DEFAULT NULL,
  `port` int(6) DEFAULT NULL,
  `retries` tinyint(1) NOT NULL DEFAULT '0',
  `failure_reason` varchar(255) DEFAULT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `vulnerability_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `jira_issues_file_fk_idx` (`file_id`),
  KEY `jira_issues_vulnerability_fk_idx` (`vulnerability_id`),
  CONSTRAINT `jira_issues_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `jira_issues_vulnerability_fk` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table open_ports
# ------------------------------------------------------------

CREATE TABLE `open_ports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` int(6) NOT NULL,
  `protocol` varchar(45) DEFAULT NULL,
  `service_name` varchar(45) DEFAULT NULL,
  `service_product` varchar(150) DEFAULT NULL,
  `service_extra_info` text,
  `service_finger_print` varchar(255) DEFAULT NULL,
  `service_banner` varchar(255) DEFAULT NULL,
  `service_message` varchar(255) DEFAULT NULL,
  `asset_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `open_ports_asset_fk_idx` (`asset_id`),
  CONSTRAINT `open_ports_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table password_resets
# ------------------------------------------------------------

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table scanner_apps
# ------------------------------------------------------------

CREATE TABLE `scanner_apps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `friendly_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `order` int(2) unsigned DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `scanner_apps` WRITE;
/*!40000 ALTER TABLE `scanner_apps` DISABLE KEYS */;

INSERT INTO `scanner_apps` (`id`, `name`, `friendly_name`, `description`, `logo`, `order`, `created_at`, `updated_at`)
VALUES
	(1,'ruggedy','Ruggedy App','Create custom vulnerability entries for your Workspace','/img/ruggedy-logo.png',1,NOW(),NOW()),
	(2,'burp','Burp','Burp Web Vulnerability Scanner','/img/burp-logo.png',2,NOW(),NOW()),
	(3,'nessus','Nessus','Nessus Vulnerability Scanner','/img/nessus-logo.png',3,NOW(),NOW()),
	(4,'nexpose','Nexpose','Nexpose Vulnerability Scanner','/img/nexpose-logo.png',4,NOW(),NOW());

/*!40000 ALTER TABLE `scanner_apps` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table software_information
# ------------------------------------------------------------

CREATE TABLE `software_information` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `version` varchar(100) DEFAULT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_url` text COLLATE utf8mb4_unicode_ci,
  `country_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `photo_url`, `country_code`, `phone`,
`is_admin`, `deleted`, `created_at`, `updated_at`)
  VALUES (1, 'Admin', 'admin@localhost', '$2y$10$0h8y8tmAvt5ztd7g3ZY7Q.gQr1YyNgIJxRQaAs0ij06LSNwwCxcOC', NULL, NULL,
  NULL, NULL, 1, 0, NOW(), NOW());



# Dump of table vulnerabilities
# ------------------------------------------------------------

CREATE TABLE `vulnerabilities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_from_scanner` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `severity` decimal(4,2) unsigned DEFAULT NULL,
  `pci_severity` decimal(4,2) unsigned DEFAULT NULL,
  `malware_available` tinyint(1) unsigned DEFAULT NULL,
  `malware_description` varchar(255) DEFAULT NULL,
  `impact` varchar(255) DEFAULT NULL,
  `cvss_score` decimal(4,2) unsigned DEFAULT NULL,
  `description` text,
  `solution` text,
  `generic_output` longtext,
  `attack_type` enum('Remote','Local') DEFAULT NULL,
  `poc` text,
  `http_port` int(6) unsigned DEFAULT NULL,
  `published_date_from_scanner` datetime DEFAULT NULL,
  `modified_date_from_scanner` datetime DEFAULT NULL,
  `thumbnail_1` text,
  `thumbnail_2` text,
  `thumbnail_3` text,
  `file_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vulnerabilities_file_fk` (`file_id`),
  CONSTRAINT `vulnerabilities_file_fk` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table vulnerabilities_exploits
# ------------------------------------------------------------

CREATE TABLE `vulnerabilities_exploits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vulnerability_id` int(10) unsigned NOT NULL,
  `exploit_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vulnerability_exploit_idx` (`vulnerability_id`,`exploit_id`),
  KEY `vulnerabilities_exploits_exploit_fk` (`exploit_id`),
  CONSTRAINT `vulnerabilities_exploits_exploit_fk` FOREIGN KEY (`exploit_id`) REFERENCES `exploits` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `vulnerabilities_exploits_vulnerability_fk` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table vulnerability_http_data
# ------------------------------------------------------------

CREATE TABLE `vulnerability_http_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `issue_detail` text,
  `http_port` int(6) unsigned DEFAULT NULL,
  `http_method` enum('GET','OPTIONS','HEAD','POST','PUT','DELETE','TRACE','CONNECT') DEFAULT NULL,
  `http_banner` varchar(45) DEFAULT NULL,
  `http_status_code` int(4) unsigned DEFAULT NULL,
  `http_uri` text,
  `http_test_parameter` varchar(45) DEFAULT NULL,
  `http_raw_request` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `http_raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `http_attack_pattern` text,
  `http_attack_response` text,
  `vulnerability_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vulnerability_http_data_vulnerability_fk_idx` (`vulnerability_id`),
  CONSTRAINT `vulnerability_http_data_vulnerability_fk` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table vulnerability_reference_codes
# ------------------------------------------------------------

CREATE TABLE `vulnerability_reference_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_type` varchar(50) NOT NULL DEFAULT 'online_other',
  `value` text NOT NULL,
  `vulnerability_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vulnerability_reference_codes_vulnerability_fk_idx` (`vulnerability_id`),
  CONSTRAINT `vulnerability_reference_codes_vulnerability_fk` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table workspace_apps
# ------------------------------------------------------------

CREATE TABLE `workspace_apps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `scanner_app_id` int(10) unsigned NOT NULL,
  `workspace_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `workspace_apps_scanner_app_fk_idx` (`scanner_app_id`),
  KEY `workspace_apps_workspace_fk_idx` (`workspace_id`),
  CONSTRAINT `workspace_apps_scanner_app_fk` FOREIGN KEY (`scanner_app_id`) REFERENCES `scanner_apps` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `workspace_apps_workspace_fk` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table workspaces
# ------------------------------------------------------------

CREATE TABLE `workspaces` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workspaces_fk_user` (`user_id`),
  CONSTRAINT `workspaces_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
SQL;
    }
}
