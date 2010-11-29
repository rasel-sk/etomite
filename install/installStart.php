<?php
// installStart.php
// Modified 2008-04-08 [v1.0] by Ralph Dahlgren

$installationType = $_GET['installationType'];

if($installationType=='full')
{
  header("Location: newInstallStart.php?sqlFile=etomite_full.sql");
  exit;
}
elseif($installationType=='lite')
{
  header("Location: newInstallStart.php?sqlFile=etomite_lite.sql");
  exit;
}
elseif($installationType=='bare')
{
  header("Location: newInstallStart.php?sqlFile=etomite_bare.sql");
  exit;
}
elseif($installationType=='upgrade')
{
  header("Location: upgradeStart.php");
  exit;
}
else
{
  echo "No installationType found in \$_GET.";
}

?>
