<?php
// login.processor.php
// Modified: 2006-04-07 by Ralph Dahlgren
// * Added the variable $maxAttempts for adjusting the number of attempts before blocking
// Modified 2006-07-27 by Ralph Dahlgren
// * Added preg_replace() to $_POST variables to avoid injection exploits
// Modified 2008-04-14 by Ralph Dahlgren
// * Added if clause to logging due to new $use_mgr_logging system setting
// * Implemented system setting $_max_attempts instead of hard-coded $maxAttempts

// we use this to make sure files are accessed through
// the manager instead of seperately.
define("IN_ETOMITE_SYSTEM", "true");

// include the database configuration file
include_once("../includes/config.inc.php");

// connect to the database
if(@!$etomiteDBConn = mysql_connect($database_server, $database_user, $database_password))
{
  die("Failed to create the database connection!");
}
else
{
  mysql_select_db($dbase);
}

// get the settings from the database
include_once("../includes/settings.inc.php");

// include version info
include_once("../includes/version.inc.php");

// include the crypto thing
include_once("../includes/crypt.class.inc.php");

startCMSSession();

// include_once the error handler
include_once("../includes/error.class.inc.php");
$e = new errorHandler;

// if $max_attempts system setting is set, use it, otherwise set to 3
$max_attempts = !empty($max_attempts) ? $max_attempts : 3;

$username = preg_replace("/[^\w\.@-]/", "", htmlspecialchars($_POST['username']));
$givenPassword = preg_replace("/[^\w\.@-]/", "", htmlspecialchars($_POST['password']));
$captcha_code = preg_replace("/[^\w\.@-]/", "", $_POST['captcha_code']);

$sql = "SELECT $dbase.".$table_prefix."manager_users.*, $dbase.".$table_prefix."user_attributes.* FROM $dbase.".$table_prefix."manager_users, $dbase.".$table_prefix."user_attributes WHERE $dbase.".$table_prefix."manager_users.username REGEXP BINARY '^".$username."$' and $dbase.".$table_prefix."user_attributes.internalKey=$dbase.".$table_prefix."manager_users.id;";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);

if($limit == 0 || $limit > 1)
{
  $e->setError(900);
  $e->dumpError();
}

$row = mysql_fetch_assoc($rs);

$internalKey          = $row['internalKey'];
$dbasePassword        = $row['password'];
$failedlogins         = $row['failedlogincount'];
$blocked              = $row['blocked'];
$blockeduntil         = $row['blockeduntil'];
$registeredsessionid  = $row['sessionid'];
$role                 = $row['role'];
$lastlogin            = $row['lastlogin'];
$nrlogins             = $row['logincount'];
$fullname             = $row['fullname'];
$email                = $row['email'];

if($failedlogins >= $max_attempts && $blockeduntil > time())
{
  // blocked due to number of login errors.
  session_destroy();
  session_unset();
  $e->setError(902);
  $e->dumpError();
}

if($failedlogins >= $max_attempts && $blockeduntil < time())
{
  // blocked due to number of login errors, but get to try again
  $sql = "UPDATE $dbase.".$table_prefix."user_attributes SET failedlogincount='0', blockeduntil='".(time()-1)."' where internalKey=$internalKey";
  $rs = mysql_query($sql);
}

if($blocked == "1")
{
  // this user has been blocked by an admin, so no way he's loggin in!
  session_destroy();
  session_unset();
  $e->setError(903);
  $e->dumpError();
}

if($blockeduntil > time())
{
  // this user has a block date in the future. Shouldn't really occur, unless someones been editing the database.
  session_destroy();
  session_unset();
  $e->setError(904);
  $e->dumpError();
}

if($dbasePassword != md5($givenPassword))
{
  $e->setError(901);
  $newloginerror = 1;
}

if($use_captcha == 1)
{
  if($_SESSION['veriword']!=$captcha_code)
  {
    unset($_SESSION['veriword']);
    $e->setError(905);
    $newloginerror = 1;
  }
}

unset($_SESSION['veriword']);

if($newloginerror==1)
{
  $failedlogins += $newloginerror;
  if($failedlogins>=$max_attempts)
  {
    //increment the failed login counter, and block!
    $sql = "update $dbase.".$table_prefix."user_attributes SET failedlogincount='$failedlogins', blockeduntil='".(time()+(1*60*60))."' where internalKey=$internalKey";
    $rs = mysql_query($sql);
  }
  else
  {
    //increment the failed login counter
    $sql = "update $dbase.".$table_prefix."user_attributes SET failedlogincount='$failedlogins' where internalKey=$internalKey";
    $rs = mysql_query($sql);
  }
  session_destroy();
  session_unset();
  $e->dumpError();
}

$currentsessionid = session_id();

if(!isset($_SESSION['validated']))
{
  $sql = "update $dbase.".$table_prefix."user_attributes SET failedlogincount=0, logincount=logincount+1, lastlogin=thislogin, thislogin=".time().", sessionid='$currentsessionid' where internalKey=$internalKey";
  $rs = mysql_query($sql);
}

// get permissions
$_SESSION['shortname'] = $username;
$_SESSION['fullname'] = $fullname;
$_SESSION['email'] = $email;
$_SESSION['validated'] = 1;
$_SESSION['internalKey'] = $internalKey;
$_SESSION['failedlogins'] = $failedlogins;
$_SESSION['lastlogin'] = $lastlogin;
$_SESSION['sessionRegistered'] = $sessionRegistered;
$_SESSION['role'] = $role;
$_SESSION['lastlogin'] = $lastlogin;
$_SESSION['nrlogins'] = $nrlogins;

$sql = "SELECT * FROM $dbase.".$table_prefix."user_roles where id=".$role.";";
$rs = mysql_query($sql);
$row = mysql_fetch_assoc($rs);
$_SESSION['permissions'] = $row;

if($_SESSION['permissions']['frames'] != 1)
{
  // $location = "../index.php?a=8";
  $location = "../../";
  header("Location: ".$location);
}

$cookieName = $site_id;
$_POST['rememberme'] = 1;

if($_POST['rememberme'] == 1)
{
  $rc4 = new rc4crypt;
  $username = $_POST['username'];
  $keyPhrase = "cryptocipher";
  $thestring = $rc4->endecrypt($keyPhrase,$username);
  setcookie($cookieName, $thestring,time()+604800, "/", "", 0);
}
else
{
  setcookie($cookieName, "",time()-604800, "/", "", 0);
}

// include the logger and add an audit trail entry unless logging is disabled
if($use_mgr_logging != 0)
{
  include_once("../includes/log.class.inc.php");
  $log = new logHandler;
  $log->initAndWriteLog("Logged in", $_SESSION['internalKey'], $_SESSION['shortname'], "58", "-", "Etomite");
}

$location = ($_POST['location'] != "") ? $_POST['location'] : "../index.php";
header("Location: ".$location);
?>
