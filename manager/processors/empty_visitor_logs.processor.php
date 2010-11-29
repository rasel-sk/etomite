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
$handle = mysql_connect($database_server, $database_user, $database_password)or die('Could not connect: ' . mysql_error());
mysql_select_db(str_replace("`","",$dbase)) or die('Could not select database');
$db = $dbase.".".$table_prefix;

// Empty visitor related logging tables
$sql = "TRUNCATE TABLE ".$db."log_access;";
$rs = @ mysql_query($sql);

$sql = "TRUNCATE TABLE ".$db."log_hosts;";
$rs = @ mysql_query($sql);

$sql = "TRUNCATE TABLE ".$db."log_operating_systems;";
$rs = @ mysql_query($sql);

$sql = "TRUNCATE TABLE ".$db."log_referers;";
$rs = @ mysql_query($sql);

$sql = "TRUNCATE TABLE ".$db."log_totals;";
$rs = @ mysql_query($sql);

$sql = "TRUNCATE TABLE ".$db."log_user_agents;";
$rs = @ mysql_query($sql);

$sql = "TRUNCATE TABLE ".$db."log_visitors;";
$rs = @ mysql_query($sql);

$header="Location: index.php?a=68";
header($header);

// Done

?>
