<?php
// logging.static.action.php
// Modified 2008-03-22 [v1.0] by Ralph for jscalendar-1.0 integration and more...
// Modified 2008-05-08 by Ralph to fix minor bugs

if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);

if($_SESSION['permissions']['logs']!=1 && $_REQUEST['a']==55)
{
  $e->setError(3);
  $e->dumpError();
}
?>

<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="media/jscalendar-1.0/calendar-etomite.css" title="etomite"/>
<!-- main calendar program -->
<script type="text/javascript" src="media/jscalendar-1.0/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="media/jscalendar-1.0/lang/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes
    adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="media/jscalendar-1.0/calendar-setup.js"></script>
<!-- END::HTML head section -->

<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name." - ". $_lang["view_logging"]; ?>
  </span>
</div>

<div class="sectionHeader">
  <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['audit_trail_heading']; ?>
</div>

<div class="sectionBody" id="lyr1">
<p><?php echo $_lang['audit_trail_help']; ?></p>
<form action='index.php?a=13' name="logging" method='POST'>
<table border="0"cellpadding="2" cellspacing="0" align="center">
<thead>

  <tr>
    <td width="200"><b><?php echo $_lang['audit_trail_field']; ?></b></td>
    <td align="right"><b><?php echo $_lang['audit_trail_value']; ?></b></td>
  </tr>

</thead>
<tbody>

  <tr class="odd">
    <td><b><?php echo $_lang['audit_trail_user']; ?></b></td>
    <td align="right">

<?php
// get all users currently in logging
$sql = "SELECT DISTINCT(username) AS name, internalKey FROM $dbase.".$table_prefix."manager_log";
$rs = mysql_query($sql);
?>

    <select name="selecteduser" class="inputBox" style="width:240px">
    <option value="0"><?php echo $_lang['audit_trail_anyall']; ?></option>

<?php
while ($row = mysql_fetch_assoc($rs)) {
  $seletedUser = $row['internalKey']==$_REQUEST['selecteduser'] ? "selected='selected'" : "" ;
?>

    <option value="<?php echo $row['internalKey']; ?>" <?php echo $seletedUser; ?>><?php echo $row['name']; ?></option>

<?php
}
?>

    </select>
    </td>
  </tr>

  <tr class="even">
    <td><b><?php echo $_lang['audit_trail_action']; ?></b></td>
    <td align="right">
      <select name="action" class='inputBox' style='width:240px;'>
        <option value="0"><?php echo $_lang['audit_trail_anyall']; ?></option>

<?php
include_once("includes/actionlist.inc.php");
for($i = 1; $i < 1000; $i++)
{
  $actionname = getAction($i);
  $seletedAction = ($_REQUEST['action'] == $i) ? "selected='selected'" : "" ;
  if($actionname!="Idle")
  {
    $actions[$i] = $actionname;
    echo "        <option value='$i' " . $seletedAction . ">$i - $actionname</option>\n";
  }
}
?>
      </select>
    </td>
  </tr>

  <tr class="odd">
    <td><b><?php echo $_lang['audit_trail_item_id']; ?></b></td>
    <td align="right">

<?php
// get all users currently in logging
$sql = "SELECT DISTINCT(itemid) AS item, itemid FROM $dbase.".$table_prefix."manager_log";
$rs = mysql_query($sql);
?>

    <select name="itemid" class="inputBox" style="width:240px">
    <option value="0"><?php echo $_lang['audit_trail_anyall']; ?></option>

<?php
while ($row = mysql_fetch_assoc($rs))
{
  $selectedtext = $row['itemid']==$_REQUEST['itemid'] ? "selected='selected'" : "" ;
?>

    <option value="<?php echo $row['itemid']; ?>" <?php echo $selectedtext; ?>><?php echo $row['item']; ?></option>

<?php
}
?>

    </select>
    </td>
  </tr>

  <tr class="even">
    <td><b><?php echo $_lang['audit_trail_item_name']; ?></b></td>
    <td align="right">

<?php
// get all users currently in logging
$sql = "SELECT DISTINCT(itemname), itemname FROM $dbase.".$table_prefix."manager_log ORDER BY itemname;";
$rs = mysql_query($sql);
?>

    <select name="itemname" class="inputBox" style="width:240px">
    <option value="0"><?php echo $_lang['audit_trail_anyall']; ?></option>

<?php
while ($row = mysql_fetch_assoc($rs))
{
  $selectedtext = $row['itemname']==$_REQUEST['itemname'] ? "selected='selected'" : "" ;
?>

    <option value="<?php echo $row['itemname']; ?>" <?php echo $selectedtext; ?>><?php echo $row['itemname']; ?></option>

<?php
}
?>

    </select>
    </td>
  </tr>

  <tr class="odd">
    <td><b><?php echo $_lang['audit_trail_msg']; ?></b></td>
    <td align="right">
      <input type=text name='message' class="inputbox" style="width:240px" value="<?php echo $_REQUEST['message']; ?>">
    </td>
  </tr>

  <tr class="even">
    <td><b><?php echo $_lang['audit_trail_date_from']; ?></b></td>
        <td>
          <input type=hidden name='datefrom' class="inputbox" style="width:100px" value="<?php echo isset($_REQUEST['datefrom']) ? $_REQUEST['datefrom'] : "" ; ?>">
          <img id="datefrom_cal" src="media/images/icons/cal.gif" width="16" height="16" border="0" style="cursor:pointer; cursor:hand">
          <a onClick="document.logging.datefrom.value=''; document.getElementById('datefrom_show').innerHTML='<i>(not set)</i>'; return true;" onMouseOver="window.status='Don\'t set a date'; return true;" onMouseOut="window.status=''; return true;" style="cursor:pointer; cursor:hand"><img src="media/images/icons/cal_nodate.gif" width="16" height="16" border="0" alt="No date"></a>
          <span id="datefrom_show" style="font-weight: bold;"><?php echo isset($_REQUEST['datefrom']) ? $_REQUEST['datefrom'] : "<i>(not set)</i>" ; ?></span>
    </td>
  </tr>

  <tr class="odd">
    <td><b><?php echo $_lang['audit_trail_date_to']; ?></b></td>
    <td>
      <input type=hidden name='dateto' class="inputbox" style="width:100px" value="<?php echo isset($_REQUEST['dateto']) ? $_REQUEST['dateto'] : "" ; ?>">
      <img id="dateto_cal" src="media/images/icons/cal.gif" width="16" height="16" border="0" style="cursor:pointer; cursor:hand">
      <a onClick="document.logging.dateto.value=''; document.getElementById('dateto_show').innerHTML='<i>(not set)</i>'; return true;" onMouseOver="window.status='Don\'t set a date'; return true;" onMouseOut="window.status=''; return true;" style="cursor:pointer; cursor:hand"><img src="media/images/icons/cal_nodate.gif" width="16" height="16" border="0" alt="No date"></a>
      <span id="dateto_show" style="font-weight: bold;"><?php echo isset($_REQUEST['dateto']) ? $_REQUEST['dateto'] : "<i>(not set)</i>" ; ?></span>
    </td>
  </tr>

  <tr class="even">
    <td><strong><?php echo $_lang['audit_trail_dates_sorted']; ?></strong></td>
    <td>
      <select name="datesortdir" class="inputbox">
        <option value="DESC"<?php echo ($_REQUEST['datesortdir']=='DESC')? ' selected="selected"' : '' ;?>><?php echo $_lang['audit_trail_sort_desc']; ?></option>
        <option value="ASC"<?php echo ($_REQUEST['datesortdir']=='ASC')? ' selected="selected"' : '' ;?>><?php echo $_lang['audit_trail_sort_asc']; ?></option>
      </select>
    </td>
  </tr>

  <tr class="odd">
    <td><b><?php echo $_lang['audit_trail_nr']; ?></b></td>
    <td>
      <input type=text name='nrresults' class="inputbox" style="width:100px" value="<?php echo isset($_REQUEST['nrresults']) ? $_REQUEST['nrresults'] : $number_of_logs; ?>"><img src="media/images/_tx_.gif" width="18" height="16" border="0">
    </td>
  </tr>

  <tr class="even">
    <td colspan="2">
      <span>
        <input type="submit" name='log_submit' value="<?php echo $_lang['search']; ?>" />
        <input type="button" name="cancel" value="<?php echo $_lang['cancel']; ?>" onclick="document.location.href='index.php?a=2';" />
        <input type="button" name='clear_logs' value="<?php echo $_lang['audit_trail_empty_logs']; ?>" onclick="if(confirm('<?php echo $_lang['audit_trail_empty_logs_confirm']; ?>')==true) document.location.href='index.php?a=55';" />
      </span>
    </td>
  </tr>

  </tbody>
</table>
</form>
</div>

<script type="text/javascript">

Calendar.setup({
  inputField  : "datefrom",       // id of the input field
  ifFormat    : "%s",             // format of the input field
  displayArea : "datefrom_show",  // id of the display element
  daFormat    : "<?php echo $date_format; ?>", // format for the display element
  showsTime   : false,             // will display a time selector
  button      : "datefrom_cal",   // trigger for the calendar (button ID)
  electric    : false,            // instant update
  singleClick : false,            // single-click mode
  weekNumbers : false,            // display week numbers
  step        : 1,                // increment for drop-downs
  align       : "tR"              // calendar alignment
});

Calendar.setup({
  inputField  : "dateto",         // id of the input field
  ifFormat    : "%s",             // format of the input field
  displayArea : "dateto_show",    // id of the display element
  daFormat    : "<?php echo $date_format; ?>", // format for the display element
  showsTime   : false,             // will display a time selector
  button      : "dateto_cal",     // trigger for the calendar (button ID)
  electric    : false,            // instant update
  singleClick : false,            // single-click mode
  weekNumbers : false,            // display week numbers
  step        : 1,                // increment for drop-downs
  align       : "tR"              // calendar alignment
});

</script>

<div class="sectionHeader"><?php echo $_lang['audit_trail_results'];?></div><div class="sectionBody" id="lyr2">

<?php
if(isset($_REQUEST['log_submit']))
{
// get the selections the user made.
$sqladd ="";
if($_REQUEST['selecteduser']!=0) $sqladd .= " AND internalKey=".$_REQUEST['selecteduser'];
if($_REQUEST['action']!=0) $sqladd .= " AND action=".$_REQUEST['action'];
if($_REQUEST['itemid']!=0 || $_REQUEST['itemid']=="-") $sqladd .= " AND itemid='".$_REQUEST['itemid']."'";
if($_REQUEST['itemname']) $sqladd .= " AND itemname='".$_REQUEST['itemname']."'";
if($_REQUEST['message']!="") $sqladd .= " AND message LIKE '%".$_REQUEST['message']."%'";
// date stuff
if($_REQUEST['datefrom']!="") $sqladd .= " AND timestamp>=".$_REQUEST['datefrom'];
if($_REQUEST['dateto']!="") $sqladd .= " AND timestamp<=" . ($_REQUEST['dateto'] + (24*60*60-1));
if($_REQUEST['datesortdir'] != '') $sqladd .= " ORDER BY timestamp " . $_REQUEST['datesortdir'];

// Get  number of rows
$sql = "SELECT count(id) FROM $dbase.".$table_prefix."manager_log WHERE 1=1";
$sql .= $sqladd;
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
$int_num_result = is_int($_REQUEST['nrresults']) ? $_REQUEST['nrresults'] : $number_of_logs ;

$extargv =   "&a=13&searchuser=".$_REQUEST['searchuser']."&action=".$_REQUEST['action'].
      "&itemid=".$_REQUEST['itemid']."&itemname".$_REQUEST['itemname']."&message=".
      $_REQUEST['message']."&dateto=".$_REQUEST['dateto']."&datefrom=".
      $_REQUEST['datefrom']."&nrresults=".$int_num_result."&log_submit=".$_REQUEST['log_submit']; // extra argv here (could be anything depending on your page)


// build the sql
$sql = "SELECT * FROM $dbase.".$table_prefix."manager_log WHERE 1=1";
$sql .= $sqladd;
$sql .= " LIMIT ".$int_cur_position.", ".$int_num_result;

$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit<1)
{
  echo "<p>".$_lang['audit_trail_no_results']."<br />\n</div></div>";
  exit;
} else {

echo $_lang['audit_trail_sort_help']."<p>";

include_once("includes/paginate.inc.php");
// New instance of the Paging class, you can modify the color and the width of the html table
$p = new Paging( $num_rows, $int_cur_position, $int_num_result, $extargv );

// Load up the 2 array in order to display result
$array_paging = $p->getPagingArray();
$array_row_paging = $p->getPagingRowArray();
$current_row = $int_cur_position/$int_num_result;

// Display the result as you like...
print $_lang['audit_trail_paging_showing']. $array_paging['lower'];
print $_lang['audit_trail_paging_to']. $array_paging['upper'];
print " (". $array_paging['total'].$_lang['audit_trail_paging_total'].")";
print "<br />". $array_paging['first_link'] .$_lang['audit_trail_paging_first']."</a> " ;
print $array_paging['previous_link'] .$_lang['audit_trail_paging_previous']."</a> " ;
$pagesfound = sizeof($array_row_paging);
if($pagesfound>6)
{
  print $array_row_paging[$current_row-1]; // ."&nbsp;";
  print $array_row_paging[$current_row]; // ."&nbsp;";
  print $array_row_paging[$current_row+1]; // ."&nbsp;";
}
else
{
  for( $i=0; $i<$pagesfound; $i++ )
  {
    print $array_row_paging[$i] ."&nbsp;";
  }
}
print $array_paging['next_link'] .$_lang['audit_trail_paging_next']."</a> ";
print $array_paging['last_link'] .$_lang['audit_trail_paging_last']."</a>";
// The above exemple print somethings like:
// Results 1 to 20 of 597  <<< 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 >>>
// Of course you can now play with array_row_paging in order to print
// only the results you would like...
?>

<p>
  <script type="text/javascript" src="media/script/sortabletable.js"></script>
  <table border=0 cellpadding=2 cellspacing=0  class="sort-table" id="table-1" width="95%">
    <thead>
      <tr>
        <td><b><?php echo $_lang['audit_trail_user']; ?></b></td>
        <td><b><?php echo $_lang['audit_trail_action']; ?></b></td>
        <td><b><?php echo $_lang['audit_trail_item_id']; ?></b></td>
        <td><b><?php echo $_lang['audit_trail_item_name']; ?></b></td>
        <td><b><?php echo $_lang['audit_trail_msg']; ?></b></td>
        <td><b><?php echo $_lang['audit_trail_time']; ?></b></td>
      </tr>
    </thead>
    <tbody>

<?php
      for ($i = 0; $i < $limit; $i++) {
        $logentry = mysql_fetch_assoc($rs);
        $classname = ($i % 2) ? 'class="even" ' : 'class="odd" ';
?>

    <tr <?php echo $classname; ?>>
      <td class="cell"><?php echo ucfirst($logentry['username'])." (".$logentry['internalKey'].")"; ?></td>
      <td class="cell"><?php echo $logentry['action']; ?></td>
      <td class="cell"><?php echo $logentry['itemid']=="-" ? "" : $logentry['itemid'] ; ?></td>
      <td class="cell"><?php echo $logentry['itemname']; ?></td>
      <td class="cell"><?php echo $logentry['message']; ?></td>
      <td class="cell"><?php echo strftime($date_format.', %H:%M:%S', $logentry['timestamp']+$server_offset_time); ?></td>
    </tr>

<?php
      }
    }
?>

    </tbody>
    </table>

<script type="text/javascript">

var st1 = new SortableTable(document.getElementById("table-1"),
  ["CaseInsensitiveString", "Number", "Number", "CaseInsensitiveString", "CaseInsensitiveString", "Date"]);

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

</div>

<?php
}
else
{
  echo $_lang['audit_trail_no_search'];
}
?>

</div>
