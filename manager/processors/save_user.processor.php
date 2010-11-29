<?php
if(IN_ETOMITE_SYSTEM!="true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['save_user']!=1 && $_REQUEST['a']==32)
{
  $e->setError(3);
  $e->dumpError();
}

function generate_password($length = 10) {
  $allowable_characters = "abcdefghjkmnpqrstuvxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
  $ps_len = strlen($allowable_characters);
  mt_srand((double)microtime()*1000000);
  $pass = "";
  for($i = 0; $i < $length; $i++) {
    $pass .= $allowable_characters[mt_rand(0,$ps_len-1)];
  }
  return $pass;
}

$id = $_POST['id'];
$newusername = !empty($_POST['newusername']) ? $_POST['newusername'] : "New User";
$fullname = addslashes($_POST['fullname']);
$genpassword = $_POST['newpassword'];
$passwordgenmethod = $_POST['passwordgenmethod'];
$passwordnotifymethod = $_POST['passwordnotifymethod'];
$specifiedpassword = $_POST['specifiedpassword'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$mobilephone = $_POST['mobilephone'];
$role = $_POST['role'];
$failedlogincount = $_POST['failedlogincount'];
$blocked = $_POST['blocked'];
$blockeduntil = $_POST['blockeduntil'];
$user_groups = $_POST['user_groups'];

switch ($_POST['mode']) {
  case '11':
  if($email=='' || !ereg("^[-!#$%&'*+./0-9=?A-Z^_`a-z{|}~]+", $email)){
    ?>
    <script language="JavaScript">
    alert("E-mail address doesn't seem to be valid!");
    history.back(1);
    </script>
    <?php
    exit;
  }
  // check this user doesn't already exist
  $sql = "SELECT id FROM $dbase.".$table_prefix."manager_users WHERE username='$newusername'";
  if(!$rs = mysql_query($sql)){
    echo "An error occured while attempting to retreive all users with username $newusername.";
    exit;
  }
  $limit = mysql_num_rows($rs);
  if($limit>0) {
    echo "Username is already in use!<p>";
    exit;
  }

  // generate a new password for this user
  if($specifiedpassword!="" && $passwordgenmethod=="spec") {
    if(strlen($specifiedpassword) < 6 ) {
      echo "Password is too short!";
      exit;
    } else {
      $newpassword = $specifiedpassword;
    }
  } elseif($specifiedpassword=="" && $passwordgenmethod=="spec") {
    echo "You didn't specify a password for this user!";
    exit;
  } elseif($passwordgenmethod=='g') {
    $newpassword = generate_password(8);
  } else {
    echo "No password generation method specified!";
    exit;
  }
  // build the SQL
  $sql = "INSERT INTO $dbase.".$table_prefix."manager_users(username, password)
      VALUES('".$newusername."', md5('".$newpassword."'));";
  $rs = mysql_query($sql);
  if(!$rs){
    echo "An error occured while attempting to save the user.";
    exit;
  }
  // now get the id
  if(!$key=mysql_insert_id()) {
    //get the key by sql
  }
  $sql = "INSERT INTO $dbase.".$table_prefix."user_attributes(internalKey, fullname, role, email, phone, mobilephone)
      VALUES($key, '$fullname', '$role', '$email', '$phone', '$mobilephone');";
  $rs = mysql_query($sql);
  if(!$rs){
    echo "An error occured while attempting to save the user's attributes.";
    exit;
  }
/*******************************************************************************/
    // put the user in the user_groups he/ she should be in
    // first, check that up_perms are switched on!
    if($use_udperms==1) {
      if(count($user_groups)>0) {
        foreach ($user_groups as $groupKey => $value) {
          $sql = "INSERT INTO $dbase.".$table_prefix."member_groups(user_group, member) values(".stripslashes($groupKey).", $key)";
          $rs = mysql_query($sql);
          if(!$rs){
            echo "An error occured while attempting to add the user to a user_group.";
            exit;
          }
        }
      }
    }
    // end of user_groups stuff!

    if($passwordnotifymethod=='e') {
        $message = sprintf($signupemail_message, $newusername, $newpassword);
        if(!mail($email, $emailsubject, $message, "From: ".$emailsender."\r\n"."X-Mailer: Etomite - PHP/" . phpversion())) {
          echo "Error while sending mail!<br />";
          echo $mailto;
          exit;
        }
        $header="Location: index.php?a=75&r=2";
        header($header);
    } else {
      include_once("includes/header.inc.php");
    ?>
  <div class="subTitle">
  <span class="floatRight"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['cleaningup']; ?></span>
  </div>

  <div id="disp">
  <?php echo $_lang['actioncomplete']; ?><p>&nbsp;
  <?php printf($_lang["password_msg"], $newusername, $newpassword); ?>

  <script language="JavaScript">
  doRefresh(2);
  </script>
  </div>

  <?php
    include_once("includes/footer.inc.php");
  }
  break;
  case '12':
  if($email=='' || !ereg("^[-!#$%&'*+./0-9=?A-Z^_`a-z{|}~]+", $email)){
    ?>
    <script language="JavaScript">
    alert("E-mail address doesn't seem to be valid!");
    history.back(1);
    </script>
    <?php
    exit;
  }
  // generate a new password for this user
  if($genpassword==1) {
    if($specifiedpassword!="" && $passwordgenmethod=="spec") {
      if(strlen($specifiedpassword) < 6 ) {
        echo "Password is too short!";
        exit;
      } else {
        $newpassword = $specifiedpassword;
      }
      } elseif($specifiedpassword=="" && $passwordgenmethod=="spec") {
        echo "You didn't specify a password for this user!";
        exit;
      } elseif($passwordgenmethod=='g') {
        $newpassword = generate_password(8);
      } else {
        echo "No password generation method specified!";
        exit;
      }
    $updatepasswordsql=", password=MD5('$newpassword') ";
  }
  if($passwordnotifymethod=='e') {

      $message = sprintf($signupemail_message, $newusername, $newpassword);
      if(!mail($email, $emailsubject, $message, "From: ".$emailsender."\r\n"."X-Mailer: Etomite - PHP/" . phpversion())) {
        echo "Error while sending mail!<br />";
        echo $mailto;
        exit;
      }

  }

  // build the SQL to check the username doesn't exist yet
  $sql = "SELECT id FROM $dbase.".$table_prefix."manager_users WHERE username='$newusername'";
  if(!$rs = mysql_query($sql)){
    echo "An error occured while attempting to retreive all users with username $newusername.";
    exit;
  }
  $limit = mysql_num_rows($rs);
  if($limit>0) {
    $row=mysql_fetch_assoc($rs);
    if($row['id']!=$id) {
      echo "Username is already in use!<p>";
      exit;
    }
  }

  $sql = "UPDATE $dbase.".$table_prefix."manager_users SET username='$newusername'".$updatepasswordsql." WHERE id=$id";
  if(!$rs = mysql_query($sql)){
    echo "An error occured while attempting to update the user's data.";
    exit;
  }
  $sql = "UPDATE $dbase.".$table_prefix."user_attributes SET fullname='$fullname', role='$role', email='$email', phone='$phone',
      mobilephone='$mobilephone', failedlogincount='$failedlogincount', blocked=$blocked, blockeduntil='$blockeduntil' WHERE internalKey=$id";
  if(!$rs = mysql_query($sql)){
    echo "An error occured while attempting to update the user's attributes.";
    exit;
  }
/*******************************************************************************/
  // put the user in the user_groups he/ she should be in
  // first, check that up_perms are switched on!
  if($use_udperms==1) {
    // as this is an existing user, delete his/ her entries in the groups before saving the new groups
    $sql = "DELETE FROM $dbase.".$table_prefix."member_groups WHERE member=$id;";
    $rs = mysql_query($sql);
    if(!$rs){
      echo "An error occured while attempting to delete previous user_groups entries.";
      exit;
    }
    if(count($user_groups)>0) {
      foreach ($user_groups as $key => $value) {
        $sql = "INSERT INTO $dbase.".$table_prefix."member_groups(user_group, member) values(".stripslashes($key).", $id)";
        $rs = mysql_query($sql);
        if(!$rs){
          echo "An error occured while attempting to add the user to a user_group.<br />$sql;";
          exit;
        }
      }
    }
  }
  // end of user_groups stuff!
/*******************************************************************************/
  if($id==$_SESSION['internalKey']) {
  ?>
  <body bgcolor='#efefef'>
  <script language="JavaScript">
  alert("Your data has been changed.\nPlease log in again.");
  top.location.href='index.php?a=8';
  </script>
  </body>
  <?php
    exit;
  }
  if($genpassword==1 && $passwordnotifymethod=='s') {
  include_once("includes/header.inc.php");
  ?>
  <div class="subTitle">
    <span class="floatRight"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['cleaningup']; ?></span>
  </div>

  <div id="disp">
    <?php echo $_lang['actioncomplete']; ?><p>&nbsp;
    <?php printf($_lang["password_msg"], $newusername, $newpassword); ?>
    </p>
    <script language="JavaScript">
      doRefresh(2);
    </script>
  </div>
  <?php
    include_once("includes/footer.inc.php");
  } else {
    $header="Location: index.php?a=75&r=2";
    header($header);
  }
  break;
  default:
    echo "Erm... You supposed to be here now?";
    exit;
}
?>
