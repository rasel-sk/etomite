<?php
// logout.processor.php

if(IN_ETOMITE_SYSTEM != "true" && IN_ETOMITE_PARSER != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}
$_SESSION = array();
session_destroy();
/*
// remarked out in Etomite Prelude Final to prevent front end logouts from destroying manager sessions
$sessionID = md5(date('d-m-Y H:i:s'));
session_id($sessionID);
session_start();
startCMSSession();
session_destroy();
*/
$url = isset($url) ? $url : "./index.php";
header("Location: ".$url);
?>
