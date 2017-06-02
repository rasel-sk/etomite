<?php
// empty_visitor_logs.processor.php

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['settings'] != 1 && $_REQUEST['a'] == 56)
{
  $e->setError(3);
  $e->dumpError();
}

//  START: Connect to database
$handle = mysqli_connect($database_server, $database_user, $database_password, null, $database_server_port)or die('Could not connect: ' . mysqli_error());
mysqli_set_charset($handle, $database_charset);
mysqli_select_db($handle, str_replace("`","",$dbase)) or die('Could not select database');
$db = $dbase.".".$table_prefix;

// Empty visitor related logging tables
$sql = "TRUNCATE TABLE ".$db."log_access;";
$rs = @ mysqli_query($etomiteDBConn, $sql);

$sql = "TRUNCATE TABLE ".$db."log_hosts;";
$rs = @ mysqli_query($etomiteDBConn, $sql);

$sql = "TRUNCATE TABLE ".$db."log_operating_systems;";
$rs = @ mysqli_query($etomiteDBConn, $sql);

$sql = "TRUNCATE TABLE ".$db."log_referers;";
$rs = @ mysqli_query($etomiteDBConn, $sql);

$sql = "TRUNCATE TABLE ".$db."log_totals;";
$rs = @ mysqli_query($etomiteDBConn, $sql);

$sql = "TRUNCATE TABLE ".$db."log_user_agents;";
$rs = @ mysqli_query($etomiteDBConn, $sql);

$sql = "TRUNCATE TABLE ".$db."log_visitors;";
$rs = @ mysqli_query($etomiteDBConn, $sql);

$header="Location: index.php?a=68";
header($header);

// Done

?>
