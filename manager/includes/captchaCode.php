<?php
// captchaCode.php file modified for Etomite
// Created by: Ralph A. Dahlgren
// Last Modified: 2006-12-07
// Generates and returns CaptchaCode images to the Etomite parser
// for use with visitor authentication procedures

// assign width and height of the returned image
$width = isset($width) ? $width : 148;
$height = isset($height) ? $height : 80;

// as of [0613] we send the realm constant in a variable [IN_ETOMITE_PARSER|IN_ETOMITE_SYSTEM]
if(isset($_GET['realm'])) define($_GET['realm'], "true");

// load the system configuration settings
include("config.inc.php");

// initialize the install-specific session
startCMSSession();

// as of [0613] we send the session_id in a variable
if(isset($_GET['sessid'])) session_id($_GET['sessid']);

// load the PHP class that does all the real work
include("captchaClass.php");

// generate and return the CaptchaCode image
$vword = new VeriWord(
  $width,
  $height,
  $database_server,
  $database_user,
  $database_password,
  $dbase,
  $table_prefix
);
$vword->output_image();
$vword->destroy_image();
// THE END
?>
