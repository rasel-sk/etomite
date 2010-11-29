<?php
// mutate_user.dynamic.action.php
// Create/Edit User Module
// Modified in 0.6.1 by Ralph
// Modified 2008-05-10 [v1.1] by Ralph Dahlgren
// - HTML markup improvements

if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);
if($_SESSION['permissions']['edit_user']!=1 && $_REQUEST['a']==12) {  $e->setError(3);
  $e->dumpError();
}
if($_SESSION['permissions']['new_user']!=1 && $_REQUEST['a']==11) {  $e->setError(3);
  $e->dumpError();
}

$user = $_REQUEST['id'];
if($user=="") $user=0;
// check to see the snippet editor isn't locked
$sql = "SELECT internalKey, username FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.action=12 AND $dbase.".$table_prefix."active_users.id=$user";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit>1) {
  for ($i=0;$i<$limit;$i++) {
    $lock = mysql_fetch_assoc($rs);
    if($lock['internalKey']!=$_SESSION['internalKey']) {
      $msg = $lock['username']." is currently editing this user. Please wait until the other user has finished and try again.";
      $e->setError(5, $msg);
      $e->dumpError();
    }
  }
}
// end check for lock

if($_REQUEST['a']==12) {
  $sql = "SELECT * FROM $dbase.".$table_prefix."user_attributes WHERE $dbase.".$table_prefix."user_attributes.internalKey = ".$user.";";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit>1) {
    echo "More than one user returned!<p>";
    exit;
  }
  if($limit<1) {
    echo "No user returned!<p>";
    exit;
  }
  $userdata = mysql_fetch_assoc($rs);

  if($_SESSION['role']!=1 && $userdata['role'] == 1) {
    $e->setError(3);
    $e->dumpError();
  }

  $sql = "SELECT * FROM $dbase.".$table_prefix."manager_users WHERE $dbase.".$table_prefix."manager_users.id = ".$user.";";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit>1) {
    echo "More than one user returned while getting username!<p>";
    exit;
  }
  if($limit<1) {
    echo "No user returned while getting username!<p>";
    exit;
  }
  $usernamedata = mysql_fetch_assoc($rs);
  $_SESSION['itemname']=$usernamedata['username'];
} else {
  $userdata = 0;
  $usernamedata = 0;
  $_SESSION['itemname']="New user";
}

?>
<script type="text/javascript">

function changestate(element) {
  documentDirty=true;
  currval = eval(element).value;
  if(currval==1) {
    eval(element).value=0;
  } else {
    eval(element).value=1;
  }
}

function changePasswordState(element) {
  currval = eval(element).value;
  if(currval==1) {
    document.getElementById("passwordBlock").style.display="block";
  } else {
    document.getElementById("passwordBlock").style.display="none";
  }
}

function changeblockstate(element, checkelement) {
  currval = eval(element).value;
  if(currval==1) {
    if(confirm("<?php echo $_lang['confirm_unblock']; ?>")==true){
      document.userform.blocked.value=0;
      document.userform.blockeduntil.value=0;
      document.userform.failedlogincount.value=0;
      document.getElementById('blocked').innerHTML="<span><?php echo $_lang['unblock_message']; ?><\/span>";
      document.getElementById('blocked').classname="warning";
      eval(element).value=0;
    } else {
      eval(checkelement).checked=true;
    }
  } else {
    if(confirm("<?php echo $_lang['confirm_block']; ?>")==true){
      document.userform.blocked.value=1;
      document.getElementById('blocked').innerHTML="<span><?php echo $_lang['block_message']; ?><\/span>";
      document.getElementById('blocked').classname="warning";
      eval(element).value=1;
    } else {
      eval(checkelement).checked=false;
    }
  }
}

function resetFailed() {
  document.userform.failedlogincount.value=0;
  document.getElementById("failed").innerHTML="0";
}

function deleteuser() {
<?php if($_GET['id']==$_SESSION['internalKey']) { ?>
  alert("<?php echo $_lang['alert_delete_self']; ?>");
<?php } else { ?>
  if(confirm("<?php echo $_lang['confirm_delete_user']; ?>")==true) {
    document.location.href="index.php?id=" + document.userform.id.value + "&a=33";
  }
<?php } ?>
}
</script>

<div class="subTitle">
  <span class="floatLeft">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <a href="#" class="doSomethingButton" onClick="documentDirty=false; document.userform.submit();"><?php echo $_lang['save']; ?></a>
    <?php if($_GET['a']=='12') { ?>
      <a href="#" class="doSomethingButton" onClick="deleteuser();"><?php echo $_lang['delete']; ?></a>
    <?php } ?>
    <a href="index.php?a=75" class="doSomethingButton"><?php echo $_lang['cancel']; ?></a>
  </span>
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['user_title']; ?>
  </span>
</div>

<script type="text/javascript">
function checkIM() {
  im_on = document.settings.im_plugin[0].checked; // check if im_plugin is on
  if(im_on==true) {
    showHide(/imRow/, 1);
  }
}

function showHide(what, onoff){

  var all = document.getElementsByTagName( "*" );
  var l = all.length;
  var buttonRe = what;
  var id, el, stylevar;

  if(onoff==1) {
    stylevar = "<?php echo $displayStyle; ?>";
  } else {
    stylevar = "none";
  }

  for ( var i = 0; i < l; i++ ) {
    el = all[i]
    id = el.id;
    if ( id == "" ) continue;
    if (buttonRe.test(id)) {
      el.style.display = stylevar;
    }
  }
}
</script>

<!--<link type="text/css" rel="stylesheet" href="media/style/tabs.css" />-->
<script type="text/javascript" src="media/script/tabpane.js"></script>

<div class="tab-pane" id="usersPane">

  <script type="text/javascript">
    tpSettings = new WebFXTabPane(document.getElementById("usersPane"));
  </script>

  <!-- User Information Panel -->
  <div class="tab-page" id="tabPage1">
    <div class="tab">
      <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['user_title']; ?>
    </div>
    <script type="text/javascript">
      tpSettings.addTabPage(document.getElementById("tabPage1"));
    </script>

    <div class="sectionBody">
    <form action="index.php?a=32" method="post" name="userform" id="userform">
    <input type="hidden" name="mode" value="<?php echo $_GET['a'] ?>">
    <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
    <input type="hidden" name="blockeduntil" value="<?php echo $userdata['blockeduntil'] ?>">
    <table border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td colspan="3">
          <span id="blocked" class="warning"><?php if($userdata['blocked']==1 || ($userdata['blockeduntil']>time() && $userdata['blockeduntil']!=0) || $userdata['failedlogins']>3) { ?><span class='warning'><?php echo $_lang['user_is_blocked']; ?></span><?php } ?></span>
          <br />
        </td>
      </tr>
      <tr>
        <td><?php echo $_lang['username']; ?>:</td>
        <td>&nbsp;</td>
        <td width="400"><input type="text" name="newusername" class="inputBox" style="width:150px" value="<?php echo $usernamedata['username']; ?>" onchange='documentDirty=true;'></td>
      </tr>
      <tr>
        <td valign="top"><?php echo $_GET['a']=='11' ? $_lang['password'].":" : $_lang['change_password_new'].":" ; ?></td>
        <td>&nbsp;</td>
        <td><input name="newpasswordcheck" type="checkbox" onClick="changestate(document.userform.newpassword);changePasswordState(document.userform.newpassword);"<?php echo $_REQUEST['a']=="11" ? " checked disabled": "" ; ?>><input type="hidden" name="newpassword" value="<?php echo $_REQUEST['a']=="11" ? 1 : 0 ; ?>" onchange='documentDirty=true;'><br />
        <div style="display:<?php echo $_REQUEST['a']=="11" ? "block": "none" ; ?>" id="passwordBlock">
        <fieldset style="width:400px">
        <legend><span class='warning'><?php echo $_lang['password_gen_method']; ?></span></legend>
        <input type="radio" name="passwordgenmethod" value="g" <?php echo $_GET['id']==$_SESSION['internalKey'] ? " checked disabled" : "checked" ; ?>><?php echo $_lang['password_gen_gen']; ?><br />
        <input type="radio" name="passwordgenmethod" value="spec" <?php echo $_GET['id']==$_SESSION['internalKey'] ? "disabled" : "" ; ?>><?php echo $_lang['password_gen_specify']; ?> <input type=text name="specifiedpassword" onchange='documentDirty=true;'><br />
        <small><?php echo $_lang['password_gen_length']; ?></small>
        </fieldset>
        <br />
        <fieldset style="width:400px">
        <legend><span class='warning'><?php echo $_lang['password_gen_method']; ?></span></legend>
        <input type="radio" name="passwordnotifymethod" value="e" <?php echo $_GET['id']==$_SESSION['internalKey'] ? "checked disabled" : "" ; ?>><?php echo $_lang['password_method_email']; ?><br />
        <input type="radio" name="passwordnotifymethod" value="s" <?php echo $_GET['id']==$_SESSION['internalKey'] ? "disabled" : "checked" ; ?>><?php echo $_lang['password_method_screen']; ?>
        </fieldset>
        </div>
      </td>
      </tr>
      <tr>
        <td><?php echo $_lang['user_full_name']; ?>:</td>
        <td>&nbsp;</td>
        <td><input type="text" name="fullname" class="inputBox" style="width:150px" value="<?php echo $userdata['fullname']; ?>" onchange='documentDirty=true;'></td>
      </tr>
      <tr>
        <td><?php echo $_lang['user_email']; ?>:</td>
        <td>&nbsp;</td>
        <td><input type="text" name="email" class="inputBox" style="width:150px" value="<?php echo $userdata['email']; ?>" onchange='documentDirty=true;'></td>
      </tr>
      <tr>
        <td><?php echo $_lang['user_phone']; ?>:</td>
        <td>&nbsp;</td>
        <td><input type="text" name="phone" class="inputBox" style="width:150px" value="<?php echo $userdata['phone']; ?>" onchange='documentDirty=true;'></td>
      </tr>
      <tr>
        <td><?php echo $_lang['user_mobile']; ?>:</td>
        <td>&nbsp;</td>
        <td><input type="text" name="mobilephone" class="inputBox" style="width:150px" value="<?php echo $userdata['mobilephone']; ?>" onchange='documentDirty=true;'></td>
      </tr>
      <tr>
        <td><?php echo $_lang['user_role']; ?>:</td>
        <td>&nbsp;</td>
        <td>
    <?php
        if($_SESSION['role'] != 1){
          $sql = "select name, id from $dbase.".$table_prefix."user_roles WHERE id != 1";
        } else {
          $sql = "select name, id from $dbase.".$table_prefix."user_roles";
        }

        $rs = mysql_query($sql);
    ?>
    <select name="role" class="inputBox" onchange='documentDirty=true;' style="width:150px">
    <?php
    while ($row = mysql_fetch_assoc($rs)) {
      $selectedtext = $row['id']==$userdata['role'] ? "selected='selected'" : "" ;
    ?>
      <option value="<?php echo $row['id']; ?>" <?php echo $selectedtext; ?>><?php echo $row['name']; ?></option>
    <?php
    }
    ?>
    </select>
      </td>
      </tr>
    <?php if($_GET['a']=='12') { ?>
      <tr>
        <td><?php echo $_lang['user_logincount']; ?>:</td>
        <td>&nbsp;</td>
        <td><?php echo $userdata['logincount'] ?></td>
      </tr>
      <tr>
        <td><?php echo $_lang['user_prevlogin']; ?>:</td>
        <td>&nbsp;</td>
        <td><?php echo strftime('%d-%m-%y %H:%M:%S', $userdata['lastlogin']+$server_offset_time) ?></td>
      </tr>
      <tr>
        <td><?php echo $_lang['user_failedlogincount']; ?>:</td>
        <td>&nbsp;<input type="hidden" name="failedlogincount"  onchange='documentDirty=true;' value="<?php echo $userdata['failedlogincount']; ?>"></td>
        <td><span id='failed'><?php echo $userdata['failedlogincount'] ?></span>&nbsp;&nbsp;&nbsp;[<a href="javascript:resetFailed()"><?php echo $_lang['reset_failedlogins']; ?></a>]</td>
      </tr>
      <tr>
        <td><?php echo $_lang['user_block']; ?>:</td>
        <td>&nbsp;</td>
        <td><input name="blockedcheck" type="checkbox" onClick="changeblockstate(document.userform.blocked, document.userform.blockedcheck);"<?php echo $userdata['blocked']==1 ? " checked": "" ; ?>><input type="hidden" name="blocked" value="<?php echo $userdata['blocked'] ?>"></td>
      </tr>
    <?php
    }
    ?>
    </table>

    <?php if($_GET['id']==$_SESSION['internalKey']) { ?><span class='warning'><b><?php echo $_lang['user_edit_self_msg']; ?></span><br /><?php } ?>
  </div><!-- tabPage1 -->

<?php
if($use_udperms==1) {
$groupsarray = array();

if($_GET['a']=='12') { // only do this bit if the user is being edited
  $sql = "SELECT * FROM $dbase.".$table_prefix."member_groups where member=".$_GET['id']."";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  for ($i = 0; $i < $limit; $i++) {
    $currentgroup=mysql_fetch_assoc($rs);
    $groupsarray[$i] = $currentgroup['user_group'];
  }
}
?>

<!-- User Information Panel -->
  <div class="tab-page" id="tabPage2">
    <div class="tab">
      <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['access_permissions']; ?>
    </div>
    <script type="text/javascript">
      tpSettings.addTabPage(document.getElementById("tabPage2"));
    </script>

    <div class="sectionBody">
      <?php echo $_lang['access_permissions_user_message']; ?><br />
        <?php
        $sql = "SELECT name, id FROM $dbase.".$table_prefix."membergroup_names";
        $rs = mysql_query($sql);
        $limit = mysql_num_rows($rs);
        for($i=0; $i<$limit; $i++) {
          $row=mysql_fetch_assoc($rs);
    ?>
      <input type="checkbox" name="user_groups['<?php echo $row['id']; ?>']" <?php echo in_array($row['id'], $groupsarray) ? "checked='checked'" : "" ; ?>><?php echo $row['name']; ?><br />
    <?php
        }
    ?>
    </div><!-- sectionBody -->
    <?php
    }
    ?>
    <input type="submit" name="save" style="display:none">

    </form>
  </div><!-- tabPage2 -->

</div><!-- usersPane -->