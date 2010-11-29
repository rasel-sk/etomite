ALTER TABLE `{PREFIX}site_snippets` ADD `locked` TINYINT DEFAULT '0' NOT NULL ;

ALTER TABLE `{PREFIX}site_templates` ADD `locked` TINYINT DEFAULT '0' NOT NULL ;

ALTER TABLE `{PREFIX}membergroup_names` ADD UNIQUE (`name`) ;

ALTER TABLE `{PREFIX}documentgroup_names` ADD UNIQUE (`name`) ;

ALTER TABLE `{PREFIX}site_content` ADD `contentType` VARCHAR( 50 ) DEFAULT 'text/html' NOT NULL AFTER `type` ;

ALTER TABLE `{PREFIX}site_content` ADD `longtitle` VARCHAR( 255 ) DEFAULT '' NOT NULL AFTER `pagetitle` ;

ALTER TABLE `{PREFIX}site_content` ADD `authenticate` int(1) NOT NULL DEFAULT '0' ;

ALTER TABLE `{PREFIX}site_content` ADD `showinmenu` int(1) NOT NULL DEFAULT '1' ;

CREATE TABLE `{PREFIX}keyword_xref` (
  `content_id` int(11) NOT NULL default '0',
  `keyword_id` int(11) NOT NULL default '0',
  KEY `content_id` (`content_id`),
  KEY `keyword_id` (`keyword_id`)
) TYPE=MyISAM COMMENT='Cross reference bewteen keywords and content';


CREATE TABLE `{PREFIX}log_access` (
  `visitor` bigint(11) NOT NULL default '0',
  `document` bigint(11) NOT NULL default '0',
  `timestamp` int(20) NOT NULL default '0',
  `hour` tinyint(2) NOT NULL default '0',
  `weekday` tinyint(1) NOT NULL default '0',
  `referer` bigint(11) NOT NULL default '0',
  `entry` tinyint(1) NOT NULL default '0',
  KEY `visitor` (`visitor`),
  KEY `document` (`document`),
  KEY `timestamp` (`timestamp`),
  KEY `referer` (`referer`),
  KEY `entry` (`entry`),
  KEY `hour` (`hour`),
  KEY `weekday` (`weekday`)
) TYPE=MyISAM COMMENT='Contains visitor statistics.';


CREATE TABLE `{PREFIX}log_hosts` (
  `id` bigint(11) NOT NULL default '0',
  `data` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Contains visitor statistics.';


CREATE TABLE `{PREFIX}log_operating_systems` (
  `id` bigint(11) NOT NULL default '0',
  `data` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Contains visitor statistics.';


CREATE TABLE `{PREFIX}log_referers` (
  `id` bigint(11) NOT NULL default '0',
  `data` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Contains visitor statistics.';


CREATE TABLE `{PREFIX}log_totals` (
  `today` date NOT NULL default '0000-00-00',
  `month` char(2) NOT NULL default '0',
  `piDay` int(11) NOT NULL default '0',
  `piMonth` int(11) NOT NULL default '0',
  `piAll` int(11) NOT NULL default '0',
  `viDay` int(11) NOT NULL default '0',
  `viMonth` int(11) NOT NULL default '0',
  `viAll` int(11) NOT NULL default '0',
  `visDay` int(11) NOT NULL default '0',
  `visMonth` int(11) NOT NULL default '0',
  `visAll` int(11) NOT NULL default '0'
) TYPE=MyISAM COMMENT='Stores temporary logging information.';


CREATE TABLE `{PREFIX}log_user_agents` (
  `id` bigint(11) NOT NULL default '0',
  `data` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Contains visitor statistics.';


CREATE TABLE `{PREFIX}log_visitors` (
  `id` bigint(11) NOT NULL default '0',
  `os_id` bigint(11) NOT NULL default '0',
  `ua_id` bigint(11) NOT NULL default '0',
  `host_id` bigint(11) NOT NULL default '0',
  KEY `id` (`id`),
  KEY `os_id` (`os_id`),
  KEY `ua_id` (`ua_id`),
  KEY `host_id` (`host_id`)
) TYPE=MyISAM COMMENT='Contains visitor statistics.';


CREATE TABLE `{PREFIX}site_keywords` (
  `id` int(11) NOT NULL auto_increment,
  `keyword` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `keyword` (`keyword`)
) TYPE=MyISAM COMMENT='Site keyword list';
