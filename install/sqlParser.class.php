<?php
// sqlParser.class.php
// MySQL Dump Parser
// SNUFFKIN/ Alex 2004
// Modified 2008-04-08 [v1.0] by Ralph A. Dahlgren

class SqlParser
{
  var $host, $dbname, $prefix, $user, $password, $mysqlErrors;
  var $conn, $installFailed, $sitename, $adminname, $adminpass;

  function SqlParser($host, $user, $password, $db, $prefix='test_', $adminname, $adminpass)
  {
    $this->host = $host;
    $this->dbname = $db;
    $this->prefix = $prefix;
    $this->user = $user;
    $this->password = $password;
    $this->adminpass = $adminpass;
    $this->adminname = $adminname;
  }

  function connect()
  {
    $this->conn = mysql_connect($this->host, $this->user, $this->password);
    mysql_select_db($this->dbname, $this->conn);
  }

  function process($filename)
  {
    $fh = fopen($filename, 'r');
    $idata = '';

    while (!feof($fh))
    {
      $idata .= fread($fh, 1024);
    }

    fclose($fh);
    $idata = str_replace("\r", '', $idata);

    $sql_array = split("\n\n", $idata);

    $num = 0;
    $timestamp = time();
    foreach($sql_array as $sql_entry)
    {
      $sql_do = trim($sql_entry, "\r\n; ");
      $sql_do = str_replace('{PREFIX}', $this->prefix, $sql_do);
      $sql_do = str_replace('{ADMIN}', $this->adminname, $sql_do);
      $sql_do = str_replace('{ADMINPASS}', $this->adminpass, $sql_do);
      $sql_do = str_replace('{TIMESTAMP}', $timestamp, $sql_do);

      // skip older style MySQL dump comments
      if (ereg('^\#', $sql_do)) continue;
      // skip newer style MySQL dump comments
      if (ereg('^\--', $sql_do)) continue;
      if($sql_do == null) continue;

      $num = $num + 1;
      mysql_query($sql_do, $this->conn);
      if(mysql_error())
      {
        $this->mysqlErrors[] = array("error" => mysql_error(), "sql" => $sql_do);
        $this->installFailed = true;
      }
    }
  }

  function close()
  {
    @mysql_close($this->conn);
  }
}

?>
