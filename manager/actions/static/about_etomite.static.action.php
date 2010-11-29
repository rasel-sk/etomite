<?php
if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);
?>

<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name ;?> - <?php echo $_lang['about_title']; ?>
  </span>
</div>

<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['about_title']; ?>
</div>

<div class="sectionBody">
  <?php echo $_lang['about_msg']; ?>
</div>
