# Etomite

**Manual upgrade to `master` branch with Texy!**
- SQL script:
```sql
ALTER TABLE `etomite_site_content` ADD `texy` mediumtext collate utf8_unicode_ci NOT NULL;
UPDATE etomite_site_content SET texy = content WHERE 1 = 1;
UPDATE etomite_system_settings SET setting_value = 0 WHERE setting_name = 'use_captcha';
```
- File: `manager\includes\config.inc.php`, add at line 18: `$database_server_port = "3308";`

 **Texy! (Texyla, FSHL)**
- Texy! (syntax): https://texy.info
- Texyla (editor): https://github.com/janmarek/Texyla/wiki
- FSHL (fast syntax highlighter): https://github.com/kukulich/fshl

**History:**
- 2017-10-13: Manual merge with `Texyla` branch (support for Texy! 0.6.1, Taxyla, FSHL)
- 2017-10-05: Fix: `mysqli_escape_string()` and `mysqli_insert_id()`
- 2017-06-02: Fix: port for PHP 7 and MySQL 5.7
- 2010-11-29: Fix: PHP 5.3, first release by Matej Kolesár
- 2008-05-10: Last official released version by *Ralph A. Dahlgren*
 
### PHP 7.1.×
- `mysql_*` to `mysqli_*` (procedural style, add connection),
- add port support for mysqli,
- db config for *log* and *cache*,
- remove `set_magic_quotes_runtime` for PHP > 5.3

### MySQL 5.7
- remove `ENGINE=MyISAM`,
- `date` to `datetime`,
- `0000-00-00` -> `CURRENT_TIMESTAMP`,
- `0000-00-00 00:00:00` -> `CURRENT_TIMESTAMP`
- `ip` from `20` to `30` -> `etomite_active_users` .

*Etomite for PHP 7.1.× by Matej Kolesár :bowtie:*

## Etomite Prelude v1.1 installation and upgrade notes

*Modified 2008-05-08 [v1.1] by Ralph Dahlgren*


The Etomite Prelude v1.1 code base includes several database modifications
which must be performed on any existing installations prior to v1.0 before the
installation will perform properly. New installations will automatically
include these modifications so no further action will be required after the
installation is complete other than editing and saving the site configuration
settings as prompted by the installer.

## New Installations

IMPORTANT: As of Etomite Prelude v1.1 the code base no longer ships with a
manager/includes/config.inc.php file. In order to make upgrades easier the
package now contains a placeholder file named manager/includes/config.php
which needs to be renamed or copied to manager/includes/config.inc.php
and be set to have full read + write permissions before a new installation
can be performed.

* Perform backups just in case something goes wrong. YOU HAVE BEEN WARNED!
* Upload all files to your web server
* Follow the installation procedures described at
  http://docs.etomite.com/installation.html
* If you don't want to read the instructions simply point your browser to
  http://your_etomite_server/ and follow the installation instructions.
  It'll be easier if you follow the instructions mentioned above, however.
* If you have problems you can seek help in the support forums at
  http://www.etomite.com/forums


## Upgrading Previous Releases

IMPORTANT: As of Etomite Prelude v1.1 you no longer have to manually perform
any manager/includes/config.inc.php maintenance during upgrades. All that is
required is that the existing file have write permissions so the installer can
make any required changes.

* Perform backups just in case something goes wrong. YOU HAVE BEEN WARNED!
* Upload all files to your web server
* Point your browser to http://your_etomite_server/install/
* Select: Upgrade an existing installation
* Read the entire upgrade document before proceeding. YOU HAVE BEEN WARNED!
* Follow any instructions which pertain to your specific needs based on your
  previous release
* Make sure you click on the link, "v1_db_patches.php", to perform add
  database upgrades! YOU HAVE BEEN WARNED!
* If you didn't go into your [Etomite manager > Configuration] yet to verify
  and save your settings, do so now and then perform a [Clear site cache]
  from the Etomite Main Menu. (There are new settings that should be reviewed
  and set according to your needs!)
* You should have a fully functional Etomite Prelude v1.1 installation
* If you have problems you can seek help in the support forums at
  http://www.etomite.com/forums

PLEASE NOTE: When v1_db_patches.php is run it only sets the new chunk and
export role permissions for Admin users. If other user roles require chunk or
export permissions they must be assigned manually within the
[Etomite Manager > Manage Users > Role Management] section.


GOOD LUCK...
*The Etomite CMS Project Development Team*
