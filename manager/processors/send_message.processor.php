<?php

// Last modified 2005-12-04 by Ralph to fix problems when sending a message to a Group or Everyone

if(IN_ETOMITE_SYSTEM!="true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if(($_SESSION['permissions']['messages'] != 1 ) && ($_POST['a'] == 66))
{
  $e->setError(3);
  $e->dumpError();
}

//$db->debug = true;

$db = $dbase.".".$table_prefix;

$sendto = $_POST['sendto'];
$userid = $_POST['user'];
$groupid = $_POST['group'];
$subject = addslashes($_POST['messagesubject']);
if($subject == "") $subject = "(no subject)";
$message = addslashes($_POST['messagebody']);
if($message == "") $message = "(no message)";
$postdate = time();

if($sendto == 'u') {
  if($userid == 0) {
    $e->setError(13);
    $e->dumpError();
  }
  $sql = "INSERT INTO ".$db."user_messages SET
    id = '',
    type = 'Message',
    subject = '$subject',
    message = '$message',
    sender = ". $_SESSION['internalKey'].",
    recipient = $userid,
    private = 1,
    postdate = $postdate,
    messageread = 0;
  ";
  $rs = mysql_query($sql);
}

if($sendto == 'g') {
  if($groupid == 0) {
    $e->setError(14);
    $e->dumpError();
  }
  $sql = "SELECT internalKey FROM ".$db."user_attributes WHERE ".$db."user_attributes.role=$groupid;";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  for( $i=0; $i<$limit; $i++ ){
    $row=mysql_fetch_assoc($rs);
    if($row['internalKey']!=$_SESSION['internalKey']) {
      $sql2 = "INSERT INTO ".$db."user_messages SET
        id = '',
        type = 'Message',
        subject = '$subject',
        message = '$message',
        sender = ".$_SESSION['internalKey'].",
        recipient = ".$row['internalKey'].",
        private = 0,
        postdate = $postdate,
        messageread = 0;
      ";
      $rs2 =  mysql_query($sql2);
    }
  }
}

if($sendto == 'a') {
  $sql = "SELECT id FROM ".$db."manager_users;";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  for( $i=0; $i<$limit; $i++ ){
    $row=mysql_fetch_assoc($rs);
    if($row['id'] != $_SESSION['internalKey']) {
      $sql2 = "INSERT INTO ".$db."user_messages SET
        id = '',
        type = 'Message',
        subject = '$subject',
        message = '$message',
        sender = ".$_SESSION['internalKey'].",
        recipient = ".$row['id'].",
        private = 0,
        postdate = $postdate,
        messageread = 0;
      ";
      $rs2 =  mysql_query($sql2);
    }
  }
}

$header = "Location: index.php?a=10";
header($header);

?>
