<?php
// settings.inc.php
// grab the settings from the database.
// Last Modified 2008-04-18 [v1.0] by Ralph A. Dahlgren

$settings = array();
$sql = "SELECT setting_name, setting_value FROM $dbase.".$table_prefix."system_settings";
$rs = mysqli_query($etomiteDBConn, $sql);
$number_of_settings = mysqli_num_rows($rs);

while ($row = mysqli_fetch_assoc($rs)) {
  $settings[$row['setting_name']] = $row['setting_value'];
}

extract($settings, EXTR_OVERWRITE);

?>
