<?php
if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);
?>

<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name ;?>&nbsp;-&nbsp;<?php echo $_lang['help_title']; ?>
  </span>
</div>

<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['help_title']; ?>
</div>

<div class="sectionBody">
  <?php echo $_lang['help_msg']; ?>
</div>
