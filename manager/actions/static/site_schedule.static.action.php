<?php
// site_schedule.static.action.php
// Publish & Un-Publish Events Management
// Modified in 0.6.1 by Ralph
// Modified 2008-03-22 [v1.0] by Ralph to use system date|time formatting

if(IN_ETOMITE_SYSTEM!="true") die($_lang["include_ordering_error"]);
/* if($_SESSION['permissions']['edit_document']!=1 && $_REQUEST['a']==51) {  $e->setError(3);
  $e->dumpError();
} */
?>

<div class="subTitle">
  <span class="floatRight">
    <img src="media/images/_tx_.gif" width="1" height="5"><br />
    <?php echo $site_name." - ".$_lang["site_schedule"]; ?>
  </span>
</div>

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

<div class="tab-pane" id="settingsPane">

<script type="text/javascript">
  tpSettings = new WebFXTabPane(document.getElementById("settingsPane"),0);
</script>

<script type="text/javascript" src="media/script/sortabletable.js"></script>

<!-- Publish Documents Panel -->
<div class="tab-page" id="tabPage1">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["publish_document"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage1"));
  </script>

  <div class="sectionBody" id="lyr1">
  <?php
  //$db->debug = true;
  $sql = "SELECT id, pagetitle, pub_date FROM $dbase.".$table_prefix."site_content WHERE pub_date > ".time()." ORDER BY pub_date ASC";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit<1) {
    echo "<p>".$_lang["no_pending_publish"]."</p>";
  } else {
  ?>
    <table border=0 cellpadding=2 cellspacing=0  class="sort-table" id="table-1" width="100%">
      <thead>
        <tr>
          <td><b>Document</b></td>
          <td><b>ID</b></td>
          <td><b>Publish date</b></td>
        </tr>
      </thead>
      <tbody>
  <?php
    for ($i=0;$i<$limit;$i++) {
      $row = mysql_fetch_assoc($rs);
      $classname = ($i % 2) ? 'class="even" ' : 'class="odd" ';
  ?>
      <tr <?php echo $classname; ?>>
        <td class="cell"><?php echo $row['pagetitle'] ;?></td>
        <td class="cell"><?php echo $row['id'] ;?></td>
        <td class="cell"><?php echo strftime($date_format." @ ".$time_format, $row['pub_date']+$server_offset_time) ;?></td>
      </tr>
  <?php
    }
  ?>
      </tbody>
    </table>
  <script type="text/javascript">

  var st1 = new SortableTable(document.getElementById('table-1'),['CaseInsensitiveString', 'Number', 'Date']);

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
  }
  </script>
  <?php
  }
  ?>

  </div>
</div>

<!-- Unpublish Documents Panel -->
<div class="tab-page" id="tabPage2">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["unpublish_document"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage2"));
  </script>

  <div class="sectionBody" id="lyr2">
  <?php
  //$db->debug = true;
  $sql = "SELECT id, pagetitle, unpub_date FROM $dbase.".$table_prefix."site_content WHERE unpub_date > ".time()." ORDER BY unpub_date ASC";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit<1) {
    echo "<p>".$_lang["no_pending_unpublish"]."</p>";
  } else {
  ?>
    <table border=0 cellpadding=2 cellspacing=0  class="sort-table" id="table-2" width="100%">
      <thead>
        <tr>
          <td><b>Document</b></td>
          <td><b>ID</b></td>
          <td><b>Unpublish date</b></td>
        </tr>
      </thead>
      <tbody>
  <?php
    for ($i=0;$i<$limit;$i++) {
      $row = mysql_fetch_assoc($rs);
      $classname = ($i % 2) ? 'class="even" ' : 'class="odd" ';
  ?>
      <tr <?php echo $classname; ?>>
        <td class="cell"><?php echo $row['pagetitle'] ;?></td>
      <td class="cell"><?php echo $row['id'] ;?></td>
        <td class="cell"><?php echo strftime($date_format." @ ".$time_format, $row['unpub_date']+$server_offset_time) ;?></td>
      </tr>
  <?php
    }
  ?>
    </tbody>
  </table>
  <script type="text/javascript">

  var st2 = new SortableTable(document.getElementById("table-2"),["CaseInsensitiveString", "Number", "Date"]);

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

  st2.onsort = function () {
    var rows = st2.tBody.rows;
    var l = rows.length;
    for (var i = 0; i < l; i++) {
      removeClassName(rows[i], i % 2 ? "odd" : "even");
      addClassName(rows[i], i % 2 ? "even" : "odd");
    }
  };
  </script>
  <?php
  }
  ?>

  </div>
</div>

<!-- All Documents Panel -->
<div class="tab-page" id="tabPage3">
  <div class="tab">
    <img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang["all_pending_documents"]; ?>
  </div>
  <script type="text/javascript">
    tpSettings.addTabPage(document.getElementById("tabPage3"));
  </script>

  <div class="sectionBody">
  <?php
  $sql = "SELECT id, pagetitle, pub_date, unpub_date FROM $dbase.".$table_prefix."site_content WHERE pub_date > 0 OR unpub_date > 0 ORDER BY id";
  $rs = mysql_query($sql);
  $limit = mysql_num_rows($rs);
  if($limit<1) {
    echo "<p>".$_lang["no_pending_documents"]."</p>";
  } else {
  ?>
    <table border=0 cellpadding=2 cellspacing=0  class="sort-table" id="table-3" width="100%">
      <thead>
        <tr>
          <td><b>Document</b></td>
          <td><b>ID</b></td>
          <td><b>Publish date</b></td>
          <td><b>Unpublish date</b></td>
        </tr>
      </thead>
      <tbody>
  <?php
    for ($i=0;$i<$limit;$i++) {
      $row = mysql_fetch_assoc($rs);
      $classname = ($i % 2) ? 'class="even" ' : 'class="odd" ';
  ?>
      <tr <?php echo $classname; ?>>
        <td class="cell"><?php echo $row['pagetitle'] ;?></td>
      <td class="cell"><?php echo $row['id'] ;?></td>
        <td class="cell"><?php echo $row['pub_date']==0 ? "" : strftime($date_format." @ ".$time_format, $row['pub_date']+$server_offset_time) ;?></td>
        <td class="cell"><?php echo $row['unpub_date']==0 ? "" : strftime($date_format." @ ".$time_format, $row['unpub_date']+$server_offset_time) ;?></td>
      </tr>
  <?php
    }
  ?>
    </tbody>
  </table>
  <script type="text/javascript">

  var st3 = new SortableTable(document.getElementById("table-3"),["CaseInsensitiveString", "Number", "Date", "Date"]);

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

  st3.onsort = function () {
    var rows = st3.tBody.rows;
    var l = rows.length;
    for (var i = 0; i < l; i++) {
      removeClassName(rows[i], i % 2 ? "odd" : "even");
      addClassName(rows[i], i % 2 ? "even" : "odd");
    }
  };
  </script>
  <?php
  }
  ?>
  </div>
</div>
</div><!-- settingsPane -->