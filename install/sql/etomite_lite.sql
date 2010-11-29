-- etomite_lite.sql
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


-- START::INSERT site_content DATA

INSERT INTO `{PREFIX}site_content` VALUES (1, 'document', 'text/html', 'Etomite CMS', 'Fast, Free, and Infinitely Flexible', 'Introduction to Etomite', 'home', 1, 0, 0, 0, 0, '<p><strong>Welcome to Etomite!</strong></p>\r\n\r\n<p>This is the default installation site. If you''re reading this, it means you''ve successfully installed Etomite, and have also configured the site. Now all you need to do is to add your content, design a unique template for your site, perhaps write some snippets which make your site stand out, and, most of all, enjoy using Etomite!</p>\r\n\r\n<p>To log into the manager, point your browser to <a href="manager">yoursite/manager</a>.</p>', 1, 1, 1, 1, 0, 1, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), 0, 0, 0, 0, 1);

INSERT INTO `{PREFIX}site_content` VALUES (2, 'document', 'text/html', 'Repository', 'The secret Repository', 'Folder for other stuff :)', '', 0, 0, 0, 0, 1, 'Why are you reading this?', 0, 1, 0, 0, 0, 1, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), 0, 0, 0, 0, 1);

INSERT INTO `{PREFIX}site_content` VALUES (3, 'document', 'text/html', 'Error 404 - Page Not Found', 'Unable to locate the page you requested.', 'The error page which is displayed when the requested page cannot be found', 'http404', 1, 0, 0, 2, 0, '\r\n<p><strong>404 Error - File not Found</strong></p><p>Hm. The page you''ve requested wasn''t found. Perhaps the page has moved, or you mistyped the URL. Or, you could try going back to the main site and trying to find the page you were looking for from there.[[DontLogPageHit]]</p>', 1, 1, 0, 0, 0, 1, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), 0, 0, 0, 0, 1);

INSERT INTO `{PREFIX}site_content` VALUES (4, 'document', 'text/xml', 'Google Site Map', '', '', 'google-sitemap', 1, 0, 0, 2, 0, '', 0, 2, 10, 0, 0, 1, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), 0, 0, 0, 0, 1);

-- END::INSERT site_content DATA


-- START::INSERT site_snippets DATA

INSERT INTO `{PREFIX}site_snippets` VALUES (1, 'DontLogPageHit', 'Stops the parser from logging the page hit', '$this->config[''track_visitors'']=0;\r\nreturn "";', 0);

INSERT INTO `{PREFIX}site_snippets` VALUES (2, 'PoweredBy', 'A little link to Etomite', '// Snippet name: PoweredBy\r\n// Snippet description: A little link to Etomite.\r\n// Revision: 1.00 ships with Etomite 0.6.1-Final\r\n\r\n$version = $etomite->getVersionData();\r\nreturn ''<a href="http://www.etomite.com" title="Etomite Website">Powered by Etomite <b>''.$version[''version''].$version[''patch_level''].''</b> <i>(''.$version[''code_name''].'')</i>.</a>'';', 0);

INSERT INTO `{PREFIX}site_snippets` VALUES (3, 'PageTrail', 'Outputs the page trail, based on Bill Wilson''s script', '// Snippet name: PageTrail\r\n// Snippet description: Outputs the page trail, based on Bill Wilson''s script\r\n// Revision: 1.00 ships with Etomite 0.6.1-Final\r\n\r\n$sep = " &raquo; ";\r\n\r\n// end config\r\n$ptarr = array();\r\n$pid = $etomite->documentObject[''parent''];\r\n$ptarr[] = "<a href=''[~".$etomite->documentObject[''id'']."~]''>".$etomite->documentObject[''pagetitle'']."</a>";\r\n\r\nwhile ($parent=$etomite->getParent($pid)) {\r\n    $ptarr[] = "<a href=''[~".$parent[''id'']."~]''>".$parent[''pagetitle'']."</a>";\r\n    $pid = $parent[''parent''];\r\n}\r\n\r\n$ptarr = array_reverse($ptarr);\r\nreturn join($ptarr, $sep);', 0);

INSERT INTO `{PREFIX}site_snippets` VALUES (4, 'MenuBuilder', 'Builds the site menu', '// Snippet name: MenuBuilder\r\n// Snippet description: Builds the site menu\r\n// Revision: 1.00 ships with Etomite 0.6.1-Final\r\n\r\n$id = isset($id) ? $id : $etomite->documentIdentifier;\r\n$sortby = "menuindex";\r\n$sortdir = "ASC";\r\n$fields = "id, pagetitle, description, parent, alias";\r\n\r\n$indentString="";\r\n\r\nif(!isset($indent)) {\r\n    $indent = "";\r\n    $indentString .= "";\r\n} else {\r\n    for($in=0; $in<$indent; $in++) {\r\n        $indentString .= "&nbsp;";\r\n    }\r\n    $indentString .= "&raquo;&nbsp;";\r\n}\r\n\r\n$children = $etomite->getActiveChildren($id, $sortby, $sortdir, $fields);\r\n$menu = "";\r\n$childrenCount = count($children);\r\n$active="";\r\n\r\nif($children==false) {\r\n    return false;\r\n}\r\nfor($x=0; $x<$childrenCount; $x++) {\r\n  if($children[$x][''id'']==$etomite->documentIdentifier) {\r\n   $active="class=\\"highLight\\"";\r\n  } else {\r\n    $active="";\r\n }\r\n if($children[$x][''id'']==$etomite->documentIdentifier || $children[$x][''id'']==$etomite->documentObject[''parent'']) {\r\n    $menu .= "<a ".$active." href=\\"[~".$children[$x][''id'']."~]\\">$indentString".$children[$x][''pagetitle'']."</a>[[MenuBuilder?id=".$children[$x][''id'']."&indent=2]]";  \r\n  } else {\r\n    $menu .= "<a href=\\"[~".$children[$x][''id'']."~]\\">$indentString".$children[$x][''pagetitle'']."</a>";\r\n }\r\n}\r\nreturn $menu."";', 0);

INSERT INTO `{PREFIX}site_snippets` VALUES (5, 'MetaTagsExtra', 'Output page related meta tags', '/*\r\nSnippet Title:     MetaTagsExtra\r\nSnippet Version:   2.2\r\nEtomite Version:   0.6 +\r\n\r\nDescription:\r\n  Returns XHTML for document meta tags: \r\n     Content-Type, Content-Language, Generator,\r\n     Title, Description, Keywords, Abstract, Author, Copyright, \r\n     Robots, Googlebot, Cache-Control, Pragma, Expires, Last Modified,\r\n     Distribution and Rating.\r\n  Can also return XHTML for Dublin Core Metadata Initiative meta tags:\r\n     DC.format, DC.language, DC.title, \r\n     DC.description, DC.subject, DC.title.alternative,\r\n     DC.publisher, DC.creator, DC.rights,\r\n     DC.date.created, DC.date.modified, DC.date.valid and DC.identifier.\r\n  Can also return the GeoURL and GeoTags meta tags:\r\n     DC.title, ICBM, geo.position, geo.placename and geo.region.\r\n\r\nSnippet Author:\r\n  Miels with mods by Lloyd Borrett (lloyd@borrett.id.au)\r\n\r\nVersion History:\r\n  1.3 - Lloyd Borrett added the Robots meta tag based \r\n  on the idea in the SearchableSE snippet by jaredc\r\n\r\n  1.4 - Lloyd Borrett added the Abstract meta tag\r\n  based on the Site Name and the Long Title.\r\n  Also added the Generator meta tag based on the Etomite version details.\r\n  The Robots meta tag is now only output if the document is non-searchable,\r\n  to reduce XHTML bloat. The Googlebot meta tag is now also output \r\n  when the document is non-searchable.\r\n\r\n  1.5 - Lloyd Borrett added no-cache directives via the Cache-Control \r\n  and Pragma meta tags if the document is non-cacheable.\r\n  Abstract meta tag uses the document description if long title not set.\r\n  Cleaned up some other tests.\r\n\r\n  1.6 - 2006-01-26 - Lloyd Borrett cleaned up some code.\r\n\r\n  1.7 - 2006-01-27 - Lloyd Borrett\r\n  Added support for the Distribution and Rating meta tags.\r\n  Copyright meta tag can now include a year range being from either \r\n  a site creation year to the current year, or from the year the \r\n  document was created to the current year, e.g. 2005-2006.\r\n  Added ability to specify a site wide author, and thus be able to\r\n  skip looking up document author details.\r\n\r\n  1.8 - 2006-01-27 - Lloyd Borrett\r\n  Current year now based on local time using the Etomite\r\n  Server Offset Time configuration setting\r\n\r\n  1.9 - 2006-03-08 - Lloyd Borrett\r\n  Dates in meta tags can be output in your choice of ISO 8601\r\n  or RFC 822 formats.\r\n  Dates in the meta tags are now corrected to local time.\r\n  Fixed problem with the generation of the "description" meta tag.\r\n\r\n  2.0 - 2006-03-10 - Lloyd Borrett\r\n  Moved the generation of the "content-type", "content-language" \r\n  and "title" meta tags into this snippet.\r\n  Added in support for the Dublin Core Metadata Initiative meta tags.\r\n\r\n  2.1 - 2006-03-15 - Lloyd Borrett\r\n  Dropped the choice of date formats. Dublin Core tags now use ISO dates. \r\n  Others tags use RFC 822 dates. This is what is properly supported.\r\n  Added in support for GeoURL (www.geourl.org) and \r\n  GeoTags (www.geotags.com) meta tags.\r\n\r\n  2.2 - 2006-04-07 - Lloyd Borrett\r\n  Get the base URL from Etomite instead of it being a configuration option.\r\n   \r\nSnippet Category:\r\n  Search Engines           \r\n\r\nUsage:\r\n  Insert [[MetaTagsExtra]] anywhere in the head section of your template.\r\n  Don''t forget to set the full name of all document authors.\r\n  You can find it at "Manage users" -> your username -> "full name".\r\n  This value is used for the Author and Copyright meta tags.\r\n\r\n  When you mark a page as "NOT searchable" - a Robots meta tag \r\n  with "noindex, nofollow" is inserted to keep web search engines\r\n  from indexing that document. After all, there''s little value in \r\n  making your Etomite document unsearchable to Etomite, when \r\n  Google still knows where it is! For "searchable" documents, no\r\n  Robots meta tag is inserted. The default is "index, follow", so not\r\n  putting it in reduced HTML bloat.\r\n  A Googlebot meta tag with "noindex, nofollow, noarchive, nosnippet"\r\n  is also output, to tell Google to clean out its cache.\r\n\r\n  When you mark a page as "non cacheable", no-cache directives \r\n  are inserted via the Cache-Control and Pragma meta tags.\r\n*/\r\n\r\n// *** Configuration Settings ***\r\n\r\n// Provide the content type setting.\r\n$content_type = "text/html; charset=iso-8859-1";\r\n\r\n// Provide the language setting.\r\n$language = "en";\r\n\r\n// Distribution can be "global", "local" or "iu"\r\n// If you want no Distribution meta tag use ''''\r\n$distribution = ''global'';\r\n\r\n// Rating can be "14 years", "general", "mature", "restricted" or "safe for kids"\r\n// If you want no Rating meta tag use ''''\r\n$rating = ''general'';\r\n\r\n// Start Date of the web site as used for the copyright meta tag\r\n// To use the document creation date, set this to ''''\r\n$site_start_year = '''';\r\n\r\n// Site Author can be used for the Author and Copyright meta tags\r\n// To use the document author details of each document, set this to ''''\r\n$site_author = $etomite->config[''site_name''];\r\n\r\n// Provide the full URL of your web site.\r\n// For example: http://www.yourdomain.com\r\n// NOTE: Do not put a / on the end of the web site URL.\r\n// Used to build the DC.identifier tag\r\nglobal $ETOMITE_PAGE_BASE;\r\n$websiteurl = $ETOMITE_PAGE_BASE[''www''];\r\n\r\n// Provide the latitude of the resource\r\n$latitude = "";\r\n\r\n// Provide the longitude of the resource\r\n$longitude = "";\r\n\r\n// Provide the place name of the resource\r\n$placename = "";\r\n\r\n// Provide the ISO 3166 region code of the resource\r\n$region = "";\r\n\r\n// DC Tags is used to specify if the Dublin Core Metadata Initiative \r\n// meta tags should also be generated.\r\n// Set to true to generate them, false otherwise.\r\n$dc_tags = true;\r\n\r\n// Geo Tags is used to specify if the Geo Tags \r\n// meta tags should also be generated.\r\n// Set to true to generate them, false otherwise.\r\n$geo_tags = true;\r\n\r\n\r\n// Initialise variables\r\n\r\n$MetaType = "";\r\n$MetaLanguage = "";\r\n$MetaTitle = "";\r\n$MetaGenerator = "";\r\n$MetaDesc = "";\r\n$MetaKeys = "";\r\n$MetaAbstract = "";\r\n$MetaAuthor = "";\r\n$MetaCopyright = "";\r\n$MetaRobots = "";\r\n$MetaGooglebot = "";\r\n$MetaCache = "";\r\n$MetaPragma = "";\r\n$MetaExpires = "";\r\n$MetaEditedOn = "";\r\n$MetaDistribution = "";\r\n$MetaRating = "";\r\n\r\n// The data format of the resource\r\n$DC_format = "";\r\n\r\n// The language of the content of the resource\r\n$DC_language = "";\r\n\r\n// The name given to the resource\r\n$DC_title = "";\r\n\r\n// A textual description of the content and/or purpose of the resource\r\n// Equivalent to "description"\r\n$DC_description = "";\r\n\r\n// The subject and topic of the resource that succinctly \r\n// describes the content of the resource.\r\n// Equivalent to "keywords"\r\n$DC_subject = "";\r\n\r\n// Any form of the title used as a substitute or alternative \r\n// to the formal title of the resource.\r\n// Equivalent to "abstract"\r\n$DC_title_alternative = "";\r\n\r\n// The name of the entity responsible for making the resource available\r\n// Equivalent to "author"\r\n$DC_publisher = "";\r\n\r\n// An entity primarily responsible for making the content of the resource\r\n// Equivalent to "author"\r\n$DC_creator = "";\r\n\r\n// A statement or pointer to a statement about the \r\n// rights management information for the resource\r\n// Equivalent to "copyright"\r\n$DC_rights = "";\r\n\r\n// The date the resource was created in its current form\r\n$DC_date_created = "";\r\n\r\n// The date the resource was last modified or updated\r\n$DC_date_modified = "";\r\n\r\n// The date of validity of the resource.\r\n// Specified as from the creation date to the expiry date\r\n$DC_date_valid = "";\r\n\r\n// A unique identifier for the resource\r\n$DC_identifier = "";\r\n\r\n\r\n// The latitude and longitude of the resource\r\n$Geo_position = "";\r\n\r\n// The latitude and longitude of the resource\r\n$Geo_icbm = "";\r\n\r\n// The place name of the resource\r\n$Geo_placename = "";\r\n\r\n// The region of the resource\r\n$Geo_region = "";\r\n\r\n\r\n// *** FUNCTIONS ***\r\n\r\nfunction get_local_GMT_offset($server_offset_time) {\r\n    // Get the local GMT offset when given the\r\n    // local to Etomite server offset time in seconds\r\n    $GMT_offset = date("O");\r\n    $GMT_hr = substr($GMT_offset,1,2);\r\n    $GMT_min = substr($GMT_offset,4,2);\r\n    $GMT_sign = substr($GMT_offset,0,1);\r\n    $GMT_secs = (intval($GMT_hr) * 3600) + (intval($GMT_min) * 60);\r\n    if ($GMT_sign == ''-'') { $GMT_secs = $GMT_secs * (-1); }\r\n\r\n    // Get the local GMT offset in seconds\r\n    $GMT_local_seconds = $GMT_secs + $server_offset_time;\r\n    $GMT_local_secs = abs($GMT_local_seconds);\r\n\r\n    // round down to the number of hours\r\n    $GMT_local_hours = intval($GMT_local_secs / 3600);\r\n    // round down to the number of minutes\r\n    $GMT_local_minutes = intval(($GMT_local_secs - ($GMT_local_hours * 3600)) / 60);\r\n    if ($GMT_local_seconds < 0) {\r\n      $GMT_value = "-";\r\n    } else {\r\n      $GMT_value = "+";\r\n    }\r\n    $GMT_value .= sprintf("%02d:%02d", $GMT_local_hours, $GMT_local_minutes);\r\n    return $GMT_value;\r\n}\r\n\r\nfunction get_local_iso_8601_date($int_date, $server_offset_time) {\r\n    // Return an ISO 8601 style local date\r\n    // $int_date: current date in UNIX timestamp\r\n    $GMT_value = get_local_GMT_offset($server_offset_time);\r\n    $local_date = date("Y-m-d\\TH:i:s", $int_date + $server_offset_time);\r\n    $local_date .= $GMT_value;\r\n    return $local_date;\r\n}\r\n\r\nfunction get_local_rfc_822_date($int_date, $server_offset_time) {\r\n    // return an RFC 822 style local date\r\n    // $int_date: current date in UNIX timestamp\r\n    $GMT_value = get_local_GMT_offset($server_offset_time);\r\n    $local_date = date("D, d M Y H:i:s", $int_date + $server_offset_time);\r\n    $local_date .= " ".str_replace('':'', '''', $GMT_value);\r\n    return $local_date;\r\n}\r\n\r\n\r\n// *** Start Creating Meta Tags ***\r\n\r\n// *** CONTENT-TYPE ***\r\n$MetaType = " <meta http-equiv=\\"content-type\\" content=\\"".$content_type."\\" />\\n";\r\n\r\n// *** DC.FORMAT ***\r\nif ($dc_tags) {\r\n   $DC_format = " <meta name=\\"DC.format\\" content=\\"".$content_type."\\" />\\n";\r\n}\r\n\r\n\r\n// *** CONTENT-LANGUAGE ***\r\n$MetaLanguage = " <meta http-equiv=\\"content-language\\" content=\\"".$language."\\" />\\n";\r\n\r\n// *** DC.LANGUAGE ***\r\nif ($dc_tags) {\r\n   $DC_language = " <meta name=\\"DC.language\\" content=\\"".$language."\\" />\\n";\r\n}\r\n\r\n// *** GENERATOR ***\r\n$version = $etomite->getVersionData();\r\n$version[''version''] = trim($version[''version'']);\r\n$version[''code_name''] = trim($version[''code_name'']);\r\nif (($version[''version''] != "") || ($version[''code_name''] != "")) {\r\n   $MetaGenerator = " <meta name=\\"generator\\" content=\\"Etomite";\r\n   if($version[''version''] != ""){\r\n      $MetaGenerator .= " ".$version[''version''];\r\n   }\r\n   if($version[''code_name''] != ""){\r\n      $MetaGenerator .= " (".$version[''code_name''].")";\r\n   }\r\n   $MetaGenerator .= "\\" />\\n";\r\n}\r\n\r\n$docInfo = $etomite->getDocument($etomite->documentIdentifier);\r\n\r\n// *** DESCRIPTION ***\r\n// Trim and replace double quotes with entity\r\n$description = $docInfo[''description''];\r\n$description = str_replace(''"'', ''&#34;'', trim($description)); \r\nif(!$description == ""){\r\n   $MetaDesc = " <meta name=\\"description\\" content=\\"$description\\" />\\n";\r\n\r\n// *** DC.DESCRIPTION ***\r\n   if ($dc_tags) {\r\n      $DC_description = " <meta name=\\"DC.description\\"";\r\n      $DC_description .= " content=\\"$description\\" />\\n";\r\n   }\r\n}\r\n\r\n// *** KEYWORDS ***\r\n$keywords = $etomite->getKeywords();\r\nif(count($keywords)>0) {\r\n   $keys = join($keywords, ", ");\r\n   $MetaKeys = " <meta name=\\"keywords\\" content=\\"$keys\\" />\\n";\r\n\r\n// *** DC.SUBJECT ***\r\n   if ($dc_tags) {\r\n      $keys = join($keywords, "; ");\r\n      $DC_subject = " <meta name=\\"DC.subject\\"";\r\n      $DC_subject .= " content=\\"$keys\\" />\\n";\r\n   }\r\n}\r\n\r\n// *** ABSTRACT ***\r\n// Use the Site Name and the documents Long Title (or Description)  \r\n// to build an Abstract meta tag.\r\n$sitename = $etomite->config[''site_name''];\r\n// Trim and replace double quotes with entity\r\n$sitename = str_replace(''"'', ''&#34;'', trim($sitename)); \r\n\r\n$abstract = trim($docInfo[''longtitle'']);\r\nif($abstract == ""){\r\n   $abstract = $description;\r\n}\r\n// Replace double quotes with entity\r\n$abstract = str_replace(''"'', ''&#34;'', $abstract); \r\n\r\nif(($sitename != "") || ($abstract != "")) {\r\n   $separator = " - ";\r\n   if($sitename == ""){\r\n      $separator = "";\r\n   }\r\n   $MetaAbstract = " <meta name=\\"abstract\\" content=\\"".$sitename.$separator.$abstract."\\" />\\n";\r\n\r\n// *** DC.TITLE.ALTERNATIVE ***\r\n   if ($dc_tags) {\r\n      $DC_title_alternative = " <meta name=\\"DC.title.alternative\\"";\r\n      $DC_title_alternative .= " content=\\"".$sitename.$separator.$abstract."\\" />\\n";\r\n   }\r\n}\r\n\r\n// *** TITLE ***\r\n// Use the Site Name and the documents Page Title and Long Title  \r\n// to build the Title meta tag.\r\n\r\n// Start with the site name\r\n$title = $sitename;\r\n// Get the pagetitle, trim and replace double quotes with entity\r\n$pagetitle = str_replace(''"'', ''&#34;'', trim($docInfo[''pagetitle''])); \r\nif ($pagetitle != "") {\r\n   if ($title == "") {\r\n      $title = $pagetitle;\r\n   } else {\r\n      $title .= " - ".$pagetitle;\r\n   }\r\n}\r\n// Get the longtitle, trim and replace double quotes with entity\r\n$longtitle = str_replace(''"'', ''&#34;'', trim($docInfo[''longtitle''])); \r\nif ($longtitle != "") {\r\n   if ($title == "") {\r\n      $title = $longtitle;\r\n   } else {\r\n      $title .= " - ".$longtitle;\r\n   }\r\n}\r\nif ($title != "") {\r\n   $MetaTitle = " <title>".$title."</title>\\n";\r\n\r\n// *** DC.TITLE ***\r\n   if ($dc_tags || $geo_tags) {\r\n      $DC_title = " <meta name=\\"DC.title\\"";\r\n      $DC_title .= " content=\\"".$title."\\" />\\n";\r\n   }\r\n}\r\n\r\n// *** AUTHOR ***\r\nif ($site_author == '''') {\r\n   $authorid = $docInfo[''createdby''];\r\n   $tbl = $etomite->dbConfig[''dbase''].".".$etomite->dbConfig[''table_prefix'']."user_attributes";\r\n   $query = "SELECT fullname FROM $tbl WHERE $tbl.id = $authorid"; \r\n   $rs = $etomite->dbQuery($query);\r\n   $limit = $etomite->recordCount($rs); \r\n   if($limit=1) {\r\n      $resourceauthor = $etomite->fetchRow($rs); \r\n      $authorname = $resourceauthor[''fullname''];  \r\n   }\r\n   // Trim and replace double quotes with entity\r\n   $authorname = str_replace(''"'', ''&#34;'', trim($authorname));\r\n} else {\r\n   $authorname = $site_author;\r\n}\r\nif (!$authorname == ""){\r\n   $MetaAuthor = " <meta name=\\"author\\" content=\\"$authorname\\" />\\n";\r\n\r\n// *** DC.PUBLISHER & DC.CREATOR ***\r\n   if ($dc_tags) {\r\n      $DC_publisher = " <meta name=\\"DC.publisher\\" content=\\"$authorname\\" />\\n";\r\n      $DC_creator = " <meta name=\\"DC.creator\\" content=\\"$authorname\\" />\\n";\r\n   }\r\n}\r\n\r\n// *** COPYRIGHT ***\r\n// get the Etomite server offset time in seconds\r\n$server_offset_time = $etomite->config[''server_offset_time''];\r\nif (!$server_offset_time) {\r\n   $server_offset_time = 0;\r\n}\r\n\r\n// get the current time and apply the offset\r\n$timestamp = time() + $server_offset_time;\r\n// Set the current year\r\n$today_year = date(''Y'',$timestamp);\r\n$createdon = date(''Y'',$docInfo[''createdon'']);\r\nif ($site_start_year == '''') {\r\n   if ($today_year != $createdon) {\r\n      $copydate = $createdon."&#8211;".$today_year;\r\n   } else {\r\n      $copydate = $today_year;\r\n   }\r\n} else {\r\n   if ($today_year != $site_start_year) {\r\n      $copydate = $site_start_year."&#8211;".$today_year;\r\n   } else {\r\n      $copydate = $today_year;\r\n   }\r\n}\r\nif ($authorname == '''') {\r\n   $copyname = $authorname;\r\n} else {\r\n   $copyname = " by ".$authorname;\r\n}\r\n$MetaCopyright = " <meta name=\\"copyright\\" content=\\"Copyright &#169; ";\r\n$MetaCopyright .= $copydate.$copyname.". All rights reserved.\\" />\\n";\r\n\r\n// *** DC.RIGHTS ***\r\nif ($dc_tags) {\r\n   $DC_rights = " <meta name=\\"DC.rights\\" content=\\"Copyright &#169; ";\r\n   $DC_rights .= $copydate.$copyname.". All rights reserved.\\" />\\n";\r\n}\r\n\r\n// *** ROBOTS and GOOGLEBOT ***\r\n// Determine if this document has been set to non-searchable.\r\n// As the default for the Robots and Googlebot Meta Tags are index and follow,\r\n// these tags are only needed when we don''t want the document searched. \r\nif(!$etomite->documentObject[''searchable'']){\r\n   $MetaRobots = " <meta name=\\"robots\\" content=\\"noindex, nofollow\\" />\\n";\r\n   $MetaGooglebot = " <meta name=\\"googlebot\\" content=\\"noindex, nofollow, noarchive, nosnippet\\" />\\n";\r\n}\r\n\r\n// *** CACHE-CONTROL and PRAGMA ***\r\n// Output no-cache directives via the Cache-Control and Pragma meta tags\r\n// if this document is set to non-cacheable. \r\n$cacheable = $docInfo[''cacheable''];\r\nif (!$cacheable) {\r\n   $MetaCache = " <meta http-equiv=\\"cache-control\\" content=\\"no-cache\\" />\\n";\r\n   $MetaPragma = " <meta http-equiv=\\"pragma\\" content=\\"no-cache\\" />\\n";\r\n}\r\n\r\n// *** DC.DATE.CREATED ***\r\nif ($dc_tags) {\r\n   $createdon = get_local_iso_8601_date($docInfo[''createdon''], $server_offset_time);\r\n   $created = substr($createdon,0,10);\r\n   $DC_date_created = " <meta name=\\"DC.date.created\\" content=\\"";\r\n   $DC_date_created .= $created."\\" />\\n";\r\n}\r\n\r\n// *** EXPIRES ***\r\n$unpub_date = $docInfo[''unpub_date''];\r\nif ($unpub_date > 0) {\r\n   $unpubdate = get_local_rfc_822_date($unpub_date, $server_offset_time);\r\n   $MetaExpires = " <meta http-equiv=\\"expires\\" content=\\"$unpubdate\\" />\\n";\r\n\r\n// *** DC.DATE.VALID ***\r\n   if ($dc_tags) {\r\n      $dcunpubdate = get_local_iso_8601_date($unpub_date, $server_offset_time);\r\n      $valid = substr($dcunpubdate,0,10);\r\n      $DC_date_valid = " <meta name=\\"DC.date.valid\\" content=\\"";\r\n      $DC_date_valid .= $created."/".$valid."\\" />\\n";\r\n   }\r\n}\r\n\r\n// *** LAST MODIFIED ***\r\n$editedon = get_local_rfc_822_date($docInfo[''editedon''], $server_offset_time);\r\n$MetaEditedOn = " <meta http-equiv=\\"last-modified\\" content=\\"$editedon\\" />\\n";\r\n\r\n// *** DC.DATE.MODIFIED ***\r\nif ($dc_tags) {\r\n   $dceditedon = get_local_iso_8601_date($docInfo[''editedon''], $server_offset_time);\r\n   $modified = substr($dceditedon,0,10);\r\n   $DC_date_modified = " <meta name=\\"DC.date.modified\\" content=\\"";\r\n   $DC_date_modified .= $modified."\\" />\\n";\r\n}\r\n\r\n// *** DISTRIBUTION ***\r\nif (!$distribution == '''') {\r\n   $MetaDistribution = " <meta name=\\"distribution\\" content=\\"".$distribution."\\" />\\n";\r\n}\r\n\r\n// *** RATING ***\r\nif (!$rating == '''') {\r\n   $MetaRating = " <meta name=\\"rating\\" content=\\"".$rating."\\" />\\n";\r\n}\r\n\r\nif ($dc_tags) {\r\n\r\n// *** DC.IDENTIFIER ***\r\n   $url = $websiteurl."[~".$etomite->documentIdentifier."~]";\r\n   $DC_identifier = " <meta name=\\"DC.identifier\\" content=\\"".$url."\\" />\\n";\r\n}\r\n\r\n\r\nif ($geo_tags) {\r\n   if (($latitude != "") && (longitude != "")) {\r\n\r\n// *** GEO.ICBM ***\r\n      $Geo_icbm = " <meta name=\\"ICBM\\"";\r\n      $Geo_icbm .= " content=\\"".$latitude.", ".$longitude."\\" />\\n";\r\n\r\n// *** GEO.POSITION ***\r\n      $Geo_position = " <meta name=\\"geo.position\\"";\r\n      $Geo_position .= " content=\\"".$latitude.";".$longitude."\\" />\\n";\r\n   }\r\n\r\n   if ($region != "") {\r\n\r\n// *** GEO.REGION ***\r\n      $Geo_region = " <meta name=\\"geo.region\\"";\r\n      $Geo_region .= " content=\\"".$region."\\" />\\n";\r\n   }\r\n\r\n   if ($placename != "") {\r\n\r\n// *** GEO.PLACENAME ***\r\n      $Geo_placename = " <meta name=\\"geo.placename\\"";\r\n      $Geo_placename .= " content=\\"".$placename."\\" />\\n";\r\n   }\r\n\r\n}\r\n\r\n\r\n// *** RETURN RESULTS ***\r\n\r\n$output = $MetaType.$MetaLanguage.$MetaGenerator;\r\n$output .= $MetaTitle.$MetaDesc.$MetaKeys;\r\n$output .= $MetaAbstract.$MetaAuthor.$MetaCopyright;\r\n$output .= $MetaRobots.$MetaGooglebot;\r\n$output .= $MetaCache.$MetaPragma.$MetaExpires.$MetaEditedOn;\r\n$output .= $MetaDistribution.$MetaRating;\r\n\r\nif ($dc_tags) {\r\n  $dc_output = $DC_format.$DC_language.$DC_title;\r\n  $dc_output .= $DC_description.$DC_subject.$DC_title_alternative;\r\n  $dc_output .= $DC_publisher.$DC_creator.$DC_rights;\r\n  $dc_output .= $DC_date_created.$DC_date_modified.$DC_date_valid;\r\n  $dc_output .= $DC_identifier;\r\n  if ($dc_output != "") {\r\n    $output .= " \\n".$dc_output;\r\n  }\r\n}\r\nif ($geo_tags) {\r\n  $geo_output = "";\r\n  if (!$dc_tags) {\r\n    $geo_output .= $DC_title;\r\n  }\r\n  $geo_output .= $Geo_icbm;\r\n  $geo_output .= $Geo_position.$Geo_region.$Geo_placename;\r\n  if ($geo_output != "") {\r\n    $output .= " \\n".$geo_output;\r\n  }\r\n}\r\n\r\nreturn $output;', 0);

INSERT INTO `{PREFIX}site_snippets` VALUES (6, 'GoogleSiteMap_XML', 'Output a Google XML site map', '/**\r\n * GoogleSiteMap_XML Snippet for Etomite CMS\r\n * Version 0.8 2006-11-17\r\n *\r\n * Parameters:\r\n * [!GoogleSiteMap_XML?validate=true!] or [!GoogleSiteMap_XML?validate=1!]\r\n * tells the snippet to output the additional headers required to validate \r\n * your Sitemap file against a schema.\r\n *\r\n * Useage:\r\n * Create a snippet: GoogleSiteMap_XML\r\n * with the content of this file.\r\n * Update the configuration options below to suit your needs.\r\n * Create a template: GoogleSiteMap_Template \r\n * with the content "[!GoogleSiteMap_XML!]".\r\n * Create a page in your repository: Google Site Map\r\n * with no content, the alias "google-sitemap",\r\n * using the GoogleSiteMap_Template, not searchable,\r\n * not cacheable, with content type "text/xml".\r\n *\r\n * Goto the Google Webaster Tools site at https://www.google.com/webmasters/tools/\r\n * Create an account, or login using your existing account.\r\n * Enter http://www.<your domain name>/ in the add site box and click OK.\r\n * Click on "Verify your site".\r\n * Choose "Add a META tag" as your verification option.\r\n * Add the generated meta tag to the head section of your home page template.\r\n * Back in Google Webmaster Tools, click on "Verify".\r\n * Click on the "Sitemaps" button.\r\n * Click on "Add a Sitemap".\r\n * Select "Add General Web Sitemap".\r\n * Enter "http://www.<your domain name>/google-sitemap.htm" as your sitemap URL.\r\n * Click on "Add Web Sitemap".\r\n *\r\n * \r\n * Ryan Nutt - http://blog.nutt.net\r\n * v0.1 - June 4, 2005\r\n * v0.2 - June 5, 2005 - Fixed a stupid mistake :-)\r\n * \r\n * Changes by Lloyd Borrett - http://www.borrett.id.au\r\n *\r\n * v0.3 - Sep 22, 2005\r\n * Only list searchable pages (Mod suggested by mplx)\r\n * Added configuration settings.\r\n * Made the site URL a configuration option.\r\n * Made displaying lastmoddate, priority and/or changefreq optional.\r\n * Added ability to display long date & time for lastmoddate\r\n * Made the long or short timeformat optional.\r\n * \r\n * v0.4 - 05-Feb-2006\r\n * Changed the snippet to output the local time for all date values\r\n * based on the Etomite server offset time\r\n * \r\n * v0.5 - 15-Feb-2006\r\n * Fixed incorrect local GMT offset value\r\n * \r\n * v0.6 - 7-Apr-2006\r\n * Get the base URL from Etomite instead of it being a configuration option.\r\n * \r\n * v0.7 - 30-Apr-2006\r\n * Get the base URL from Etomite using the new available \r\n * method built in to Etomite 0.6.1 Final. If using an earlier\r\n * version of Etomite, you''ll still need to provide the URL\r\n * as a configuration option.\r\n * \r\n * v0.8 - 17-Nov-2006\r\n * Updated to identify itself as using the Sitemap 0.9 protocol.\r\n * Added ability to force the change frequency to a set value for all documents.\r\n * Added ability to output the additional headers required to validate the sitemap format.\r\n * Additional comments added.\r\n * Code layout made consistent.\r\n * \r\n * Based on the ListSiteMap snippet by\r\n * JaredDC\r\n * \r\n * datediff function from\r\n * www.ilovejackdaniels.com\r\n */ \r\n\r\n// Overcome single use limitation on functions\r\nglobal $MakeMapDefined;\r\n\r\n// Get the validate parameter, if any\r\n$validateschema = false;\r\nif (isset($validate)) {\r\n   if (($validate == "1") || ($validate == "true")) {\r\n       $validateschema = true;\r\n   }\r\n}\r\n\r\n// Determine values required to convert the lastmod date and\r\n// time to local time. \r\n// get the Etomite server offset time in seconds\r\nglobal $server_offset_time;\r\nglobal $GMT_value;\r\n$server_offset_time = $etomite->config[''server_offset_time''];\r\nif (!$server_offset_time) {\r\n    $server_offset_time = 0;\r\n} \r\n\r\n// Get the server GMT offset in seconds\r\n$GMT_offset = date("O");\r\n$GMT_hr = substr($GMT_offset, 1, 2);\r\n$GMT_min = substr($GMT_offset, 4, 2);\r\n$GMT_sign = substr($GMT_offset, 0, 1);\r\n$GMT_secs = (intval($GMT_hr) * 3600) + (intval($GMT_min) * 60);\r\nif ($GMT_sign == ''-'') {\r\n    $GMT_secs = $GMT_secs * (-1);\r\n} \r\n\r\n// Get the local GMT offset in seconds\r\n$GMT_local_seconds = $GMT_secs + $server_offset_time;\r\n$GMT_local_secs = abs($GMT_local_seconds); \r\n// round down to the number of hours\r\n$GMT_local_hours = intval($GMT_local_secs / 3600); \r\n// round down to the number of minutes\r\n$GMT_local_minutes = intval(($GMT_local_secs - ($GMT_local_hours * 3600)) / 60);\r\nif ($GMT_local_seconds < 0) {\r\n    $GMT_value = "-";\r\n} else {\r\n    $GMT_value = "+";\r\n} \r\n$GMT_value .= sprintf("%02d:%02d", $GMT_local_hours, $GMT_local_minutes);\r\n\r\nif (!function_exists(datediff)) {\r\n    function datediff($interval, $datefrom, $dateto, $using_timestamps = false)\r\n    {\r\n        /**\r\n         * $interval can be:\r\n         * yyyy - Number of full years\r\n         * q - Number of full quarters\r\n         * m - Number of full months\r\n         * y - Difference between day numbers\r\n         * (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)\r\n         * d - Number of full days\r\n         * w - Number of full weekdays\r\n         * ww - Number of full weeks\r\n         * h - Number of full hours\r\n         * n - Number of full minutes\r\n         * s - Number of full seconds (default)\r\n         */\r\n\r\n        if (!$using_timestamps) {\r\n            $datefrom = strtotime($datefrom, 0);\r\n            $dateto = strtotime($dateto, 0);\r\n        } \r\n\r\n        $difference = $dateto - $datefrom; // Difference in seconds\r\n        \r\n        switch ($interval) {\r\n            case ''yyyy'': // Number of full years\r\n                $years_difference = floor($difference / 31536000);\r\n                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {\r\n                    $years_difference--;\r\n                } \r\n                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {\r\n                    $years_difference++;\r\n                } \r\n                $datediff = $years_difference;\r\n                break;\r\n\r\n            case "q": // Number of full quarters\r\n                $quarters_difference = floor($difference / 8035200);\r\n                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {\r\n                    $months_difference++;\r\n                } \r\n                $quarters_difference--;\r\n                $datediff = $quarters_difference;\r\n                break;\r\n\r\n            case "m": // Number of full months\r\n                $months_difference = floor($difference / 2678400);\r\n                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {\r\n                    $months_difference++;\r\n                } \r\n                $months_difference--;\r\n                $datediff = $months_difference;\r\n                break;\r\n\r\n            case ''y'': // Difference between day numbers\r\n                $datediff = date("z", $dateto) - date("z", $datefrom);\r\n                break;\r\n\r\n            case "d": // Number of full days\r\n                $datediff = floor($difference / 86400);\r\n                break;\r\n\r\n            case "w": // Number of full weekdays\r\n                $days_difference = floor($difference / 86400);\r\n                $weeks_difference = floor($days_difference / 7); // Complete weeks\r\n                $first_day = date("w", $datefrom);\r\n                $days_remainder = floor($days_difference % 7);\r\n                $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?\r\n                if ($odd_days > 7) { // Sunday\r\n                    $days_remainder--;\r\n                } \r\n                if ($odd_days > 6) { // Saturday\r\n                    $days_remainder--;\r\n                } \r\n                $datediff = ($weeks_difference * 5) + $days_remainder;\r\n                break;\r\n\r\n            case "ww": // Number of full weeks\r\n                $datediff = floor($difference / 604800);\r\n                break;\r\n\r\n            case "h": // Number of full hours\r\n                $datediff = floor($difference / 3600);\r\n                break;\r\n\r\n            case "n": // Number of full minutes\r\n                $datediff = floor($difference / 60);\r\n                break;\r\n\r\n            default: // Number of full seconds (default)\r\n                $datediff = $difference;\r\n                break;\r\n        } \r\n\r\n        return $datediff;\r\n    } \r\n} \r\n\r\nif (!isset($MakeMapDefined)) {\r\n    function MakeMap($funcEtomite, $listParent)\r\n    {\r\n        global $server_offset_time;\r\n        global $GMT_value;\r\n\r\n        // ***********************************\r\n        // Configuration Settings \r\n        // ***********************************\r\n\r\n        // $websiteURL [string]\r\n        // Provide the full base path URL of your web site,\r\n        // or let Etomite get it (v0.6.1 Final).\r\n        // For example: http://www.yourdomain.com/\r\n        // NOTE: You must put a / on the end of the web site URL.\r\n        //\r\n        // Original hard coded way to specify $websiteURL\r\n        // $websiteurl = ''http://www.<your domain name>/''; \r\n        //\r\n        // Early Etomite way to get $websiteURL automatically\r\n        // $websiteurl = $etomite->config[''www_base_path''];\r\n        //\r\n        // Etomite 0.6.1 Final way to get $websiteURL automatically\r\n        global $ETOMITE_PAGE_BASE;\r\n        $websiteurl = $ETOMITE_PAGE_BASE[''www''];\r\n\r\n        // $showlastmoddate [true | false]\r\n        // You can choose to disable providing the last modification\r\n        // date, or get it from the documents.\r\n        // true  - Get time from documents\r\n        // false - Disabled, do not write it\r\n        $showlastmoddate = true; \r\n\r\n        // $showlongtimeformat [ true | false ]\r\n        // You can choose to provide the time format as:\r\n        // true  - Long time format (with time, e.g. 2006-09-29T13:43:51+11:00)\r\n        // false - Short time format (date only, e.g. 2006-11-17)\r\n        $showlongtimeformat = true; \r\n\r\n        // $showpriority [ true | false ]\r\n        // You can choose to disable prividing the priority\r\n        // of a document relative to the whole set of documents,\r\n        // or calculate it based on the date difference.\r\n        // true  - Provide the priority\r\n        // false - Disabled, do not write it\r\n        $showpriority = true; \r\n\r\n        // $showchangefreq [true | false]\r\n        // You can choose to disable prividing the update\r\n        // (change) frequency of a document relative to the\r\n        // whole set of documents, or calculate it based on\r\n        // the date difference.\r\n        // true  - Provide the change frequency\r\n        // false - Disabled, do not write it\r\n        $showchangefreq = true;\r\n\r\n        // $forcechangefreq [string]\r\n        // You can choose to force the change frequency for all \r\n        // documents to one of the valid values.\r\n        // By specifying nothing, the snippet will calculate the \r\n        // change frequency of a document relative to the\r\n        // whole set of documents, or calculate it based on\r\n        // the date difference.\r\n        // "always", "hourly", "daily", "weekly", "monthly",\r\n        // "yearly", "never" - Force this value for every document\r\n        // "" - Calculate change frequency from last mod date\r\n        $forcechangefreq = "";\r\n\r\n        // ***********************************\r\n        // END CONFIG SETTINGS\r\n        // THE REST SHOULD TAKE CARE OF ITSELF\r\n        // ***********************************\r\n\r\n        $children = $funcEtomite->getActiveChildren($listParent, "menuindex", "ASC", "id, editedon, searchable");\r\n        foreach($children as $child) {\r\n            $id = $child[''id''];\r\n            $url = $websiteurl . "[~" . $id . "~]";\r\n\r\n            $date = $child[''editedon''];\r\n            $lastmoddate = $date;\r\n            $date = date("Y-m-d", $date);\r\n\r\n            $searchable = $child[''searchable''];\r\n            if ($searchable) {\r\n                // Get the date difference\r\n                $datediff = datediff("d", $date, date("Y-m-d"));\r\n                if ($datediff <= 1) {\r\n                    $priority = "1.0";\r\n                    $update = "daily";\r\n                } elseif (($datediff > 1) && ($datediff <= 7)) {\r\n                    $priority = "0.75";\r\n                    $update = "weekly";\r\n                } elseif (($datediff > 7) && ($datediff <= 30)) {\r\n                    $priority = "0.50";\r\n                    $update = "weekly";\r\n                } else {\r\n                    $priority = "0.25";\r\n                    $update = "monthly";\r\n                } \r\n\r\n                $output .= "<url>\\n";\r\n\r\n                $output .= "<loc>$url</loc>\\n";\r\n\r\n                if ($showlastmoddate) {\r\n                    if (!$showlongtimeformat) {\r\n                        $lastmoddate = date("Y-m-d", $lastmoddate + $server_offset_time);\r\n                    } else {\r\n                        $lastmoddate = date("Y-m-d\\TH:i:s", $lastmoddate + $server_offset_time) . $GMT_value;\r\n                    } \r\n                    $output .= "<lastmod>$lastmoddate</lastmod>\\n";\r\n                } \r\n\r\n                if ($showchangefreq) {\r\n                    if ($forcechangefreq == "") {\r\n                        $output .= "<changefreq>$update</changefreq>\\n";\r\n                    } else {\r\n                        $output .= "<changefreq>$forcechangefreq</changefreq>\\n";\r\n                    }\r\n                } \r\n\r\n                if ($showpriority) {\r\n                    $output .= "<priority>$priority</priority>\\n";\r\n                } \r\n\r\n                $output .= "</url>\\n";\r\n            } \r\n\r\n            if ($funcEtomite->getActiveChildren($child[''id''])) {\r\n                $output .= MakeMap($funcEtomite, $child[''id'']);\r\n            } \r\n        } \r\n        return $output;\r\n    } \r\n    $MakeMapDefined = true;\r\n} \r\n\r\n$out = "<?xml version=\\"1.0\\" encoding=\\"UTF-8\\"?>\\n";\r\nif ($validateschema) {\r\n    $out .= "<urlset xmlns:xsi=\\"http://www.w3.org/2001/XMLSchema-instance\\"\\n";\r\n    $out .= "         xsi:schemaLocation=\\"http://www.sitemaps.org/schemas/sitemaps/0.9\\n";\r\n    $out .= "         http://www.sitemaps.org/schemas/sitemaps/sitemap.xsd\\"\\n";\r\n    $out .= "         xmlns=\\"http://www.sitemaps.org/schemas/sitemap/0.9\\">\\n";\r\n} else {\r\n    // $out .= "<urlset xmlns=\\"http://www.google.com/schemas/sitemap/0.84\\">\\n";\r\n    $out .= "<urlset xmlns=\\"http://www.sitemaps.org/schemas/sitemap/0.9\\">\\n";\r\n}\r\n\r\n// Produce the sitemap for the main web site\r\n$out .= MakeMap($etomite, 0);\r\n\r\n// To also list documents in unpublished repository folders,\r\n// place an additional call to MakeMap here for each one, e.g. \r\n// $out .= MakeMap($etomite, 8);\r\n// where 8 is the document id of the unpublished repository folder.\r\n\r\n$out .= "</urlset>";\r\n\r\nreturn $out;', 0);

-- END::INSERT site_snippets DATA


-- START::INSERT site_templates DATA

INSERT INTO `{PREFIX}site_templates` VALUES (1, 'AlexisPro Redux', 'Default template, designed by Helder :)', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" \r\n  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">\r\n<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">\r\n<head>\r\n  <title>[(site_name)] &raquo; [*pagetitle*]</title>\r\n  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />\r\n  [[GetKeywords]]\r\n  <style type="text/css">\r\n    @import url(''templates/AlexisProRedux/AlexisProRedux.css'');\r\n  </style>\r\n</head> \r\n\r\n<body>\r\n<table border="0" cellpadding="0" cellspacing="0" class="mainTable">\r\n  <tr class="fancyRow">\r\n    <td><span class="headers">&nbsp;<img \r\n      src="templates/AlexisProRedux/images/dot.gif" alt="" \r\n      style="margin-top: 1px;" />&nbsp;<a \r\n      href="[~[(site_start)]~]">[(site_name)]</a></span></td>\r\n    <td align="right"><span class="headers">[[PageTrail]]</span></td>\r\n  </tr>\r\n  <tr class="fancyRow2">\r\n    <td colspan="2" class="border-top-bottom smallText" \r\n      align="right">[[PoweredBy]]</td>\r\n  </tr>\r\n  <tr align="left" valign="top">\r\n    <td colspan="2"><table width="100%" border="0" \r\n      cellspacing="0" cellpadding="1">\r\n      <tr align="left" valign="top">\r\n        <td class="w22"><table width="100%" border="0" \r\n          cellpadding="0" cellspacing="0">\r\n          <tr>\r\n            <td align="center" valign="middle" class="logoBox"><a \r\n              href="[~[(site_start)]~]"><img \r\n              src="templates/AlexisProRedux/images/logoGradient.jpg" \r\n              width="131" height="121" alt="" /></a></td>\r\n          </tr>\r\n          <tr>\r\n            <td align="left" valign="top"><img \r\n              src="templates/AlexisProRedux/images/_tx_.gif" \r\n              height="4" alt="" /></td>\r\n          </tr>\r\n          <tr class="fancyRow2">\r\n            <td align="left" valign="top" class="navigationHead">Navigation</td>\r\n          </tr>\r\n          <tr style="padding: 0px; margin: 0px;">\r\n            <td align="left" valign="top" class="navigation" \r\n              style="padding: 0px; margin: 0px;"><img \r\n              src="templates/AlexisProRedux/images/_tx_.gif" \r\n              alt="" height="4" /><br />\r\n              [!MenuBuilder?id=0!]<img src="templates/AlexisProRedux/images/_tx_.gif" \r\n              height="4" alt="" /></td>\r\n          </tr>\r\n        </table></td>\r\n        <td class="pad" id="content">\r\n          <h1>[*longtitle*]</h1>\r\n          [*content*]</td>\r\n      </tr>\r\n    </table></td>\r\n  </tr>\r\n  <tr class="fancyRow2">\r\n    <td class="border-top-bottom smallText">&nbsp;</td>\r\n    <td class="border-top-bottom smallText" align="right">MySQL: \r\n      [^qt^], [^q^] request(s), PHP: [^p^], total: [^t^], \r\n      document retrieved from [^s^].</td>\r\n  </tr>\r\n</table>\r\n</body>\r\n</html>', 0);

INSERT INTO `{PREFIX}site_templates` VALUES (2, 'GoogleSiteMap_Template', 'Used to create a Google XML site map', '[!GoogleSiteMap_XML!]', 0);

-- START::INSERT site_templates DATA

-- END --
