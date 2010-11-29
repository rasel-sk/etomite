<?php
if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);
?>
<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name; ?>&nbsp;-&nbsp;<?php echo $_lang['cleaningup']; ?>
  </span>
</div>

<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['cleaningup']; ?>
</div>

<div class="sectionBody">
  <p><?php echo $_lang['actioncomplete']; ?></p>

  <?php if($_REQUEST['r']==10) { ?>

  <script type="text/javascript">
    function goHome() {
      top.location.href="index.php";
      //top.location.reload();
    }
    x=window.setTimeout('goHome()',2000);
    </script>

    <?php } elseif($_REQUEST['dv']==1 && $_REQUEST['id']!='') { ?>

    <script type="text/javascript">
    function goHome() {
      document.location.href="index.php?a=3&id=<?php echo $_REQUEST['id']; ?>";
    }
    x=window.setTimeout('goHome()',2000);
    </script>

    <?php } else { ?>

    <script type="text/javascript">
    function goHome() {
      document.location.href="index.php?a=2";
    }
    x=window.setTimeout('goHome()',2000);
  </script>

  <?php } ?>
</div>
