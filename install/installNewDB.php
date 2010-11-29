<?php
// installNewDB.php
// Performs a new installation of the Etomite CMS
// Last Modified: 2008-04-08 [v1.0] by Ralph A. Dahlgren

session_start();
// the SQL file to import
$sqlFile = "sql/".$_SESSION['sqlFile'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Etomite &raquo; Install</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <style type="text/css">
    @import url('../assets/site/style.css');
    ul li { margin-top: 7px; }
    .ok { color : green; font-weight: bold; }
    .notok { color : red; font-weight: bold; }
    .labelHolder {
      width : 180px;
      float : left;
      font-weight: bold;
    }
  </style>
</head>

<body>
<table border="0" cellpadding="0" cellspacing="0" class="mainTable">
  <tr class="fancyRow">
    <td><span class="headers">&nbsp;<img src="../manager/media/images/misc/dot.gif" alt="" style="margin-top: 1px;" />&nbsp;Etomite</span></td>
    <td align="right"><span class="headers">Installation</span></td>
  </tr>
  <tr class="fancyRow2">
    <td colspan="2" class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
  <tr align="left" valign="top">
    <td colspan="2"><table width="100%"  border="0" cellspacing="0" cellpadding="1">
      <tr align="left" valign="top">
        <td class="pad" id="content" colspan="2">

<?php
// define the page footer that all sections will use
$pageFooter = <<<PAGEFOOTER
        </td>
      </tr>
     </table>
    </td>
  </tr>
  <tr class="fancyRow2">
    <td class="border-top-bottom smallText">&nbsp;</td>
    <td class="border-top-bottom smallText" align="right">&nbsp;</td>
  </tr>
</table>
</body>
</html>
PAGEFOOTER;

if(!isset($_POST['licenseOK']) || empty($_POST['licenseOK']))
{
  echo "<p>You need to agree to the license before proceeding with the setup!</p>";
  echo $pageFooter;
  exit;
}

echo "<h1>Etomite setup will now attempt to setup the database</h1>";

$create = false;
$errors = 0;

// get db info from session
$host = $_SESSION['databasehost'];
$name = $_SESSION['databaseloginname'];
$pass = $_SESSION['databaseloginpassword'];
$db = $_SESSION['databasename'];
$table_prefix = $_SESSION['tableprefix'];
$adminname = $_SESSION['cmsadmin'];
$adminpass = $_SESSION['cmspassword'];

// attempt to connect to the MySQL server
echo "<p>Creating connection to the database: ";
if(!@$conn = mysql_connect($host, $name, $pass))
{
  echo "<span class='notok'>Failed!</span></p><p>Please check the database login details and try again.</p>";
  echo $pageFooter;
  exit;
}
else
{
  echo "<span class='ok'>OK!</span></p>";
}

// attempt to connect to the desired database
echo "<p>Selecting database `".$db."`: ";
if(!@mysql_select_db($db, $conn))
{
  echo "<span class='notok'>Failed...</span> - database does not exist. Will attempt to create:</p>";
  $errors += 1;
  $create = true;
}
else
{
  echo "<span class='ok'>OK!</span></p>";
}

// attempt to create the database
if($create)
{
  echo "<p>Creating database `".$db."`: ";
  if(!@mysql_create_db($db, $conn))
  {
    echo "<span class='notok'>Failed!</span> - Could not create database!</p>";
    $errors += 1;
    echo "<p>Etomite setup could not create the database, and no existing database with the same name was found. </p><p>Please create a database, and run setup again.</p>";
    echo $pageFooter;
    exit;
  }
  else
  {
    echo "<span class='ok'>OK!</span></p>";
  }
}

// cehck to see if the desired table prefix is alreay in use
echo "<p>Checking table prefix `".$table_prefix."`: ";
if(@$rs=mysql_query("SELECT COUNT(*) FROM $db.".$table_prefix."site_content"))
{
  echo "<span class='notok'>Failed!</span> - Table prefix is already in use in this database!</p>";
  $errors += 1;
  echo "<p>Etomite setup couldn't install into the selected database, as it already contains Etomite tables. Please choose a new table_prefix, and run setup again.</p>";
  echo $pageFooter;
    exit;
}
else
{
  echo "<span class='ok'>OK!</span></p>";
}

// load the sqlParser class and attempt to load the desired SQL file
include("sqlParser.class.php");
$sqlParser = new SqlParser($host, $name, $pass, $db, $table_prefix, $adminname, $adminpass);
$sqlParser->connect();
$sqlParser->process($sqlFile);
$sqlParser->close();

// handle errors
echo "<p>Importing default site: ";

if($sqlParser->installFailed==true) {
  echo "<span class='notok'>Failed!</span> - Installation failed!</p>";
  $errors += 1;
  echo "<p>Etomite setup couldn't install the default site into the selected database. The last error to occur was <i>".$sqlParser->mysqlErrors[count($sqlParser->mysqlErrors)-1]['error']."</i> during the execution of SQL statement <span class=\"mono\">".strip_tags($sqlParser->mysqlErrors[count($sqlParser->mysqlErrors)-1]['sql'])."</span></p>";
  echo $pageFooter;
  exit;
}
else
{
  echo "<span class='ok'>OK!</span></p>";
}

// attempt to write the manager/includes/config.inc.php file
echo "<p>Writing configuration file: ";
// read in the config.inc.php template
$filename = "./config.inc.php";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);

// perform global search and replace of tags in the SQL
$search = array('{HOST}','{USER}','{PASS}','{DBASE}','{PREFIX}');
$replace = array($host,$name,$pass,$db,$table_prefix);
$configString = str_replace($search,$replace,$contents);

// open config.inc.php
$filename = '../manager/includes/config.inc.php';
$configFileFailed = false;
if (@!$handle = fopen($filename, 'w'))
{
  $configFileFailed = true;
}

// write $configString to our opened file.
if(@fwrite($handle, $configString) === FALSE)
{
  $configFileFailed = true;
}
@fclose($handle);

// display config file write error or success message
if($configFileFailed==true)
{
  echo "<span class='notok'>Failed!</span></p>";
  $errors += 1;
  echo "<p>Etomite couldn't write the config file. Please copy the following into the <span class=\"mono\">manager/includes/config.inc.php</span> file:</p>
  <textarea style=\"width:400px; height:160px;\">
  $configString
  </textarea>
  Once that's been done, you can log into Etomite by pointing your browser at yoursite/manager/.</p>";
  echo $pageFooter;
  exit;
}
else
{
  echo "<span class='ok'>OK!</span></p>";
}

// installation completed successfully
echo "<p>Installation was successful! You can now log into the <a href=\"../manager/\"><b><u>Etomite manager</u></b></a>. First thing you need to do is to update and save the Etomite configuration. Etomite will ask you to do so once you've logged in.</p><p>Please make sure you CHMOD the config.inc.php file so it is not writeable by anyone other than yourself... Also, don't forget to remove the installer folder, as it is no longer needed.</p>";
echo $pageFooter;

?>
