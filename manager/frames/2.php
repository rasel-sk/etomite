<?php
// 2.php
// Main Navigation Menu
// Last Modified 2008-03-18 by Ralph

if(IN_ETOMITE_SYSTEM!="true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><!--<br />--><!--<br />-->Please use the Etomite Manager instead of accessing this file directly.");
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>nav</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>">
<link rel="stylesheet" type="text/css" href="media/style/style.css" />

<script type="text/javascript">
function openCredits() {
  parent.main.document.location.href = "http://www.etomite.com/credits.html";
  xwwd = window.setTimeout('stopIt()', 2000);
}

function stopIt() {
  top.scripter.stopWork();
}
</script>

</head>
<body class="menuContainer">
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="2%">&nbsp;</td>
    <td width="47%" valign="top">
      <div class="menuHeader"><?php echo $_lang["site"]; ?></div>
      <a onclick="this.blur();" href="index.php?a=2" target="main">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["home"]; ?>
      </a>
      <a onclick="this.blur();" href="../?z=manprev" target="_blank">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["launch_site"]; ?>
      </a>
      <a onclick="this.blur();" href="index.php?a=26" target="main">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["refresh_site"]; ?>
      </a>
      <a onclick="this.blur();" href="index.php?a=70" target="main">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["site_schedule"]; ?>
      </a>
      <a onclick="this.blur();" href="index.php?a=68" target="main">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["visitor_stats"]; ?>
      </a>
      <a onclick="this.blur();" href="index.php?a=69" target="main">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["visitor_stats_online"]; ?>
      </a>

<?php  if($_SESSION['permissions']['new_document']==1) { ?>
      <div class="menuHeader">
        <?php echo $_lang["content"]; ?>
      </div>
      <a onclick="this.blur();" href="index.php?a=4" target="main">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["add_document"]; ?>
      </a>
      <a onclick="this.blur();" href="index.php?a=72" target="main">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["add_weblink"]; ?>
      </a>
<?php } ?>

<?php if($_SESSION['permissions']['messages']==1 || $_SESSION['permissions']['change_password']==1) { ?>
      <div class="menuHeader">
        <?php echo $_lang["my_etomite"]; ?>
      </div>

<?php   if($_SESSION['permissions']['messages']==1) { ?>
        <a onclick="this.blur();" href="index.php?a=10" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["messages"]; ?>
          <span id="msgCounter">(? / ? )</span>
        </a>
<?php   }
    if($_SESSION['permissions']['change_password']==1) { ?>
        <a onclick="this.blur();" href="index.php?a=28" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["change_password"]; ?>
        </a>
<?php   } ?>
<?php } ?>

<?php if($_SESSION['permissions']['new_user']==1 || $_SESSION['permissions']['edit_user']==1 || $_SESSION['permissions']['new_role']==1 || $_SESSION['permissions']['edit_role']==1 || $_SESSION['permissions']['access_permissions']==1) { ?>
      <div class="menuHeader">
        <?php echo $_lang["users"]; ?>
      </div>

<?php   if($_SESSION['permissions']['edit_user']==1) { ?>
        <a onclick="this.blur();" href="index.php?a=75" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["user_management_title"]; ?>
        </a>
<?php   } ?>

<?php if($_SESSION['permissions']['access_permissions']==1) { ?>
        <a onclick="this.blur();" href="index.php?a=40" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["access_permissions"]; ?>
        </a>
<?php   } ?>
<?php } ?>

    </td>
    <td width="2%">&nbsp;</td>
    <td width="47%" valign="top">

<?php if($_SESSION['permissions']['new_template']==1 || $_SESSION['permissions']['edit_template']==1 || $_SESSION['permissions']['new_snippet']==1 || $_SESSION['permissions']['edit_snippet']==1) { ?>
      <div class="menuHeader">
        <?php echo $_lang["resources"]; ?>
      </div>
        <a onclick="this.blur();" href="index.php?a=76" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["resource_management"]; ?>
        </a>
<?php } ?>

<?php if($_SESSION['permissions']['settings']==1 || $_SESSION['permissions']['edit_parser']==1 || $_SESSION['permissions']['logs']==1 || $_SESSION['permissions']['file_manager']==1 || $_SESSION['permissions']['export_html']==1) { ?>
      <div class="menuHeader">
        <?php echo $_lang["administration"]; ?>
      </div>

<?php   if($_SESSION['permissions']['settings']==1) { ?>
        <a onclick="this.blur();" href="index.php?a=17" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["edit_settings"]; ?>
        </a>
        <a onclick="this.blur();" href="index.php?a=53" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["view_sysinfo"]; ?>
        </a>
        <a onclick="this.blur();" href="javascript:top.scripter.removeLocks();">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["remove_locks"]; ?>
        </a>
<?php   } ?>

<?php   if($_SESSION['permissions']['logs']==1) { ?>
        <a onclick="this.blur();" href="index.php?a=13" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["view_logging"]; ?>
        </a>
<?php   } ?>

<?php   if($_SESSION['permissions']['file_manager']==1) { ?>
        <a onclick="this.blur();" href="index.php?a=31" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["manage_files"]; ?>
        </a>
<?php   } ?>
<?php   if($_SESSION['permissions']['export_html']==1) { ?>
        <a onclick="this.blur();" href="index.php?a=83" target="main">
          <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["export_site"]; ?>
        </a>
<?php   } ?>

<?php } ?>

<?php if($_SESSION['permissions']['help']==1) { ?>
      <div class="menuHeader">
        <?php echo $_lang["help"]; ?>
      </div>
      <a onclick="this.blur();" href="javascript:openCredits();">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["credits"]; ?>
      </a>
      <a onclick="this.blur();" href="index.php?a=9" target="main">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["help"]; ?>
      </a>
      <a onclick="this.blur();" href="index.php?a=59" target="main">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["about"]; ?>
      </a>
<?php } ?>
      <br />
      <a onclick="this.blur();" href="index.php?a=8" target="_top">
        <img src='media/images/misc/arrow.gif' alt="Arrow!" /><?php echo $_lang["logout"]; ?>
      </a>
    </td>
    <td width="2%">&nbsp;</td>
  </tr>

</table>

</body>
</html>
