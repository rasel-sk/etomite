<?php
// pre_061_notes.php
// Etomite CMS depricated release notes
// Created: 2007-05-04 by Ralph Dahlgren
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Etomite &raquo; Install</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
      @import url('../assets/site/style.css');
  </style>
</head>

<body>
<table border="0" cellpadding="0" cellspacing="0" class="mainTable">
  <tr class="fancyRow">
    <td><span class="headers">&nbsp;<img src="../manager/media/images/misc/dot.gif" alt="" style="margin-top: 1px;" />&nbsp;Etomite</span></td>
    <td align="right"><span class="headers">Installation</span></td>
  </tr>
  <tr class="fancyRow2">
    <td colspan="2" class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
  <tr align="left" valign="top">
    <td colspan="2"><table width="100%"  border="0" cellspacing="0" cellpadding="1">
      <tr align="left" valign="top">
        <td class="pad" id="content" colspan="2">

<p><span style="font-weight: bold; color: rgb(255, 0, 0);">Upgrading from Etomite 0.6 to this release</span></p>

<p>The upgrade from 0.6 to 0.6.1 requires no database structure changes other than the one mentioned on the main upgrade page.</p>

<p>The only other change, other than the config file mentioned on the main upgrade page, is to delete your existing <span style="font-weight: bold;">assets/cache/etomiteCache.idx</span> file and then perform a <b>Clear site cache</b> from the Etomite Manager main menu as soon it has been deleted so that a new <span style="font-weight: bold;">assets/cache/etomiteCache.idx.php</span> file is created. This step must be done for security reasons and your site will not be functional until the new cache file has been created.</p>

<p>It is recommended that the <span style="font-weight: bold;">assets/cache/</span>, <span style="font-weight: bold;">assets/export/</span>, and <span style="font-weight: bold;">assets/images/</span> directories be checked to assure proper permissions. These directories must be readable and writable by Etomite. Insufficient permissions will result in parser errors when attempting to access your Etomite web pages.</p>

<p>If the steps listed here have been followed and settings confirmed this should complete the 0.6 to 0.6.1 upgrade process and your installation should now be fully functional.</p>

<p>&nbsp;</p>

<p><span style="font-weight: bold; color: rgb(255, 0, 0);">Upgrading from Phase 0.5.3 to this release</span></p>

<p>This guide will help you to get started on upgrading your Phase 0.5.3 installation to Etomite 0.6.1. </p>

<p><strong style="color: rgb(255, 0, 0);">Please note</strong>: Your Phase 0.5.3 snippets will <strong>NOT</strong> work with Etomite 0.6.1. Therefore, it is advisable not to perform these actions on a production site!</p>

<p><strong style="color: rgb(255, 0, 0);">Recommendation</strong>: A lot has changed between Phase 0.5.3 and Etomite 0.6.1. Although it is possible to upgrade 0.5.3 to 0.6.1 successfully, it requires quite a bit of manual work (especially for rewriting the snippets). In fact, it's easier to install a fresh Etomite 0.6.1 site, and then to copy the site's content into that installation.</p>

<p>In order to prepare your 0.5.3 installation for 0.6.1, you'll need to manually run some SQL statements to upgrade the database. These SQL statements are available  <a class="external" href="sql/053upgrade.sql">here</a>. Open this file, and replace all instances of {PREFIX} with the current table_prefix used in Phase.</p>

<p>Once the file has been prepared, you need to run it using a tool such as phpMyAdmin. It's a good idea to do each statement seperately, although you can try to do it all at once. Please note, some of the statements may fail, this will mean that the action that statement performs isn't necessary. </p>

<p>Once the SQL statements have been run, your database is up to date for Etomite 0.6.1. Now we need to update the main config file. Paste the code displayed above into <span style="font-weight: bold;">manager/includes/config.inc.php</span>, checking the database details for accuracy:</p>

<p>Now it's up to you to upgrade the snippets to work with the new Etomite 0.6.1 parser/ API. More information can be found on the Etomite site, and support is offered in the forums... to an extent. Please remember Phase 0.5.x is many years old!</p>

    </td>
      </tr>
    </table></td>
  </tr>
  <tr class="fancyRow2">
    <td class="border-top-bottom smallText">&nbsp;</td>
    <td class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
</table>
</body>
</html>
