<?php    
    $user_agents = array();
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \(compatible; iCab ([^;]); ([^;]); [NUI]; ([^;])\)#', 'string' => 'iCab $1');
    $user_agents[] = array('pattern' => '#^Opera/(\d+\.\d+) \(([^;]+); [^)]+\)#', 'string' => 'Opera $1');
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \(compatible; MSIE [^;]+; ([^)]+)\) Opera (\d+\.\d+)#', 'string' => 'Opera $2');
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \(([^;]+); [^)]+\) Opera (\d+\.\d+)#', 'string' => 'Opera $2');
    $user_agents[] = array('pattern' => '#^Mozilla/[1-9]\.0 ?\(compatible; MSIE ([1-9]\.[0-9b]+);(?: ?[^;]+;)*? (Mac_[^;)]+|Windows [^;)]+)(?:; [^;]+)*\)#', 'string' => 'Internet Explorer $1');
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+); [^;]+; Galeon\) Gecko/\d{8}$#', 'string' => 'Galeon');
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; Galeon; [^;]+; ([^;)]+)\)$#', 'string' => 'Galeon $1');
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ Galeon/([0-9.]+) \(([^;)]+)\) Gecko/\d{8}$#', 'string' => 'Galeon $1');
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+); [^;]+; rv:[^;]+(?:; [^;]+)*\) Gecko/\d{8} ([a-zA-Z ]+/[0-9.b]+)#', 'string' => '$2');
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+); [^;]+; rv:([^;]+)(?:; [^;]+)*\) Gecko/\d{8}$#', 'string' => 'Mozilla $2');
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+); [^;]+; (m\d+)(?:; [^;]+)*\) Gecko/\d{8}$#', 'string' => 'Mozilla $2');
    $user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+)(?:; [^;]+)*\) Mozilla/(.+)$#', 'string' => 'Mozilla $2');
    $user_agents[] = array('pattern' => '#^Mozilla/4\.(\d+)[^(]+\(X11; [NIU] ?; ([^;]+)(?:; [^;]+)*\)#', 'string' => 'Netscape 4.$1');
    $user_agents[] = array('pattern' => '#^Mozilla/4\.(\d+)[^(]+\((OS/2|Linux|Macintosh|Win[^;]*)[;,] [NUI] ?[^)]*\)#', 'string' => 'Netscape 4.$1');
    $user_agents[] = array('pattern' => '#^Mozilla/3\.(\d+)\S*[^(]+\(X11; [NIU] ?; ([^;]+)(?:; [^;)]+)*\)#', 'string' => 'Netscape 3.$1');
    $user_agents[] = array('pattern' => '#^Mozilla/3\.(\d+)\S*[^(]+\(([^;]+); [NIU] ?(?:; [^;)]+)*\)#', 'string' => 'Netscape 3.$1');
    $user_agents[] = array('pattern' => '#^Mozilla/2\.(\d+)\S*[^(]+\(([^;]+); [NIU] ?(?:; [^;)]+)*\)#', 'string' => 'Netscape 2.$1');
    $user_agents[] = array('pattern' => '#^Mozilla \(X11; [NIU] ?; ([^;)]+)\)#', 'string' => 'Netscape');
    $user_agents[] = array('pattern' => '#^Mozilla/3.0 \(compatible; StarOffice/(\d+)\.\d+; ([^)]+)\)$#', 'string' => 'StarOffice $1');
    $user_agents[] = array('pattern' => '#^ELinks \((.+); (.+); .+\)$#', 'string' => 'ELinks $1');
    $user_agents[] = array('pattern' => '#^Mozilla/3\.0 \(compatible; NetPositive/([0-9.]+); BeOS\)$#', 'string' => 'NetPositive $1');
    $user_agents[] = array('pattern' => '#^Konqueror/(\S+)$#', 'string' => 'Konqueror $1');
    $user_agents[] = array('pattern' => '#^Mozilla/5\.0 \(compatible; Konqueror/([^;]); ([^)]+)\).*$#', 'string' => 'Konqueror $1');
    $user_agents[] = array('pattern' => '#^Lynx/(\S+)#', 'string' => 'Lynx/$1');
    $user_agents[] = array('pattern' => '#^Mozilla/4.0 WebTV/(\d+\.\d+) \(compatible; MSIE 4.0\)$#', 'string' => 'WebTV $1');
    $user_agents[] = array('pattern' => '#^Mozilla/4.0 \(compatible; MSIE 5.0; (Win98 A); (ATHMWWW1.1); MSOCD;\)$#', 'string' => '$2');
    $user_agents[] = array('pattern' => '#^(RMA/1.0) \(compatible; RealMedia\)$#', 'string' => '$1');
    $user_agents[] = array('pattern' => '#^antibot\D+([0-9.]+)/(\S+)#', 'string' => 'antibot $1');
    $user_agents[] = array('pattern' => '#^Mozilla/[1-9]\.\d+ \(compatible; ([^;]+); ([^)]+)\)$#', 'string' => '$1');
    $user_agents[] = array('pattern' => '#^Mozilla/([1-9]\.\d+)#', 'string' => 'compatible Mozilla/$1');
    $user_agents[] = array('pattern' => '#^([^;]+)$#', 'string' => '$1');
    $GLOBALS['user_agents'] = $user_agents;

    $operating_systems = array();
    $operating_systems[] = array('pattern' => '#Win.*NT 5.0#', 'string' => 'Windows 2000');
    $operating_systems[] = array('pattern' => '#Win.*NT 5.1#', 'string' => 'Windows XP');
    $operating_systems[] = array('pattern' => '#Win.*(XP|2000|ME|NT|9.?)#', 'string' => 'Windows $1');
    $operating_systems[] = array('pattern' => '#Windows .*(3\.11|NT)#', 'string' => 'Windows $1');
    $operating_systems[] = array('pattern' => '#Win32#', 'string' => 'Windows [unknown version)');
    $operating_systems[] = array('pattern' => '#Linux 2\.(.?)\.#', 'string' => 'Linux 2.$1.x');
    $operating_systems[] = array('pattern' => '#Linux#', 'string' => 'Linux (unknown version)');
    $operating_systems[] = array('pattern' => '#FreeBSD .*-CURRENT$#', 'string' => 'FreeBSD Current');
    $operating_systems[] = array('pattern' => '#FreeBSD (.?)\.#', 'string' => 'FreeBSD $1.x');
    $operating_systems[] = array('pattern' => '#NetBSD 1\.(.?)\.#', 'string' => 'NetBSD 1.$1.x');
    $operating_systems[] = array('pattern' => '#(Free|Net|Open)BSD#', 'string' => '$1BSD [unknown version]');
    $operating_systems[] = array('pattern' => '#HP-UX B\.(10|11)\.#', 'string' => 'HP-UX B.$1.xP');
    $operating_systems[] = array('pattern' => '#IRIX(64)? 6\.#', 'string' => 'IRIX 6.x');
    $operating_systems[] = array('pattern' => '#SunOS 4\.1#', 'string' => 'SunOS 4.1.x');
    $operating_systems[] = array('pattern' => '#SunOS 5\.([4-6])#', 'string' => 'Solaris 2.$1.x');
    $operating_systems[] = array('pattern' => '#SunOS 5\.([78])#', 'string' => 'Solaris $1.x');
    $operating_systems[] = array('pattern' => '#Mac_PowerPC#', 'string' => 'Mac OS [PowerPC]');
    $operating_systems[] = array('pattern' => '#Mac#', 'string' => 'Mac OS');
    $operating_systems[] = array('pattern' => '#X11#', 'string' => 'UNIX [unknown version]');
    $operating_systems[] = array('pattern' => '#Unix#', 'string' => 'UNIX [unknown version]');
    $operating_systems[] = array('pattern' => '#BeOS#', 'string' => 'BeOS [unknown version]');
    $operating_systems[] = array('pattern' => '#QNX#', 'string' => 'QNX [unknown version]');
    $GLOBALS['operating_systems'] = $operating_systems;

    // fix for stupid browser shells sending lots of requests
    if(strpos($_SERVER['HTTP_USER_AGENT'], "http://www.avantbrowser.com") > -1) {
      exit;
    }

    if(strpos($_SERVER['HTTP_USER_AGENT'], "WebDAV") > -1) {
      exit;
    }

    //work out browser and operating system
    $user_agent = $this->useragent($_SERVER['HTTP_USER_AGENT']);
    $os = crc32($user_agent['operating_system']);
    $ua = crc32($user_agent['user_agent']);

    //work out access time data
    $accesstime = getdate();
    $hour = $accesstime['hours'];
    $weekday = $accesstime['wday'];

    // work out the host
    if (isset($_SERVER['REMOTE_ADDR'])) {
      $hostname = $_SERVER['REMOTE_ADDR'];
      if (isset($_SERVER['REMOTE_HOST'])) {
        $hostname = $_SERVER['REMOTE_HOST'];
      } else {
        if ($this->config['resolve_hostnames']==1) {
          $hostname = gethostbyaddr($hostname); // should be an IP address
        }
      }
    } else {
      $hostname = 'Unknown';
    }
    $host = crc32($hostname);

    // work out the referer
    $referer = urldecode($_SERVER['HTTP_REFERER']);
    if(empty($referer)) {
      $referer = "Unknown";
    } else {
      $pieces = parse_url($referer);
        $referer = $pieces['scheme']."://".$pieces['host'].$pieces['path'];
    }
    if(strpos($referer, $_SERVER['SERVER_NAME'])>0) {
      $referer = "Internal";
    }
    $ref = crc32($referer);

    if($this->documentIdentifier==0) {
      $docid=$this->config['error_page'];
    } else {
      $docid=$this->documentIdentifier;
    }

    if(($docid==$this->config['error_page']) && (!$docid==$this->config['site_start'])) {
      exit; //stop logging 404's
    }

    // log the access hit
    $tbl = $this->db."log_access";
    $sql = "INSERT INTO $tbl(visitor, document, timestamp, hour, weekday, referer, entry) VALUES('".$this->visitor."', '".$docid."', '".(time()+$this->config['server_offset_time'])."', '".$hour."', '".$weekday."', '".$ref."', '".$this->entrypage."')";
    $result = $this->dbQuery($sql);

    // check if the visitor exists in the database
    if(!isset($_SESSION['visitorLogged'])) {
      $tbl = $this->db."log_visitors";
      $sql = "SELECT COUNT(*) FROM $tbl WHERE id='".$this->visitor."'";
      $result = $this->dbQuery($sql);
      $tmp = $this->fetchRow($result);
      $_SESSION['visitorLogged'] = $tmp['COUNT(*)'];
    } else {
      $_SESSION['visitorLogged'] = 1;
    }

    // log the visitor
    if($_SESSION['visitorLogged']==0) {
      $tbl = $this->db."log_visitors";
      $sql = "INSERT INTO $tbl(id, os_id, ua_id, host_id) VALUES('".$this->visitor."', '".crc32($user_agent['operating_system'])."', '".$ua."', '".$host."')";
      $result = $this->dbQuery($sql);
      $_SESSION['visitorLogged'] = 1;
    }

    // check if the user_agent exists in the database
    if(!isset($_SESSION['userAgentLogged'])) {
      $tbl = $this->db."log_user_agents";
      $sql = "SELECT COUNT(*) FROM $tbl WHERE id='".$ua."'";
      $result = $this->dbQuery($sql);
      $tmp = $this->fetchRow($result);
      $_SESSION['userAgentLogged'] = $tmp['COUNT(*)'];
    } else {
      $_SESSION['userAgentLogged'] = 1;
    }

    // log the user_agent
    if($_SESSION['userAgentLogged']==0) {
      $tbl = $this->db."log_user_agents";
      $sql = "INSERT INTO $tbl(id, data) VALUES('".$ua."', '".$user_agent['user_agent']."')";
      $result = $this->dbQuery($sql);
      $_SESSION['userAgentLogged'] = 1;
    }

    // check if the os exists in the database
    if(!isset($_SESSION['operatingSystemLogged'])) {
      $tbl = $this->db."log_operating_systems";
      $sql = "SELECT COUNT(*) FROM $tbl WHERE id='".$os."'";
      $result = $this->dbQuery($sql);
      $tmp = $this->fetchRow($result);
      $_SESSION['operatingSystemLogged'] = $tmp['COUNT(*)'];
    } else {
      $_SESSION['operatingSystemLogged'] = 1;
    }

    // log the os
    if($_SESSION['operatingSystemLogged']==0) {
      $tbl = $this->db."log_operating_systems";
      $sql = "INSERT INTO $tbl(id, data) VALUES('".$os."', '".$user_agent['operating_system']."')";
      $result = $this->dbQuery($sql);
      $_SESSION['operatingSystemLogged'] = 1;
    }

    // check if the hostname exists in the database
    if(!isset($_SESSION['hostNameLogged'])) {
      $tbl = $this->db."log_hosts";
      $sql = "SELECT COUNT(*) FROM $tbl WHERE id='".$host."'";
      $result = $this->dbQuery($sql);
      $tmp = $this->fetchRow($result);
      $_SESSION['hostNameLogged'] = $tmp['COUNT(*)'];
    } else {
      $_SESSION['hostNameLogged'] = 1;
    }

    // log the hostname
    if($_SESSION['hostNameLogged']==0) {
      $tbl = $this->db."log_hosts";
      $sql = "INSERT INTO $tbl(id, data) VALUES('".$host."', '".$hostname."')";
      $result = $this->dbQuery($sql);
      $_SESSION['hostNameLogged'] = 1;
    }

    // log the referrer
    $tbl = $this->db."log_referers";
    $sql = "REPLACE INTO $tbl(id, data) VALUES('".$ref."', '".$referer."')";
    $result = $this->dbQuery($sql);

    /*************************************************************************************/
    // update the logging cache
    $tbl = $this->db."log_totals";
    $realMonth = strftime("%m");
    $realToday = strftime("%Y-%m-%d");

    // find out if we're on a new day
    $sql = "SELECT today, month FROM $tbl LIMIT 1";
    $result = $this->dbQuery($sql);
    $rowCount = $this->recordCount($result);
    if($rowCount<1) {
      $sql = "INSERT $tbl(today, month) VALUES('$realToday', '$realMonth')";
      $tmpresult = $this->dbQuery($sql);
      $sql = "SELECT today, month FROM $tbl LIMIT 1";
      $result = $this->dbQuery($sql);
    }
    $tmpRow = $this->fetchRow($result);
    $dbMonth = $tmpRow['month'];
    $dbToday = $tmpRow['today'];

    if($dbToday!=$realToday) {
      $sql = "UPDATE $tbl SET today='$realToday', piDay=0, viDay=0, visDay=0";
      $result = $this->dbQuery($sql);
    }

    if($dbMonth!=$realMonth) {
      $sql = "UPDATE $tbl SET month='$realMonth', piMonth=0, viMonth=0, visMonth=0";
      $result = $this->dbQuery($sql);
    }

    // update the table for page impressions
    $sql = "UPDATE $tbl SET piDay=piDay+1, piMonth=piMonth+1, piAll=piAll+1";
    $result = $this->dbQuery($sql);

    // update the table for visits
    if($this->entrypage==1) {
      $sql = "UPDATE $tbl SET viDay=viDay+1, viMonth=viMonth+1, viAll=viAll+1";
      $result = $this->dbQuery($sql);
    }

    // get visitor counts from the logging tables
    $day      = date('j');
    $month    = date('n');
    $year     = date('Y');

    $monthStart = mktime(0,   0,  0, $month, 1, $year);
    $dayStart = mktime(0,   0,  0, $month, $day, $year);
    $dayEnd   = mktime(23, 59, 59, $month, $day, $year);

    $tmptbl = $this->db."log_access";

    $sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tmptbl WHERE timestamp > '".$dayStart."' AND timestamp < '".$dayEnd."'";
    $rs = $this->dbQuery($sql);
    $tmp = $this->fetchRow($rs);
    $visDay = $tmp['COUNT(DISTINCT(visitor))'];

    $sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tmptbl WHERE timestamp > '".$monthStart."' AND timestamp < '".$dayEnd."'";
    $rs = $this->dbQuery($sql);
    $tmp = $this->fetchRow($rs);
    $visMonth = $tmp['COUNT(DISTINCT(visitor))'];

    $sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tmptbl";
    $rs = $this->dbQuery($sql);
    $tmp = $this->fetchRow($rs);
    $visAll = $tmp['COUNT(DISTINCT(visitor))'];

    // update the table for visitors
    $sql = "UPDATE $tbl SET visDay=$visDay, visMonth=$visMonth, visAll=$visAll";
    $result = $this->dbQuery($sql);
    /*************************************************************************************/
?>
