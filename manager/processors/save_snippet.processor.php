<?php
// save_snippet.processor.php

if(IN_ETOMITE_SYSTEM != "true")
{
  die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
}

if($_SESSION['permissions']['save_snippet'] != 1 && $_REQUEST['a'] == 24)
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
    if ( substr_count ("0123456789", substr ($var, $i, 1) ) == 0 )
    {
      return false;
    }
  }
  return true;
}

switch($_POST['mode'])
{
  case '23':
    //do stuff to save the new doc
    $snippet = mysql_escape_string($_POST['post']);
    $name = mysql_escape_string(htmlentities($_POST['name']));
    $description = mysql_escape_string(htmlentities($_POST['description']));
    $locked = $_POST['locked'] == 'on' ? 1 : 0 ;
    if($name == "")
    {
      $name = "Untitled snippet";
    }
    $sql = "INSERT INTO $dbase.".$table_prefix."site_snippets(name, description, snippet, locked) VALUES('".$name."', '".$description."', '".$snippet."', '".$locked."');";
    $rs = mysql_query($sql);
    if(!$rs)
    {
      echo "\$rs not set! New snippet not saved!";
    }
    else
    {
      // get the id
      if(!$newid=mysql_insert_id())
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
        $header="Location: index.php?a=22&id=$newid&r=2";
        header($header);
      }
      else
      {
        $header="Location: index.php?a=76&r=2";
        header($header);
      }
    }
  break;

  case '22':
    //do stuff to save the edited doc
    $snippet = mysql_escape_string($_POST['post']);
    $name = mysql_escape_string(htmlentities($_POST['name']));
    $description = mysql_escape_string(htmlentities($_POST['description']));
    $locked = $_POST['locked']=='on' ? 1 : 0 ;
    if($name == "")
    {
      $name = "Untitled snippet";
    }
    $id = $_POST['id'];
    $sql = "UPDATE $dbase.".$table_prefix."site_snippets SET name='".$name."', description='".$description."', snippet='".$snippet."', locked='".$locked."' WHERE id='".$id."';";
    $rs = mysql_query($sql);
    if(!$rs)
    {
      echo "\$rs not set! Edited snippet not saved!";
    }
    else
    {
      // empty cache
      include_once("cache_sync.class.processor.php");
      $sync = new synccache();
      $sync->setCachepath("../assets/cache/");
      $sync->setReport(false);
      $sync->emptyCache(); // first empty the cache
      // finished emptying cache - redirect
      if($_POST['stay'] != '')
      {
        $header="Location: index.php?a=22&id=$id&r=2";
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
