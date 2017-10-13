<?php
// mutate_settings.dynamic.action.php
// Modified in 0.6.1 by Ralph
// Modified: 2007-01-29 by Ralph for better sitestatus performance
// Modified: 2008-04-21 [v1.0] by Ralph to add more settings
// Modified 2008-05-08 by Ralph to fix minor bugs
// Modified 2008-05-10 [v1.1] by Ralph Dahlgren
// - HTML markup improvements
// Modified: 2008-13.06 Petr Vaněk aka krteczek
if(IN_ETOMITE_SYSTEM!="true")
{
  die($_lang["include_ordering_error"]);
}

if($_SESSION['permissions']['settings']!=1 && $_REQUEST['a']==17) {
  $e->setError(3);
  $e->dumpError();
}

// check to see the edit settings page isn't locked
$sql = "SELECT internalKey, username FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.action=17";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit > 1)
{
  for ($i=0; $i < $limit; $i++)
  {
    $lock = mysql_fetch_assoc($rs);
    if($lock['internalKey']!=$_SESSION['internalKey'])
    {
      $msg = $lock['username']." is currently editing these settings. Please wait until the other user has finished and try again.";
      $e->setError(5, $msg);
      $e->dumpError();
    }
  }
}
// end check for lock

$displayStyle = $_SESSION['browser']=='mz' ? "table-row" : "block" ;

// calculate fm_plugin_document_url
function getFileDocumentUrl()
{
  return 'http://'.$_SERVER['SERVER_NAME'].str_replace("/manager/index.php", "/assets/documents", $_SERVER["PHP_SELF"]);
}
// calculate fm_plugin_base_url
function getFileBaseUrl()
{
  return 'http://'.$_SERVER['SERVER_NAME'].str_replace("/manager/index.php", "", $_SERVER["PHP_SELF"]);
}
// calculate im_plugin_base_url
function getImageBaseUrl()
{
  return 'http://'.$_SERVER['SERVER_NAME'].str_replace("/manager/index.php", "/assets/images/", $_SERVER["PHP_SELF"]);
}
// calculate im_plugin_base_dir
function getImageBaseDir()
{
  return str_replace("/manager/index.php", "/assets/images/", $_SERVER["PATH_TRANSLATED"]);
}
// calculate xp_Stylist_path
function getBaseUrl()
{
  return 'http://'.$_SERVER['SERVER_NAME'].str_replace("/manager/index.php", "/", $_SERVER["PHP_SELF"]);
}
// calculate Etomite root directory
function getEtomiteRoot()
{
  return str_replace("/manager/index.php", "", $_SERVER["PATH_TRANSLATED"]);
}

?>

<script type="text/javascript">
function checkEditor()
{
  editor = document.settings.which_editor[document.settings.which_editor.selectedIndex].value;
  switch(editor)
  {
    case "1":  //TinyMCE
      showHide(/tmceRow/, 1);
      showHide(/haRow/, 0);
      showHide(/xaRow/, 0);
      showHide(/haxaRow7/, 1);
      showHide(/haxaRow8/, 1);
      showHide(/haxaRow9/, 1);
      //hide FCK rows
      showHide(/imRow/, 0);
      showHide(/fmRow/, 0);
      showHide(/stylistRow/, 0);
    break;
    case "2":  //HtmlArea
      showHide(/tmceRow/, 0);
      showHide(/xaRow/, 0);
      showHide(/stylistRow/, 0);
      //hide FCK rows
      showHide(/haRow/, 1);
      showHide(/haxaRow/, 1);
    break;
    case "3":  //FCKeditor
      //show FCK rows
      showHide(/tmceRow/, 0);
      showHide(/haRow/, 0);
      showHide(/haxaRow/, 0);
      showHide(/xaRow/, 0);
      showHide(/imRow/, 0);
      showHide(/fmRow/, 0);
      showHide(/stylistRow/, 0);
    break;
    case "4":  //XINHA
      showHide(/xaRow/, 1);
      showHide(/haxaRow/, 1);
      showHide(/tmceRow/, 0);
      showHide(/haRow/, 0);
      //hide FCK rows
    break;
  }

  im_on = document.settings.im_plugin[0].checked; // check if im_plugin is on
  if(im_on == true && (editor == 2 || editor == 4))
  {
    showHide(/imRow/, 1);
  }
  fm_on = document.settings.fm_plugin[0].checked; // check if fm_plugin is on
  if(fm_on == true && (editor == 2 || editor == 4))
  {
    showHide(/fmRow/, 1);
  }
  stylist_on = document.settings.xp_Stylist[0].checked; // check if xp_Stylist is on
  if(stylist_on == true && editor == 4)
  {
    showHide(/stylistRow/, 1);
  }
}

function showHide(what, onoff)
{
  var all = document.getElementsByTagName( "*" );
  var l = all.length;
  var buttonRe = what;
  var id, el, stylevar;

  if(onoff == 1)
  {
    stylevar = "<?php echo $displayStyle; ?>";
  }
  else
  {
    stylevar = "none";
  }

  for( var i = 0; i < l; i++ )
  {
    el = all[i]
    id = el.id;
    if( id == "" ) continue;
    if(buttonRe.test(id))
    {
      el.style.display = stylevar;
    }
  }
}

// To handle the checkboxes for the XINHA plugins,
// we need to set unselected plugins to have a value of 0,
// and check them so that they will submit and be turned off
function fixCheckboxes()
{
  var all = document.getElementsByTagName( "INPUT" );
  var l = all.length;
  for( var i = 0; i < l; i++ )
  {
    el = all[i];
    type = el.type;
    name = el.name;
    if(type == "checkbox" && name.indexOf('xp_') == 0)
    {
      if(!el.checked)
      {
        el.value = 0;
        el.checked = 1;
      }
    }
  }
  //document.settings.submit();
}

function xp_select(state)
{
  var all = document.getElementsByTagName( "INPUT" );
  var l = all.length;
  for( var i = 0; i < l; i++ )
  {
    el = all[i];
    type = el.type;
    name = el.name;
    if(type == "checkbox" && name.indexOf('xp_') == 0)
    {
      if (state=='all')
      {
        el.checked = 1;
      }
      else
      {
        el.checked = 0;
      }
    }
  }
}
</script>

<form name="settings" action="index.php?a=30" method="post">
<div id="sysSettings">
<div class="subTitle">
  <span class="floatLeft">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
  <input type="submit" name="submit" value="<?php echo $_lang['save']; ?>" onClick="documentDirty=false; fixCheckboxes();" class="doSomethingButton">
    <a href="index.php?a=2" onClick="" class="doSomethingButton">
      <?php echo $_lang['cancel']; ?>
    </a>
  </span>
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name ;?> - <?php echo $_lang['settings_title']; ?>
  </span>
</div>

  <input onChange="documentDirty=true;" type="hidden" name="settings_version" value="<?php echo $release; ?>">
  <!-- this field is used to check site settings have been entered / updated after install or upgrade -->
  <?php if(!isset($settings_version) || $settings_version!=$release) { ?>
  <div class='sectionBody' style='margin-left: 0px; margin-right: 0px;'><?php echo $_lang['settings_after_install']; ?></div>
  <?php } ?>
  <script type="text/javascript" src="media/script/tabpane.js"></script>
  <div class="tab-pane" id="settingsPane">
  <script type="text/javascript">
    tpSettings = new WebFXTabPane( document.getElementById( "settingsPane" ) );
    </script>

    <!-- #1 Site Settings -->

    <div class="tab-page" id="tabPage1">
      <h2 class="tab"><?php echo $_lang["settings_site"] ?></h2>
    <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage1" ) );</script>
      <table border="0" cellspacing="0" cellpadding="3">

<!-- START::Added new Date and Time format options -->
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["date_format"]; ?></b></td>
          <td>
            <select name="date_format" size="1" class="inputBox" onChange="documentDirty=true;" style="width: 120px;">
              <option value="%Y-%m-%d"<?php echo ($date_format=="%I:%M %p" || $date_format==null) ? ' selected="selected"' : ""?>>YYYY-MM-DD</option>
              <option value="%m-%d-%Y"<?php echo ($date_format=="%m-%d-%Y") ? ' selected="selected"' : ""?>>MM-DD-YYYY</option>
              <option value="%d-%m-%Y"<?php echo ($date_format=="%d-%m-%Y") ? ' selected="selected"' : ""?>>DD-MM-YYYY</option>
            </select>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["date_format_message"]; ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>

        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["time_format"]; ?></b></td>
          <td>
            <select name="time_format" size="1" class="inputBox" onChange="documentDirty=true;" style="width: 120px;">
              <option value="%I:%M %p"<?php echo ($time_format=="%I:%M %p" || $time_format==null) ? ' selected="selected"' : ""?>>HH:MM AM|PM</option>
              <option value="%H:%M"<?php echo ($time_format=="%H:%M") ? ' selected="selected"' : ""?>>HH:MM (24H)</option>
            </select>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["time_format_message"]; ?></td>
        </tr>
<!-- END::Added new Date and Time format options -->

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>

        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["serveroffset_title"] ?></b></td>
          <td>
            <select name="server_offset_time" size="1" class="inputBox">
<?php
  for($i=-24; $i<25; $i++)
  {
    $seconds = $i*60*60;
    $selectedtext = $seconds==$server_offset_time ? "selected='selected'" : "" ;
?>
              <option value="<?php echo $seconds; ?>" <?php echo $selectedtext; ?>><?php echo $i; ?></option>
<?php
  }
?>
            </select> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php printf($_lang["serveroffset_message"], strftime($time_format, time()), strftime($time_format, time()+$server_offset_time)); ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'>&nbsp;</div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["server_protocol_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="server_protocol" value="http" <?php echo ($server_protocol=='http' || !isset($server_protocol))? 'checked="checked"' : "" ; ?>>
      <?php echo $_lang["server_protocol_http"]; ?><br />
      <input onChange="documentDirty=true;" type="radio" name="server_protocol" value="https" <?php echo $server_protocol=='https' ? 'checked="checked"' : "" ; ?>>
      <?php echo $_lang["server_protocol_https"]; ?> </td>
    </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["server_protocol_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["language_title"]; ?></b></td>
          <td> <select name="manager_language" size="1" class="inputBox" onChange="documentDirty=true;">
            <?php
              $dir = dir("includes/lang");
					$optLang = '';
					while ($file = $dir->read())
              		{
              			if(strpos($file, ".inc.php")>0)
              				{
              					$endpos = strpos ($file, ".");
              					$languagename = substr($file, 0, $endpos);
              					$selectedtext = $languagename==$manager_language ? "selected='selected'" : "" ;
              					$languagename1 = ucwords(str_replace("_", " ", $languagename));
              					$optLang .= <<< EEE
<option value="{$languagename}" {$selectedtext}>{$languagename1}</option>
EEE;
								}
						}
					echo $optLang;
              $dir->close();
            ?>
            </select> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["language_message"]; ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["charset_title"]; ?></b></td>
          <td>
            <select name="etomite_charset" size="1" class="inputBox" onChange="documentDirty=true;">
              <?php include("includes/charsets.php"); ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["charset_message"]; ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["sitename_title"] ?></b></td>
      <td ><input onChange="documentDirty=true;" type='text' maxlength='50' style="width: 200px;" name="site_name" value="<?php echo isset($site_name) ? $site_name : "My Etomite Site" ; ?>"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["sitename_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["sitestart_title"] ?></b></td>
      <td ><input onChange="documentDirty=true;" type='text' maxlength='10' size='5' name="site_start" value="<?php echo isset($site_start) ? $site_start : 1 ; ?>"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["sitestart_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["errorpage_title"] ?></b></td>
      <td ><input onChange="documentDirty=true;" type='text' maxlength='10' size='5' name="error_page" value="<?php echo isset($error_page) ? $error_page : 3 ; ?>"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["errorpage_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["sitestatus_title"] ?></b></td>
      <td> <input onChange="documentDirty=true;" type="radio" name="site_status" value="1" <?php echo ($site_status=='1' || !isset($site_status)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["online"]; ?><br />
      <input onChange="documentDirty=true;" type="radio" name="site_status" value="0" <?php echo $site_status=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["offline"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["sitestatus_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["siteunavailable_title"] ?></b></td>
          <td> <textarea name="site_unavailable_message" style="width:100%; height: 120px;"><?php echo ($site_unavailable_message != "") ? $site_unavailable_message : "The site is currently unavailable" ; ?></textarea> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["siteunavailable_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["track_visitors_title"] ?></b></td>
      <td> <input onChange="documentDirty=true;" type="radio" name="track_visitors" value="1" <?php echo ($track_visitors=='1' || !isset($track_visitors)) ? 'checked="checked"' : "" ; ?> onClick='showHide(/logRow/, 1);'>
            <?php echo $_lang["yes"]; ?><br />
      <input onChange="documentDirty=true;" type="radio" name="track_visitors" value="0" <?php echo $track_visitors=='0' ? 'checked="checked"' : "" ; ?> onClick='showHide(/logRow/, 0);'>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["track_visitors_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr id='logRow1' class='row1' style="display: <?php echo $track_visitors==1 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["resolve_hostnames_title"] ?></b></td>
      <td> <input onChange="documentDirty=true;" type="radio" name="resolve_hostnames" value="1" <?php echo ($resolve_hostnames=='1' || !isset($resolve_hostnames)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
      <input onChange="documentDirty=true;" type="radio" name="resolve_hostnames" value="0" <?php echo $resolve_hostnames=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr id='logRow2' class='row1' style="display: <?php echo $track_visitors==1 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["resolve_hostnames_message"] ?></td>
        </tr>
        <tr id='logRow3' style="display: <?php echo $track_visitors==1 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["top_howmany_title"] ?></b></td>
      <td><input onChange="documentDirty=true;" type='text' maxlength='50' size="5" name="top_howmany" value="<?php echo isset($top_howmany) ? $top_howmany : 10 ; ?>"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["top_howmany_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["defaulttemplate_title"] ?></b></td>
          <td>
            <?php
              $sql = "SELECT templatename, id FROM $dbase.".$table_prefix."site_templates ORDER BY templatename";
              $rs = mysql_query($sql);
            ?>
            <select name="default_template" class="inputBox" onChange='documentDirty=true;' style="width:150px">
            <?php
            while ($row = mysql_fetch_assoc($rs)) {
                $selectedtext = $row['id']==$default_template ? "selected='selected'" : "" ;
            ?>
              <option value="<?php echo $row['id']; ?>" <?php echo $selectedtext; ?>><?php echo $row['templatename']; ?></option>
            <?php
            }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["defaulttemplate_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["defaultpublish_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="publish_default" value="1" <?php echo ($publish_default=='1' || !isset($publish_default)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="publish_default" value="0" <?php echo $publish_default=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["defaultpublish_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["defaultsearch_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="search_default" value="1" <?php echo ($search_default=='1' || !isset($search_default)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="search_default" value="0" <?php echo $search_default=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["defaultsearch_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["defaultcache_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="cache_default" value="1" <?php echo ($cache_default=='1' || !isset($cache_default)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="cache_default" value="0" <?php echo $cache_default=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["defaultcache_message"] ?></td>
        </tr>
<!-- START::Added 2008-03-17 by Ralph for Empty Cache default -->
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["defaultsyncsitecheck_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="syncsitecheck_default" value="1" <?php echo ($syncsitecheck_default=='1' || !isset($syncsitecheck_default)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="syncsitecheck_default" value="0" <?php echo $syncsitecheck_default=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["defaultsyncsitecheck_message"] ?></td>
        </tr>
<!-- END::Added 2008-03-17 by Ralph for Empty Cache default -->
<!-- START::Added 2008-03-17 by Ralph for Show in menu default -->
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["defaultshowinmenu_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="showinmenu_default" value="1" <?php echo ($showinmenu_default=='1' || !isset($showinmenu_default)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="showinmenu_default" value="0" <?php echo $showinmenu_default=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["defaultshowinmenu_message"] ?></td>
        </tr>
<!-- END::Added 2008-03-17 by Ralph for show in menu default -->
      </table>
    </div>

    <!-- #2 FURL Settings -->

    <div class="tab-page" id="tabPage2">
      <h2 class="tab"><?php echo $_lang["settings_furls"] ?></h2>
      <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage2" ) );</script>
      <table border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["friendlyurls_title"] ?></b></td>
      <td> <input onChange="documentDirty=true;" type="radio" name="friendly_urls" value="1" <?php echo $friendly_urls=='1' ? 'checked="checked"' : "" ; ?> onClick='showHide(/furlRow/, 1);'>
            <?php echo $_lang["yes"]; ?><br />
      <input onChange="documentDirty=true;" type="radio" name="friendly_urls" value="0" <?php echo ($friendly_urls=='0' || !isset($friendly_urls)) ? 'checked="checked"' : "" ; ?> onClick='showHide(/furlRow/, 0);'>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["friendlyurls_message"] ?></td>
        </tr>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <tr id='furlRow1' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>">
      <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["friendlyurlsprefix_title"] ?></b></td>
          <td><input onChange="documentDirty=true;" type='text' maxlength='50' style="width: 200px;" name="friendly_url_prefix" value="<?php echo isset($friendly_url_prefix) ? $friendly_url_prefix : "p_" ; ?>"></td>
        </tr>
        <tr id='furlRow2' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["friendlyurlsprefix_message"] ?></td>
        </tr>
        <tr id='furlRow3' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr id='furlRow4' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["friendlyurlsuffix_title"] ?></b></td>
          <td><input onChange="documentDirty=true;" type='text' maxlength='50' style="width: 200px;" name="friendly_url_suffix" value="<?php echo isset($friendly_url_suffix) ? $friendly_url_suffix : ".html" ; ?>"></td>
        </tr>
        <tr id='furlRow5' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["friendlyurlsuffix_message"] ?></td>
        </tr>
        <tr id='furlRow6' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr id='furlRow7' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["friendly_alias_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="friendly_alias_urls" value="1" <?php echo $friendly_alias_urls=='1' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="friendly_alias_urls" value="0" <?php echo ($friendly_alias_urls=='0' || !isset($friendly_alias_urls)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr id='furlRow8' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["friendly_alias_message"] ?></td>
        </tr>
      </table>
    </div>

    <!-- #3 User Settings -->

    <div class="tab-page" id="tabPage3">
      <h2 class="tab"><?php echo $_lang["settings_users"] ?></h2>
      <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage3" ) );</script>
      <table border="0" cellspacing="0" cellpadding="3">

        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["udperms_title"] ?></b></td>
      <td> <input onChange="documentDirty=true;" type="radio" name="use_udperms" value="1" <?php echo $use_udperms=='1' ? 'checked="checked"' : "" ; ?> onClick='showHide(/udPerms/, 1);'>
            <?php echo $_lang["yes"]; ?><br />
      <input onChange="documentDirty=true;" type="radio" name="use_udperms" value="0" <?php echo ($use_udperms=='0' || !isset($use_udperms)) ? 'checked="checked"' : "" ; ?> onClick='showHide(/udPerms/, 0);'>
            <?php echo $_lang["no"]; ?> </td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["udperms_message"] ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>

        <tr id='udPermsRow1' class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["uvperms_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="use_uvperms" value="1" <?php echo $use_uvperms=='1' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="use_uvperms" value="0" <?php echo ($use_uvperms=='0' || !isset($use_uvperms)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>

        <tr id='udPermsRow2' class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["uvperms_message"] ?></td>
        </tr>

        <tr class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>

        <tr class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["access_denied_title"] ?></b></td>
          <td> <textarea name="access_denied_message" style="width:100%; height: 120px;"><?php echo isset($access_denied_message) ? $access_denied_message : "" ; ?></textarea> </td>
        </tr>
        <tr class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["access_denied_message"] ?></td>
        </tr>

        <tr class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["udperms_allowroot_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="udperms_allowroot" value="1" <?php echo $udperms_allowroot=='1' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="udperms_allowroot" value="0" <?php echo ($udperms_allowroot=='0' || !isset($udperms_allowroot)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["udperms_allowroot_message"] ?></td>
        </tr>
        <tr id='udPermsRow3' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>

<!-- START::manager logging control added by Ralph in [v1.0] -->
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["mgr_logging_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="use_mgr_logging" value="1" <?php echo ($use_mgr_logging=='1' || !isset($use_mgr_logging)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="use_mgr_logging" value="0" <?php echo $use_mgr_logging=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["mgr_logging_message"] ?></td>
        </tr>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
<!-- END::manager logging control added by Ralph in [v1.0] -->

<!-- START::maximum failed manager login attempts added by Ralph in [v1.0] -->
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["max_attempts_title"] ?></b></td>
          <td ><input onChange="documentDirty=true;" type='text' maxlength='4' style="width: 50px;" name="max_attempts" value="<?php echo isset($max_attempts) ? $max_attempts : "3" ; ?>"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["max_attempts_message"] ?></td>
        </tr>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
<!-- END::maximum failed manager login attempts added by Ralph in [v1.0] -->

        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["captcha_title"] ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="use_captcha" value="1" <?php echo $use_captcha=='1' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="use_captcha" value="0" <?php echo ($use_captcha=='0' || !isset($use_captcha)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["captcha_message"] ?></td>
        </tr>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["captcha_words_title"] ?></b></td>
          <td><input name="captcha_words" style="width:400px" value="<?php echo isset($captcha_words) ? $captcha_words : "Array,BitCode,Chunk,Document,Etomite,Forum,Index,Javascript,Keyword,MySQL,Parser,Query,Random,Snippet,Template,Website"; ?>" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["captcha_words_message"] ?></td>
        </tr>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["signupemail_title"] ?></b></td>
          <td> <textarea name="signupemail_message" style="width:100%; height: 120px;"><?php echo isset($signupemail_message) ? $signupemail_message : "Hi! \n\nHere are your login details for Etomite:\n\nUsername: %s\nPassword: %s\n\nOnce you log into Etomite, you can change your password.\n\nRegards,\nThe Management" ; ?></textarea> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["signupemail_message"] ?></td>
        </tr>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["emailsender_title"] ?></b></td>
          <td ><input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 200px;" name="emailsender" value="<?php echo isset($emailsender) ? $emailsender : "you@yourdomain.com" ; ?>"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["emailsender_message"] ?></td>
        </tr>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["emailsubject_title"] ?></b></td>
          <td ><input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 200px;" name="emailsubject" value="<?php echo isset($emailsubject) ? $emailsubject : "Your Etomite login details" ; ?>"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["emailsubject_message"] ?></td>
        </tr>
      </table>
    </div>

  <!-- #4 Interface & Editor Settings -->

    <div class="tab-page" id="tabPage4">
      <h2 class="tab"><?php echo $_lang["settings_ui"] ?></h2>
      <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage4" ) );</script>
      <table border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["nologentries_title"]; ?></b></td>
          <td><input onChange="documentDirty=true;" type='text' maxlength='50' size="5" name="number_of_logs" value="<?php echo isset($number_of_logs) ? $number_of_logs : 100 ; ?>"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["nologentries_message"]; ?></td>
        </tr>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["nomessages_title"]; ?></b></td>
          <td><input onChange="documentDirty=true;" type='text' maxlength='50' size="5" name="number_of_messages" value="<?php echo isset($number_of_messages) ? $number_of_messages : 30 ; ?>"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["nomessages_message"]; ?></td>
        </tr>
        <!--
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["treetype_message"]; ?></td>
        </tr>
        -->
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["use_preview_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="show_doc_data_preview" id="show_doc_data_preview_yes" value="1" <?php echo ($show_doc_data_preview=='1' || !isset($show_doc_data_preview)) ? 'checked="checked"' : "" ; ?>>
      <label for="show_doc_data_preview_yes"><?php echo $_lang["yes"]; ?></label><br />
      <input onChange="documentDirty=true;" type="radio" name="show_doc_data_preview" id="show_doc_data_preview_no" value="0" <?php echo ($show_doc_data_preview=='0') ? 'checked="checked"' : "" ; ?>>
            <label for="show_doc_data_preview_no"><?php echo $_lang["no"]; ?></label></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["use_preview_message"]; ?></td>
    </tr>
    <tr>
      <td colspan="2"><div class='split'></div></td>
    </tr>
    <tr>
      <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["use_doc_editor_title"]; ?></b></td>
      <td> <input onChange="documentDirty=true;" type="radio" name="use_doc_editor" id="use_doc_editor_yes" value="1" <?php echo ($use_doc_editor=='1' || !isset($use_doc_editor)) ? 'checked="checked"' : "" ; ?> onClick="checkEditor(); showHide(/editorRow/, 1);">
      <label for="use_doc_editor_yes"><?php echo $_lang["yes"]; ?></label><br />
      <input onChange="documentDirty=true;" type="radio" name="use_doc_editor" id="use_doc_editor_no" value="0" <?php echo $use_doc_editor=='0' ? 'checked="checked"' : "" ; ?> onClick="showHide(/editorRow/, 0); showHide(/haRow/, 0); showHide(/haxaRow/, 0); showHide(/imRow/, 0); showHide(/fmRow/, 0); showHide(/xaRow/, 0); showHide(/stylistRow/, 0); showHide(/tmceRow/, 0);">
      <label for="use_doc_editor_no"><?php echo $_lang["no"]; ?></label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td class='comment'><?php echo $_lang["use_doc_editor_message"]; ?></td>
    </tr>
    <tr id='editorRow6'>
      <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr id='editorRow7'>
      <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["which_doc_editor_title"]; ?></b></td>
          <td>
            <select onChange="documentDirty=true;checkEditor();" name="which_editor">
            <?php
            	/******************************************************
            		edit by Petr Vaněk aka krteczek
            	******************************************************/
            	if(file_exists('media/tinymce/'))
            		{
            			echo "\n\t\t\t\t" . '<option value="1"' . (!isset($which_editor) || $which_editor==1 ? "selected='selected'" : "") . '>TinyMCE</option>';
            		}
					if(file_exists('media/editor/'))
						{
							echo "\n\t\t\t\t" . '<option value="2"' . ($which_editor == 2 ? "selected='selected'" : "") . '>HTMLArea</option>';
						}
					if(file_exists('media/fckeditor/'))
						{
							echo "\n\t\t\t\t" . '<option value="3"' . ($which_editor == 3 ? "selected='selected'" : "") . '>FCKeditor</option>';
						}
					if(file_exists('media/xinha/'))
						{
							echo "\n\t\t\t\t" . '<option value="4"' . ($which_editor == 4 ? "selected='selected'" : "") . '>Xinha</option>';
						}
					if(file_exists('media/rte/'))
        				{
							echo "\n\t\t\t\t" . '<option value="5"' . ($which_editor == 5 ? "selected='selected'" : "") . '>RTE</option>';
						}
					if(file_exists('media/texyla/'))
						{
							//added tag for Texyla editor
							echo "\n\t\t\t\t" . '<option value="6"' . ($which_editor == 6 ? "selected='selected'" : "") . '>Texyla</option>';
						}
					?>
            </select>
          </td>
        </tr>
        <tr id='editorRow8'>
          <td>&nbsp;</td>
      <td class='comment'><?php echo $_lang["which_doc_editor_message"]; ?></td>
        </tr>
    <tr id='editorRow9' style="display: <?php echo $use_doc_editor==1||!isset($which_editor) ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>

    <!-- XINHA Skins -->
    <?php if(file_exists('media/xinha/skins/')) { ?>
    <tr id='xaRow6' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["xSkin"]; ?></b></td>
          <td> <select name="xSkin" size="1" class="inputBox" onChange="documentDirty=true;">
          <?php
            $dir = "./media/xinha/skins";
            if($handle = opendir($dir)) {
              while(false !== ($file = readdir($handle))) {
                if($file != "." && $file != "..") {
                  $skins[] = $file;
                }
              }
              closedir($handle);
              sort($skins);
              $selectedtext = ($xSkin=="none") ? 'selected="selected"' : '' ;
              echo '<option value="" '.$selectedtext.'>None</option>';
              foreach($skins as $skin) {
                $selectedtext = ($xSkin==$skin) ? 'selected="selected"' : '' ;
                echo '<option value="'.trim($skin).'" '.$selectedtext.'>'.ucwords(str_replace(array("_","-"), " ", $skin)).'</option>';
              }
            }
            ?>
            </select>
          </td>
        </tr>
    <tr id='xaRow7' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["xSkin_message"]; ?></td>
        </tr>
    <tr id='xaRow8' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <?php } ?>
    <!-- /XINHA Skins -->

    <!-- XINHA Languages -->
    <?php if(file_exists('media/xinha/lang/')) { ?>
    <tr id='xaRow9' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["xLang"]; ?></b></td>
          <td> <select name="xLang" size="1" class="inputBox" onChange="documentDirty=true;">
          <?php
    $langs[] = "en";
    if($xLang=='') $xLang = "en";

            $dir = "./media/xinha/lang";
            if($handle = opendir($dir)) {
              while(false !== ($file = readdir($handle))) {
                if($file != "." && $file != "..") {
                  $langs[] = basename(strtolower($file), ".js");
                }
              }
              closedir($handle);
              sort($langs);
              foreach($langs as $lang) {
                $selectedtext = ($xLang==$lang) ? 'selected="selected"' : '' ;
                echo '<option value="'.trim($lang).'" '.$selectedtext.'>'. $lang.'</option>';
              }
            }
            ?>
            </select>
          </td>
        </tr>
    <tr id='xaRow10' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["xLang_message"]; ?></td>
        </tr>
    <tr id='xaRow11' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <?php } ?>
    <!-- /XINHA Languages -->

    <!-- HTMLArea Strict? -->
    <tr id='haRow0' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==2 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["use_strict_editor_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="strict_editor" id="strict_editor_yes" value="1" <?php echo ($strict_editor=='1' || !isset($strict_editor)) ? 'checked="checked"' : "" ; ?>>
            <label for="strict_editor_yes"><?php echo $_lang["yes"]; ?></label><br />
            <input onChange="documentDirty=true;" type="radio" name="strict_editor" id="strict_editor_no" value="0" <?php echo $strict_editor=='0' ? 'checked="checked"' : "" ; ?>>
            <label for="strict_editor_no"><?php echo $_lang["no"]; ?></label>
          </td>
        </tr>
    <tr id='haRow1' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==2 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["use_strict_editor_message"]; ?></td>
        </tr>
  <tr id='haRow2' style="display: <?php echo $use_doc_editor==1 && $which_editor==2 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <!-- /HTMLArea Strict? -->

    <!-- XINHA and HTMLArea stripBaseURL? -->
    <tr id='haxaRow7' class='row1' style="display: <?php echo $use_doc_editor==1 && ($which_editor==1||$which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader xfloatRight" valign="top"><b><?php echo $_lang["strip_base_href_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="strip_base_href" id="strip_base_href_yes" value="1" <?php echo ($strip_base_href=='1'||!isset($strip_base_href)) ? 'checked="checked"' : "" ; ?>>
            <label for="strip_base_href_yes"><?php echo $_lang["yes"]; ?></label><br />
            <input onChange="documentDirty=true;" type="radio" name="strip_base_href" id="strip_base_href_no" value="0" <?php echo $strip_base_href=='0' ? 'checked="checked"' : "" ; ?>>
            <label for="strip_base_href_no"><?php echo $_lang["no"]; ?></label>
          </td>
        </tr>
    <tr id='haxaRow8' class='row1' style="display: <?php echo $use_doc_editor==1 && ($which_editor==1||$which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["strip_base_href_message"]; ?></td>
        </tr>
    <tr id='haxaRow9' style="display: <?php echo $use_doc_editor==1 && ($which_editor==1||$which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <!-- /XINHA and HTMLArea stripBaseURL? -->

    <!-- HTMLArea Context Menu and Table Operations -->
    <tr id='haRow3' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==2 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["cm_plugin_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="cm_plugin" id="cm_plugin_yes" value="1" <?php echo $cm_plugin=='1' ? 'checked="checked"' : "" ; ?>>
            <label for="cm_plugin_yes"><?php echo $_lang["yes"]; ?></label><br />
            <input onChange="documentDirty=true;" type="radio" name="cm_plugin" id="cm_plugin_no" value="0" <?php echo ($cm_plugin=='0' || !isset($cm_plugin)) ? 'checked="checked"' : "" ; ?>>
            <label for="cm_plugin_no"><?php echo $_lang["no"]; ?></label></td>
        </tr>
    <tr id='haRow4' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==2 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["cm_plugin_message"]; ?></td>
        </tr>
    <tr id='haRow5' style="display: <?php echo $use_doc_editor==1 && $which_editor==2 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <tr id='haRow6' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==2 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["to_plugin_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="to_plugin" id="to_plugin_yes" value="1" <?php echo $to_plugin=='1' ? 'checked="checked"' : "" ; ?>>
            <label for="to_plugin_yes"><?php echo $_lang["yes"]; ?></label><br />
            <input onChange="documentDirty=true;" type="radio" name="to_plugin" id="to_plugin_no" value="0" <?php echo ($to_plugin=='0' || !isset($to_plugin)) ? 'checked="checked"' : "" ; ?>>
            <label for="to_plugin_no"><?php echo $_lang["no"]; ?></label></td>
        </tr>
    <tr id='haRow7' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==2 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["to_plugin_message"]; ?></td>
    </tr><tr id='haRow8' style="display: <?php echo $use_doc_editor==1 && $which_editor==2 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <!-- /HTMLArea Context Menu and Table Operations -->

    <!-- XINHA plugins -->
    <?php if(file_exists('./media/xinha/')) {
    // scan the xinha plugin directory for available plugins
    // skip a few plugins because the either don't work well with etomite,
    // or require extra settings we can't facilitate
    // **NOTE: The skip_plugins list must match the one found in mutate_content.dynamic.action.php.
    //$skip_plugins = array("ImageManager","InsertFile","Stylist","FullPage","FullScreen","FormOperations","Forms","Linker","Template");
    $skip_plugins = array("ImageManager","Stylist","InsertFile","InsertPicture","HorizontalRule","Linker","InsertAnchor","FullScreen","EnterParagraphs");
    $dir = "./media/xinha/plugins";
    $plugins = array();
    if ($handle = opendir($dir)) {
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && !in_array($file,$skip_plugins)) {
          $plugins[] = $file;
        }
      }
      closedir($handle);
    }
    ?>
  <tr id='xaRow0' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
      <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["xp_plugins_title"]; ?></b></td>
      <td>
        <table>
      <?php
      // use columns to display the list of plugins
      $cols = 3;
      $collen = ceil(count($plugins)/$cols);
      sort($plugins);
      $column = array();$column[0] = array();$column[1] = array();$column[2] = array();
      $currcol = 0;
      foreach($plugins as $p) {
        $column[$currcol][] = $p;
      if (count($column[$currcol])==$collen) $currcol++;
      }
      for ($i=0;$i<$collen;$i++) {
      ?>
      <tr>
      <?php for ($j=0;$j<$cols;$j++) { ?>
      <td style="padding-right: 5px;">
      <?php if ($column[$j][$i]!='') { ?>
        <input type="checkbox" name="xp_<?php echo $column[$j][$i]; ?>" id="xp_<?php echo $column[$j][$i]; ?>" value="1" <?php $pname = 'xp_'.$column[$j][$i]; echo $$pname=='1' ? 'checked="checked"' : "" ; ?>>
        <label for="<?php echo $pname; ?>"><?php echo $column[$j][$i]; ?></label>
        <?php } ?>
        </td>
      <?php
      }
      ?>
      </tr><?php
      }
      ?>
      </table>

      <table border="0" cellspacing="0" cellpadding="0" style="padding: 5px 0;">
      <tr>
      <td style="padding-right: 12px;"><input type="button" onClick="javascript:xp_select('all')" value="<?php echo $_lang['select_all']; ?>" /></td>
      <td><input type="button" onClick="javascript:xp_select('none')" value="<?php echo $_lang['clear_selected']; ?>"/></td>
      </tr>
      </table></td>
        </tr>
    <tr id='xaRow1' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["xp_plugins_message"]; ?></td>
        </tr>
  <tr id='xaRow2' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <?php } ?>
    <!-- /XINHA plugins -->

    <!-- Stylist -->
    <tr id='xaRow3' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["xp_stylist_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="xp_Stylist" id="xp_stylist_yes" value="1" <?php echo $xp_Stylist=='1' ? 'checked="checked"' : "" ; ?> onClick="showHide(/stylistRow/, 1);">
            <label for="xp_stylist_yes"><?php echo $_lang["yes"]; ?></label><br />
            <input onChange="documentDirty=true;" type="radio" name="xp_Stylist" id="xp_stylist_no" value="0" <?php echo ($xp_Stylist=='0' || !isset($xp_Stylist)) ? 'checked="checked"' : "" ; ?> onClick="showHide(/stylistRow/, 0);">
            <label for="xp_stylist_no"><?php echo $_lang["no"]; ?></label> </td>
        </tr>
    <tr id='xaRow4' class='row1' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["xp_stylist_message"]; ?></td>
        </tr>
  <tr id='stylistRow0' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 && $xp_Stylist==1 ? $displayStyle : 'none' ; ?>">
            <td colspan="2"><div class='split'></div></td>
          </tr>
      <tr id='stylistRow1' class='row3' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 && $xp_Stylist==1 ? $displayStyle : 'none' ; ?>">
            <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["xp_stylist_path_title"]; ?></b></td>
            <td>
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="xp_Stylist_path" value="<?php echo isset($xp_Stylist_path) ? $xp_Stylist_path : getBaseUrl()."assets/site/example.css"; ?>">
            </td>
          </tr>
      <tr id='stylistRow2' class='row3' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 && $xp_Stylist==1 ? $displayStyle : 'none' ; ?>">
            <td>&nbsp;</td>
            <td class='comment'><?php echo $_lang["xp_stylist_path_message"]; ?></td>
          </tr>
    <tr id='xaRow5' style="display: <?php echo $use_doc_editor==1 && $which_editor==4 ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <!-- /Stylist -->


    <!-- ImageManager -->
    <tr id='haxaRow1' class='row1' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["im_plugin_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="im_plugin" id="im_plugin_yes" value="1" <?php echo ($im_plugin=='1' || !isset($im_plugin)) ? 'checked="checked"' : "" ; ?> onClick="showHide(/imRow/, 1);">
            <label for="im_plugin_yes"> <?php echo $_lang["yes"]; ?></label><br />
            <input onChange="documentDirty=true;" type="radio" name="im_plugin" id="im_plugin_no" value="0" <?php echo $im_plugin=='0' ? 'checked="checked"' : "" ; ?> onClick="showHide(/imRow/, 0);">
            <label for="im_plugin_no"><?php echo $_lang["no"]; ?></label></td>
        </tr>
    <tr id='haxaRow2' class='row1' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["im_plugin_message"]; ?></td>
        </tr>
    <tr id='haxaRow3' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <tr id='imRow1' class='row3' style="display: <?php echo $im_plugin==1 && $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["im_plugin_base_dir_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="im_plugin_base_dir" value="<?php echo isset($im_plugin_base_dir) ? $im_plugin_base_dir : getImageBaseDir() ; ?>">
            <br />
          </td>
        </tr>
    <tr id='imRow2' class='row3' style="display: <?php echo $im_plugin==1 && $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["im_plugin_base_dir_message"]; ?></td>
        </tr>
    <tr id='imRow3' style="display: <?php echo $im_plugin==1 && $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <tr id='imRow4' class='row3' style="display: <?php echo $im_plugin==1 && $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["im_plugin_base_url_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="im_plugin_base_url" value="<?php echo isset($im_plugin_base_url) ? $im_plugin_base_url : getImageBaseUrl() ; ?>">
            <br />
          </td>
        </tr>
    <tr id='imRow5' class='row3' style="display: <?php echo $im_plugin==1 && $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["im_plugin_base_url_message"]; ?></td>
        </tr>
    <tr id='imRow6' style="display: <?php echo $im_plugin==1 && $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <!-- /ImageManager -->

    <!-- FileManager -->
  <tr id='haxaRow4' class='row1' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
            <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["fm_plugin_title"]; ?></b></td>
            <td> <input onChange="documentDirty=true;" type="radio" name="fm_plugin" id="fm_plugin_yes" value="1" <?php echo ($fm_plugin=='1' || !isset($fm_plugin)) ? 'checked="checked"' : "" ; ?>  onClick="showHide(/fmRow/, 1);">
              <label for="fm_plugin_yes"><?php echo $_lang["yes"]; ?></label><br />
              <input onChange="documentDirty=true;" type="radio" name="fm_plugin" id="fm_plugin_no" value="0" <?php echo $fm_plugin=='0' ? 'checked="checked"' : "" ; ?>  onClick="showHide(/fmRow/, 0);">
              <label for="fm_plugin_no"><?php echo $_lang["no"]; ?></label></td>
          </tr>
      <tr id='haxaRow5' class='row1' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
            <td>&nbsp;</td>
            <td class='comment'><?php echo $_lang["fm_plugin_message"]; ?></td>
          </tr>
    <tr id='haxaRow6' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) ? $displayStyle : 'none' ; ?>">
            <td colspan="2"><div class='split'></div></td>
          </tr>

      <tr id='fmRow1' class='row3' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["fm_plugin_base_url_title"]; ?></b></td>
            <td>
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="fm_plugin_base_url" value="<?php echo isset($fm_plugin_base_url) ? $fm_plugin_base_url : getFileBaseUrl() ; ?>">
              <br /> </td>
          </tr>
      <tr id='fmRow2' class='row3' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td>&nbsp;</td>
            <td class='comment'><?php echo $_lang["fm_plugin_base_url_message"]; ?></td>
          </tr>
    <tr id='fmRow3' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td colspan="2"><div class='split'></div></td>
          </tr>
      <tr id='fmRow4' class='row3' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["fm_plugin_document_url_title"]; ?></b></td>
            <td>
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="fm_plugin_document_url" value="<?php echo isset($fm_plugin_document_url) ? $fm_plugin_document_url : getFileDocumentUrl() ; ?>">
              <br /> </td>
          </tr>
      <tr id='fmRow5' class='row3' style="display: <?php echo $use_doc_editor==1 && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td>&nbsp;</td>
            <td class='comment'><?php echo $_lang["fm_plugin_document_url_message"]; ?></td>
          </tr>
  <tr id='fmRow6' style="display: <?php echo $use_doc_editor==1  && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td colspan="2"><div class='split'></div></td>
          </tr>
      <tr id='fmRow7' class='row3' style="display: <?php echo $use_doc_editor==1  && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["fm_path_title"]; ?></b></td>
            <td>
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="fm_path" value="<?php echo isset($fm_path) ? $fm_path : getEtomiteRoot()."/assets/documents"; ?>">
              <br /> </td>
          </tr>
      <tr id='fmRow8' class='row3' style="display: <?php echo $use_doc_editor==1  && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td>&nbsp;</td>
            <td class='comment'><?php echo $_lang["fm_path_message"]; ?></td>
          </tr>
    <tr id='fmRow9' style="display: <?php echo $use_doc_editor==1  && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td colspan="2"><div class='split'></div></td>
          </tr>
      <tr id='fmRow10' class='row3' style="display: <?php echo $use_doc_editor==1  && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["fm_exclude_title"]; ?></b></td>
            <td>
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="fm_exclude" value="<?php echo isset($fm_exclude) ? $fm_exclude : ".,..,cgi-bin,aspnet_client,index.php,index.html"; ?>">
              <br /> </td>
          </tr>
      <tr id='fmRow11' class='row3' style="display: <?php echo $use_doc_editor==1  && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td>&nbsp;</td>
            <td class='comment'><?php echo $_lang["fm_exclude_message"]; ?></td>
          </tr>
      <tr id='fmRow12' style="display: <?php echo $use_doc_editor==1  && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td colspan="2"><div class='split'></div></td>
          </tr>
      <tr  id='fmRow13' class='row3'style="display: <?php echo $use_doc_editor==1  && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["fm_uploadable_files_title"]; ?></b></td>
            <td>
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="fm_upload_files" value="<?php echo isset($upload_files) ? $upload_files : "jpg,gif,png,bmp,ico,txt,html,htm,xml,js,css,zip,gz,rar,tgz,tar,mp3,mp4,wav,au,wmv,avi,mpg,mpeg,mov,pdf,doc,xls,ppt,swf,dcr" ; ?>">
            </td>
          </tr>
      <tr id='fmRow14' class='row3' style="display: <?php echo $use_doc_editor==1  && ($which_editor==2||$which_editor==4) && $fm_plugin==1 ? $displayStyle : 'none' ; ?>">
            <td>&nbsp;</td>
            <td class='comment'><?php echo $_lang["fm_uploadable_files_message"]; ?></td>
          </tr>
      <!-- /FileManager -->

  <!-- TinyMCE -->
    <tr id='tmceRow0' style="display: <?php echo ($use_doc_editor==1 && $which_editor==1)||!isset($which_editor) ? $displayStyle : 'none' ; ?>">
      <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["tiny_css_path_title"]; ?></b></td>
      <td>
      <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="tiny_css_path" value="<?php echo isset($tiny_css_path) ? $tiny_css_path : "" ; ?>">
      </td>
    </tr>
    <tr id='tmceRow1' style="display: <?php echo ($use_doc_editor==1 && $which_editor==1)||!isset($which_editor) ? $displayStyle : 'none' ; ?>">
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["tiny_css_path_message"]; ?></td>
    </tr>
    <tr id='tmceRow2' style="display: <?php echo ($use_doc_editor==1 && $which_editor==1)||!isset($which_editor) ? $displayStyle : 'none' ; ?>">
          <td colspan="2"><div class='split'></div></td>
        </tr>
    <tr id='tmceRow3' style="display: <?php echo ($use_doc_editor==1 && $which_editor==1)||!isset($which_editor) ? $displayStyle : 'none' ; ?>">
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["tiny_css_selectors_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="tiny_css_selectors" value="<?php echo isset($tiny_css_selectors) ? $tiny_css_selectors : "" ; ?>">
          </td>
        </tr>
    <tr id='tmceRow4' style="display: <?php echo ($use_doc_editor==1 && $which_editor==1)||!isset($which_editor) ? $displayStyle : 'none' ; ?>">
      <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["tiny_css_selectors_message"]; ?></td>
        </tr>
  <!-- /TinyMCE -->

  <!-- The Code Editor - editarea -->
  <?php if(!file_exists('media/edit_area/')){
    $use_code_editor=0;
    $code_highlight=0;
    $ea_default_display='none';
    }else{
    $ea_default_display='table-row';
    }
  ?>
    <tr style="display:<?php echo $ea_default_display;?>">
      <td colspan="2"><div class='split'></div></td>
    </tr>
    <tr id="eaRow" style="display:<?php echo $ea_default_display;?>">
      <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["use_code_editor_title"]; ?></b></td>
      <td>
      <input onChange="documentDirty=true;" type="radio" name="use_code_editor" id="use_code_editor_yes" value="1" <?php echo ($use_code_editor=='1') ? 'checked="checked"' : "" ; ?> onClick="showHide(/codeOptions/, 1);showHide(/codeOptionsRow/, 1);">
        <label for="use_code_editor_yes"><?php echo $_lang["yes"]; ?></label><br />
      <input onChange="documentDirty=true;" type="radio" name="use_code_editor" id="use_code_editor_no" value="0" <?php echo ($use_code_editor=='0' || $use_code_editor=='') ? 'checked="checked"' : "" ; ?> onClick="showHide(/codeOptions/,0);showHide(/codeOptionsRow/, 0);">
        <label for="use_code_editor_no"><?php echo $_lang["no"]; ?></label>
      </td>
    </tr>
    <tr id="eaRow2" style="display:<?php echo $ea_default_display;?>">
      <td>&nbsp;</td>
      <td class='comment'><?php echo $_lang["use_code_editor_message"]; ?></td>
    </tr>

    <tr style="display: <?php echo $use_code_editor==1 ? $displayStyle : 'none' ; ?>">
      <td colspan="2"><div class='split'></div></td>
    </tr>

    <tr id="codeOptions" style="display: <?php echo $use_code_editor==1 ? $displayStyle : 'none' ; ?>">
      <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["code_highlight_title"]; ?></b></td>
      <td>
      <input onChange="documentDirty=true;" type="radio" name="code_highlight" id="code_highlight_yes" value="1" <?php echo ($code_highlight=='1') ? 'checked="checked"' : "" ; ?>>
        <label for="code_highlight_yes"><?php echo $_lang["yes"]; ?></label><br />
      <input onChange="documentDirty=true;" type="radio" name="code_highlight" id="code_highlight_no" value="0" <?php echo ($code_highlight=='0' || $code_highlight=='') ? 'checked=""' : "" ; ?>>
        <label for="code_highlight_no"><?php echo $_lang["no"]; ?></label>
      </td>
    </tr>
    <tr id="codeOptionsRow" style="display: <?php echo $use_code_editor==1 ? $displayStyle : 'none' ; ?>">
      <td>&nbsp;</td>
      <td class="comment" valign="top"><?php echo $_lang["code_highlight_message"]; ?></td>
    </tr>

    <tr>
      <td colspan="2"><div class='split'></div></td>
    </tr>
  <!-- /The Code Editor - editarea -->

<!-- START: Added 2008-04-21 [v1.0] by Ralph for dumpSQL -->
      <tr>
        <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["dumpSQL_title"]; ?></b></td>
        <td> <input onChange="documentDirty=true;" type="radio" name="dumpSQL" value="1" <?php echo $dumpSQL=='1' ? 'checked="checked"' : "" ; ?>>
          <?php echo $_lang["yes"]; ?><br />
          <input onChange="documentDirty=true;" type="radio" name="dumpSQL" value="0" <?php echo ($dumpSQL=='0' || !isset($dumpSQL)) ? 'checked="checked"' : "" ; ?>>
          <?php echo $_lang["no"]; ?> </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class='comment'><?php echo $_lang["dumpSQL_message"]; ?></td>
      </tr>

      <tr>
        <td colspan="2"><div class='split'></div></td>
      </tr>
<!-- END: Added 2008-04-21 [v1.0] by Ralph for dumpSQL -->

<!-- START: Added 2008-04-21 [v1.0] by Ralph for dumpSnippets -->
      <tr>
        <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["dumpSnippets_title"]; ?></b></td>
        <td> <input onChange="documentDirty=true;" type="radio" name="dumpSnippets" value="1" <?php echo $dumpSnippets=='1' ? 'checked="checked"' : "" ; ?>>
          <?php echo $_lang["yes"]; ?><br />
          <input onChange="documentDirty=true;" type="radio" name="dumpSnippets" value="0" <?php echo ($dumpSnippets=='0' || !isset($dumpSnippets)) ? 'checked="checked"' : "" ; ?>>
          <?php echo $_lang["no"]; ?> </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class='comment'><?php echo $_lang["dumpSnippets_message"]; ?></td>
      </tr>
<!-- END: Added 2008-04-21 [v1.0] by Ralph for dumpSnippets -->

    </table>
  </div>

  <!-- #5 Miscellaneous Settings -->

  <div class="tab-page" id="tabPage5">
    <h2 class="tab"><?php echo $_lang["settings_misc"] ?></h2>
    <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage5" ) );</script>
    <table border="0" cellspacing="0" cellpadding="3">

<!-- START: Added 2008-04-19 by Ralph for allow_embedded_php -->
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["allow_embedded_php_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="allow_embedded_php" value="1" <?php echo $allow_embedded_php=='1' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="allow_embedded_php" value="0" <?php echo ($allow_embedded_php=='0' || !isset($allow_embedded_php)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["allow_embedded_php_message"]; ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
<!-- END: Added 2008-04-19 by Ralph for allow_embedded_php -->

        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["settings_strip_image_paths_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="strip_image_paths" value="1" <?php echo $strip_image_paths=='1' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="strip_image_paths" value="0" <?php echo ($strip_image_paths=='0' || !isset($strip_image_paths)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["settings_strip_image_paths_message"]; ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["filemanager_path_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="filemanager_path" value="<?php echo isset($filemanager_path) ? $filemanager_path : getEtomiteRoot() ; ?>">
            <br />
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["filemanager_path_message"]; ?></td>
        </tr>

        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["uploadable_files_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="upload_files" value="<?php echo isset($upload_files) ?  str_replace(" ","",$upload_files) :  str_replace(" ","","jpg,gif,png,ico,txt,php,html,htm,xml,js,css,cache,zip,gz,rar,z,tgz,tar,htaccess,bmp,mp3,wav,au,wmv,avi,mpg,mpeg,pdf,psd") ; ?>">
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["uploadable_files_message"]; ?></td>
        </tr>

<!-- START: Added 2008-03-17 by Ralph for inlineviewable files -->
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["inlineviewable_files_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="inlineview_files" value="<?php echo isset($inlineview_files) ?  str_replace(" ","",$inlineview_files) :  str_replace(" ","","txt,php,html,htm,xml,js,css") ; ?>">
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["inlineviewable_files_message"]; ?></td>
        </tr>
<!-- END: Added 2008-03-17 by Ralph for inlineviewable files -->

<!-- START: Added 2008-03-17 by Ralph for viewable files -->
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["viewable_files_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="view_files" value="<?php echo isset($view_files) ?  str_replace(" ","",$view_files) :  str_replace(" ","","jpg,gif,png,ico") ; ?>">
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["viewable_files_message"]; ?></td>
        </tr>
<!-- END: Added 2008-03-17 by Ralph for viewable files -->

<!-- START: Added 2008-03-17 by Ralph for editable files -->
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["editable_files_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="edit_files" value="<?php echo isset($edit_files) ? str_replace(" ","",$edit_files) :  str_replace(" ","","txt,php,html,htm,xml,js,css") ; ?>">
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["editable_files_message"]; ?></td>
        </tr>
<!-- END: Added 2008-03-17 by Ralph for editable files -->

<!-- START: Added 2008-03-17 by Ralph for exclude paths -->
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["exclude_paths_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="exclude_paths" value="<?php echo isset($exclude_paths) ? str_replace(" ","",$exclude_paths) : str_replace(" ","","., ..,cgi-bin,manager") ; ?>">
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["exclude_paths_message"]; ?></td>
        </tr>
<!-- END: Added 2008-03-17 by Ralph for exclude paths -->

<!-- START: Added 2008-03-17 by Ralph for Max upload size -->
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["maxuploadsize_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type='text' maxlength='10' style="width: 100px;" name="maxuploadsize" value="<?php echo isset($maxuploadsize) ? $maxuploadsize : "" ; ?>">
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["maxuploadsize_message"]; ?></td>
        </tr>
<!-- END: Added 2008-03-17 by Ralph for Max upload size -->

<!-- START: Added 2008-04-19 by Ralph for useNotice -->
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["useNotice_title"]; ?></b></td>
          <td> <input onChange="documentDirty=true;" type="radio" name="useNotice" value="1" <?php echo ($useNotice=='1' || !isset($useNotice)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["yes"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="useNotice" value="0" <?php echo $useNotice=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["no"]; ?> </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["useNotice_message"]; ?></td>
        </tr>
<!-- END: Added 2008-04-19 by Ralph for useNotice -->

<?php if($_SESSION['browser']=='ie') { ?>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
        <tr>
          <td nowrap class="menuHeader" valign="top"><b><?php echo $_lang["layout_title"]; ?></b></td>
          <td>
            <input onChange="documentDirty=true;" type="radio" name="manager_layout" value="1" <?php echo ($manager_layout=='1' || !isset($manager_layout)) ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["layout_settings_1"]; ?><br />
            <input onChange="documentDirty=true;" type="radio" name="manager_layout" value="0" <?php echo $manager_layout=='0' ? 'checked="checked"' : "" ; ?>>
            <?php echo $_lang["layout_settings_2"]; ?><br />
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class='comment'><?php echo $_lang["layout_message"]; ?></td>
        </tr>
        <tr>
          <td colspan="2"><div class='split'></div></td>
        </tr>
<?php } ?>
      </table>
<?php if($_SESSION['browser']!='ie') { ?>
      <input onChange="documentDirty=true;" type="hidden" name="manager_layout" value="1">
<?php } ?>
    </div>
  </div>
</div>
</form>
