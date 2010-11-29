<?php

class udperms{

  var $user;
  var $document;
  var $role;
  
  function checkPermissions() {
    
    global $table_prefix;
    global $dbase;
    global $udperms_allowroot;

    $user = $this->user;
    $document = $this->document;
    $role = $this->role;

    if($role==1) {
      return true;  // administrator - grant all document permissions
    }
    
    $permissionsok = false;  // set permissions to false
    
    if($GLOBALS['use_udperms']==0 || $GLOBALS['use_udperms']=="" || !isset($GLOBALS['use_udperms'])) {
      return true; // permissions aren't in use
    }
    
    if($document==0 && $udperms_allowroot==1) {
      return true; // we are allowed to create documents at the root level, modified by Johan Larsson (Nalagar) 2006-10-11
    }

    // get the groups this user is a member of
    $sql = "SELECT * FROM $dbase.".$table_prefix."member_groups WHERE $dbase.".$table_prefix."member_groups.member = $user;";
    $rs = mysql_query($sql);
    $limit = mysql_num_rows($rs);
    if($limit<1) {
      return false;
    }
    for($i=0; $i < $limit; $i++) {
      $row = mysql_fetch_assoc($rs);
      $membergroups[$i] = $row['user_group'];
    }

    $list = implode(",", $membergroups);

    // get the permissions for the groups this user is a member of
    $sql = "SELECT * FROM $dbase.".$table_prefix."membergroup_access WHERE $dbase.".$table_prefix."membergroup_access.membergroup IN($list);";
    $rs = mysql_query($sql);
    $limit = mysql_num_rows($rs);
    if($limit<1) {
      return false;
    }
    
    for($i=0; $i < $limit; $i++) {
      $row = mysql_fetch_assoc($rs);
      $documentgroups[$i] = $row['documentgroup'];
    }
    
    $list = implode(",", $documentgroups);

    // get the groups this user has permissions for
    $sql = "SELECT * FROM $dbase.".$table_prefix."document_groups WHERE $dbase.".$table_prefix."document_groups.document_group IN($list);";
    $rs = mysql_query($sql);
    $limit = mysql_num_rows($rs);
    if($limit<1) {
      return false;
    }
    
    for($i=0; $i < $limit; $i++) {
      $row = mysql_fetch_assoc($rs);
      if($row['document']==$document) {
        $permissionsok = true;
      }
    }
    return $permissionsok;
  }
}

?>
