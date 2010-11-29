<?php
// messages.static.action.php
// Modified in 0.6.1 by Ralph
// Modified 2008-03-23 [v1.0] by Ralph to use system date|time formatting
// Modified 2008-05-10 [v1.1] by Ralph Dahlgren
// - HTML markup improvements

if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);

if(($_SESSION['permissions']['messages'] != 1) && ($_REQUEST['a'] == 10)) {
  $e->setError(3);
  $e->dumpError();
}
?>

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

<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['messages_title']; ?>
  </span>
</div>

<!--<link type="text/css" rel="stylesheet" href="media/style/tabs.css" />-->
<script type="text/javascript" src="media/script/tabpane.js"></script>

<div class="tab-pane" id="messagesPane">

<script type="text/javascript">
  tpSettings = new WebFXTabPane(document.getElementById("messagesPane"));
</script>

<?php if(isset($_REQUEST['id']) && $_REQUEST['m']=='r') { ?>

<!-- Read Messages -->
<div class="tab-page" id="tabPage1">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['messages_read_message']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage1"));
  </script>

  <div class="sectionBody" id="lyr3">
  <?php
  $sql = "SELECT * FROM $dbase.".$table_prefix."user_messages WHERE $dbase.".$table_prefix."user_messages.id=".$_REQUEST['id'];
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit!=1) {
    echo "Wrong number of messages returned!";
  } else {
    $message=mysql_fetch_assoc($rs);
    if($message['recipient']!=$_SESSION['internalKey']) {
      echo $_lang['messages_not_allowed_to_read'];
    } else {
      // output message!
      // get the name of the sender
      $sender = $message['sender'];
      if($sender==0) {
        $sendername = $_lang['messages_system_user'];
      } else {
        $sql = "SELECT username FROM $dbase.".$table_prefix."manager_users WHERE id=$sender";
        $rs2 = mysql_query($sql);
        $row2 = mysql_fetch_assoc($rs2);
        $sendername = $row2['username'];
      }
  ?>

  <table style="width:100%;" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="2">
        <div>
          <span class="floatLeft">
            <img src="media/images/_tx_.gif" width="1" height="5"><br />
	<input type="button" onClick="document.location.href='index.php?a=10&t=c&m=rp&id=<?php echo $message['id']; ?>'" value="<?php echo $_lang['messages_reply']; ?>" />
	<input type="button" onClick="document.location.href='index.php?a=10&t=c&m=f&id=<?php echo $message['id']; ?>'" value="<?php echo $_lang['messages_forward']; ?>" />
	<input type="button" onClick="document.location.href='index.php?a=65&id=<?php echo $message['id']; ?>'" value="<?php echo $_lang["delete"]; ?>" />

          </span>
        </div>
    </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td style="width: 100px;"><b><?php echo $_lang['messages_from']; ?>:</b></td>
      <td style="width: auto;"><?php echo $sendername; ?></td>
    </tr>
    <tr>
      <td><b><?php echo $_lang['messages_sent']; ?>:</b></td>
      <td><?php echo strftime($date_format.' @ '.$time_format, $message['postdate']+$server_offset_time); ?></td>
    </tr>
    <tr>
      <td><b><?php echo $_lang['messages_subject']; ?>:</b></td>
      <td><?php echo $message['subject']; ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">
    <?php
    // format the message :)
    $message = str_replace ("\n", "<br />", $message['message']);
    $dashcount = substr_count($message, "-----");
    $message = str_replace ("-----", "<i style='color:#666;'>", $message);
    for( $i=0; $i<$dashcount; $i++ ){
    $message .= "</i>";
    }

    echo "<div style=\"width:inherit; border:1px solid #000; padding:5px; background:#fff;\">$message</div>";
    ?>

    </td>
    </tr>
  </table>

  <?php
      // mark the message as read
      $sql = "UPDATE $dbase.".$table_prefix."user_messages SET $dbase.".$table_prefix."user_messages.messageread=1 WHERE $dbase.".$table_prefix."user_messages.id=".$_REQUEST['id'];
      $rs = mysql_query($sql);
    }
  }
  ?>
  </div>
</div>
<?php } ?>

<!-- Message Inbox -->
<div class="tab-page" id="tabPage2">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['messages_inbox']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage2"));
  </script>

  <div class="sectionBody">

  <?php
  // Get  number of rows
  $sql = "SELECT count(id) FROM $dbase.".$table_prefix."user_messages WHERE recipient=".$_SESSION['internalKey']."";
  $rs=mysql_query($sql);
  $countrows = mysql_fetch_assoc($rs);
  $num_rows = $countrows['count(id)'];

  // ==============================================================
  // Example Usage
  // Note: I make 2 query to the database for this exemple, it
  // could (and should) be made with only one query...
  // ==============================================================

  // If current position is not set, set it to zero
  if( !isset( $_REQUEST['int_cur_position'] ) || $_REQUEST['int_cur_position'] == 0 ){
    $int_cur_position = 0;
  } else {
    $int_cur_position = $_REQUEST['int_cur_position'];
  }

  // Number of result to display on the page, will be in the LIMIT of the sql query also
  $int_num_result = $number_of_messages;


  $extargv = "&a=10"; // extra argv here (could be anything depending on your page)

  include_once("includes/paginate.inc.php");
  // New instance of the Paging class, you can modify the color and the width of the html table
  $p = new Paging( $num_rows, $int_cur_position, $int_num_result, $extargv );

  // Load up the 2 array in order to display result
  $array_paging = $p->getPagingArray();
  $array_row_paging = $p->getPagingRowArray();

  // Display the result as you like...
  $pager .= $_lang['showing']." ". $array_paging['lower'];
  $pager .=  " ".$_lang['to']." ". $array_paging['upper'];
  $pager .=  " (". $array_paging['total']." ".$_lang['total'].")";
  $pager .=  "<br />". $array_paging['previous_link'] ."<<</a> " ;
  for( $i=0; $i<sizeof($array_row_paging); $i++ ){
    $pager .=  $array_row_paging[$i] ."&nbsp;";
  }
  $pager .=  $array_paging['next_link'] .">></a>";

  // The above example prints something like:
  // Results 1 to 20 of 597  <<< 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 >>>
  // Of course you can now play with array_row_paging in order to print only the results you would like...

  $sql = "SELECT * FROM $dbase.".$table_prefix."user_messages WHERE $dbase.".$table_prefix."user_messages.recipient=".$_SESSION['internalKey']." ORDER BY postdate DESC LIMIT ".$int_cur_position.", ".$int_num_result;
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit<1) {
    echo $_lang['messages_no_messages'];
  } else {
  echo $pager;
  $dotablestuff = 1;
  ?>

  <script type="text/javascript" src="media/script/sortabletable.js"></script>
    <table border=0 cellpadding=0 cellspacing=0  class="sort-table" id="table-1" style="width:100%;">
      <thead>
        <tr>
          <td width="12"></td>
          <td width="50%"><b><?php echo $_lang['messages_subject']; ?></b></td>
          <td><b><?php echo $_lang['messages_from']; ?></b></td>
          <td><b><?php echo $_lang['messages_private']; ?></b></td>
      <td width="150px"><b><?php echo $_lang['messages_sent']; ?></b></td>
        </tr>
      </thead>
      <tbody>

    <?php
      for ($i = 0; $i < $limit; $i++) {
        $message = mysql_fetch_assoc($rs);
        $sender = $message['sender'];
        if($sender==0) {
          $sendername = "[System]";
        } else {
          $sql = "SELECT username FROM $dbase.".$table_prefix."manager_users WHERE id=$sender";
          $rs2 = mysql_query($sql);
          $row2 = mysql_fetch_assoc($rs2);
          $sendername = $row2['username'];
        }
        $classname = ($i % 2) ? 'class="even" ' : 'class="odd" ';
        $messagestyle = $message['messageread']==0 ? "messageUnread" : "messageRead";
    ?>
      <tr <?php echo $classname; ?>>
      <td ><?php echo $message['messageread']==0 ? "<img src='media/images/icons/new1-09.gif'>" : ""; ?></td>
        <td class="<?php echo $messagestyle; ?>" style="cursor: pointer; text-decoration: underline;" onClick="
document.location.href='index.php?a=10&id=<?php echo $message['id']; ?>&m=r';">
          <?php echo $message['subject']; ?>
        </td>
      <td ><?php echo $sendername; ?></td>
      <td ><?php echo $message['private']==0 ? "No" : "Yes"; ?></td>
        <td ><?php echo strftime($date_format.' @ '.$time_format, $message['postdate']+$server_offset_time); ?></td>
      </tr>

      <?php
        }
    }

  if($dotablestuff==1) { ?>

  </tbody>
  </table>

  <script type="text/javascript">
  var st1 = new SortableTable(document.getElementById("table-1"),
    ["None", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "None"]);

  function addClassName(el, sClassName) {
    var s = el.className;
    var p = s.split(" ");
    var l = p.length;
    for (var i = 0; i < l; i++) {
      if (p[i] == sClassName)
        return;
    }
    p[p.length] = sClassName;
    el.className = p.join(" ");

  }

  function removeClassName(el, sClassName) {
    var s = el.className;
    var p = s.split(" ");
    var np = [];
    var l = p.length;
    var j = 0;
    for (var i = 0; i < l; i++) {
      if (p[i] != sClassName)
        np[j++] = p[i];
    }
    el.className = np.join(" ");
  }

  st1.onsort = function () {
    var rows = st1.tBody.rows;
    var l = rows.length;
    for (var i = 0; i < l; i++) {
      removeClassName(rows[i], i % 2 ? "odd" : "even");
      addClassName(rows[i], i % 2 ? "even" : "odd");
    }
  };
  </script>

  <?php } ?>

  </div>
</div>
<!-- Message Inbox -->

<!-- Compose Message -->
<div class="tab-page" id="tabPage3">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['messages_compose']; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage3"));
  </script>

  <div class="sectionBody">

  <?php
  if(($_REQUEST['m']=='rp' || $_REQUEST['m']=='f') && isset($_REQUEST['id'])) {
    $sql = "SELECT * FROM $dbase.".$table_prefix."user_messages WHERE $dbase.".$table_prefix."user_messages.id=".$_REQUEST['id'];
    $rs = mysql_query($sql);
    $limit = mysql_num_rows($rs);
    if($limit!=1) {
      echo "Wrong number of messages returned!";
    } else {
      $message=mysql_fetch_assoc($rs);
      if($message['recipient']!=$_SESSION['internalKey']) {
        echo $_lang['messages_not_allowed_to_read'];
      } else {
        // output message!
        // get the name of the sender
        $sender = $message['sender'];
        if($sender==0) {
          $sendername = "[System]";
        } else {
          $sql = "SELECT username FROM $dbase.".$table_prefix."manager_users WHERE id=$sender";
          $rs2 = mysql_query($sql);
          $row2 = mysql_fetch_assoc($rs2);
          $sendername = $row2['username'];
        }
        $subjecttext = $_REQUEST['m']=='rp' ? "Re: " : "Fwd: ";
        $subjecttext .= $message['subject'];
        $messagetext = "\n\n\n-----\n".$_lang['messages_from'].": $sendername\n".$_lang['messages_sent'].": ".strftime($date_format.' @ '.$time_format, $message['postdate']+$server_offset_time)."\n".$_lang['messages_subject'].": ".$message['subject']."\n\n".$message['message'];
        if($_REQUEST['m']=='rp') {
          $recipientindex = $message['sender'];
        }
      }
    }
  }
  ?>

  <script type="text/javascript">
  function hideSpans(showSpan) {
    document.getElementById("userspan").style.display="none";
    document.getElementById("groupspan").style.display="none";
    document.getElementById("allspan").style.display="none";
    if(showSpan==1) {
      document.getElementById("userspan").style.display="block";
    }
    if(showSpan==2) {
      document.getElementById("groupspan").style.display="block";
    }
    if(showSpan==3) {
      document.getElementById("allspan").style.display="block";
    }
  }
  </script>

  <form action="index.php?a=66" method="post" name="messagefrm">
    <fieldset style="width:auto;">
    <legend><b><?php echo $_lang['messages_send_to']; ?>:</b></legend>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
      <input type=radio name="sendto" value="u" checked onClick='hideSpans(1);'><?php echo $_lang['messages_user']; ?>&nbsp;&nbsp;&nbsp;
      <input type=radio name="sendto" value="g" onClick='hideSpans(2);'><?php echo $_lang['messages_group']; ?>&nbsp;&nbsp;&nbsp;
      <input type=radio name="sendto" value="a" onClick='hideSpans(3);'><?php echo $_lang['messages_all']; ?>&nbsp;&nbsp;<br />
      <span id='userspan' style="display:block;"> <?php echo $_lang['messages_select_user']; ?>:&nbsp;
      <?php
      // get all usernames
      $sql = "SELECT username, id FROM $dbase.".$table_prefix."manager_users";
      $rs = mysql_query($sql);
      ?>
      <select name="user" class="inputBox" style="width:150px">
      <?php
        while ($row = mysql_fetch_assoc($rs)) {
      ?>
          <option value="<?php echo $row['id']; ?>" ><?php echo $row['username']; ?></option>
    <?php } ?>
      </select>
      </span>
      <span id='groupspan' style="display:none;"> <?php echo $_lang['messages_select_group']; ?>:&nbsp;
      <?php
      // get all usernames
      $sql = "SELECT name, id FROM $dbase.".$table_prefix."user_roles";
      $rs = mysql_query($sql);
      ?>
      <select name="group" class="inputBox" style="width:150px">
      <?php
      while ($row = mysql_fetch_assoc($rs)) {
        ?>
        <option value="<?php echo $row['id']; ?>" ><?php echo $row['name']; ?></option>
      <?php } ?>
      </select>
      </span>
      <span id='allspan' style="display:none;"></span>
        </td>
        </tr>
      </table>
      </fieldset>

    <fieldset style="width:auto;">
    <legend><b><?php echo $_lang['messages_message']; ?>:</b></legend>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><?php echo $_lang['messages_subject']; ?>:</td>
        <td><input name="messagesubject" type=text class="inputBox" style="width: 500px;" maxlength="60" value="<?php echo $subjecttext; ?>"></td>
      </tr>
      <tr>
        <td valign="top"><?php echo $_lang['messages_message']; ?>:</td>
        <td><textarea name="messagebody" style="width:98%; height: 200px;" class="inputBox"><?php echo $messagetext; ?></textarea></td>
      </tr>
      <tr>
        <td></td>
      </tr>
    </table>

      <div>
        <span class="floatLeft">
          <img src="media/images/_tx_.gif" width="1" height="5"><br />
          <input type="submit" name="submit" value="<?php echo $_lang['messages_send']; ?>" onclick="documentDirty=false;" />
          <input type="button" name="cancel" value="<?php echo $_lang['cancel']; ?>" onClick="document.location.href='index.php?a=10&t=c';" />
        </span>
      </div>

    </fieldset>
  </form>
  </div>
</div>
</div>

<?php
// count messages again, as any action on the messages page may have altered the message count
$sql="SELECT count(*) FROM $dbase.".$table_prefix."user_messages where recipient=".$_SESSION['internalKey']." and messageread=0;";
$rs = mysql_query($sql);
$row = mysql_fetch_assoc($rs);
$_SESSION['nrnewmessages'] = $row['count(*)'];
$sql="SELECT count(*) FROM $dbase.".$table_prefix."user_messages where recipient=".$_SESSION['internalKey']."";
$rs = mysql_query($sql);
$row = mysql_fetch_assoc($rs);
$_SESSION['nrtotalmessages'] = $row['count(*)'];
$messagesallowed = $_SESSION['permissions']['messages'];
?>

<script type="text/javascript">
  function msgCountAgain() {
    try {
      top.scripter.startmsgcount(<?php echo $_SESSION['nrnewmessages'] ; ?>,<?php echo $_SESSION['nrtotalmessages'] ; ?>,<?php echo $messagesallowed ; ?>);
    } catch(oException) {
      vv = window.setTimeout('msgCountAgain()',1500);
    }
  }
  v = setTimeout('msgCountAgain()', 1500); // do this with a slight delay so it overwrites msgCount()
</script>
