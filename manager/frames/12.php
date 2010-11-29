<?php
// Generates bar at top of the manager page
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
$enable_debug=false;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>Frame 12</title>
  <link rel="stylesheet" type="text/css" href="media/style/style.css" />
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>">
</head>
<body class="topFrame"><img src='media/images/misc/topbarlogo.gif' class='topBarLogo' title='<?php echo $full_appname; ?>'/>
        <div id="tocText"></div>
        <span id="workText">&nbsp;<img src='media/images/icons/delete.gif' align='absmiddle' width='16' height='16'>&nbsp;<?php echo $_lang['working']; ?></span>
        <span id="buildText">&nbsp;&nbsp;<img src='media/images/icons/b02.gif' align='absmiddle' width='16' height='16'>&nbsp;<?php echo $_lang['loading_doc_tree']; ?></span>
        <span id="appnameText"><?php echo $full_appname; ?></span>

</body>
</html>
