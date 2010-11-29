<?php
// Modified by Ralph for 0.6.1
// Last Modified: 2006-12-07 by Ralph Dahlgren
// * as of [0613] we send the realm constant IN_ETOMITE_SYSTEM to captchaCode.php
// * as of [0613] we send the session_id to captchaCode.php
// * as of [0613] updated legal stuff - Dean
// * as of [0614] updated legal stuff incorrect grammar + added version alt text on logo - Dean
// Modified 2008-05-10 [v1.1] by Ralph Dahlgren
// - HTML markup improvements

startCMSSession();

if(!isset($_SESSION['validated']) || ($_SESSION['validated'] != 1)) {
  $_SESSION = array();
  @session_destroy();
  startCMSSession();

  include_once("browsercheck.inc.php");
  if(isset($manager_language)) {
    include_once("lang/".$manager_language.".inc.php");
  } else {
    include_once("lang/english.inc.php");
  }

  $cookieName = $site_id;

  include_once("crypt.class.inc.php");
  if(isset($_COOKIE[$cookieName])) {
    $cookieSet = 1;
    $username = $_COOKIE[$cookieName];
  }
  $rc4 = new rc4crypt;
  $keyPhrase = "cryptocipher";
  $uid = $rc4->endecrypt($keyPhrase,$username,'de');

?>

<html>
<head>
<title>Etomite</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $etomite_charset; ?>" />
<meta name="robots" content="noindex, nofollow" />
<link type="text/css" rel="StyleSheet" href="../manager/media/style/style.css" />
<script type="text/javascript" src="media/script/ieemu.js"></script>

<script type="text/javascript">
  function checkRemember () {
    if(document.loginfrm.rememberme.value==1) {
      document.loginfrm.rememberme.value=0;
    } else {
      document.loginfrm.rememberme.value=1;
    }
  }

  if (top.frames.length!=0) {
    top.location=self.document.location;
  }

  function enter(nextfield) {
    if(nextfield && window.event && window.event.keyCode == 13) {
        nextfield.focus();
        return false;
    } else {
        return true;
    }
  }

  function agreeToTerms(){
    if (!document.loginfrm.licenseOK.checked){
      alert("<?php echo $_lang['login_terms_error'];?>");
      return false;
    } else {
      return true;
    }
  }
</script>

</head>
<body>
<form method="post" name="loginfrm" action="processors/login.processor.php" onsubmit="javascript:return agreeToTerms();" >
<!--<input type="hidden" id="licenseOK" name="licenseOK" checked='checked' />-->
<input type="hidden" value="<?php echo isset($cookieSet) ? 1 : 0; ?>" name="rememberme">
<input type="hidden" value="<?php echo $_REQUEST['location']; ?>" name="location">
<table style="width:100%; height:100%;" border="0" cellspacing="0" cellpadding="0">
  <tr><td>&nbsp;</td></tr>
  <tr>
    <td align="center">
      <div class="loginTbl">
        <!-- intro text, logo and login box -->
        <table border="0" width="500" cellspacing="0">
          <tr>
            <td><img src='media/images/misc/logo.gif' alt='<?php echo $release; ?>' title='<?php echo $release; ?>' /></td>
            <td style="padding:1em;"><p><?php echo $_lang["login_message"]; ?></p><?php echo $use_captcha==1 ? "<p />".$_lang["login_captcha_message"]."</p>" : "" ; ?></td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <?php if($use_captcha==1) { ?>
                  <td>
                  <?php $dummy = rand(); ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'];?>"><img src="includes/captchaCode.php?dummy=<?php echo rand(); ?>&amp;sessid=<?php echo session_id(); ?>&amp;realm=IN_ETOMITE_SYSTEM" width="148" height="80" alt="<?php echo $_lang["login_captcha_message"]; ?>"></a>
                  </td>
                  <td>&nbsp;&nbsp;&nbsp;</td>
                  <?php } ?>
                  <td>
                    <table border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td><b><?php echo $_lang["username"]; ?>:</b></td>
                        <td><input type="text" name="username" tabindex="1" onKeyPress="return enter(document.loginfrm.password);" value="<?php echo $uid ?>" /></td>
                      </tr>
                      <tr>
                        <td><b><?php echo $_lang["password"]; ?>:</b></td>
                        <td><input type="password" name="password" tabindex="2" onKeyPress="return enter(<?php echo $use_captcha==1 ? "document.loginfrm.captcha_code" : "document.getElementById('Button1')" ;?>);" value="" /></td>
                      </tr>
                      <?php if($use_captcha==1) { ?>
                      <tr>
                        <td><b><?php echo $_lang["captcha_code"]; ?>:</b></td>
                        <td><input type="text" name="captcha_code" tabindex="3" onKeyPress="return enter(document.getElementById('Button1'));" value="" /></td>
                      </tr>
                      <?php } ?>
                      <tr>
                        <td><label for="thing"><?php echo $_lang["remember_username"]; ?>:&nbsp; </label></td>
                        <td>
                          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td valign="top">
                                <input type="checkbox" id="thing" name="thing" tabindex="4" size="1" value="" <?php echo isset($cookieSet) ? "checked" : ""; ?> onClick="checkRemember()" />
                              </td>
                              <td align="right">
                                <input class="inputButton" type="submit" tabindex="5" name="submit" value="<?php echo  $_lang['login_button']; ?>"  />
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
      <br />
      <div class="loginTbl">
        <table border="0" width="500" cellspacing="0">
          <tr>
            <td style="text-align:right;">
              <b>Etomite is &copy; and &trade; of The Etomite Project.</b><br />
              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><input type="checkbox" id="licenseOK" name="licenseOK" checked='checked' tabindex="6" /></td>
                  <td><label for='licenseOK'><i>"I agree to use Etomite in Accordance with the GPL."</i></label></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
</form>

<script type="text/javascript">
  <?php echo !empty($uid) ? "document.loginfrm.password.focus();" : "document.loginfrm.username.focus();" ?>
</script>

</body>
</html>

<?php
  exit;
}

if(isset($_SESSION['validated']) && $_SESSION['permissions']['frames']!=1) {
  // we use this to make sure files are accessed through the manager instead of seperately.
  define("IN_ETOMITE_SYSTEM", "true");
  echo '
    <script type="text/javascript">
    <!--
    var answer = alert ("You don\'t have enough privileges for this action!");
    if (!answer) window.location=window.location;
    // -->
    </script>
  ';
  include_once("./processors/logout.processor.php");
}

if(getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
elseif(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
elseif(getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
else $ip = "UNKNOWN";
$_SESSION['ip'] = $ip;

$itemid = (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) ? $_REQUEST['id'] : 'NULL';
$lasthittime = time();
$a = isset($_REQUEST['a']) ? $_REQUEST['a'] : "" ;

if($a!=1) {
  $sql = "REPLACE INTO $dbase.".$table_prefix."active_users(internalKey, username, lasthit, action, id, ip) values(".$_SESSION['internalKey'].", '".$_SESSION['shortname']."', '".$lasthittime."', '".$a."', ".($itemid == 'NULL' ? 'NULL' : "'$itemid'").", '$ip')";
  if(!$rs = mysql_query($sql)) {
    echo "error replacing into active users! SQL: ".$sql;
    exit;
  }
}

?>
