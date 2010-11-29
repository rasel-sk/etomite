<?php
// save_template.processor.php

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['save_template'] != 1 && $_REQUEST['a'] == 20)
{
  $e->setError(3);
  $e->dumpError();
}

function isNumber($var)
{
  if(strlen($var) == 0)
  {
    return false;
  }
  for ($i=0; $i < strlen($var); $i++)
  {
    if( substr_count ("0123456789", substr ($var, $i, 1) ) == 0 )
    {
      return false;
    }
  }
  return true;
}

switch($_POST['mode'])
{
  case '19':
    //do stuff to save the new doc
    $template = mysql_escape_string($_POST['post']);
    $templatename = mysql_escape_string(htmlentities($_POST['templatename']));
    $description = mysql_escape_string(htmlentities($_POST['description']));
    $locked = $_POST['locked']=='on' ? 1 : 0 ;
    if($templatename=="")
    {
      $templatename = "Untitled template";
    }
    $sql = "INSERT INTO $dbase.".$table_prefix."site_templates(templatename, description, content, locked) VALUES('$templatename', '$description', '$template', '$locked');";
    $rs = mysql_query($sql);
    if(!$rs)
    {
      echo "\$rs not set! New template not saved!";
    }
    else
    {
      // get the id
      if(!$newid = mysql_insert_id())
      {
        echo "Couldn't get last insert key!";
        exit;
      }
      // empty cache
      include_once("cache_sync.class.processor.php");
      $sync = new synccache();
      $sync->setCachepath("../assets/cache/");
      $sync->setReport(false);
      $sync->emptyCache(); // first empty the cache
      // finished emptying cache - redirect
      if($_POST['stay'] != '')
      {
        $header="Location: index.php?a=16&id=$newid&r=2";
        header($header);
      }
      else
      {
        $header="Location: index.php?a=76&r=2";
        header($header);
      }
    }
  break;

  case '16':
    //do stuff to save the edited doc
    $template = mysql_escape_string($_POST['post']);
    $templatename = mysql_escape_string(htmlentities($_POST['templatename']));
    $description = mysql_escape_string(htmlentities($_POST['description']));
    $locked = $_POST['locked'] == 'on' ? 1 : 0 ;
    if($templatename == "")
    {
      $templatename = "Untitled template";
    }
    $id = $_POST['id'];
    $sql = "UPDATE $dbase.".$table_prefix."site_templates SET templatename='$templatename', description='$description', content='$template', locked='$locked' WHERE id=$id;";
    $rs = mysql_query($sql);
    if(!$rs)
    {
      echo "\$rs not set! Edited template not saved!";
    }
    else
    {
      // first empty the cache
      include_once("cache_sync.class.processor.php");
      $sync = new synccache();
      $sync->setCachepath("../assets/cache/");
      $sync->setReport(false);
      $sync->emptyCache();
      // finished emptying cache - redirect
      if($_POST['stay'] != '')
      {
        $header="Location: index.php?a=16&id=$id&r=2";
        header($header);
      }
      else
      {
        $header="Location: index.php?a=76&r=2";
        header($header);
      }
    }
  break;

  default: echo "You supposed to be here now?";
}
?>
