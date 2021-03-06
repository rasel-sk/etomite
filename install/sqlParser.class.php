<?php
// sqlParser.class.php
// MySQL Dump Parser
// SNUFFKIN/ Alex 2004
// Modified 2008-04-08 [v1.0] by Ralph A. Dahlgren

class SqlParser
{
  var $host, $dbname, $prefix, $user, $password, $mysqlErrors;
  var $conn, $installFailed, $sitename, $adminname, $adminpass;

  function __construct($host, $user, $password, $db, $prefix='test_', $adminname, $adminpass, $host_port)
  {
    $this->host = $host;
	$this->host_port = $host_port;
    $this->dbname = $db;
    $this->prefix = $prefix;
    $this->user = $user;
    $this->password = $password;
    $this->adminpass = $adminpass;
    $this->adminname = $adminname;
  }

  function connect()
  {			
    echo "<p>Creating connection to the database (SQL parse): ";
    if (!$this->conn = mysqli_connect($this->host, $this->user, $this->password, null, $this->host_port)) {
      echo "<span class='notok'>Failed!</span></p><p>Please check the database login details and try again.</p>";
	} else {
      echo "<span class='ok'>OK!</span></p>";
	  mysqli_select_db($this->conn, $this->dbname);
    }
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

    $sql_array = explode("\n\n", $idata);

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
      if (preg_match('/^\#/', $sql_do)) continue;
      // skip newer style MySQL dump comments
      if (preg_match('/^\--/', $sql_do)) continue;
      if($sql_do == null) continue;
	  
      $num = $num + 1;
      mysqli_query($this->conn, $sql_do);
      if(mysqli_error())
      {
        $this->mysqlErrors[] = array("error" => mysqli_error(), "sql" => $sql_do);
        $this->installFailed = true;
      }
    }
  }

  function close()
  {
    @mysqli_close($this->conn);
  }
}

?>
