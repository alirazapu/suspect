/*
SQLyog Community v13.3.1 (64 bit)
MySQL - 10.4.11-MariaDB : Database - aiesplus
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `access_log` */

DROP TABLE IF EXISTS `access_log`;

CREATE TABLE `access_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip` text DEFAULT NULL,
  `activity_time` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `url` text DEFAULT NULL,
  `host` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1691707 DEFAULT CHARSET=latin1;

/*Table structure for table `admin_email_messages` */

DROP TABLE IF EXISTS `admin_email_messages`;

CREATE TABLE `admin_email_messages` (
  `message_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message_subject` varchar(256) DEFAULT NULL,
  `message_body` blob DEFAULT NULL,
  `sender_id` varchar(250) NOT NULL,
  `message_date` datetime DEFAULT NULL,
  `is_draft` int(11) NOT NULL DEFAULT 0,
  `is_draft_to` varchar(255) DEFAULT NULL,
  `received_file_path` varchar(255) DEFAULT NULL,
  `received_body` varchar(1000) DEFAULT NULL,
  `received_body_raw` blob NOT NULL,
  `received_date` datetime NOT NULL COMMENT 'Message received Date',
  `is_active` int(11) DEFAULT 1,
  `is_deleted` int(11) DEFAULT 0,
  `is_screened` int(1) DEFAULT 0,
  `custom_folder_id` int(11) DEFAULT 0,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/*Table structure for table `admin_familytree_request` */

DROP TABLE IF EXISTS `admin_familytree_request`;

CREATE TABLE `admin_familytree_request` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `cnic` bigint(25) DEFAULT NULL,
  `passport` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_id` int(5) DEFAULT NULL,
  `district_id` int(5) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `rqtbyname` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `admin_nadra_request` */

DROP TABLE IF EXISTS `admin_nadra_request`;

CREATE TABLE `admin_nadra_request` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `cnic` bigint(25) DEFAULT NULL,
  `request_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_id` int(5) DEFAULT NULL,
  `district_id` int(5) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `rqtbyname` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1371 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `admin_request` */

DROP TABLE IF EXISTS `admin_request`;

CREATE TABLE `admin_request` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` int(10) NOT NULL COMMENT 'Reference ID to be sent to company for reference',
  `user_id` int(10) unsigned NOT NULL,
  `user_request_type_id` int(10) unsigned NOT NULL,
  `message_id` int(10) NOT NULL,
  `company_name` int(2) DEFAULT NULL COMMENT 'MNC (Mobile Network Value)',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Requst not send, 1=request send, 2=email received, 3=email sending error, 4=request rejected',
  `reply` smallint(6) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1 = Sent',
  `requested_value` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processing_index` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '0=Waiting Response 1=email format error 2=No data found, 3=Parsing Error, 4=Waiting for parsing, 5=Parsing completed,6=partially parsing completed, 7=mark completed',
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rqtbyname` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_killed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `killed_at` datetime DEFAULT NULL,
  `killed_error_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `request_priority` int(1) NOT NULL DEFAULT 1 COMMENT '1=Normal, 2=Medium, 3=High',
  `sending_date` datetime NOT NULL COMMENT 'Sending or Failed Date',
  `request_send_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `admin_travel_request` */

DROP TABLE IF EXISTS `admin_travel_request`;

CREATE TABLE `admin_travel_request` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `cnic` bigint(25) DEFAULT NULL,
  `passport` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_id` int(5) DEFAULT NULL,
  `district_id` int(5) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `rqtbyname` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `banned_organizations` */

DROP TABLE IF EXISTS `banned_organizations`;

CREATE TABLE `banned_organizations` (
  `org_id` int(11) NOT NULL AUTO_INCREMENT,
  `org_name` varchar(250) DEFAULT NULL,
  `org_acronym` varchar(20) DEFAULT NULL,
  `drived_from_id` int(25) DEFAULT NULL,
  `notification_no` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB AUTO_INCREMENT=494 DEFAULT CHARSET=latin1;

/*Table structure for table `blocked_numbers` */

DROP TABLE IF EXISTS `blocked_numbers`;

CREATE TABLE `blocked_numbers` (
  `blocked_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `blocked_number_type` int(5) NOT NULL COMMENT '1=Mobile Number, 2=CNIC Number, 3=IMEI Number',
  `blocked_value` varchar(25) NOT NULL,
  `blocked_reason` varchar(50) NOT NULL,
  `blocked_details` blob DEFAULT NULL,
  `blocked_by` int(10) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'entry time',
  PRIMARY KEY (`blocked_id`)
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=latin1;

/*Table structure for table `case_assets_url` */

DROP TABLE IF EXISTS `case_assets_url`;

CREATE TABLE `case_assets_url` (
  `case_id` bigint(20) DEFAULT NULL,
  `server_name` varchar(100) DEFAULT NULL,
  `case_save_data_path` varchar(100) DEFAULT NULL,
  `case_download_data_path` varchar(50) DEFAULT 'www.ctfu.com'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cis_sensitive_person_acl` */

DROP TABLE IF EXISTS `cis_sensitive_person_acl`;

CREATE TABLE `cis_sensitive_person_acl` (
  `user_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `allowed_user_id` int(10) unsigned NOT NULL,
  `allowed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `cis_sensitive_search_notifications` */

DROP TABLE IF EXISTS `cis_sensitive_search_notifications`;

CREATE TABLE `cis_sensitive_search_notifications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sensitive_person_id` bigint(25) DEFAULT NULL,
  `sensitive_by` int(25) DEFAULT NULL,
  `search_by` int(25) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0 COMMENT 'seen=1, not seen=0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8899 DEFAULT CHARSET=latin1;

/*Table structure for table `cis_user_activity_timeline` */

DROP TABLE IF EXISTS `cis_user_activity_timeline`;

CREATE TABLE `cis_user_activity_timeline` (
  `timeline_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_activity_type_id` tinyint(3) unsigned NOT NULL,
  `activity_time` datetime NOT NULL,
  `person_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`timeline_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5998 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `cis_user_activity_timeline_detail` */

DROP TABLE IF EXISTS `cis_user_activity_timeline_detail`;

CREATE TABLE `cis_user_activity_timeline_detail` (
  `timeline_id` bigint(10) unsigned NOT NULL,
  `key_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key_value` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctd_police_station` */

DROP TABLE IF EXISTS `ctd_police_station`;

CREATE TABLE `ctd_police_station` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `region_id` int(2) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=907 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_accused_details` */

DROP TABLE IF EXISTS `ctfu_accused_details`;

CREATE TABLE `ctfu_accused_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `case_id` int(25) NOT NULL,
  `is_mla_initiated` tinyint(1) DEFAULT NULL COMMENT '0=NO, 1=Yes',
  `organization_position` int(5) DEFAULT NULL,
  `accused_proscription` int(5) DEFAULT NULL,
  `accused_occupation` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `ctfu_cases` */

DROP TABLE IF EXISTS `ctfu_cases`;

CREATE TABLE `ctfu_cases` (
  `case_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `case_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Case Number With respect to year e.g 26/15',
  `fir_date` date NOT NULL COMMENT 'Date OF FIR',
  `involved_persons` int(10) DEFAULT NULL,
  `arrested_persons` int(10) NOT NULL COMMENT 'Number Of Persons Arrested',
  `money_recovered` int(10) NOT NULL COMMENT 'Number Of Persons Arrested',
  `fir_police_station` int(10) unsigned NOT NULL,
  `organization_case` int(10) unsigned NOT NULL,
  `nature_of_tf_source` int(10) DEFAULT NULL,
  `channel_used` int(10) DEFAULT NULL,
  `added_by` int(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `prosecution_status` int(11) NOT NULL DEFAULT 1,
  `region` int(10) NOT NULL,
  `district` int(10) NOT NULL,
  `judgment` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `case_notification_file` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `case_fir_file` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `case_court_order_file` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`case_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_cases_accused_status_detial` */

DROP TABLE IF EXISTS `ctfu_cases_accused_status_detial`;

CREATE TABLE `ctfu_cases_accused_status_detial` (
  `record_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cases_persons_record_id` int(10) unsigned NOT NULL COMMENT 'Record ID form ctfu_cases_persons',
  `section` int(10) DEFAULT NULL,
  `punishment_type` int(10) NOT NULL COMMENT 'type of punishment from lookup table lu_punishemnt_type',
  `punishment_value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Value of punishment',
  `added_by_user_id` int(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_cases_casestudy` */

DROP TABLE IF EXISTS `ctfu_cases_casestudy`;

CREATE TABLE `ctfu_cases_casestudy` (
  `case_id` int(10) unsigned NOT NULL,
  `fatfrelevance` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Relevance for FATF Immediate Outcomes',
  `summary` blob DEFAULT NULL,
  `any_other_relevant_info` blob DEFAULT NULL,
  `outcomes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_cases_persons` */

DROP TABLE IF EXISTS `ctfu_cases_persons`;

CREATE TABLE `ctfu_cases_persons` (
  `record_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `case_id` int(10) unsigned NOT NULL COMMENT 'Case ID from ctfu_cases',
  `aies_person_id` int(10) NOT NULL COMMENT 'Person ID from aies person table',
  `ctfu_person_id` int(10) NOT NULL COMMENT 'Person ID from local ctfu person table',
  `status` int(11) NOT NULL DEFAULT 1,
  `added_by_user_id` int(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_cases_sections` */

DROP TABLE IF EXISTS `ctfu_cases_sections`;

CREATE TABLE `ctfu_cases_sections` (
  `case_id` int(10) unsigned NOT NULL,
  `section_id` int(10) NOT NULL COMMENT 'id from lu_sections'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_persons` */

DROP TABLE IF EXISTS `ctfu_persons`;

CREATE TABLE `ctfu_persons` (
  `ctfu_person_id` int(10) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`ctfu_person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_user_activity_timeline` */

DROP TABLE IF EXISTS `ctfu_user_activity_timeline`;

CREATE TABLE `ctfu_user_activity_timeline` (
  `timeline_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_activity_type_id` tinyint(3) unsigned NOT NULL,
  `activity_time` datetime NOT NULL,
  `case_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`timeline_id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_user_activity_timeline_detail` */

DROP TABLE IF EXISTS `ctfu_user_activity_timeline_detail`;

CREATE TABLE `ctfu_user_activity_timeline_detail` (
  `timeline_id` bigint(10) unsigned NOT NULL,
  `key_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key_value` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_user_request` */

DROP TABLE IF EXISTS `ctfu_user_request`;

CREATE TABLE `ctfu_user_request` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` int(10) NOT NULL COMMENT 'Reference ID to be sent to company for reference',
  `dispatch_id` int(10) NOT NULL COMMENT 'Dispatch Number for reference',
  `user_id` int(10) unsigned NOT NULL,
  `user_request_type_id` int(10) unsigned NOT NULL,
  `bank_id` int(2) DEFAULT NULL COMMENT 'Bank ID from lu_banks',
  `request_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Request Send, 2=Request Dispatched, 3=Request Received',
  `concerned_person_id` int(10) unsigned DEFAULT NULL,
  `requested_value` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'request link with a particular project.',
  `is_killed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `killed_at` datetime DEFAULT NULL,
  `killed_error_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `dispatch_date` datetime NOT NULL COMMENT 'Request Dispatch Date',
  `receive_date` datetime NOT NULL COMMENT 'Response Received From Company Date',
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `ctfu_user_request_files` */

DROP TABLE IF EXISTS `ctfu_user_request_files`;

CREATE TABLE `ctfu_user_request_files` (
  `record_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `request_bank_id` bigint(20) DEFAULT NULL,
  `dispatch_id` bigint(20) DEFAULT NULL,
  `received_file_path` varchar(255) DEFAULT NULL,
  `received_date` datetime NOT NULL COMMENT 'Response Receive Data',
  PRIMARY KEY (`record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `data_server_details` */

DROP TABLE IF EXISTS `data_server_details`;

CREATE TABLE `data_server_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_data_type` varchar(50) NOT NULL,
  `data_from_id` bigint(25) DEFAULT NULL COMMENT 'server will save data starting from this id(included)',
  `data_to_id` bigint(25) DEFAULT NULL COMMENT 'server will save data till this id(included)',
  `server_name` varchar(100) DEFAULT NULL COMMENT 'name of server where data is uploaded',
  `save_data_path` varchar(100) DEFAULT NULL COMMENT 'data path on server to save data',
  `download_data_path` varchar(100) DEFAULT NULL COMMENT 'download data path url_alias',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Table structure for table `debugging_insertion` */

DROP TABLE IF EXISTS `debugging_insertion`;

CREATE TABLE `debugging_insertion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `details` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `district` */

DROP TABLE IF EXISTS `district`;

CREATE TABLE `district` (
  `district_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` int(10) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`district_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `email_messages` */

DROP TABLE IF EXISTS `email_messages`;

CREATE TABLE `email_messages` (
  `message_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message_subject` varchar(256) DEFAULT NULL,
  `message_body` blob DEFAULT NULL,
  `sender_id` varchar(250) NOT NULL,
  `message_date` datetime DEFAULT NULL,
  `is_draft` int(11) NOT NULL DEFAULT 0,
  `is_draft_to` varchar(255) DEFAULT NULL,
  `received_file_path` varchar(255) DEFAULT NULL,
  `received_body` varchar(1000) DEFAULT NULL,
  `received_body_raw` blob NOT NULL DEFAULT 0,
  `received_date` datetime NOT NULL COMMENT 'Message received Date',
  `is_active` int(11) DEFAULT 1,
  `is_deleted` int(11) DEFAULT 0,
  `is_screened` int(1) DEFAULT 0,
  `custom_folder_id` int(11) DEFAULT 0,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1985732 DEFAULT CHARSET=latin1;

/*Table structure for table `email_read_status` */

DROP TABLE IF EXISTS `email_read_status`;

CREATE TABLE `email_read_status` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `gmail_id` int(20) NOT NULL,
  `request_id` int(20) NOT NULL,
  `is_read` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = Unread, 1 = Read',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1533991 DEFAULT CHARSET=utf8;

/*Table structure for table `email_templates` */

DROP TABLE IF EXISTS `email_templates`;

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(5) NOT NULL,
  `email_type` varchar(100) DEFAULT NULL,
  `company_id` varchar(20) DEFAULT NULL COMMENT 'mobile network code:mnc',
  `smtp_server` varchar(50) DEFAULT NULL,
  `from_email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `body_txt` text DEFAULT NULL,
  `header_img` varchar(75) DEFAULT NULL,
  `footer_img` varchar(75) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `email_templates_type` */

DROP TABLE IF EXISTS `email_templates_type`;

CREATE TABLE `email_templates_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_type_name` varchar(50) DEFAULT NULL,
  `is_active` int(11) DEFAULT 1,
  `is_deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

/*Table structure for table `email_tokens` */

DROP TABLE IF EXISTS `email_tokens`;

CREATE TABLE `email_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token_name` varchar(50) NOT NULL,
  `token` varchar(50) DEFAULT NULL,
  `extra1` bigint(50) DEFAULT NULL,
  `is_active` int(11) DEFAULT 1,
  `is_deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

/*Table structure for table `family_tree` */

DROP TABLE IF EXISTS `family_tree`;

CREATE TABLE `family_tree` (
  `family_tree_id` int(10) unsigned NOT NULL,
  `person_count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`family_tree_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `familytree_temp_files` */

DROP TABLE IF EXISTS `familytree_temp_files`;

CREATE TABLE `familytree_temp_files` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `cnic_number` bigint(25) DEFAULT NULL,
  `image_name` varchar(45) DEFAULT NULL,
  `uploaded_by_user` int(11) DEFAULT NULL,
  `upload_date` datetime DEFAULT NULL,
  `attachment_status` int(11) DEFAULT 0 COMMENT '0=waiting, 1= attached, 2= error',
  PRIMARY KEY (`row_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15178 DEFAULT CHARSET=utf8;

/*Table structure for table `files` */

DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
  `id` bigint(25) NOT NULL,
  `file` text NOT NULL,
  `type` varchar(4) NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `no_of_record` int(10) NOT NULL COMMENT 'no of records in cdr',
  `data_from_date` datetime DEFAULT NULL COMMENT 'contains data from date',
  `data_to_date` datetime DEFAULT NULL COMMENT 'contains data to date',
  `description` text DEFAULT NULL,
  `upload_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=saved but not parsed,1=partially parsed,2=fully parsed,3=parsing error',
  `error_type` tinyint(4) DEFAULT 0 COMMENT '0=no error, 1=company format not matched,2=a party not matched,3=multiple a parties,4=imei not matched,5=data already exist',
  `request_type` varchar(50) DEFAULT NULL COMMENT '1= cdr against mobile no, 2=cdr against imei no, 6=sms details against mobile no',
  `request_id` bigint(20) DEFAULT NULL,
  `company_name` varchar(50) DEFAULT NULL COMMENT 'mnc=mobile network code of company',
  `phone_number` bigint(25) NOT NULL,
  `imei` bigint(25) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `created_on` datetime DEFAULT NULL,
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_manual` tinyint(1) DEFAULT 0 COMMENT '1= manual, 2=auto',
  `created_by` bigint(20) DEFAULT NULL,
  `changed_by` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `fix_sim_owner_worklist` */

DROP TABLE IF EXISTS `fix_sim_owner_worklist`;

CREATE TABLE `fix_sim_owner_worklist` (
  `ppn_id` int(11) NOT NULL DEFAULT 0,
  `phone_number` bigint(23) NOT NULL COMMENT 'e.g. 3004158199',
  `old_person_id` bigint(11) NOT NULL,
  `new_person_id` bigint(25) unsigned NOT NULL,
  `request_id` int(10) unsigned NOT NULL DEFAULT 0,
  `reference_id` int(10) NOT NULL COMMENT 'Reference ID to be sent to company for reference',
  `request_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `fp_flat` */

DROP TABLE IF EXISTS `fp_flat`;

CREATE TABLE `fp_flat` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `left_flat_index` varchar(100) DEFAULT NULL COMMENT 'Left Flat Index',
  `left_flat_little` varchar(100) DEFAULT NULL COMMENT 'Left Flat Little',
  `left_flat_middle` varchar(100) DEFAULT NULL COMMENT 'Left Flat Middle',
  `left_flat_ring` varchar(100) DEFAULT NULL COMMENT 'Left Flat Ring',
  `left_flat_thumb` varchar(100) DEFAULT NULL COMMENT 'Left Flat Thumb',
  `right_flat_index` varchar(100) DEFAULT NULL COMMENT 'Right Flat Index',
  `right_flat_little` varchar(100) DEFAULT NULL COMMENT 'Right Flat Little',
  `right_flat_middle` varchar(100) DEFAULT NULL COMMENT 'Right Flat Middle',
  `right_flat_ring` varchar(100) DEFAULT NULL COMMENT 'Right Flat Ring',
  `right_flat_thumb` varchar(100) DEFAULT NULL COMMENT 'Right Flat Thumb',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=234 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_other_slaps` */

DROP TABLE IF EXISTS `fp_other_slaps`;

CREATE TABLE `fp_other_slaps` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `left_two_fingers` varchar(100) DEFAULT NULL,
  `left_two_fingers_0` varchar(100) DEFAULT NULL,
  `left_two_fingers_1` varchar(100) DEFAULT NULL,
  `right_two_fingers` varchar(100) DEFAULT NULL,
  `right_two_fingers_0` varchar(100) DEFAULT NULL,
  `right_two_fingers_1` varchar(100) DEFAULT NULL,
  `two_indexes` varchar(100) DEFAULT NULL,
  `two_indexes_0` varchar(100) DEFAULT NULL,
  `two_indexes_1` varchar(100) DEFAULT NULL,
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `fp_palm_prints` */

DROP TABLE IF EXISTS `fp_palm_prints`;

CREATE TABLE `fp_palm_prints` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `left_lower_half_palm` varchar(100) DEFAULT NULL COMMENT 'Left Lower Half Palm',
  `left_upper_half_palm` varchar(100) DEFAULT NULL COMMENT 'Left Upper Half Palm',
  `left_upper_half_palm_0` varchar(100) DEFAULT NULL COMMENT 'Left Upper Half Palm_0',
  `left_upper_half_palm_1` varchar(100) DEFAULT NULL COMMENT 'Left Upper Half Palm_1',
  `left_upper_half_palm_2` varchar(100) DEFAULT NULL COMMENT 'Left Upper Half Palm_2',
  `left_upper_half_palm_3` varchar(100) DEFAULT NULL COMMENT 'Left Upper Half Palm_3',
  `left_writers_palm` varchar(100) DEFAULT NULL COMMENT 'Left Writers Palm',
  `right_lower_half_palm` varchar(100) DEFAULT NULL COMMENT 'Right Lower Half Palm',
  `right_upper_half_palm` varchar(100) DEFAULT NULL COMMENT 'Right Upper Half Palm',
  `right_upper_half_palm_0` varchar(100) DEFAULT NULL COMMENT 'Right Upper Half Palm_0',
  `right_upper_half_palm_1` varchar(100) DEFAULT NULL COMMENT 'Right Upper Half Palm_1',
  `right_upper_half_palm_2` varchar(100) DEFAULT NULL COMMENT 'Right Upper Half Palm_2',
  `right_upper_half_palm_3` varchar(100) DEFAULT NULL COMMENT 'Right Upper Half Palm_3',
  `right_writers_palm` varchar(100) DEFAULT NULL COMMENT 'Right Writers Palm',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_plain_joint_left_side` */

DROP TABLE IF EXISTS `fp_plain_joint_left_side`;

CREATE TABLE `fp_plain_joint_left_side` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `plain_joint_left_side_left_index` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Left Index',
  `plain_joint_left_side_left_little` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Left Little',
  `plain_joint_left_side_left_middle` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Left Middle',
  `plain_joint_left_side_left_ring` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Left Ring',
  `plain_joint_left_side_left_thumb` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Left Thumb',
  `plain_joint_left_side_right_index` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Right Index',
  `plain_joint_left_side_right_little` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Right Little',
  `plain_joint_left_side_right_middle` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Right Middle',
  `plain_joint_left_side_right_ring` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Right Ring',
  `plain_joint_left_side_right_thumb` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Left Side - Right Thumb',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_plain_joint_right_side` */

DROP TABLE IF EXISTS `fp_plain_joint_right_side`;

CREATE TABLE `fp_plain_joint_right_side` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `plain_joint_right_side_left_ring` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Left Ring',
  `plain_joint_right_side_left_index` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Left Index',
  `plain_joint_right_side_right_thumb` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Right Thumb',
  `plain_joint_right_side_right_ring` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Right Ring',
  `plain_joint_right_side_right_middle` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Right Middle',
  `plain_joint_right_side_left_little` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Left Little',
  `plain_joint_right_side_right_index` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Right Index',
  `plain_joint_right_side_right_little` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Right Little',
  `plain_joint_right_side_left_thumb` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Left Thumb',
  `plain_joint_right_side_left_middle` varchar(100) DEFAULT NULL COMMENT 'Plain Joint Right Side - Left Middle',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=214 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_rolled` */

DROP TABLE IF EXISTS `fp_rolled`;

CREATE TABLE `fp_rolled` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `left_rolled_index` varchar(100) DEFAULT NULL COMMENT 'Left Rolled Index',
  `left_rolled_little` varchar(100) DEFAULT NULL COMMENT 'Left Rolled Little',
  `left_rolled_middle` varchar(100) DEFAULT NULL COMMENT 'Left Rolled Middle',
  `left_rolled_ring` varchar(100) DEFAULT NULL COMMENT 'Left Rolled Ring',
  `left_rolled_thumb` varchar(100) DEFAULT NULL COMMENT 'Left Rolled Thumb',
  `right_rolled_index` varchar(100) DEFAULT NULL COMMENT 'Right Rolled Index',
  `right_rolled_little` varchar(100) DEFAULT NULL COMMENT 'Right Rolled Little',
  `right_rolled_middle` varchar(100) DEFAULT NULL COMMENT 'Right Rolled Middle',
  `right_rolled_ring` varchar(100) DEFAULT NULL COMMENT 'Right Rolled Ring',
  `right_rolled_thumb` varchar(100) DEFAULT NULL COMMENT 'Right Rolled Thumb',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=225 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_rolled_down` */

DROP TABLE IF EXISTS `fp_rolled_down`;

CREATE TABLE `fp_rolled_down` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `left_index` varchar(100) DEFAULT NULL,
  `left_little` varchar(100) DEFAULT NULL,
  `left_middle` varchar(100) DEFAULT NULL,
  `left_ring` varchar(100) DEFAULT NULL,
  `left_thumb` varchar(100) DEFAULT NULL,
  `right_index` varchar(100) DEFAULT NULL,
  `right_little` varchar(100) DEFAULT NULL,
  `right_middle` varchar(100) DEFAULT NULL,
  `right_ring` varchar(100) DEFAULT NULL,
  `right_thumb` varchar(100) DEFAULT NULL,
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `fp_rolled_hypothenar` */

DROP TABLE IF EXISTS `fp_rolled_hypothenar`;

CREATE TABLE `fp_rolled_hypothenar` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `rolled_left_hypothenar` varchar(100) DEFAULT NULL COMMENT 'Rolled Left Hypothenar',
  `rolled_right_hypothenar` varchar(100) DEFAULT NULL COMMENT 'Rolled Right Hypothenar',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_rolled_joint` */

DROP TABLE IF EXISTS `fp_rolled_joint`;

CREATE TABLE `fp_rolled_joint` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `rolled_joint_left_index` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Left Index',
  `rolled_joint_left_little` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Left Little',
  `rolled_joint_left_middle` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Left Middle',
  `rolled_joint_left_ring` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Left Ring',
  `rolled_joint_left_thumb` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Left Thumb',
  `rolled_joint_right_index` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Right Index',
  `rolled_joint_right_little` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Right Little',
  `rolled_joint_right_middle` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Right Middle',
  `rolled_joint_right_ring` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Right Ring',
  `rolled_joint_right_thumb` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint - Right Thumb',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_rolled_joint_center` */

DROP TABLE IF EXISTS `fp_rolled_joint_center`;

CREATE TABLE `fp_rolled_joint_center` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `rolled_joint_center_left_index` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Left Index',
  `rolled_joint_center_left_little` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Left Little',
  `rolled_joint_center_left_middle` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Left Middle',
  `rolled_joint_center_left_ring` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Left Ring',
  `rolled_joint_center_left_thumb` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Left Thumb',
  `rolled_joint_center_right_index` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Right Index',
  `rolled_joint_center_right_little` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Right Little',
  `rolled_joint_center_right_middle` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Right Middle',
  `rolled_joint_center_right_ring` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Right Ring',
  `rolled_joint_center_right_thumb` varchar(100) DEFAULT NULL COMMENT 'Rolled Joint Center - Right Thumb',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_rolled_thenar` */

DROP TABLE IF EXISTS `fp_rolled_thenar`;

CREATE TABLE `fp_rolled_thenar` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `rolle_left_thenar` varchar(100) DEFAULT NULL COMMENT 'Rolled Left Thenar',
  `rolle_right_thenar` varchar(100) DEFAULT NULL COMMENT 'Rolled Right Thenar',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_rolled_tips` */

DROP TABLE IF EXISTS `fp_rolled_tips`;

CREATE TABLE `fp_rolled_tips` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `rolled_tip_left_index` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Left Index',
  `rolled_tip_left_little` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Left Little',
  `rolled_tip_left_middle` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Left Middle',
  `rolled_tip_left_ring` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Left Ring',
  `rolled_tip_left_thumb` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Left Thumb',
  `rolled_tip_right_index` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Right Index',
  `rolled_tip_right_little` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Right Little',
  `rolled_tip_right_middle` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Right Middle',
  `rolled_tip_right_ring` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Right Ring',
  `rolled_tip_right_thumb` varchar(100) DEFAULT NULL COMMENT 'Rolled Tip - Right Thumb',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=208 DEFAULT CHARSET=utf8;

/*Table structure for table `fp_rolled_up` */

DROP TABLE IF EXISTS `fp_rolled_up`;

CREATE TABLE `fp_rolled_up` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `left_index` varchar(100) DEFAULT NULL,
  `left_little` varchar(100) DEFAULT NULL,
  `left_middle` varchar(100) DEFAULT NULL,
  `left_ring` varchar(100) DEFAULT NULL,
  `left_thumb` varchar(100) DEFAULT NULL,
  `right_index` varchar(100) DEFAULT NULL,
  `right_little` varchar(100) DEFAULT NULL,
  `right_middle` varchar(100) DEFAULT NULL,
  `right_ring` varchar(100) DEFAULT NULL,
  `right_thumb` varchar(100) DEFAULT NULL,
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `fp_slaps` */

DROP TABLE IF EXISTS `fp_slaps`;

CREATE TABLE `fp_slaps` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) DEFAULT NULL,
  `flat_two_thumbs` varchar(100) DEFAULT NULL COMMENT 'Flat Two Thumbs',
  `flat_two_thumbs_0` varchar(100) DEFAULT NULL COMMENT 'Flat Two Thumbs_0',
  `flat_two_thumbs_1` varchar(100) DEFAULT NULL COMMENT 'Flat Two Thumbs_1',
  `left_flat_four_fingers` varchar(100) DEFAULT NULL COMMENT 'Left Flat Four Fingers',
  `left_flat_four_fingers_0` varchar(100) DEFAULT NULL COMMENT 'Left Flat Four Fingers_0',
  `left_flat_four_fingers_1` varchar(100) DEFAULT NULL COMMENT 'Left Flat Four Fingers_1',
  `left_flat_four_fingers_2` varchar(100) DEFAULT NULL COMMENT 'Left Flat Four Fingers_2',
  `left_flat_four_fingers_3` varchar(100) DEFAULT NULL COMMENT 'Left Flat Four Fingers_3',
  `right_flat_four_fingers` varchar(100) DEFAULT NULL COMMENT 'Right Flat Four Fingers',
  `right_flat_four_fingers_0` varchar(100) DEFAULT NULL COMMENT 'Right Flat Four Fingers_0',
  `right_flat_four_fingers_1` varchar(100) DEFAULT NULL COMMENT 'Right Flat Four Fingers_1',
  `right_flat_four_fingers_2` varchar(100) DEFAULT NULL COMMENT 'Right Flat Four Fingers_2',
  `right_flat_four_fingers_3` varchar(100) DEFAULT NULL COMMENT 'Right Flat Four Fingers_3',
  `user_id` int(5) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=245 DEFAULT CHARSET=utf8;

/*Table structure for table `headquarter` */

DROP TABLE IF EXISTS `headquarter`;

CREATE TABLE `headquarter` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `id_generator` */

DROP TABLE IF EXISTS `id_generator`;

CREATE TABLE `id_generator` (
  `id_type` varchar(50) NOT NULL,
  `last_id` bigint(20) NOT NULL,
  `comment` varchar(100) DEFAULT NULL,
  `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `idx_id_type` (`id_type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `inner_token` */

DROP TABLE IF EXISTS `inner_token`;

CREATE TABLE `inner_token` (
  `key_id` int(11) NOT NULL,
  `key_type` varchar(50) NOT NULL,
  `key_value` varchar(256) NOT NULL,
  `key_value_2` varchar(256) DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`key_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `int_projects` */

DROP TABLE IF EXISTS `int_projects`;

CREATE TABLE `int_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(500) DEFAULT NULL,
  `region_id` int(5) DEFAULT NULL COMMENT 'Region Name of Project',
  `district_id` int(5) NOT NULL COMMENT 'District ID of project',
  `project_status` int(5) DEFAULT 0 COMMENT '0=open, 1 = close',
  `details` varchar(1000) DEFAULT NULL,
  `created_by` int(5) NOT NULL,
  `modified_by` int(5) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1701 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `int_projects_organizations` */

DROP TABLE IF EXISTS `int_projects_organizations`;

CREATE TABLE `int_projects_organizations` (
  `project_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `kpk_accused_person` */

DROP TABLE IF EXISTS `kpk_accused_person`;

CREATE TABLE `kpk_accused_person` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `kpid` int(11) DEFAULT NULL,
  `name` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnic` double DEFAULT NULL,
  `perm_add_place` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terrorism_attack_id` int(11) DEFAULT NULL,
  `motive_name` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fir_no` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fir_date` int(11) DEFAULT NULL,
  `section_law` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ps_name` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_status` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pre_status_date` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `case_source` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motive_detail` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occ_distt` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14373 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `loginattempts` */

DROP TABLE IF EXISTS `loginattempts`;

CREATE TABLE `loginattempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IP` varchar(20) DEFAULT NULL,
  `Attempts` int(11) DEFAULT 10,
  `Username` varchar(20) DEFAULT NULL,
  `LastLogin` datetime DEFAULT NULL,
  `is_block` int(11) DEFAULT 1,
  `reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_accused_status` */

DROP TABLE IF EXISTS `lu_accused_status`;

CREATE TABLE `lu_accused_status` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_act` */

DROP TABLE IF EXISTS `lu_act`;

CREATE TABLE `lu_act` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `abbreviation` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_api` */

DROP TABLE IF EXISTS `lu_api`;

CREATE TABLE `lu_api` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `api_type` varchar(100) DEFAULT NULL,
  `base_url` varchar(100) DEFAULT NULL,
  `api_url` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `parameters` varchar(500) DEFAULT NULL,
  `response` varchar(500) DEFAULT NULL,
  `method` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_api_error_type` */

DROP TABLE IF EXISTS `lu_api_error_type`;

CREATE TABLE `lu_api_error_type` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `error` int(5) DEFAULT NULL,
  `message` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_api_user_types` */

DROP TABLE IF EXISTS `lu_api_user_types`;

CREATE TABLE `lu_api_user_types` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_api_users` */

DROP TABLE IF EXISTS `lu_api_users`;

CREATE TABLE `lu_api_users` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `api_type_id` int(5) DEFAULT NULL,
  `key` varchar(100) DEFAULT NULL,
  `api_user_type_id` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_arms_used` */

DROP TABLE IF EXISTS `lu_arms_used`;

CREATE TABLE `lu_arms_used` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `arms_used` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_associate_detail` */

DROP TABLE IF EXISTS `lu_associate_detail`;

CREATE TABLE `lu_associate_detail` (
  `id` int(11) DEFAULT NULL,
  `associate_name` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_banks` */

DROP TABLE IF EXISTS `lu_banks`;

CREATE TABLE `lu_banks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `bank_type` tinyint(4) DEFAULT NULL COMMENT '1="commercial bank", 2= "Microfinance Banks ", 3="Development Financial Institutions (DFIs)", 4= "NON-BANK FINANCIAL COMPANIES (NBFCs)", 5=" Investment Banks", 6="Modaraba Companies", 7="Funds", 8="Branchless Banking"',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=latin1;

/*Table structure for table `lu_branchless_transactions` */

DROP TABLE IF EXISTS `lu_branchless_transactions`;

CREATE TABLE `lu_branchless_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_branchless_transation` varchar(100) NOT NULL,
  `bank_company_name` varchar(100) DEFAULT NULL,
  `transaction_code` int(10) NOT NULL,
  `transaction_code_countrycode` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_caste` */

DROP TABLE IF EXISTS `lu_caste`;

CREATE TABLE `lu_caste` (
  `id` int(11) DEFAULT NULL,
  `caste` text DEFAULT NULL,
  KEY `lu_caste_id_IDX` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_category` */

DROP TABLE IF EXISTS `lu_category`;

CREATE TABLE `lu_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_cis_user_activity_type` */

DROP TABLE IF EXISTS `lu_cis_user_activity_type`;

CREATE TABLE `lu_cis_user_activity_type` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail_level` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=no view details, 1=view details',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_communication_mode` */

DROP TABLE IF EXISTS `lu_communication_mode`;

CREATE TABLE `lu_communication_mode` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `communication_mode` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_contact_type` */

DROP TABLE IF EXISTS `lu_contact_type`;

CREATE TABLE `lu_contact_type` (
  `id` int(5) DEFAULT NULL,
  `contact_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_conveyance` */

DROP TABLE IF EXISTS `lu_conveyance`;

CREATE TABLE `lu_conveyance` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `conveyance` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_country` */

DROP TABLE IF EXISTS `lu_country`;

CREATE TABLE `lu_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iso` char(2) NOT NULL,
  `name` varchar(80) NOT NULL,
  `nicename` varchar(80) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `phonecode` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=254 DEFAULT CHARSET=latin1;

/*Table structure for table `lu_ctfu_user_activity_type` */

DROP TABLE IF EXISTS `lu_ctfu_user_activity_type`;

CREATE TABLE `lu_ctfu_user_activity_type` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail_level` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=no view details, 1=view details',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_education_level` */

DROP TABLE IF EXISTS `lu_education_level`;

CREATE TABLE `lu_education_level` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `education_level` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_financial_aid` */

DROP TABLE IF EXISTS `lu_financial_aid`;

CREATE TABLE `lu_financial_aid` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `financial_aid` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_finger_print_category` */

DROP TABLE IF EXISTS `lu_finger_print_category`;

CREATE TABLE `lu_finger_print_category` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `fp_category` varchar(100) DEFAULT NULL,
  `fp_category_description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_finger_print_types` */

DROP TABLE IF EXISTS `lu_finger_print_types`;

CREATE TABLE `lu_finger_print_types` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `fp_category_id` int(5) DEFAULT NULL,
  `finger_print_type` varchar(100) DEFAULT NULL,
  `fp_file_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_fund_transfer_channels` */

DROP TABLE IF EXISTS `lu_fund_transfer_channels`;

CREATE TABLE `lu_fund_transfer_channels` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_funding_ways` */

DROP TABLE IF EXISTS `lu_funding_ways`;

CREATE TABLE `lu_funding_ways` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `ways_fund_raising` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_funds_utilization` */

DROP TABLE IF EXISTS `lu_funds_utilization`;

CREATE TABLE `lu_funds_utilization` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `utilization` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_how_recce_was_done` */

DROP TABLE IF EXISTS `lu_how_recce_was_done`;

CREATE TABLE `lu_how_recce_was_done` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `how_reccee_done` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_identity` */

DROP TABLE IF EXISTS `lu_identity`;

CREATE TABLE `lu_identity` (
  `id` int(5) DEFAULT NULL,
  `identity` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_investment_type` */

DROP TABLE IF EXISTS `lu_investment_type`;

CREATE TABLE `lu_investment_type` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_ip_user_list` */

DROP TABLE IF EXISTS `lu_ip_user_list`;

CREATE TABLE `lu_ip_user_list` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`row_id`)
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_languages` */

DROP TABLE IF EXISTS `lu_languages`;

CREATE TABLE `lu_languages` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `language_name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

/*Table structure for table `lu_manu` */

DROP TABLE IF EXISTS `lu_manu`;

CREATE TABLE `lu_manu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manu_name` varchar(100) DEFAULT NULL,
  `manu_details` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_marital_status` */

DROP TABLE IF EXISTS `lu_marital_status`;

CREATE TABLE `lu_marital_status` (
  `id` int(5) DEFAULT NULL,
  `marital_status` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_organization_designation` */

DROP TABLE IF EXISTS `lu_organization_designation`;

CREATE TABLE `lu_organization_designation` (
  `id` int(5) NOT NULL,
  `organization_designation` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_organization_stance` */

DROP TABLE IF EXISTS `lu_organization_stance`;

CREATE TABLE `lu_organization_stance` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `organization_stance` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_person_activity_type` */

DROP TABLE IF EXISTS `lu_person_activity_type`;

CREATE TABLE `lu_person_activity_type` (
  `id` tinyint(3) unsigned NOT NULL,
  `label` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_profession` */

DROP TABLE IF EXISTS `lu_profession`;

CREATE TABLE `lu_profession` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `profession_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_proscription` */

DROP TABLE IF EXISTS `lu_proscription`;

CREATE TABLE `lu_proscription` (
  `id` int(5) NOT NULL,
  `proscription` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_prosecution_status` */

DROP TABLE IF EXISTS `lu_prosecution_status`;

CREATE TABLE `lu_prosecution_status` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_province` */

DROP TABLE IF EXISTS `lu_province`;

CREATE TABLE `lu_province` (
  `province_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
  PRIMARY KEY (`province_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_purpose_fund_raising` */

DROP TABLE IF EXISTS `lu_purpose_fund_raising`;

CREATE TABLE `lu_purpose_fund_raising` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `purpose` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_ranks` */

DROP TABLE IF EXISTS `lu_ranks`;

CREATE TABLE `lu_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_reason_to_assign_trgt` */

DROP TABLE IF EXISTS `lu_reason_to_assign_trgt`;

CREATE TABLE `lu_reason_to_assign_trgt` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `reason` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_relation_type` */

DROP TABLE IF EXISTS `lu_relation_type`;

CREATE TABLE `lu_relation_type` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `relation_name` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

/*Table structure for table `lu_religion` */

DROP TABLE IF EXISTS `lu_religion`;

CREATE TABLE `lu_religion` (
  `id` int(11) DEFAULT NULL,
  `religion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_sect` */

DROP TABLE IF EXISTS `lu_sect`;

CREATE TABLE `lu_sect` (
  `id` int(11) DEFAULT NULL,
  `sect` text DEFAULT NULL,
  `religion_id` int(11) DEFAULT NULL,
  `abr` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_sections` */

DROP TABLE IF EXISTS `lu_sections`;

CREATE TABLE `lu_sections` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `section_value` varchar(100) DEFAULT NULL,
  `section_description` varchar(100) DEFAULT NULL,
  `act_name` varchar(100) DEFAULT NULL,
  `is_deleted` int(10) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_sensitive_departments` */

DROP TABLE IF EXISTS `lu_sensitive_departments`;

CREATE TABLE `lu_sensitive_departments` (
  `id` int(5) NOT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_subscriber_flags` */

DROP TABLE IF EXISTS `lu_subscriber_flags`;

CREATE TABLE `lu_subscriber_flags` (
  `id` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_suspect_user_activity_type` */

DROP TABLE IF EXISTS `lu_suspect_user_activity_type`;

CREATE TABLE `lu_suspect_user_activity_type` (
  `activity_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `activity_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_tags` */

DROP TABLE IF EXISTS `lu_tags`;

CREATE TABLE `lu_tags` (
  `id` int(11) NOT NULL,
  `tag_name` varchar(100) DEFAULT NULL,
  `tag_description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_tf_sources` */

DROP TABLE IF EXISTS `lu_tf_sources`;

CREATE TABLE `lu_tf_sources` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `lu_training_camp` */

DROP TABLE IF EXISTS `lu_training_camp`;

CREATE TABLE `lu_training_camp` (
  `id` int(5) DEFAULT NULL,
  `training_camp` varchar(100) DEFAULT NULL,
  `tc_adrs` varchar(250) DEFAULT NULL,
  `head_name` varchar(250) DEFAULT NULL,
  `training_site` varchar(250) DEFAULT NULL,
  `trainig_duration` varchar(250) DEFAULT NULL,
  `training_type` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_training_type` */

DROP TABLE IF EXISTS `lu_training_type`;

CREATE TABLE `lu_training_type` (
  `id` int(5) DEFAULT NULL,
  `training_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `lu_user_access_level` */

DROP TABLE IF EXISTS `lu_user_access_level`;

CREATE TABLE `lu_user_access_level` (
  `id` tinyint(3) unsigned NOT NULL,
  `item` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `user_role` bigint(20) DEFAULT NULL,
  `permission` int(11) DEFAULT 1,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_user_access_type` */

DROP TABLE IF EXISTS `lu_user_access_type`;

CREATE TABLE `lu_user_access_type` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_user_activity_type` */

DROP TABLE IF EXISTS `lu_user_activity_type`;

CREATE TABLE `lu_user_activity_type` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_user_cft_type` */

DROP TABLE IF EXISTS `lu_user_cft_type`;

CREATE TABLE `lu_user_cft_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Table structure for table `lu_user_request_type` */

DROP TABLE IF EXISTS `lu_user_request_type`;

CREATE TABLE `lu_user_request_type` (
  `id` tinyint(3) unsigned NOT NULL,
  `label` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `lu_visit_purpose` */

DROP TABLE IF EXISTS `lu_visit_purpose`;

CREATE TABLE `lu_visit_purpose` (
  `id` int(5) DEFAULT NULL,
  `visit_purpose` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `manu_management` */

DROP TABLE IF EXISTS `manu_management`;

CREATE TABLE `manu_management` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `manu_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `access_status` tinyint(4) NOT NULL COMMENT '0=No Access, 1=Access',
  `updated_by_user_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=401 DEFAULT CHARSET=utf8;

/*Table structure for table `message_attachments` */

DROP TABLE IF EXISTS `message_attachments`;

CREATE TABLE `message_attachments` (
  `sr_no` bigint(20) NOT NULL,
  `message_id` bigint(20) DEFAULT NULL,
  `attachment_type` int(11) DEFAULT NULL COMMENT 'This will tell content type for 1 for photos 2 for videos. etc',
  `attachment_id` bigint(20) DEFAULT NULL,
  `attachment_token` varchar(255) DEFAULT NULL,
  `attachment_date` date DEFAULT NULL,
  `sender_member_id` bigint(20) DEFAULT NULL,
  `actual_file_name` varchar(200) DEFAULT NULL,
  `attachment_name` varchar(100) DEFAULT NULL,
  `is_external` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `message_custom_folders` */

DROP TABLE IF EXISTS `message_custom_folders`;

CREATE TABLE `message_custom_folders` (
  `custom_folder_id` int(11) NOT NULL,
  `folder_name` varchar(256) DEFAULT NULL,
  `owner_member_id` bigint(20) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `is_deleted` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `message_recipients` */

DROP TABLE IF EXISTS `message_recipients`;

CREATE TABLE `message_recipients` (
  `sr_no` bigint(20) NOT NULL,
  `recipient_id` bigint(20) DEFAULT NULL,
  `message_id` bigint(20) DEFAULT NULL,
  `sender_id` bigint(20) DEFAULT NULL,
  `date_received` datetime DEFAULT NULL,
  `is_read` int(11) DEFAULT 0,
  `date_read` datetime DEFAULT NULL,
  `recipient_type` int(11) DEFAULT NULL COMMENT 'This will tell if it is received as to, cc or bcc',
  `custom_folder_id` int(11) DEFAULT NULL COMMENT 'This will contain any custom folder id. if null then it means message is not in custom folder',
  `is_external` int(11) DEFAULT 0,
  `external_email` varchar(250) DEFAULT NULL,
  `is_active` int(11) DEFAULT 1,
  `is_deleted` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `migration_invalid_phones_backup` */

DROP TABLE IF EXISTS `migration_invalid_phones_backup`;

CREATE TABLE `migration_invalid_phones_backup` (
  `person_id` bigint(25) NOT NULL,
  `phone` bigint(25) NOT NULL,
  `request_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `mobile_companies` */

DROP TABLE IF EXISTS `mobile_companies`;

CREATE TABLE `mobile_companies` (
  `company_id` int(2) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mnc` int(2) NOT NULL,
  `check_counter` int(40) DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `nadra_profile_stats` */

DROP TABLE IF EXISTS `nadra_profile_stats`;

CREATE TABLE `nadra_profile_stats` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `region_id` int(5) NOT NULL,
  `date` date DEFAULT NULL,
  `count` int(100) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;

/*Table structure for table `old_data` */

DROP TABLE IF EXISTS `old_data`;

CREATE TABLE `old_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country` int(1) DEFAULT 0 COMMENT '0=Pakistan, 1=Foreign',
  `project_id` int(5) DEFAULT NULL,
  `phone_number` bigint(23) NOT NULL COMMENT 'e.g. 3004158199',
  `activation_date` date DEFAULT NULL,
  `imsi_number` bigint(40) DEFAULT NULL,
  `imei_number` bigint(32) DEFAULT NULL,
  `first_name` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnic_number` bigint(25) DEFAULT NULL,
  `address` blob DEFAULT NULL,
  `con_type` int(1) DEFAULT 0 COMMENT '0=Pre-paid, 1=Post-paid',
  `status` int(1) DEFAULT 0 COMMENT '0=Active, 1=In-Active',
  `file` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `other_numbers` */

DROP TABLE IF EXISTS `other_numbers`;

CREATE TABLE `other_numbers` (
  `person_id` bigint(11) DEFAULT NULL,
  `phone_number` bigint(30) NOT NULL,
  `sim_activated_at` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1=Active, 0=In-Active',
  `mnc` int(2) DEFAULT NULL COMMENT '11=PTCL, 12=International ',
  `user_id` int(5) NOT NULL,
  `contact_type` tinyint(1) DEFAULT 1 COMMENT ' ''1=personal,2=home,3=office'' ',
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person` */

DROP TABLE IF EXISTS `person`;

CREATE TABLE `person` (
  `person_id` int(10) NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_nadra_profile_exists` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `view_access_level_id` tinyint(3) unsigned NOT NULL COMMENT 'Min. access level required to view this resource',
  `edit_access_level_id` tinyint(3) unsigned NOT NULL COMMENT 'Min. access level required to edit this resource',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_complete` tinyint(1) NOT NULL COMMENT '0=incomplete, 1=complete',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'zero for no',
  `user_id` int(11) NOT NULL,
  `view_count` int(10) NOT NULL COMMENT 'Person Profile View Count',
  `image_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_id` int(5) DEFAULT NULL,
  `region_id` int(5) DEFAULT NULL,
  `police_station_id` int(5) DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_4th_schedule_tag` */

DROP TABLE IF EXISTS `person_4th_schedule_tag`;

CREATE TABLE `person_4th_schedule_tag` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `tag_id` int(5) DEFAULT NULL,
  `person_id` int(25) DEFAULT NULL,
  `notification_number` varchar(100) DEFAULT NULL COMMENT '4th schedulte notification number',
  `issue_date` date DEFAULT NULL COMMENT 'notification issue date',
  `expiry_date` date DEFAULT NULL COMMENT 'notification expiry date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `person_activity_timeline` */

DROP TABLE IF EXISTS `person_activity_timeline`;

CREATE TABLE `person_activity_timeline` (
  `timeline_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `person_activity_type_id` tinyint(3) unsigned NOT NULL,
  `logged_at` datetime NOT NULL,
  PRIMARY KEY (`timeline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_activity_timeline_detail` */

DROP TABLE IF EXISTS `person_activity_timeline_detail`;

CREATE TABLE `person_activity_timeline_detail` (
  `timeline_id` bigint(20) unsigned NOT NULL,
  `key_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_affiliations` */

DROP TABLE IF EXISTS `person_affiliations`;

CREATE TABLE `person_affiliations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `organization_id` int(5) DEFAULT NULL,
  `ideological_stance` tinyint(4) DEFAULT NULL COMMENT '1=hatred against state,2=hatred against india,3=sectarian,4=other',
  `details` varchar(500) DEFAULT NULL COMMENT 'What Was Recruitment Procedure? / other details',
  `self_recruitment_details` varchar(500) DEFAULT NULL COMMENT 'Did You Ever Recruit? How Did You Do It',
  `is_trained` tinyint(1) DEFAULT 0 COMMENT '0=no, 1=yes',
  `designation` int(5) DEFAULT NULL COMMENT '1=president,2=Vice President, 3=Secretary,4=member,5=sympathizer, 6=coordinator, 7=commander,8=facilitator, 9=financier, 10=founder,11=handler,12=Ex 13=Unknown,14=Other',
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34084 DEFAULT CHARSET=latin1;

/*Table structure for table `person_asset_freezing_status` */

DROP TABLE IF EXISTS `person_asset_freezing_status`;

CREATE TABLE `person_asset_freezing_status` (
  `person_id` int(25) NOT NULL,
  `lettertype` tinyint(1) DEFAULT NULL COMMENT '1=Letter to Deputy Commissioner, 2=Letter to Excuse&Texation,3=Letter to Dist.Collector Revenue, 4=Letter to Governor State Bank, 5=Action taken against Assets Freezing',
  `letterno` varchar(30) DEFAULT NULL,
  `letterdate` varchar(15) DEFAULT NULL COMMENT '0=No, 2=Yes,',
  `is_reminder` tinyint(3) NOT NULL,
  `letterbrief` varchar(1000) DEFAULT NULL,
  `file_link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'document link',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_deleted` tinyint(3) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1820 DEFAULT CHARSET=latin1;

/*Table structure for table `person_assets` */

DROP TABLE IF EXISTS `person_assets`;

CREATE TABLE `person_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `asset_name` varchar(100) NOT NULL,
  `details` varchar(1000) NOT NULL,
  `file_link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moveable_immovable` int(11) DEFAULT 0 COMMENT '0=Unknown, 1=Moveable, 2=Immovable',
  `since_year` bigint(5) DEFAULT NULL,
  `asset_value` bigint(50) DEFAULT NULL,
  `asset_acquired_how` varchar(45) DEFAULT NULL,
  `asset_type` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3271 DEFAULT CHARSET=latin1;

/*Table structure for table `person_assets_url` */

DROP TABLE IF EXISTS `person_assets_url`;

CREATE TABLE `person_assets_url` (
  `person_id` bigint(20) DEFAULT NULL,
  `server_name` varchar(100) DEFAULT NULL,
  `person_save_data_path` varchar(100) DEFAULT NULL,
  `person_download_data_path` varchar(50) DEFAULT 'www.aiesmail.com'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `person_assetsprobe_status` */

DROP TABLE IF EXISTS `person_assetsprobe_status`;

CREATE TABLE `person_assetsprobe_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `lock_status` int(11) DEFAULT 0 COMMENT '0=Un-Locked, 1=Locked',
  `user_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  UNIQUE KEY `assetsprobe_status_UN` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1145 DEFAULT CHARSET=utf8;

/*Table structure for table `person_associate_detail` */

DROP TABLE IF EXISTS `person_associate_detail`;

CREATE TABLE `person_associate_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `criminal_record_id` bigint(10) DEFAULT NULL,
  `person_id` int(25) DEFAULT NULL,
  `associate_type_id` varchar(30) DEFAULT NULL,
  `associate_pid` int(25) DEFAULT NULL,
  `user_id` int(5) DEFAULT NULL,
  `associate_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `person_backup_20260411_121633` */

DROP TABLE IF EXISTS `person_backup_20260411_121633`;

CREATE TABLE `person_backup_20260411_121633` (
  `person_id` int(10) NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `person_backup_20260413_153901` */

DROP TABLE IF EXISTS `person_backup_20260413_153901`;

CREATE TABLE `person_backup_20260413_153901` (
  `person_id` int(10) NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `person_backup_20260418_134631` */

DROP TABLE IF EXISTS `person_backup_20260418_134631`;

CREATE TABLE `person_backup_20260418_134631` (
  `person_id` int(10) NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `person_banks` */

DROP TABLE IF EXISTS `person_banks`;

CREATE TABLE `person_banks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `atm_number` bigint(25) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `is_internet_banking` tinyint(4) DEFAULT 0 COMMENT '0=no, 1=yes',
  `bank_name` int(5) DEFAULT NULL COMMENT 'lu_bank ',
  `ban_bank` int(11) DEFAULT NULL COMMENT '''1: Blocked 0: Not Blocked'',',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1439 DEFAULT CHARSET=latin1;

/*Table structure for table `person_banks_details` */

DROP TABLE IF EXISTS `person_banks_details`;

CREATE TABLE `person_banks_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_record_id` bigint(25) DEFAULT NULL,
  `opening_balance` bigint(25) DEFAULT NULL,
  `opening_balance_date` date DEFAULT NULL,
  `closing_balance` bigint(25) DEFAULT NULL,
  `closing_balance_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

/*Table structure for table `person_bparty_count` */

DROP TABLE IF EXISTS `person_bparty_count`;

CREATE TABLE `person_bparty_count` (
  `id` int(200) NOT NULL AUTO_INCREMENT,
  `other_person_phone_number` varchar(50) NOT NULL,
  `count` int(20) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3135986 DEFAULT CHARSET=utf8;

/*Table structure for table `person_call_log` */

DROP TABLE IF EXISTS `person_call_log`;

CREATE TABLE `person_call_log` (
  `person_call_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `phone_number` bigint(25) DEFAULT NULL COMMENT 'e.g. 3001234567',
  `other_person_phone_number` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'without zero (0)',
  `duration_in_seconds` int(10) unsigned NOT NULL,
  `is_outgoing` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '1=outgoing, 0=incoming',
  `longitude` decimal(12,8) DEFAULT NULL,
  `latitude` decimal(12,8) DEFAULT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_at` datetime DEFAULT NULL,
  `upload_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `imei_number` bigint(25) NOT NULL,
  `imsi_number` bigint(25) NOT NULL,
  `mnc` int(2) DEFAULT NULL COMMENT 'mobile network code:mnc',
  `lac_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cell_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `call_end_at` datetime NOT NULL,
  `sector` tinyint(4) NOT NULL,
  PRIMARY KEY (`person_call_log_id`),
  KEY `phone_number` (`phone_number`),
  KEY `person_call_log_other_person_phone_number_IDX` (`other_person_phone_number`) USING BTREE,
  KEY `idx_pid_phone_callat` (`person_id`,`phone_number`,`call_at`),
  KEY `idx_pid_imei` (`person_id`,`imei_number`)
) ENGINE=InnoDB AUTO_INCREMENT=139929898 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_category` */

DROP TABLE IF EXISTS `person_category`;

CREATE TABLE `person_category` (
  `person_id` int(10) unsigned NOT NULL,
  `category_id` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'white=0, gray=1, black=2',
  `user_id` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int(5) NOT NULL DEFAULT 1 COMMENT '1= unkonw project'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_category_history` */

DROP TABLE IF EXISTS `person_category_history`;

CREATE TABLE `person_category_history` (
  `person_id` int(10) unsigned NOT NULL,
  `old_category_id` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'white=0, gray=1, black=2',
  `new_category_id` tinyint(1) NOT NULL COMMENT 'white=0, gray=1, black=2',
  `user_id` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int(5) NOT NULL DEFAULT 1 COMMENT '1= unkonw project'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_contacted_person` */

DROP TABLE IF EXISTS `person_contacted_person`;

CREATE TABLE `person_contacted_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_person_id` int(10) unsigned NOT NULL,
  `second_person_id` int(10) unsigned NOT NULL,
  `contact_made_at` datetime NOT NULL,
  `person_activity_type_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_criminal_activity_detail` */

DROP TABLE IF EXISTS `person_criminal_activity_detail`;

CREATE TABLE `person_criminal_activity_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `criminal_record_id` bigint(10) DEFAULT NULL,
  `person_id` int(25) DEFAULT NULL,
  `financial_aid_id` int(5) DEFAULT NULL,
  `rsn_assgn_trgt_id` int(5) DEFAULT NULL COMMENT 'reason to assign target',
  `how_recce_done_id` int(5) DEFAULT NULL,
  `arms_used_id` int(5) DEFAULT NULL,
  `conveyance_id_used_bfr` int(5) DEFAULT NULL,
  `conveyance_id_used_aftr` int(5) DEFAULT NULL,
  `communication_mode_id` int(5) DEFAULT NULL,
  `crime_methodology` varchar(100) DEFAULT NULL,
  `how_many_persons_did_recce` int(5) DEFAULT NULL COMMENT 'persons',
  `cover_adopted` varchar(100) DEFAULT NULL,
  `when_arms_provided` varchar(100) DEFAULT NULL,
  `planning_crime` varchar(100) DEFAULT NULL,
  `other_details` varchar(1500) DEFAULT NULL,
  `how_arms_carried` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8;

/*Table structure for table `person_criminal_psychological_profile` */

DROP TABLE IF EXISTS `person_criminal_psychological_profile`;

CREATE TABLE `person_criminal_psychological_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `problem_faced_growingup` tinyint(2) NOT NULL,
  `belongto_religious_family` tinyint(2) NOT NULL,
  `whatis_more_important` tinyint(2) NOT NULL,
  `isit_jihad_without_state_approval` tinyint(2) NOT NULL,
  `how_often_prayer_offered` tinyint(2) NOT NULL,
  `how_believe_inwar_inname_ofjihad` tinyint(2) NOT NULL,
  `isviolence_justified_inislam` tinyint(2) NOT NULL,
  `how_exremist_views_developed` tinyint(2) NOT NULL,
  `did_family_oppose_views` tinyint(2) NOT NULL,
  `atwhat_age_started_violence` tinyint(2) NOT NULL,
  `what_drove_toterrorism` tinyint(2) NOT NULL,
  `did_family_know_terrorism` tinyint(2) NOT NULL,
  `can_you_recall_incident` blob DEFAULT NULL,
  `criminal_activity_prior_militancy` blob DEFAULT NULL,
  `other_details` blob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=latin1;

/*Table structure for table `person_criminal_record` */

DROP TABLE IF EXISTS `person_criminal_record`;

CREATE TABLE `person_criminal_record` (
  `person_id` int(25) NOT NULL,
  `fir_number` int(5) DEFAULT NULL,
  `fir_date` varchar(15) DEFAULT NULL,
  `police_station_id` int(3) DEFAULT NULL,
  `sections_applied` varchar(250) DEFAULT NULL,
  `case_position` tinyint(1) DEFAULT NULL COMMENT '1=Under Investigation,2=Under Trial,3= Convicted,4=Discharged',
  `accused_position` tinyint(1) NOT NULL COMMENT '1=Under Investigation,2=Under Trial,3= Convicted,4=Discharged',
  `user_id` int(25) NOT NULL,
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4284 DEFAULT CHARSET=latin1;

/*Table structure for table `person_detail_info` */

DROP TABLE IF EXISTS `person_detail_info`;

CREATE TABLE `person_detail_info` (
  `person_id` int(25) NOT NULL,
  `alias` varchar(30) DEFAULT NULL,
  `dob` text DEFAULT NULL,
  `religion` varchar(30) DEFAULT NULL COMMENT '0=muslim,1=Non-Muslim,',
  `marital_status` tinyint(1) DEFAULT NULL COMMENT '1=single,2=married,3=divorced,4=widowed,5=separated',
  `temporary_address` varchar(100) DEFAULT NULL,
  `police_station_id` int(5) DEFAULT NULL,
  `district_id` int(2) DEFAULT NULL,
  `region_id` int(2) DEFAULT NULL,
  `physical_appearance` varchar(500) DEFAULT NULL,
  `place_of_birth` varchar(100) DEFAULT NULL,
  `sect` int(5) DEFAULT NULL,
  `caste` int(5) DEFAULT NULL,
  `gender` tinyint(4) DEFAULT NULL COMMENT '1=male, 2=female,3=other',
  `nationality` int(5) DEFAULT NULL,
  `is_sensitive_department` tinyint(4) DEFAULT 0 COMMENT 'otherwise lu_sensitive_department id',
  `mother_tongue` tinyint(4) DEFAULT NULL,
  `language_read_write` varchar(100) DEFAULT NULL,
  `language_speak` varchar(100) DEFAULT NULL,
  `language_accent` varchar(100) DEFAULT NULL,
  `other_details` blob DEFAULT NULL,
  KEY `person_detail_info_person_id_IDX` (`person_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `person_device_numbers` */

DROP TABLE IF EXISTS `person_device_numbers`;

CREATE TABLE `person_device_numbers` (
  `device_id` int(11) DEFAULT NULL,
  `phone_number` bigint(25) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL COMMENT '1=active, 0=inactive',
  `first_use` datetime NOT NULL,
  `last_use` datetime NOT NULL,
  KEY `phone_number` (`phone_number`),
  KEY `idx_device_phone` (`device_id`,`phone_number`),
  KEY `idx_phone_active` (`phone_number`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `person_education` */

DROP TABLE IF EXISTS `person_education`;

CREATE TABLE `person_education` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `edu_type` tinyint(1) DEFAULT NULL COMMENT '0=Religious, 1=non-religious',
  `degree_name` varchar(50) DEFAULT NULL,
  `complete_year` int(4) DEFAULT NULL,
  `institute_name` varchar(200) DEFAULT NULL,
  `education_level` int(5) DEFAULT NULL COMMENT '1=primary,2=middle,3=matric,4=intermediate,5=bachelor,6=master,7=Mphil,8=Phd',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7676 DEFAULT CHARSET=latin1;

/*Table structure for table `person_expense_details` */

DROP TABLE IF EXISTS `person_expense_details`;

CREATE TABLE `person_expense_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `expense_name` varchar(100) NOT NULL,
  `expense_amount` bigint(25) DEFAULT NULL,
  `details` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2397 DEFAULT CHARSET=latin1;

/*Table structure for table `person_features_disabilities_language` */

DROP TABLE IF EXISTS `person_features_disabilities_language`;

CREATE TABLE `person_features_disabilities_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `disabilities` varchar(50) NOT NULL COMMENT 'disabilities list',
  `weaknesses` varchar(50) NOT NULL COMMENT 'weaknesses list',
  `other_feature` varchar(50) NOT NULL COMMENT 'features list',
  `mother_tongue` tinyint(2) NOT NULL,
  `languages_read_write` varchar(50) NOT NULL COMMENT 'languages list',
  `languages_speak` varchar(50) NOT NULL COMMENT 'speak list',
  `accent` tinyint(2) NOT NULL,
  `user_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `person_financial_information` */

DROP TABLE IF EXISTS `person_financial_information`;

CREATE TABLE `person_financial_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `profession_id` int(5) DEFAULT NULL,
  `financial_status_id` int(5) DEFAULT NULL,
  `is_in_fundraising` tinyint(4) DEFAULT 0 COMMENT '0=no, 1=yes',
  `funding_purpose_id` int(5) DEFAULT NULL,
  `funding_ways_id` int(5) DEFAULT NULL,
  `funds_utilization_id` int(5) DEFAULT NULL,
  `other_details` blob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1683 DEFAULT CHARSET=latin1;

/*Table structure for table `person_foreigner_profile` */

DROP TABLE IF EXISTS `person_foreigner_profile`;

CREATE TABLE `person_foreigner_profile` (
  `person_id` int(10) NOT NULL,
  `cnic_number` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'POR#, Refugee Card No, ',
  `person_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person_g_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person_gender` tinyint(1) DEFAULT NULL COMMENT '1=Male, 2=Female, 3=other',
  `martial_status` tinyint(1) DEFAULT NULL COMMENT '1=single,2=married,3=divorced,4=widowed',
  `person_dob` date DEFAULT NULL,
  `person_present_add` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person_permanent_add` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person_photo_url` blob DEFAULT NULL,
  `is_cnic_image_available` tinyint(1) unsigned DEFAULT 0,
  `family_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pak_district` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pak_tehsil` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ethnicity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnic_image_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(25) NOT NULL,
  `family_image_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_forth_schedule_details` */

DROP TABLE IF EXISTS `person_forth_schedule_details`;

CREATE TABLE `person_forth_schedule_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `ac_designation` tinyint(2) NOT NULL,
  `mla_status` tinyint(2) NOT NULL,
  `s_b_status` tinyint(2) NOT NULL,
  `int_status` tinyint(2) NOT NULL,
  `int_reason` tinyint(2) NOT NULL,
  `exemption_status` tinyint(2) NOT NULL,
  `do_remarks` blob DEFAULT NULL,
  `bank_account_recomendation` tinyint(2) NOT NULL,
  `ro_remarks` blob DEFAULT NULL,
  `unblock_remarks` blob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `person_identities` */

DROP TABLE IF EXISTS `person_identities`;

CREATE TABLE `person_identities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(25) NOT NULL,
  `identity_id` tinyint(1) DEFAULT NULL COMMENT '1=armed licence no, 2=driving licence, 3=NTN, 4=cnic,5=Afghan refugees Card,6=passport',
  `identity_no` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10820 DEFAULT CHARSET=latin1;

/*Table structure for table `person_income_sources` */

DROP TABLE IF EXISTS `person_income_sources`;

CREATE TABLE `person_income_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `income_source_name` varchar(100) NOT NULL,
  `details` varchar(1000) NOT NULL,
  `file_link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_source_duration` int(11) DEFAULT NULL COMMENT 'Null=Unknown,1=Daily,2=Monthly,3=Yearly',
  `income_amount` bigint(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5676 DEFAULT CHARSET=latin1;

/*Table structure for table `person_initiate` */

DROP TABLE IF EXISTS `person_initiate`;

CREATE TABLE `person_initiate` (
  `person_id` bigint(25) DEFAULT NULL,
  `cnic_number` bigint(25) DEFAULT NULL,
  `cnic_number_foreigner` varchar(25) DEFAULT NULL,
  `is_foreigner` tinyint(1) DEFAULT 0 COMMENT '0=pakistani, 1=foreigner',
  `is_fingerprints_exist` tinyint(4) NOT NULL DEFAULT 0,
  `user_id` int(25) DEFAULT 0,
  `created_from` tinyint(4) DEFAULT 0 COMMENT '0= aies, 1=cis, 2=aiesplus',
  `access_by` tinyint(4) DEFAULT 0 COMMENT '0= aies, 1=cis, 2=both',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_at_update_flag` tinyint(4) NOT NULL DEFAULT 0,
  KEY `person_id` (`person_id`),
  KEY `cnic_number` (`cnic_number`),
  KEY `cnic_number_foreigner` (`cnic_number_foreigner`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `person_investment_details` */

DROP TABLE IF EXISTS `person_investment_details`;

CREATE TABLE `person_investment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `investment_id` varchar(100) NOT NULL,
  `details` varchar(1000) NOT NULL,
  `file_link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` int(2) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=latin1;

/*Table structure for table `person_linked_projects` */

DROP TABLE IF EXISTS `person_linked_projects`;

CREATE TABLE `person_linked_projects` (
  `user_id` int(5) NOT NULL,
  `request_type_id` int(5) NOT NULL,
  `person_id` bigint(25) NOT NULL,
  `project_id` varchar(15) NOT NULL,
  `requested_value` bigint(25) NOT NULL,
  `request_time` datetime NOT NULL,
  KEY `person_id` (`person_id`),
  KEY `idx_pid_projid_rtid` (`person_id`,`project_id`,`request_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `person_location_history` */

DROP TABLE IF EXISTS `person_location_history`;

CREATE TABLE `person_location_history` (
  `person_id` int(10) unsigned NOT NULL,
  `phone_number` bigint(25) NOT NULL COMMENT 'e.g 3004158199',
  `mnc` int(2) NOT NULL,
  `network` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=unknown, 1=2G,2=3G,3=4G/LTE',
  `lac_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cell_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sector` int(5) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(10,8) NOT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moved_in_at` datetime NOT NULL,
  `moved_out_at` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1=Atached, 0=Deatached,2=purged',
  `upload_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY `idx_person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_monthly_summary` */

DROP TABLE IF EXISTS `person_monthly_summary`;

CREATE TABLE `person_monthly_summary` (
  `person_id` int(10) unsigned NOT NULL,
  `reported_month` date DEFAULT NULL,
  `calls_made_count` int(11) DEFAULT NULL,
  `calls_received_count` int(11) DEFAULT NULL,
  `sms_sent_count` int(11) DEFAULT NULL,
  `sms_received_count` int(11) DEFAULT NULL,
  `locations_changed_count` int(11) DEFAULT NULL,
  `pms_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`pms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1913096 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_nadra_profile` */

DROP TABLE IF EXISTS `person_nadra_profile`;

CREATE TABLE `person_nadra_profile` (
  `person_id` int(10) unsigned NOT NULL,
  `cnic_number` bigint(25) DEFAULT NULL,
  `person_name` blob NOT NULL,
  `person_g_name` blob NOT NULL,
  `person_gender` bigint(2) NOT NULL COMMENT '0=Male, 1=Female, 2=Other',
  `person_dob` date NOT NULL,
  `person_present_add` blob NOT NULL,
  `person_permanent_add` blob NOT NULL,
  `person_photo_url` blob NOT NULL,
  `person_nadra_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=Request Not send, 1=Request Send',
  `is_cnic_image_available` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `family_tree_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_street_address` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_city` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_pakistan` tinyint(1) DEFAULT 0 COMMENT '0=no, 1=yes',
  `cnic_image_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(25) NOT NULL,
  `family_image_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_nadra_profile_history` */

DROP TABLE IF EXISTS `person_nadra_profile_history`;

CREATE TABLE `person_nadra_profile_history` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `person_name` blob NOT NULL,
  `person_g_name` blob NOT NULL,
  `person_gender` bigint(2) NOT NULL COMMENT '0=Male, 1=Female, 2=Other',
  `person_dob` date NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `person_present_add` blob NOT NULL,
  `person_permanent_add` blob NOT NULL,
  `person_birth_place` blob NOT NULL,
  `person_religion` blob NOT NULL,
  `person_mother_name` blob NOT NULL,
  `person_verisys_status` bigint(2) NOT NULL DEFAULT 1 COMMENT '0=In-Active, 1=Active',
  `cnic_image_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(25) NOT NULL,
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_phone_device` */

DROP TABLE IF EXISTS `person_phone_device`;

CREATE TABLE `person_phone_device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `phone_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT ' ',
  `imei_number` bigint(25) DEFAULT NULL,
  `in_use_since` datetime DEFAULT NULL,
  `last_interaction_at` datetime DEFAULT NULL,
  `user_id` int(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person_phone_device_imei_number_IDX` (`imei_number`) USING BTREE,
  KEY `person_phone_device_person_id_IDX` (`person_id`) USING BTREE,
  KEY `idx_imei_lookup` (`imei_number`),
  KEY `idx_person_device` (`person_id`,`imei_number`)
) ENGINE=InnoDB AUTO_INCREMENT=1047949 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_phone_number` */

DROP TABLE IF EXISTS `person_phone_number`;

CREATE TABLE `person_phone_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sim_owner` bigint(25) unsigned NOT NULL,
  `person_id` bigint(11) NOT NULL,
  `phone_number` bigint(23) NOT NULL COMMENT 'e.g. 3004158199',
  `imsi_number` bigint(25) DEFAULT NULL,
  `sim_activated_at` datetime DEFAULT NULL,
  `sim_last_used_at` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1=Active, 0=In-Active',
  `mnc` int(2) DEFAULT NULL COMMENT '1=mobilink, 3=ufone, 4=zong, 6=telenor, 7=warid ',
  `connection_type` tinyint(1) DEFAULT 1 COMMENT '1=Pre-Paid, 0=Post-Paid',
  `contact_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=personal,2=home,3=office',
  `user_id` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`),
  KEY `phone_number` (`phone_number`),
  KEY `idx_phone_lookup` (`phone_number`),
  KEY `idx_person_phone` (`person_id`,`phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=616281 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_physical_appearance` */

DROP TABLE IF EXISTS `person_physical_appearance`;

CREATE TABLE `person_physical_appearance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `height` int(5) NOT NULL COMMENT 'in centimeter',
  `build` tinyint(2) NOT NULL,
  `speed` tinyint(2) NOT NULL,
  `complexion` tinyint(2) NOT NULL,
  `face_structure` tinyint(2) NOT NULL,
  `forehead` tinyint(2) NOT NULL,
  `head_shape` tinyint(2) NOT NULL,
  `hair_style` tinyint(2) NOT NULL,
  `hair_color` tinyint(2) NOT NULL,
  `eye_color` tinyint(2) NOT NULL,
  `eye_brows` tinyint(2) NOT NULL,
  `eyes` tinyint(2) NOT NULL,
  `nose` tinyint(2) NOT NULL,
  `ears` tinyint(2) NOT NULL,
  `lips` tinyint(2) NOT NULL,
  `teeth` tinyint(2) NOT NULL,
  `chin` tinyint(2) NOT NULL,
  `beard` tinyint(2) NOT NULL,
  `beard_color` tinyint(2) NOT NULL,
  `moustache` tinyint(2) NOT NULL,
  `feet_legs` tinyint(2) NOT NULL,
  `foot_size` int(5) NOT NULL COMMENT 'in centimeter',
  `apparel` tinyint(2) NOT NULL,
  `walking_style` tinyint(2) NOT NULL,
  `identity_mark` tinyint(3) DEFAULT NULL,
  `disabilities` varchar(100) DEFAULT NULL,
  `weaknesses` varchar(100) DEFAULT NULL,
  `other_details` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1983 DEFAULT CHARSET=latin1;

/*Table structure for table `person_pictures` */

DROP TABLE IF EXISTS `person_pictures`;

CREATE TABLE `person_pictures` (
  `person_id` bigint(20) NOT NULL,
  `picture_type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Front, 2=Left, 3=Right',
  `image_url` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `person_relations` */

DROP TABLE IF EXISTS `person_relations`;

CREATE TABLE `person_relations` (
  `person_id` bigint(11) NOT NULL,
  `person_relation_type` int(2) NOT NULL COMMENT 'relation type id from table: person relation type',
  `relation_with` bigint(25) NOT NULL COMMENT 'relation with person that is in person table',
  `user_id` int(11) NOT NULL,
  `under_custodian` tinyint(4) DEFAULT 0 COMMENT '0=no, 1=yes under custodian of person_id'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `person_reports` */

DROP TABLE IF EXISTS `person_reports`;

CREATE TABLE `person_reports` (
  `person_id` int(25) NOT NULL,
  `report_type` tinyint(1) DEFAULT NULL COMMENT '1=interrogation report, 2=investigation report,3=special report, 4=intelligence report, 5=ground check report, 6=fir copy,7=recommendations/remarks, 8=other',
  `report_reference_no` varchar(30) DEFAULT NULL,
  `report_date` varchar(15) DEFAULT NULL,
  `report_details` varchar(1000) DEFAULT NULL,
  `file_link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'document link',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1774 DEFAULT CHARSET=latin1;

/*Table structure for table `person_residence_details` */

DROP TABLE IF EXISTS `person_residence_details`;

CREATE TABLE `person_residence_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `residence_id` varchar(100) NOT NULL,
  `owner_name` varchar(30) NOT NULL,
  `owner_address` varchar(100) NOT NULL,
  `owner_contact` varchar(12) NOT NULL,
  `details` varchar(1000) NOT NULL,
  `file_link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` int(2) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=latin1;

/*Table structure for table `person_sms_log` */

DROP TABLE IF EXISTS `person_sms_log`;

CREATE TABLE `person_sms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `phone_number` bigint(25) DEFAULT NULL COMMENT 'e.g. 3001234567',
  `other_person_phone_number` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_outgoing` tinyint(1) NOT NULL COMMENT '1=outgoing, 0=incoming',
  `latitude` decimal(12,8) DEFAULT NULL,
  `longitude` decimal(12,8) DEFAULT NULL,
  `address` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_at` datetime NOT NULL,
  `upload_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `imei_number` bigint(25) NOT NULL,
  `imsi_number` bigint(25) NOT NULL,
  `cell_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lac_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mnc` int(2) DEFAULT NULL,
  `sector` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`),
  KEY `phone_number` (`phone_number`),
  KEY `person_sms_log_other_person_phone_number_IDX` (`other_person_phone_number`) USING BTREE,
  KEY `idx_pid_phone_smsat` (`person_id`,`phone_number`,`sms_at`),
  KEY `idx_pid_imei` (`person_id`,`imei_number`)
) ENGINE=InnoDB AUTO_INCREMENT=641634738 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_social_links` */

DROP TABLE IF EXISTS `person_social_links`;

CREATE TABLE `person_social_links` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `person_id` int(25) NOT NULL,
  `sw_type_id` int(3) DEFAULT NULL COMMENT 'social website id from social_sites',
  `person_sw_id` varchar(30) DEFAULT NULL COMMENT 'person social website id',
  `sw_profile_link` varchar(500) NOT NULL,
  `is_sw_id_against_mobile` tinyint(1) DEFAULT 1 COMMENT 'is social website id against mobile number?',
  `phone_number` bigint(25) NOT NULL,
  `information` blob DEFAULT NULL COMMENT 'information extracted about person account',
  `file_link` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'document link',
  `suggested_by` int(5) NOT NULL DEFAULT 0 COMMENT '0=AIES, 1<=agent',
  `authenticity` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=not approved, 1=approved',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `updated_by` int(25) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=337 DEFAULT CHARSET=latin1;

/*Table structure for table `person_summary` */

DROP TABLE IF EXISTS `person_summary`;

CREATE TABLE `person_summary` (
  `person_id` int(10) unsigned NOT NULL,
  `phone_number` bigint(23) NOT NULL COMMENT 'e.g. 3004158199',
  `other_person_phone_number` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'without zero (0)',
  `calls_made_count` int(11) NOT NULL,
  `calls_received_count` int(11) DEFAULT NULL,
  `sms_sent_count` int(11) DEFAULT NULL,
  `sms_received_count` int(11) DEFAULT NULL,
  `locations_changed_count` int(11) DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  KEY `person_id` (`person_id`),
  KEY `phone_number` (`phone_number`),
  KEY `person_summary_other_person_phone_number_IDX` (`other_person_phone_number`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `person_tags` */

DROP TABLE IF EXISTS `person_tags`;

CREATE TABLE `person_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `tag_district_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `added_on` datetime DEFAULT NULL,
  `in_watchlist` int(11) DEFAULT 0 COMMENT '0=No, 1=Yes',
  UNIQUE KEY `person_tags_UN` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12473 DEFAULT CHARSET=utf8;

/*Table structure for table `person_tags_details` */

DROP TABLE IF EXISTS `person_tags_details`;

CREATE TABLE `person_tags_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `notification_no` varchar(100) DEFAULT NULL,
  `reason_remove` varchar(100) DEFAULT NULL,
  `date_of_notification` datetime DEFAULT NULL,
  `in_watchlist` int(11) DEFAULT 0 COMMENT '0=No, 1=Yes',
  UNIQUE KEY `person_tags_details_UN` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3249 DEFAULT CHARSET=utf8;

/*Table structure for table `person_tags_remove_history` */

DROP TABLE IF EXISTS `person_tags_remove_history`;

CREATE TABLE `person_tags_remove_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `tag_district_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `removed_on` datetime DEFAULT NULL,
  UNIQUE KEY `person_tags_UN` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2139 DEFAULT CHARSET=utf8;

/*Table structure for table `person_trainings` */

DROP TABLE IF EXISTS `person_trainings`;

CREATE TABLE `person_trainings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` int(5) DEFAULT NULL,
  `person_id` int(5) DEFAULT NULL,
  `training_camp` int(5) DEFAULT NULL,
  `training_site` varchar(500) DEFAULT NULL,
  `training_type_id` int(5) DEFAULT NULL,
  `training_duration` int(5) DEFAULT NULL COMMENT 'days',
  `training_year` int(5) DEFAULT NULL,
  `training_purpose` varchar(500) DEFAULT NULL,
  `material_taught` varchar(500) DEFAULT NULL,
  `other_details` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3949 DEFAULT CHARSET=utf8;

/*Table structure for table `person_travel_history` */

DROP TABLE IF EXISTS `person_travel_history`;

CREATE TABLE `person_travel_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` bigint(20) DEFAULT NULL,
  `visit_from_country` int(5) DEFAULT NULL,
  `visit_to_country` int(5) DEFAULT NULL,
  `purpose_of_visit` int(5) DEFAULT NULL,
  `duration` int(5) DEFAULT NULL COMMENT 'no of days stay',
  `visit_date` date DEFAULT NULL,
  `other_details` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8;

/*Table structure for table `police_stations` */

DROP TABLE IF EXISTS `police_stations`;

CREATE TABLE `police_stations` (
  `ps_id` int(11) DEFAULT NULL,
  `ps_name` text DEFAULT NULL,
  `district_id` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `region` */

DROP TABLE IF EXISTS `region`;

CREATE TABLE `region` (
  `region_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `province_id` int(10) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `request_send_today` */

DROP TABLE IF EXISTS `request_send_today`;

CREATE TABLE `request_send_today` (
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_name` int(2) DEFAULT NULL COMMENT 'MNC (Mobile Network Value)',
  `request_priority` int(1) NOT NULL DEFAULT 1 COMMENT '1=Normal, 2=Medium, 3=High',
  `total` int(10) unsigned NOT NULL,
  UNIQUE KEY `company_name` (`company_name`,`request_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `roles_users` */

DROP TABLE IF EXISTS `roles_users`;

CREATE TABLE `roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sensitive_person_acl` */

DROP TABLE IF EXISTS `sensitive_person_acl`;

CREATE TABLE `sensitive_person_acl` (
  `user_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `allowed_user_id` int(10) unsigned NOT NULL,
  `allowed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `social_websites` */

DROP TABLE IF EXISTS `social_websites`;

CREATE TABLE `social_websites` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `website_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website_url` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_image` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website_logo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `suspect` */

DROP TABLE IF EXISTS `suspect`;

CREATE TABLE `suspect` (
  `person_id` int(10) unsigned NOT NULL,
  `type_of_case` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `made_suspect_at` datetime DEFAULT NULL,
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `suspect_user_activity_timeline` */

DROP TABLE IF EXISTS `suspect_user_activity_timeline`;

CREATE TABLE `suspect_user_activity_timeline` (
  `timeline_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_activity_type_id` tinyint(3) unsigned NOT NULL,
  `activity_time` datetime NOT NULL,
  `person_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`timeline_id`)
) ENGINE=InnoDB AUTO_INCREMENT=144882 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `suspect_user_activity_timeline_detail` */

DROP TABLE IF EXISTS `suspect_user_activity_timeline_detail`;

CREATE TABLE `suspect_user_activity_timeline_detail` (
  `timeline_id` bigint(10) unsigned NOT NULL,
  `key_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key_value` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `system_error_log` */

DROP TABLE IF EXISTS `system_error_log`;

CREATE TABLE `system_error_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `error_source` varchar(100) NOT NULL,
  `error_type` varchar(50) DEFAULT NULL,
  `process_stage` varchar(50) DEFAULT NULL,
  `severity` varchar(20) DEFAULT 'error' COMMENT 'Log severity level: error, warning, success, info',
  `request_id` bigint(20) unsigned DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `company_name` tinyint(3) unsigned DEFAULT NULL,
  `mobile_requested` varchar(20) DEFAULT NULL,
  `email_number` int(10) unsigned DEFAULT NULL,
  `error_message` text NOT NULL,
  `error_trace` text DEFAULT NULL,
  `context_data` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_source_type` (`error_source`,`error_type`,`created_at`),
  KEY `idx_request` (`request_id`),
  KEY `idx_company` (`company_name`,`created_at`),
  KEY `idx_created` (`created_at`),
  KEY `idx_severity` (`severity`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1406434 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `telco_emails` */

DROP TABLE IF EXISTS `telco_emails`;

CREATE TABLE `telco_emails` (
  `mnc` int(11) NOT NULL,
  `email1` varchar(50) NOT NULL,
  `email2` varchar(50) NOT NULL,
  `is_second` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `telco_request_summary` */

DROP TABLE IF EXISTS `telco_request_summary`;

CREATE TABLE `telco_request_summary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL COMMENT 'report date',
  `company_mnc` tinyint(1) NOT NULL COMMENT 'mnc',
  `send_high` int(5) NOT NULL,
  `send_medium` int(5) NOT NULL,
  `send_low` int(5) NOT NULL,
  `total_send` int(5) NOT NULL,
  `total_received` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23178 DEFAULT CHARSET=latin1;

/*Table structure for table `telco_short_code` */

DROP TABLE IF EXISTS `telco_short_code`;

CREATE TABLE `telco_short_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `code` int(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

/*Table structure for table `travelhistory_temp_files` */

DROP TABLE IF EXISTS `travelhistory_temp_files`;

CREATE TABLE `travelhistory_temp_files` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `cnic_number` bigint(25) DEFAULT NULL,
  `image_name` varchar(45) DEFAULT NULL,
  `uploaded_by_user` int(11) DEFAULT NULL,
  `upload_date` datetime DEFAULT NULL,
  `attachment_status` int(11) DEFAULT 0 COMMENT '0=waiting, 1= attached, 2= error',
  PRIMARY KEY (`row_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56926 DEFAULT CHARSET=utf8;

/*Table structure for table `url_hits_log` */

DROP TABLE IF EXISTS `url_hits_log`;

CREATE TABLE `url_hits_log` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) DEFAULT NULL,
  `user_ip` varchar(30) DEFAULT NULL,
  `user_agent` varchar(1000) DEFAULT NULL,
  `accessed_url` varchar(1000) DEFAULT NULL,
  `accessed_url_status_code` int(2) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1847114 DEFAULT CHARSET=utf8;

/*Table structure for table `user_access_matrix` */

DROP TABLE IF EXISTS `user_access_matrix`;

CREATE TABLE `user_access_matrix` (
  `user_id` int(10) unsigned NOT NULL,
  `user_activity_type` int(3) NOT NULL DEFAULT 1 COMMENT 'activity type id',
  `permission` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=inactive, 1=active',
  `created_by` int(3) DEFAULT NULL COMMENT 'User ID',
  `created_at` datetime NOT NULL,
  `modified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_activity_timeline` */

DROP TABLE IF EXISTS `user_activity_timeline`;

CREATE TABLE `user_activity_timeline` (
  `timeline_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_activity_type_id` tinyint(3) unsigned NOT NULL,
  `person_id` int(10) unsigned DEFAULT NULL,
  `activity_time` datetime NOT NULL,
  PRIMARY KEY (`timeline_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19558348 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_activity_timeline_detail` */

DROP TABLE IF EXISTS `user_activity_timeline_detail`;

CREATE TABLE `user_activity_timeline_detail` (
  `timeline_id` bigint(10) unsigned NOT NULL,
  `key_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key_value` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_company` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(10) DEFAULT NULL COMMENT 'activity related to this user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_block_reason` */

DROP TABLE IF EXISTS `user_block_reason`;

CREATE TABLE `user_block_reason` (
  `id` bigint(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) DEFAULT NULL,
  `block_reason` varchar(100) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8;

/*Table structure for table `user_cft_type` */

DROP TABLE IF EXISTS `user_cft_type`;

CREATE TABLE `user_cft_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `user_type_id` int(10) NOT NULL DEFAULT 0 COMMENT '0=Defaul, 1=Administrator,2=HQ CFT, 3=RO CTF, 4=DO CTF',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

/*Table structure for table `user_favorite_person` */

DROP TABLE IF EXISTS `user_favorite_person`;

CREATE TABLE `user_favorite_person` (
  `user_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_favourite_user` */

DROP TABLE IF EXISTS `user_favourite_user`;

CREATE TABLE `user_favourite_user` (
  `user_id` int(10) NOT NULL,
  `favourite_user_id` int(10) NOT NULL,
  `added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `user_monthly_summary` */

DROP TABLE IF EXISTS `user_monthly_summary`;

CREATE TABLE `user_monthly_summary` (
  `user_id` int(10) unsigned NOT NULL,
  `reported_month` date NOT NULL COMMENT 'first day of the report month e.g. 2017-05-01',
  `request_count` int(10) unsigned NOT NULL DEFAULT 0,
  `record_add_count` int(10) unsigned NOT NULL DEFAULT 0,
  `record_view_count` int(10) unsigned NOT NULL DEFAULT 0,
  `record_lock_count` int(10) unsigned NOT NULL DEFAULT 0,
  `record_favorite_count` int(10) unsigned NOT NULL DEFAULT 0,
  `login_count` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_os_req` */

DROP TABLE IF EXISTS `user_os_req`;

CREATE TABLE `user_os_req` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`request_id`),
  KEY `user_os_req_request_id_IDX` (`request_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_request` */

DROP TABLE IF EXISTS `user_request`;

CREATE TABLE `user_request` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` int(10) NOT NULL COMMENT 'Reference ID to be sent to company for reference',
  `user_id` int(10) unsigned NOT NULL,
  `user_request_type_id` int(10) unsigned NOT NULL,
  `message_id` int(10) NOT NULL DEFAULT 0,
  `company_name` int(2) DEFAULT NULL COMMENT 'MNC (Mobile Network Value)',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Requst not send, 1=request send, 2=email received, 3=email sending error, 4=request rejected',
  `reply` smallint(6) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1 = Sent',
  `concerned_person_id` int(10) unsigned DEFAULT NULL,
  `requested_value` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processing_index` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '0=Waiting Response 1=email format error 2=No data found, 3=Parsing Error, 4=Waiting for parsing, 5=Parsing completed,6=partially parsing completed, 7=mark completed',
  `reason` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'request link with a particular project.',
  `is_killed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `killed_at` datetime DEFAULT NULL,
  `killed_error_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `request_priority` int(1) NOT NULL DEFAULT 1 COMMENT '1=Normal, 2=Medium, 3=High',
  `sending_date` datetime DEFAULT NULL,
  `request_send_count` int(11) NOT NULL DEFAULT 0,
  `force_imei_last_digit_zero` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`request_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status_processing` (`status`,`processing_index`),
  KEY `idx_company_status` (`company_name`,`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1989270 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_sensitive_person` */

DROP TABLE IF EXISTS `user_sensitive_person`;

CREATE TABLE `user_sensitive_person` (
  `user_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_summary` */

DROP TABLE IF EXISTS `user_summary`;

CREATE TABLE `user_summary` (
  `user_id` int(10) unsigned NOT NULL,
  `last_logged_in_at` datetime DEFAULT NULL,
  `last_request_made_at` datetime DEFAULT NULL,
  `request_count` int(10) unsigned NOT NULL DEFAULT 0,
  `record_add_count` int(10) unsigned NOT NULL DEFAULT 0,
  `record_view_count` int(10) unsigned NOT NULL DEFAULT 0,
  `record_lock_count` int(10) unsigned NOT NULL DEFAULT 0,
  `record_favorite_count` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_tokens` */

DROP TABLE IF EXISTS `user_tokens`;

CREATE TABLE `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `type` varchar(100) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password_backup` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_access_level_id` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '1=Alpha level (can access the system)',
  `logins` int(10) unsigned NOT NULL DEFAULT 0,
  `last_login` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `is_active_cis` int(11) DEFAULT 0,
  `is_approved_cis` int(11) DEFAULT 0,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = pending approve, 1 = approved',
  `approved_by` int(10) NOT NULL,
  `approved_at` datetime NOT NULL,
  `deactivated_at` datetime DEFAULT NULL,
  `is_forget_reset` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=no, 1=yes',
  `is_login` tinyint(1) NOT NULL COMMENT '1=Yes, 0=No',
  `login_sites` tinyint(4) DEFAULT 0 COMMENT '0=aies, 1=cis, 2=both',
  `is_password_changed` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Yes, 0=No',
  `reset_password_text` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Temp password to send for user',
  `is_active_ctfu` tinyint(1) DEFAULT 0,
  `is_approved_ctfu` tinyint(1) DEFAULT 0,
  `login_token` int(11) NOT NULL,
  `token_expires` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2736 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `users_feedback` */

DROP TABLE IF EXISTS `users_feedback`;

CREATE TABLE `users_feedback` (
  `user_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `feedback` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `users_profile` */

DROP TABLE IF EXISTS `users_profile`;

CREATE TABLE `users_profile` (
  `user_id` int(10) unsigned NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_id` int(10) unsigned NOT NULL,
  `region_id` int(10) unsigned NOT NULL,
  `posted` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `belt` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `order_no` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'order of concerned,RO/DO to create user',
  `modified_at` datetime DEFAULT NULL,
  `cnic_number` bigint(15) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `users_profile_posted_IDX` (`posted`,`region_id`,`district_id`,`cnic_number`) USING BTREE,
  KEY `users_profile_mobile_number_IDX` (`mobile_number`,`belt`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `verisys_temp_files` */

DROP TABLE IF EXISTS `verisys_temp_files`;

CREATE TABLE `verisys_temp_files` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `cnic_number` bigint(25) DEFAULT NULL,
  `image_name` varchar(45) DEFAULT NULL,
  `uploaded_by_user` int(11) DEFAULT NULL,
  `upload_date` datetime DEFAULT NULL,
  `attachment_status` int(11) DEFAULT 0 COMMENT '0=waiting, 1= attached, 2= error',
  PRIMARY KEY (`row_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81642 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
