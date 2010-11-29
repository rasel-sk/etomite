<?php
if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);
if($_SESSION['permissions']['settings']!=1) {
  $e->setError(3);
  $e->dumpError();
}
?>
<div class="subTitle">
  <span class="floatRight"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['manage_modules'] ;?></span>
</div>


<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['manage_modules'] ;?></div><div class="sectionBody">


<link type="text/css" rel="stylesheet" href="media/style/tabs.css" />
<script type="text/javascript" src="media/script/tabpane.js"></script>
<div class="tab-pane" id="tabPane1">
  <script type="text/javascript">
  tp1 = new WebFXTabPane( document.getElementById( "tabPane1" ) );
</script>

  <div class="tab-page" id="tabPage1">
  <h2 class="tab"><?php echo $_lang["modules_already_installed"] ?></h2>
  <script type="text/javascript">tp1.addTabPage( document.getElementById( "tabPage1" ) );</script>

  </div>


  <div class="tab-page" id="tabPage2">
  <h2 class="tab"><?php echo $_lang["modules_install_new"] ?></h2>
  <script type="text/javascript">tp1.addTabPage( document.getElementById( "tabPage2" ) );</script>

<?php

include("includes/XPath.class.php");

$xPath = new Xpath;

$modspath = getcwd()."/modules";


if($dir = @opendir($modspath)) {
  while(($file = readdir($dir)) !== false) {
    $currPath = $modspath."/".$file;
    if(is_dir($currPath) && $file!="." && $file!="..") {
      $configFile = $currPath."/config.xml";


// XPath stuff
$xPath->XPath($fileName=$configFile);
?>
<br />
<table width="600"  border="0" cellspacing="1" cellpadding="3" bgcolor="#000000">
  <thead>
  <tr>
    <td colspan="3">
      <b><?php echo $xPath->getAttributes("/etomiteModule", $attrName="name"); ?></b>
    </td>
  </tr>
  </thead>
  <tr class="row1">
    <td colspan="3"><?php echo $xPath->getData("/etomiteModule/generalInfo/description"); ?></td>
  </tr>
  <tr class="row3">
    <td width="150">Version:</td>
    <td width="30" class="row2">&nbsp;</td>
    <td class="row2" width="320"><?php echo $xPath->getData("/etomiteModule/generalInfo/version"); ?></td>
  </tr>
  <tr class="row3">
    <td>Date:</td>
    <td width="30" class="row2">&nbsp;</td>
    <td class="row2"><?php echo $xPath->getData("/etomiteModule/generalInfo/date"); ?></td>
  </tr>
  <tr class="row3">
    <td>Author:</td>
  <td width="30" class="row2">&nbsp;</td>
    <td class="row2"><?php echo $xPath->getData("/etomiteModule/generalInfo/author"); ?></td>
  </tr>
  <tr class="row3">
    <td>Website:</td>
    <td width="30" class="row2">&nbsp;</td>
  <td class="row2"><a href="<?php $url = $xPath->getData("/etomiteModule/generalInfo/website"); echo $url; ?>" target="_blank"><?php echo $url; ?></a></td>
  </tr>
  <tr class="row3">
    <td>E-mail:</td>
  <td width="30" class="row2">&nbsp;</td>
    <td class="row2"><?php echo $xPath->getData("/etomiteModule/generalInfo/email"); ?></td>
  </tr>
  <tr class="fancyRow2">
    <td colspan="3">
      <b><?php echo $_lang["module_install_info"] ?></b>
    </td>
  </tr>
  <tr class="row3">
    <td width="150">Files:</td>
  <?php
  $tmpStr = $xPath->getData("/etomiteModule/install/files");
  ?>
  <td width="30" class="row2"><?php echo strlen($tmpStr)>0 ? count(explode(",", $tmpStr)) : 0 ; ?></td>
    <td class="row2">
    <?php echo strlen($tmpStr)>0 ? $tmpStr : "-"; ?>
  </td>
  </tr>
  <tr class="row3">
    <td>SQL tasks:</td>
  <?php
  $tmpStr = $xPath->getData("/etomiteModule/install/sql");
  ?>
  <td class="row2"><?php echo strlen($tmpStr)>0 ? count(explode(",", $tmpStr)) : 0 ; ?></td>
    <td class="row2">
    <?php echo strlen($tmpStr)>0 ? $tmpStr : "-"; ?>
  </td>
  </tr>
  <tr class="row3">
    <td>Snippets:</td>
  <?php
  $tmpStr = $xPath->getData("/etomiteModule/install/snippets");
  ?>
  <td class="row2"><?php echo strlen($tmpStr)>0 ? count(explode(",", $tmpStr)) : 0 ; ?></td>
    <td class="row2">
    <?php echo strlen($tmpStr)>0 ? $tmpStr : "-"; ?>
  </td>
  </tr>
  <tr class="row3">
    <td>Chunks:</td>
  <?php
  $tmpStr = $xPath->getData("/etomiteModule/install/chunks");
  ?>
  <td class="row2"><?php echo strlen($tmpStr)>0 ? count(explode(",", $tmpStr)) : 0 ; ?></td>
    <td class="row2">
    <?php echo strlen($tmpStr)>0 ? $tmpStr : "-"; ?>
  </td>
  </tr>
  <tr class="row3">
    <td>Settings:</td>
  <?php
  $tmpStr = $xPath->getData("/etomiteModule/install/settings");
  ?>
  <td class="row2"><?php echo strlen($tmpStr)>0 ? count(explode(",", $tmpStr)) : 0 ; ?></td>
    <td class="row2">
    <?php echo strlen($tmpStr)>0 ? $tmpStr : "-"; ?>
  </td>
  </tr>
  <tr class="row3">
    <td>Documents:</td>
  <?php
  $tmpStr = $xPath->getData("/etomiteModule/install/documents");
  ?>
  <td class="row2"><?php echo strlen($tmpStr)>0 ? count(explode(",", $tmpStr)) : 0 ; ?></td>
    <td class="row2">
    <?php echo strlen($tmpStr)>0 ? $tmpStr : "-"; ?>
  </td>
  </tr>

</table>
<br />
<?php
// reset the Xpath
$xPath->reset();
// end of XPath stuff

    }
  }
  closedir($dir);
}

?>
  </div>
  </div>


</div>

