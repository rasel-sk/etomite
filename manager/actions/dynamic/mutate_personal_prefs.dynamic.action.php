<?php 
if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);
?>

<div class="subTitle">
<ul id="navlist">
	<li><span class="etomiteButton" onClick="document.location.href='index.php?a=2';"><img src="media/images/icons/cancel.gif" align="absmiddle"> <?php echo $_lang['cancel']; ?></span></li>
</ul>

<span class="floatRight"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['personal_prefs_title']; ?></span>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['personal_prefs_title']; ?></div><div class="sectionBody">
<?php echo $_lang['personal_prefs_message']; ?>
</div>
