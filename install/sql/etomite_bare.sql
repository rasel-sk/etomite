-- etomite_bare.sql
-- 2008-04-04


-- START::CREATE TABLE SECTION

CREATE TABLE `{PREFIX}active_users` (
  `internalKey` int(9) NOT NULL default '0',
  `username` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `lasthit` int(20) NOT NULL default '0',
  `id` int(10) default NULL,
  `action` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `ip` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`internalKey`)
) ENGINE=MyISAM COMMENT='Contains data about active users.';

CREATE TABLE `{PREFIX}documentgroup_names` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM COMMENT='Contains data used for access permissions.';

CREATE TABLE `{PREFIX}document_groups` (
  `id` int(10) NOT NULL auto_increment,
  `document_group` int(10) NOT NULL default '0',
  `document` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains data used for access permissions.';

CREATE TABLE `{PREFIX}keyword_xref` (
  `content_id` int(11) NOT NULL default '0',
  `keyword_id` int(11) NOT NULL default '0',
  KEY `content_id` (`content_id`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=MyISAM COMMENT='Cross reference bewteen keywords and content';

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
) ENGINE=MyISAM COMMENT='Contains visitor statistics.';

CREATE TABLE `{PREFIX}log_hosts` (
  `id` bigint(11) NOT NULL default '0',
  `data` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains visitor statistics.';

CREATE TABLE `{PREFIX}log_operating_systems` (
  `id` bigint(11) NOT NULL default '0',
  `data` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains visitor statistics.';

CREATE TABLE `{PREFIX}log_referers` (
  `id` bigint(11) NOT NULL default '0',
  `data` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains visitor statistics.';

CREATE TABLE `{PREFIX}log_totals` (
  `today` date NOT NULL default '0000-00-00',
  `month` char(2) collate utf8_unicode_ci NOT NULL default '0',
  `piDay` int(11) NOT NULL default '0',
  `piMonth` int(11) NOT NULL default '0',
  `piAll` int(11) NOT NULL default '0',
  `viDay` int(11) NOT NULL default '0',
  `viMonth` int(11) NOT NULL default '0',
  `viAll` int(11) NOT NULL default '0',
  `visDay` int(11) NOT NULL default '0',
  `visMonth` int(11) NOT NULL default '0',
  `visAll` int(11) NOT NULL default '0'
) ENGINE=MyISAM COMMENT='Stores temporary logging information.';

CREATE TABLE `{PREFIX}log_user_agents` (
  `id` bigint(11) NOT NULL default '0',
  `data` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains visitor statistics.';

CREATE TABLE `{PREFIX}log_visitors` (
  `id` bigint(11) NOT NULL default '0',
  `os_id` bigint(11) NOT NULL default '0',
  `ua_id` bigint(11) NOT NULL default '0',
  `host_id` bigint(11) NOT NULL default '0',
  KEY `id` (`id`),
  KEY `os_id` (`os_id`),
  KEY `ua_id` (`ua_id`),
  KEY `host_id` (`host_id`)
) ENGINE=MyISAM COMMENT='Contains visitor statistics.';

CREATE TABLE `{PREFIX}manager_log` (
  `id` int(10) NOT NULL auto_increment,
  `timestamp` int(20) NOT NULL default '0',
  `internalKey` int(10) NOT NULL default '0',
  `username` varchar(255) collate utf8_unicode_ci default NULL,
  `action` int(10) NOT NULL default '0',
  `itemid` varchar(10) collate utf8_unicode_ci default '0',
  `itemname` varchar(255) collate utf8_unicode_ci default NULL,
  `message` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains a record of user interaction with Etomite.';

CREATE TABLE `{PREFIX}manager_users` (
  `id` int(10) NOT NULL auto_increment,
  `username` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `password` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM COMMENT='Contains login information for Etomite users.';

CREATE TABLE `{PREFIX}membergroup_access` (
  `id` int(10) NOT NULL auto_increment,
  `membergroup` int(10) NOT NULL default '0',
  `documentgroup` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains data used for access permissions.';

CREATE TABLE `{PREFIX}membergroup_names` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM COMMENT='Contains data used for access permissions.';

CREATE TABLE `{PREFIX}member_groups` (
  `id` int(10) NOT NULL auto_increment,
  `user_group` int(10) NOT NULL default '0',
  `member` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains data used for access permissions.';

CREATE TABLE `{PREFIX}site_content` (
  `id` int(10) NOT NULL auto_increment,
  `type` varchar(20) collate utf8_unicode_ci NOT NULL default 'document',
  `contentType` varchar(50) collate utf8_unicode_ci NOT NULL default 'text/html',
  `pagetitle` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `longtitle` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `alias` varchar(100) collate utf8_unicode_ci default '',
  `published` int(1) NOT NULL default '0',
  `pub_date` int(20) NOT NULL default '0',
  `unpub_date` int(20) NOT NULL default '0',
  `parent` int(10) NOT NULL default '0',
  `isfolder` int(1) NOT NULL default '0',
  `content` mediumtext collate utf8_unicode_ci NOT NULL,
  `richtext` tinyint(1) NOT NULL default '1',
  `template` int(10) NOT NULL default '1',
  `menuindex` int(10) NOT NULL default '0',
  `searchable` int(1) NOT NULL default '1',
  `cacheable` int(1) NOT NULL default '1',
  `createdby` int(10) NOT NULL default '0',
  `createdon` int(20) NOT NULL default '0',
  `editedby` int(10) NOT NULL default '0',
  `editedon` int(20) NOT NULL default '0',
  `deleted` int(1) NOT NULL default '0',
  `deletedon` int(20) NOT NULL default '0',
  `deletedby` int(10) NOT NULL default '0',
  `authenticate` int(1) NOT NULL default '0',
  `showinmenu` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`),
  FULLTEXT KEY `content_ft_idx` (`pagetitle`,`description`,`content`)
) ENGINE=MyISAM COMMENT='Contains the site''s document tree.';

CREATE TABLE `{PREFIX}site_htmlsnippets` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL default 'Chunk',
  `snippet` mediumtext collate utf8_unicode_ci NOT NULL,
  `locked` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains the site''s chunks.';

CREATE TABLE `{PREFIX}site_keywords` (
  `id` int(11) NOT NULL auto_increment,
  `keyword` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=MyISAM COMMENT='Site keyword list';

CREATE TABLE `{PREFIX}site_snippets` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL default 'Snippet',
  `snippet` mediumtext collate utf8_unicode_ci NOT NULL,
  `locked` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM PACK_KEYS=0 COMMENT='Contains the site''s snippets.';

CREATE TABLE `{PREFIX}site_templates` (
  `id` int(10) NOT NULL auto_increment,
  `templatename` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL default 'Template',
  `content` mediumtext collate utf8_unicode_ci NOT NULL,
  `locked` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM PACK_KEYS=0 COMMENT='Contains the site''s templates.';

CREATE TABLE `{PREFIX}system_settings` (
  `setting_name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `setting_value` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  UNIQUE KEY `setting_name` (`setting_name`)
) ENGINE=MyISAM COMMENT='Contains Etomite settings.';

CREATE TABLE `{PREFIX}user_attributes` (
  `id` int(10) NOT NULL auto_increment,
  `internalKey` int(10) NOT NULL default '0',
  `fullname` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `role` int(10) NOT NULL default '0',
  `email` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `phone` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `mobilephone` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `blocked` int(1) NOT NULL default '0',
  `blockeduntil` int(11) NOT NULL default '0',
  `logincount` int(11) NOT NULL default '0',
  `lastlogin` int(11) NOT NULL default '0',
  `thislogin` int(11) NOT NULL default '0',
  `failedlogincount` int(10) NOT NULL default '0',
  `sessionid` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `userid` (`internalKey`)
) ENGINE=MyISAM COMMENT='Contains information about Etomite users.';

CREATE TABLE `{PREFIX}user_messages` (
  `id` int(10) NOT NULL auto_increment,
  `type` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `subject` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `message` text collate utf8_unicode_ci NOT NULL,
  `sender` int(10) NOT NULL default '0',
  `recipient` int(10) NOT NULL default '0',
  `private` tinyint(4) NOT NULL default '0',
  `postdate` int(20) NOT NULL default '0',
  `messageread` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains messages for the Etomite messaging system.';

CREATE TABLE `{PREFIX}user_roles` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `frames` int(1) NOT NULL default '0',
  `home` int(1) NOT NULL default '0',
  `view_document` int(1) NOT NULL default '0',
  `new_document` int(1) NOT NULL default '0',
  `save_document` int(1) NOT NULL default '0',
  `delete_document` int(1) NOT NULL default '0',
  `action_ok` int(1) NOT NULL default '0',
  `logout` int(1) NOT NULL default '0',
  `help` int(1) NOT NULL default '0',
  `messages` int(1) NOT NULL default '0',
  `new_user` int(1) NOT NULL default '0',
  `edit_user` int(1) NOT NULL default '0',
  `logs` int(1) NOT NULL default '0',
  `edit_parser` int(1) NOT NULL default '0',
  `save_parser` int(1) NOT NULL default '0',
  `edit_template` int(1) NOT NULL default '0',
  `settings` int(1) NOT NULL default '0',
  `credits` int(1) NOT NULL default '0',
  `new_template` int(1) NOT NULL default '0',
  `save_template` int(1) NOT NULL default '0',
  `delete_template` int(1) NOT NULL default '0',
  `edit_snippet` int(1) NOT NULL default '0',
  `new_snippet` int(1) NOT NULL default '0',
  `save_snippet` int(1) NOT NULL default '0',
  `delete_snippet` int(1) NOT NULL default '0',
  `empty_cache` int(1) NOT NULL default '0',
  `edit_document` int(1) NOT NULL default '0',
  `change_password` int(1) NOT NULL default '0',
  `error_dialog` int(1) NOT NULL default '0',
  `about` int(1) NOT NULL default '0',
  `file_manager` int(1) NOT NULL default '0',
  `save_user` int(1) NOT NULL default '0',
  `delete_user` int(1) NOT NULL default '0',
  `save_password` int(11) NOT NULL default '0',
  `edit_role` int(11) NOT NULL default '0',
  `save_role` int(11) NOT NULL default '0',
  `delete_role` int(11) NOT NULL default '0',
  `new_role` int(11) NOT NULL default '0',
  `access_permissions` int(1) NOT NULL default '0',
  `new_chunk` int(1) NOT NULL default '0',
  `save_chunk` int(1) NOT NULL default '0',
  `edit_chunk` int(1) NOT NULL default '0',
  `delete_chunk` int(1) NOT NULL default '0',
  `export_html` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='Contains information describing the Etomite user roles.';

-- END::CREATE TABLE SECTION


-- START::LOAD ADMIN USER INITIAL SETTINGS

INSERT INTO `{PREFIX}manager_users` VALUES (1, '{ADMIN}', MD5('{ADMINPASS}'));

INSERT INTO `{PREFIX}user_attributes` VALUES(1, 1, 'Administration account', 1, 'Your email goes here', '', '', 0, 0, 0, {TIMESTAMP}, {TIMESTAMP}, 0, '');

INSERT INTO `{PREFIX}user_roles` VALUES(1, 'Administrator', 'Site administrators have full access to all functions', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0);

-- END::LOAD ADMIN USER INITIAL SETTINGS

