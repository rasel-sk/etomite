<?php
// user_management.dynamic.action.php
// User and Role Management Module
// Modified 2008-05-10 [v1.1] by Ralph Dahlgren
// - HTML markup improvements

if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);
?>

<div class="subTitle">
  <span class="floatRight"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['user_management_title']; ?></span>
</div>

<!-- Tab Scripting Header -->
<script type="text/javascript">
function checkIM() {
  im_on = document.settings.im_plugin[0].checked; // check if im_plugin is on
  if(im_on==true) {
    showHide(/imRow/, 1);
  }
}

function showHide(what, onoff){

  var all = document.getElementsByTagName( "*" );
  var l = all.length;
  var buttonRe = what;
  var id, el, stylevar;

  if(onoff==1) {
    stylevar = "<?php echo $displayStyle; ?>";
  } else {
    stylevar = "none";
  }

  for ( var i = 0; i < l; i++ ) {
    el = all[i]
    id = el.id;
    if ( id == "" ) continue;
    if (buttonRe.test(id)) {
      el.style.display = stylevar;
    }
  }
}
</script>

<!--<link type="text/css" rel="stylesheet" href="media/style/tabs.css" />-->
<script type="text/javascript" src="media/script/tabpane.js"></script>

<div class="tab-pane" id="usersRolesPane">

<script type="text/javascript">
  tpSettings = new WebFXTabPane(document.getElementById("usersRolesPane"));
</script>
<!-- End: Tab Scripting Header -->

<!-- User Management -->
<div class="tab-page" id="tabPage1">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['user_management_title']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage1"));
  </script>

  <div class="sectionBody">
  <p class="menuHeader"><?php echo $_lang['user_management_msg']; ?></p>
  <br />
  <div>
    <img style="padding-right:5px;" src="media/images/misc/li.gif">
    <a href="index.php?a=11"><?php echo $_lang['new_user']; ?></a>
  </div>
  <br />
  <?php

  $sql = "select username, id from $dbase.".$table_prefix."manager_users order by username";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit<1){
    echo "The request returned no users!</div>";
    exit;
    include_once("includes/footer.inc.php");
  }
  for($i=0; $i<$limit; $i++) {
    $row = mysql_fetch_assoc($rs);
  ?>
    <div>
      <img style="padding-right:5px;" src="media/images/misc/li.gif">
      <a href="index.php?id=<?php echo $row['id']; ?>&a=12"><?php echo $row['username']; ?></a>
    </div>
  <?php
  }

  ?>
  </div>
</div>
<!-- End: User Management -->

<!-- Role Management -->
<div class="tab-page" id="tabPage2">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['role_management_title']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage2"));
  </script>

  <div class="sectionBody">
  <p class="menuHeader"><?php echo $_lang['role_management_msg']; ?></p>
  <br />
  <div>
    <img style="padding-right:5px;" src="media/images/misc/li.gif">
    <a href="index.php?a=38"><?php echo $_lang['new_role']; ?></a>
  </div>
  <br />
  <?php

  $sql = "select name, id, description from $dbase.".$table_prefix."user_roles order by name";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit<1){
    echo "The request returned no roles!</div>";
    exit;
    include_once("includes/footer.inc.php");
  }
  for($i=0; $i<$limit; $i++) {
    $row = mysql_fetch_assoc($rs);
    if($row['id']==1) {
  ?>
  <div>
    <img style="padding-right:5px;" src="media/images/misc/li.gif">
    <span style="width: 200px"><i><?php echo $row['name']; ?></i></span> - <i><?php echo $_lang['administrator_role_message']; ?></i>
  </div>
  <?php
    } else {
  ?>
    <li><span style="width: 200px"><a href="index.php?id=<?php echo $row['id']; ?>&a=35"><?php echo $row['name']; ?></a></span> - <?php echo $row['description']; ?></li>
  <?php
    }
  }

  ?>
  </div>
</div>
<!-- End: Role Management -->
</div><!-- usersRolesPane -->
