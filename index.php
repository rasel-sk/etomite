<?php
/**************************************************************************
Etomite Content Management System
Copyright (c) 2003 - 2007, The Etomite Project. All Rights Reserved.

Originally Created by Alexander Andrew Butter, upto 03/2005.
Development continued 03/2005 by Ralph A. Dahlgren with Etomite 0.6.1

This file and all dependant and otherwise related files are part of Etomite.

Etomite is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

Etomite is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Etomite; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
/***************************************************************************/
// Class Name: etomite [v1.1]
// Function: This class contains the main parsing functions
// Modified: 2007-03-07 By: Ralph A. Dahlgren & Randy Casburn
// Modified: 2007-05-05 By: Ralph A. Dahlgren
// Modified: 2008-04-22 [v1.0] by: Ralph A. Dahlgren
// Modified: 2008-05-08 [v1.1] by: Ralph A. Dahlgren
/***************************************************************************/

class etomite {
  var $db, $rs, $result, $sql, $table_prefix, $config, $debug,
    $documentIdentifier, $documentMethod, $documentGenerated, $documentContent, $tstart,
    $snippetParsePasses, $documentObject, $templateObject, $snippetObjects,
    $stopOnNotice, $executedQueries, $queryTime, $currentSnippet, $documentName,
    $aliases, $visitor, $entrypage, $documentListing, $dumpSnippets, $chunkCache,
    $snippetCache, $contentTypes, $dumpSQL, $queryCode, $tbl, $error404page,
    $version, $code_name, $notice, $blockLogging, $useblockLogging, $offline_page;

  // Class constructor function used for instantiation
  function etomite() {
    $this->dbConfig['host'] = $GLOBALS['database_server'];
    $this->dbConfig['dbase'] = $GLOBALS['dbase'];
    $this->dbConfig['user'] = $GLOBALS['database_user'];
    $this->dbConfig['pass'] = $GLOBALS['database_password'];
    $this->dbConfig['table_prefix'] = $GLOBALS['table_prefix'];
    $this->db = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix'];
  }

  //
  // START: Setup, configuration, and utility related functions
  //

  function checkSession() {
    if(isset($_SESSION['validated'])) {
      return true;
    } else  {
      return false;
    }
  }

  function checkCookie() {
    if(isset($_COOKIE['etomiteLoggingCookie'])) {
      $this->visitor = $_COOKIE['etomiteLoggingCookie'];
      if(isset($_SESSION['_logging_first_hit'])) {
        $this->entrypage = 0;
      } else {
        $this->entrypage = 1;
        $_SESSION['_logging_first_hit'] = 1;
      }
    } else {
      if (function_exists('posix_getpid')) {
        $visitor = crc32(microtime().posix_getpid());
      } else {
        $visitor = crc32(microtime().session_id());
      }
      $this->visitor = $visitor;
      $this->entrypage = 1;
      setcookie('etomiteLoggingCookie', $visitor, time()+(365*24*60*60), '', '');
    }
  }

  function getMicroTime() {
     list($usec, $sec) = explode(" ", microtime());
     return ((float)$usec + (float)$sec);
  }

  function getSettings() {
    if(file_exists("assets/cache/etomiteCache.idx.php")) {
      include_once("assets/cache/etomiteCache.idx.php");
    } else {
      $result = $this->dbQuery("SELECT setting_name, setting_value FROM ".$this->db."system_settings");
      while ($row = $this->fetchRow($result, 'both')) {
        $this->config[$row[0]] = $row[1];
      }
    }
    // get current version information
    include("manager/includes/version.inc.php");
    $this->config['release'] = $release;
    $this->config['patch_level'] = $patch_level;
    $this->config['code_name'] = $code_name;
    $this->config['full_appname'] = $full_appname;
    $this->config['small_version'] = $small_version;
    $this->config['slogan'] = $full_slogan;

    // if site_unavailable_message is a number then we assume that it is a
    // document id and we use that number for redirecting to the proper document.
    $this->offline_page = (is_numeric($this->config['site_unavailable_message'])) ? $this->config['site_unavailable_message'] : "";

    // compile array of document aliases
    // relocated from rewriteUrls() for greater flexibility in 0.6.1 Final
    // we always run this routine now so that the template info gets populated too
    // a blind array(), $this->tpl_list, is also included for comparisons
    $aliases = array();
    $templates = array();
    $parents = array();
    $limit_tmp = count($this->aliasListing);
    for ($i_tmp=0; $i_tmp<$limit_tmp; $i_tmp++) {
      if($this->aliasListing[$i_tmp]['alias'] != "") {
        $aliases[$this->aliasListing[$i_tmp]['id']] = $this->aliasListing[$i_tmp]['alias'];
      }
      $templates[$this->aliasListing[$i_tmp]['id']] = $this->aliasListing[$i_tmp]['template'];
      $parents[$this->aliasListing[$i_tmp]['id']] = $this->aliasListing[$i_tmp]['parent'];
      $authenticates[$this->aliasListing[$i_tmp]['id']] = $this->aliasListing[$i_tmp]['authenticate'];
    }
    $this->aliases = $aliases;
    $this->templates = $templates;
    $this->parents = $parents;
    $this->authenticates = $authenticates;
  }

  function sendRedirect($url, $count_attempts=3, $type='') {
    if(empty($url)) {
      return false;
    } else {
      if($count_attempts==1) {
      // append the redirect count string to the url
        $currentNumberOfRedirects = isset($_REQUEST['err']) ? $_REQUEST['err'] : 0 ;
        if($currentNumberOfRedirects>3) {
          $this->messageQuit("Redirection attempt failed - please ensure the document you're trying to redirect to exists. Redirection URL: <i>$url</i>");
        } else {
          $currentNumberOfRedirects += 1;
          if(strpos($url, "?")>0) {
            $url .= "&err=$currentNumberOfRedirects";
          } else {
            $url .= "?err=$currentNumberOfRedirects";
          }
        }
      }
      if($type=="REDIRECT_REFRESH") {
        $header = "Refresh: 0;URL=".$url;
      } elseif($type=="REDIRECT_META") {
        $header = "<META HTTP-EQUIV=Refresh CONTENT='0; URL=".$url."' />";
        echo $header;
        exit;
      } elseif($type=="REDIRECT_HEADER" || empty($type)) {
        $header = "Location: $url";
      }
      header($header);
      $this->postProcess();
    }
  }

  function checkPreview() {
    if($this->checkSession()==true) {
      if(isset($_REQUEST['z']) && $_REQUEST['z']=='manprev') {
        return true;
      } else {
        return false;
      }
    } else  {
      return false;
    }
  }

  function checkSiteStatus() {
    if($this->config['site_status']==1) {
      return true;
    } else {
      return false;
    }
  }

  function syncsite() {
  // clears and rebuilds the site cache
  // added in 0.6.1.1
  // Modified 2008-03-17 by Ralph for improved cachePath handling
    include_once("./manager/processors/cache_sync.class.processor.php");
    $sync = new synccache();
    $sync->setCachepath("assets/cache/");
    $sync->setReport(false);
    $sync->emptyCache();
  }

  function checkCache($id) {
    $cacheFile = "assets/cache/docid_".$id.".etoCache";
    if(file_exists($cacheFile)) {
      $this->documentGenerated=0;
      return join("",file($cacheFile));
    } else {
      $this->documentGenerated=1;
      return "";
    }
  }

  //
  // END: Setup, configuration, and utility related functions
  //

  //
  // START: Page rendering related functions
  //

   function getDocumentMethod() {
   // function to test the query and find the retrieval method
   if(isset($_REQUEST['q'])) {
     return "alias";
   } elseif(isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
     return "id";
   } else {
     return "none";
    }
  }

  function getDocumentIdentifier($method) {
  // function to test the query and find the retrieval method
    switch($method) {
      case "alias" :
        return preg_replace("/[^\w\.@-]/", "", htmlspecialchars($_REQUEST['q']));
        break;
      case "id" :
        return is_numeric($_REQUEST['id']) ? $_REQUEST['id'] : "";
        break;
      case "none" :
        return $this->config['site_start'];
        break;
      default :
        return $this->config['site_start'];
    }
  }

  function cleanDocumentIdentifier($qOrig) {
    if(strpos($q, "/")>0) {
      $q = substr($q, 0, strpos($q, "/"));
    }
    $q = str_replace($this->config['friendly_url_prefix'], "", $qOrig);
    $q = str_replace($this->config['friendly_url_suffix'], "", $q);
    // we got an ID returned unless the error_page alias is "404"
    if(is_numeric($q) && ($q != $this->aliases[$this->config['error_page']])) {
      $this->documentMethod = 'id';
      return $q;
    // we didn't get an ID back, so instead we assume it's an alias
    } else {
      $this->documentMethod = 'alias';
      return $q;
    }
  }

  function addNotice($content, $type="text/html") {
    /* LEGAL STUFF REMOVED TO SHRINK FILE */

    if($type == "text/html"){
      $notice = "<div id='etoNotice'>\n".
          "\tPowered by <a href='http://www.etomite.com' title='Powered by the Etomite Content Management System'>Etomite CMS</a>.\n".
          "</div>\n\n";
    }

    // insert the message into the document
    if(strpos($content, "</body>")>0) {
      $content = str_replace("</body>", $notice."</body>", $content);
    } elseif(strpos($content, "</BODY>")>0) {
      $content = str_replace("</BODY>", $notice."</BODY>", $content);
    } else {
      $content .= $notice;
    }
    return $content;
  }

  function outputContent() {
    $output = $this->documentContent;

    // check for non-cached snippet output
    if(strpos($output, '[!')>-1) {
      $output = str_replace('[!', '[[', $output);
      $output = str_replace('!]', ']]', $output);

      $this->nonCachedSnippetParsePasses = empty($this->nonCachedSnippetParsePasses) ? 1 : $this->nonCachedSnippetParsePasses;
      for($i=0; $i<$this->nonCachedSnippetParsePasses; $i++) {
        if($this->config['dumpSnippets']==1) {
          echo "<fieldset style='text-align: left'><legend>NONCACHED PARSE PASS ".($i+1)."</legend>The following snipppets (if any) were parsed during this pass.<div style='width:100%' align='center'>";
        }
        // replace settings referenced in document
        $output = $this->mergeSettingsContent($output);
        // replace HTMLSnippets in document
        $output = $this->mergeHTMLSnippetsContent($output);
        // find and merge snippets
        $output = $this->evalSnippets($output);
        if($this->config['dumpSnippets']==1) {
          echo "</div></fieldset><br />";
        }
      }
    }

    $output = $this->rewriteUrls($output);

    $totalTime = ($this->getMicroTime() - $this->tstart);
    $queryTime = $this->queryTime;
    $phpTime = $totalTime-$queryTime;

    $queryTime = sprintf("%2.4f s", $queryTime);
    $totalTime = sprintf("%2.4f s", $totalTime);
    $phpTime = sprintf("%2.4f s", $phpTime);
    $source = $this->documentGenerated==1 ? "database" : "cache";
    $queries = isset($this->executedQueries) ? $this->executedQueries : 0 ;

    // send out content-type headers
    $type = !empty($this->contentTypes[$this->documentIdentifier]) && !$this->aborting
    ? $this->contentTypes[$this->documentIdentifier]
    : "text/html";

    header('Content-Type: '.$type.'; charset='.$this->config['etomite_charset']);

    if(!$this->checkSiteStatus() && ($this->documentIdentifier != $this->offline_page))
    {
      header("HTTP/1.0 307 Temporary Redirect");
    }

    if(($this->documentIdentifier == $this->config['error_page']) && ($this->config['error_page'] !=  $this->config['site_start']))
    {
      header("HTTP/1.0 404 Not Found");
    }


    // Check to see whether or not addNotice should be called
    if($this->config['useNotice'] || !isset($this->config['useNotice'])){
      $documentOutput = $this->addNotice($output, $type);
    } else {
      $documentOutput = $output;
    }

    if($this->config['dumpSQL']) {
      $documentOutput .= $this->queryCode;
    }
    $documentOutput = str_replace("[^q^]", $queries, $documentOutput);
    $documentOutput = str_replace("[^qt^]", $queryTime, $documentOutput);
    $documentOutput = str_replace("[^p^]", $phpTime, $documentOutput);
    $documentOutput = str_replace("[^t^]", $totalTime, $documentOutput);
    $documentOutput = str_replace("[^s^]", $source, $documentOutput);

    // Check to see if document content contains PHP tags.
    // PHP tag support contributed by SniperX
    if( preg_match("/(<\?php|<\?)(.*?)\?>/", $documentOutput) && $type == "text/html" && $this->config['allow_embedded_php'] )
    {
      $documentOutput = '?'.'>' . $documentOutput . '<'.'?php ';
      // Parse the PHP tags.
      eval($documentOutput);
    }
    else
    {
      // No PHP tags so just echo out the content.
      echo $documentOutput;
    }
  }


  function checkPublishStatus(){
    include("assets/cache/etomitePublishing.idx");
    $timeNow = time()+$this->config['server_offset_time'];
    if(($cacheRefreshTime<=$timeNow && $cacheRefreshTime!=0) || !isset($cacheRefreshTime)) {
      // now, check for documents that need publishing
      $sql = "UPDATE ".$this->db."site_content SET published=1 WHERE ".$this->db."site_content.pub_date <= ".$timeNow." AND ".$this->db."site_content.pub_date!=0";
      if(@!$result = $this->dbQuery($sql)) {
        $this->messageQuit("Execution of a query to the database failed", $sql);
      }

      // now, check for documents that need un-publishing
      $sql = "UPDATE ".$this->db."site_content SET published=0 WHERE ".$this->db."site_content.unpub_date <= ".$timeNow." AND ".$this->db."site_content.unpub_date!=0";
      if(@!$result = $this->dbQuery($sql)) {
        $this->messageQuit("Execution of a query to the database failed", $sql);
      }

      // clear the cache
      $basepath=dirname(__FILE__);
      if ($handle = opendir($basepath."/assets/cache")) {
        $filesincache = 0;
        $deletedfilesincache = 0;
        while (false !== ($file = readdir($handle))) {
          if ($file != "." && $file != "..") {
            $filesincache += 1;
            if (preg_match ("/\.etoCache/", $file)) {
              $deletedfilesincache += 1;
              while(!unlink($basepath."/assets/cache/".$file));
            }
          }
        }
        closedir($handle);
      }

      // update publish time file
      $timesArr = array();
      $sql = "SELECT MIN(".$this->db."site_content.pub_date) AS minpub FROM ".$this->db."site_content WHERE ".$this->db."site_content.pub_date >= ".$timeNow.";";
      if(@!$result = $this->dbQuery($sql)) {
        $this->messageQuit("Failed to find publishing timestamps", $sql);
      }
      $tmpRow = $this->fetchRow($result);
      $minpub = $tmpRow['minpub'];
      if($minpub!=NULL) {
        $timesArr[] = $minpub;
      }

      $sql = "SELECT MIN(".$this->db."site_content.unpub_date) AS minunpub FROM ".$this->db."site_content WHERE ".$this->db."site_content.unpub_date >= ".$timeNow.";";
      if(@!$result = $this->dbQuery($sql)) {
        $this->messageQuit("Failed to find publishing timestamps", $sql);
      }
      $tmpRow = $this->fetchRow($result);
      $minunpub = $tmpRow['minunpub'];
      if($minunpub!=NULL) {
        $timesArr[] = $minunpub;
      }

      if(count($timesArr)>0) {
        $nextevent = min($timesArr);
      } else {
        $nextevent = 0;
      }

      $basepath=dirname(__FILE__);
      $fp = @fopen($basepath."/assets/cache/etomitePublishing.idx","wb");
      if($fp) {
        @flock($fp, LOCK_EX);
        $data = "<?php \$cacheRefreshTime=".$nextevent."; ?>";
        $len = strlen($data);
        @fwrite($fp, $data, $len);
        @flock($fp, LOCK_UN);
        @fclose($fp);
      }
    }
  }

  function postProcess()
  {
    // if enabled, do logging
    if($this->config['track_visitors']==1 && ($_REQUEST['z']!="manprev"))
    {
      if(!((preg_match($this->blockLogging,$_SERVER['HTTP_USER_AGENT'])) && $this->useblockLogging)) $this->log();
    }
    // if the current document was generated, cache it, unless an alternate template is being used!
    if( isset($_SESSION['tpl']) && ($_SESSION['tpl'] != $this->documentObject['template']) ) return;
    if( $this->documentGenerated==1 && $this->documentObject['cacheable']==1 && $this->documentObject['type']=='document' )
    {
      $basepath=dirname(__FILE__);
      if($fp = @fopen($basepath."/assets/cache/docid_".$this->documentIdentifier.".etoCache","w"))
      {
        fputs($fp,$this->documentContent);
        fclose($fp);
      }
    }
  }

  function mergeDocumentContent($template) {
    foreach ($this->documentObject as $key => $value) {
      $template = str_replace("[*".$key."*]", stripslashes($value), $template);
    }
    return $template;
  }

  function mergeSettingsContent($template) {
    preg_match_all('~\[\((.*?)\)\]~', $template, $matches);
    $settingsCount = count($matches[1]);
    for($i=0; $i<$settingsCount; $i++) {
      $replace[$i] = $this->config[$matches[1][$i]];
    }
    $template = str_replace($matches[0], $replace, $template);
    return $template;
  }

  function mergeHTMLSnippetsContent($content) {
    preg_match_all('~{{(.*?)}}~', $content, $matches);
    $settingsCount = count($matches[1]);
    for($i=0; $i<$settingsCount; $i++) {
      if(isset($this->chunkCache[$matches[1][$i]])) {
        $replace[$i] = base64_decode($this->chunkCache[$matches[1][$i]]);
      } else {
        $sql = "SELECT * FROM ".$this->db."site_htmlsnippets WHERE ".$this->db."site_htmlsnippets.name='".$matches[1][$i]."';";
        $result = $this->dbQuery($sql);
        $limit=$this->recordCount($result);
        if($limit<1) {
          $this->chunkCache[$matches[1][$i]] = "";
          $replace[$i] = "";
        } else {
          $row=$this->fetchRow($result);
          $this->chunkCache[$matches[1][$i]] = $row['snippet'];
          $replace[$i] = $row['snippet'];
        }
      }
    }
    $content = str_replace($matches[0], $replace, $content);
    return $content;
  }

  function evalSnippet($snippet, $params) {
    $etomite = $this;
    if(is_array($params)) {
      extract($params, EXTR_SKIP);
    }
    $snip = eval(base64_decode($snippet));
    return $snip;
  }

  function evalSnippets($documentSource) {
    preg_match_all('~\[\[(.*?)\]\]~', $documentSource, $matches);

    $etomite = $this;

    $matchCount=count($matches[1]);
    for($i=0; $i<$matchCount; $i++) {
      $spos = strpos($matches[1][$i], '?', 0);
      if($spos!==false) {
        $params = substr($matches[1][$i], $spos, strlen($matches[1][$i]));
      } else {
        $params = '';
      }
      $matches[1][$i] = str_replace($params, '', $matches[1][$i]);
      $snippetParams[$i] = $params;
    }
    $nrSnippetsToGet = count($matches[1]);
    for($i=0;$i<$nrSnippetsToGet;$i++) {
      if(isset($this->snippetCache[$matches[1][$i]])) {
        $snippets[$i]['name'] = $matches[1][$i];
        $snippets[$i]['snippet'] = $this->snippetCache[$matches[1][$i]];
      } else {
        $sql = "SELECT * FROM ".$this->db."site_snippets WHERE ".$this->db."site_snippets.name='".$matches[1][$i]."';";
        $result = $this->dbQuery($sql);
        if($this->recordCount($result)==1) {
          $row = $this->fetchRow($result);
          $snippets[$i]['name'] = $row['name'];
          $snippets[$i]['snippet'] = base64_encode($row['snippet']);
          $this->snippetCache = $snippets[$i];
        } else {
          $snippets[$i]['name'] = $matches[1][$i];
          $snippets[$i]['snippet'] = base64_encode("return false;");
          $this->snippetCache = $snippets[$i];
        }
      }
    }

    for($i=0; $i<$nrSnippetsToGet; $i++) {
      $parameter = array();
      $snippetName = $this->currentSnippet = $snippets[$i]['name'];
      $currentSnippetParams = $snippetParams[$i];

      if(!empty($currentSnippetParams)) {
        $tempSnippetParams = str_replace("?", "", $currentSnippetParams);
        $splitter = strpos($tempSnippetParams, "&amp;")>0 ? "&amp;" : "&";
        $tempSnippetParams = split($splitter, $tempSnippetParams);

        for($x=0; $x<count($tempSnippetParams); $x++) {
          $parameterTemp = explode("=", $tempSnippetParams[$x],2);
          $parameter[$parameterTemp[0]] = $parameterTemp[1];
        }
      }
      $executedSnippets[$i] = $this->evalSnippet($snippets[$i]['snippet'], $parameter);

      if($this->config['dumpSnippets']==1) {
        echo "<fieldset><legend><b>$snippetName</b></legend><textarea style='width:60%; height:200px'>".htmlentities($executedSnippets[$i])."</textarea></fieldset><br />";
      }
      $documentSource = str_replace("[[".$snippetName.$currentSnippetParams."]]", $executedSnippets[$i], $documentSource);
    }
    return $documentSource;
  }

  function rewriteUrls($documentSource) {
    // rewrite the urls
    // based on code by daseymour ;)
    if($this->config['friendly_alias_urls']==1) {
      // additional code that was here originally has been moved to getSettings() for added functionality
      // write the function for the preg_replace_callback. Probably not the best way of doing this,
      // but otherwise it brakes on some people's installs...
      $func = '
      $aliases=unserialize("'.addslashes(serialize($this->aliases)).'");
      if (isset($aliases[$m[1]])) {
        if('.$this->config["friendly_alias_urls"].'==1) {
        return "'.$this->config["friendly_url_prefix"].'".$aliases[$m[1]]."'.$this->config["friendly_url_suffix"].'";
        } else {
          return $aliases[$m[1]];
        }
      } else {
        return "'.$this->config["friendly_url_prefix"].'".$m[1]."'.$this->config["friendly_url_suffix"].'";
      }';
      $in = '!\[\~(.*?)\~\]!is';
      $documentSource = preg_replace_callback($in, create_function('$m', $func), $documentSource);
    } else {
      $in = '!\[\~(.*?)\~\]!is';
      $out = "index.php?id=".'\1';
      $documentSource = preg_replace($in, $out, $documentSource);
    }
    return $documentSource;
  }

  function executeParser() {
    //error_reporting(0);
    set_error_handler(array($this,"phpError"));

    // convert variables initially calculated in config.inc.php into config variables
    $this->config['absolute_base_path'] = $GLOBALS['absolute_base_path'];
    $this->config['relative_base_path'] = $GLOBALS['relative_base_path'];
    $this->config['www_base_path'] = $GLOBALS['www_base_path'];

    // get the settings
    $this->getSettings();
    // detect current protocol
    $protocol = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? "https://" : "http://";
    // get server host name
    $host = $_SERVER['HTTP_HOST'];
    // create 404 Page Not Found error url
    $this->error404page = $this->makeURL($this->config['error_page']);

    // make sure the cache doesn't need updating
    $this->checkPublishStatus();

    // check the logging cookie
    if($this->config['track_visitors']==1 && !isset($_REQUEST['z'])) {
      $this->checkCookie();
    }

    // find out which document we need to display
    $this->documentMethod = $this->getDocumentMethod();
    $this->documentIdentifier = $this->getDocumentIdentifier($this->documentMethod);

    // now we know the site_start, change the none method to id
    if($this->documentMethod=="none"){
      $this->documentMethod = "id";
    }
    if($this->documentMethod=="alias"){
      $this->documentIdentifier = $this->cleanDocumentIdentifier($this->documentIdentifier);
    }

    if($this->documentMethod=="alias"){
      // jbc added to remove case sensitivity
      $tmpArr=array();

      foreach($this->documentListing as $key => $value) {
          $tmpArr[strtolower($key)] = $value;
      }

      $this->documentIdentifier = $tmpArr[strtolower($this->documentIdentifier)];
      $this->documentMethod = 'id';
    }

    // stop processing here, as the site's offline
    if(!$this->checkSiteStatus() && ($_REQUEST['z'] != "manprev") && ($this->offline_page == "")) {
      $this->documentContent = $this->config['site_unavailable_message'];
      $this->aborting = true; // added in [v1.0] by Ralph to resolve header issues
      $this->outputContent();
      ob_end_flush();
      exit;
    } elseif(!$this->checkSiteStatus() && $this->documentIdentifier != $this->offline_page) {
      $this->sendRedirect($this->makeURL($this->offline_page));
    }

    // if document level authentication is required, authenticate now
    if($this->authenticates[$this->documentIdentifier]) {
      if(($this->config['use_uvperms'] && !$this->checkPermissions()) || !$_SESSION['validated']) {
        include_once("manager/includes/lang/".$this->config['manager_language'].".inc.php");
        $msg = ($this->config['access_denied_message']!="") ? $this->config['access_denied_message'] : $_lang['access_permission_denied'];
        echo $msg;
        exit;
      }
    }

    $template = $this->templates[$this->documentIdentifier];

    // we now know the method and identifier, let's check the cache based on conditions below
    if(
        (
        // page uses default template
        $template == $this->config['default_template']
        // no new alternate template has been selected
        && $_GET['tpl'] == ''
        // no alternate template was previously selected
        && $_SESSION['tpl'] == ''
        // Printable Page template was not requested
        && !isset($_GET['printable'])
        )
        ||
        // no alternate template is currently being used
        $template != $this->config['default_template']
    )
    {
      $this->documentContent = $this->checkCache($this->documentIdentifier);
    }

    if($this->documentContent=="") {
      $sql = "SELECT * FROM ".$this->db."site_content WHERE ".$this->db."site_content.".$this->documentMethod." = '".$this->documentIdentifier."';";
      $result = $this->dbQuery($sql);
      if($this->recordCount($result) < 1) {
        // no match found, send the visitor to the error_page
        $this->sendRedirect($this->error404page);
        ob_clean();
        exit;
      }

      if($rowCount>1) {
        // no match found, send the visitor to the error_page
        $this->messageQuit("More than one result returned when attempting to translate `alias` to `id` - there are multiple documents using the same alias");
      }
      // this is now the document
      $this->documentObject = $this->fetchRow($result);
      // write the documentName to the object
      $this->documentName = $this->documentObject['pagetitle'];

      // validation routines
      if($this->documentObject['deleted']==1) {
        // no match found, send the visitor to the error_page
        $this->sendRedirect($this->error404page);
      }

      if($this->documentObject['published']==0){
        // no match found, send the visitor to the error_page
        $this->sendRedirect($this->error404page);
      }

      // check whether it's a reference
      if($this->documentObject['type']=="reference") {
        $this->sendRedirect($this->documentObject['content']);
        ob_clean();
        exit;
      }

      // get the template and start parsing!
      // if a request for a template change was passed, save old template and use the new one

      if( ($_GET['tpl'] != "")
      && ($template==$this->config['default_template'])
      && (in_array($_GET['tpl'],$this->tpl_list)) )
      {
        $template = strip_tags($_GET['tpl']);
        $_GET['tpl'] = "";
      // if the session template has been set, use it
      }
      elseif( isset($_SESSION['tpl'])
      && ($template==$this->config['default_template'])
      && (in_array($_SESSION['tpl'],$this->tpl_list)) )
      {
        $template = strip_tags($_SESSION['tpl']);
      }

      // if a printable page was requested, switch to the proper template
      if(isset($_GET['printable'])) {
        //$_GET['printable'] = "";
        $sql = "SELECT * FROM ".$this->db."site_templates WHERE ".$this->db."site_templates.templatename = '".$this->printable."';";

      // otherwise use the assigned template
      } else {
        $sql = "SELECT * FROM ".$this->db."site_templates WHERE ".$this->db."site_templates.id = '".$template."';";
      }

      // run query and process the results
      $result = $this->dbQuery($sql);
      $rowCount = $this->recordCount($result);

      // if the template wasn't found, send an error
      if($rowCount != 1) {
        $this->messageQuit("Row count error in template query result.",$sql,true);
      }

      // assign this template to be the active template on success
      if(($template != $this->config['default_template'])
      && ($this->templates[$this->documentIdentifier]==$this->config['default_template']))
      {
        $_SESSION['tpl']=$template;
      } else {
        if($template == $this->config['default_template']) {
          unset($_SESSION['tpl']);
        }
      }
      $row = $this->fetchRow($result);
      $documentSource = $row['content'];

      // get snippets and parse them the required number of times
      $this->snippetParsePasses = empty($this->snippetParsePasses) ? 3 : $this->snippetParsePasses ;
      for($i=0; $i<$this->snippetParsePasses; $i++) {
        if($this->config['dumpSnippets']==1) {
          echo "<fieldset><legend><b style='color: #821517;'>PARSE PASS ".($i+1)."</b></legend>The following snipppets (if any) were parsed during this pass.<div style='width:100%' align='center'>";
        }
        // combine template and content
        $documentSource = $this->mergeDocumentContent($documentSource);
        // replace settings referenced in document
        $documentSource = $this->mergeSettingsContent($documentSource);
        // replace HTMLSnippets in document
        $documentSource = $this->mergeHTMLSnippetsContent($documentSource);
        // find and merge snippets
        $documentSource = $this->evalSnippets($documentSource);
        if($this->config['dumpSnippets']==1) {
          echo "</div></fieldset><br />";
        }
      }
      $this->documentContent = $documentSource;
    }
    register_shutdown_function(array($this,"postProcess")); // tell PHP to call postProcess when it shuts down
    $this->outputContent();
  }

  //
  // END: Page rendering related functions
  //

/***************************************************************************************/
/* START: Error Handler and Logging Functions
/***************************************************************************************/

  function phpError($nr, $text, $file, $line) {
    if($nr==2048) return true; // added by mfx 10-18-2005 to ignore E_STRICT erros in PHP5
    if($nr==8 && $this->stopOnNotice==false) {
      return true;
    }
    if (is_readable($file)) {
      $source = file($file);
      $source = htmlspecialchars($source[$line-1]);
    } else {
      $source = "";
    }  //Error $nr in $file at $line: <div><code>$source</code></div>
    $this->messageQuit("PHP Parse Error", '', true, $nr, $file, $source, $text, $line);
  }

  function messageQuit($msg='unspecified error', $query='', $is_error=true,$nr='', $file='', $source='', $text='', $line='') {
    $this->aborting = true; // added in [v1.0] by Ralph to resolve header issues
    $pms = "<html><head><title>Etomite ".$this->config['release']." ".$this->config['code_name']."</title>
    <style>TD, BODY { font-size: 11px; font-family:verdana; }</style>
    <script type='text/javascript'>
      function copyToClip()
      {
        holdtext.innerText = sqlHolder.innerText;
        Copied = holdtext.createTextRange();
        Copied.execCommand('Copy');
      }
    </script>
    </head><body>
    ";
    // jbc: added link back to home page, removed "Etomite parse" and left just "error"
    $homePage = $_SERVER['PHP_SELF'];
    $siteName = $this->config['site_name'];
    if($is_error) {
      $pms .= "<h2><a href='$homePage' title='$siteName'>$siteName</a></h2>
      <h3 style='color:red'>&laquo; Error &raquo;</h3>
      <table border='0' cellpadding='1' cellspacing='0'>
      <tr><td colspan='3'>Etomite encountered the following error while attempting to parse the requested resource:</td></tr>
      <tr><td colspan='3'><b style='color:red;'>&laquo; $msg &raquo;</b></td></tr>";
    } else {
      $pms .= "<h2><a href='$homePage'
title='$siteName'>$siteName</a></h2>
      <h3 style='color:#003399'>&laquo; Etomite Debug/ stop message &raquo;</h3>
      <table border='0' cellpadding='1' cellspacing='0'>
      <tr><td colspan='3'>The Etomite parser recieved the following debug/ stop message:</td></tr>
      <tr><td colspan='3'><b style='color:#003399;'>&laquo; $msg &raquo;</b></td></tr>";
    }
    // end jbc change

    if(!empty($query)) {
      $pms .= "<tr><td colspan='3'><b style='color:#999;font-size: 9px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SQL:&nbsp;<span id='sqlHolder'>$query</span></b>
      <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:copyToClip();' style='color:#821517;font-size: 9px; text-decoration: none'>[Copy SQL to ClipBoard]</a><textarea id='holdtext' style='display:none;'></textarea></td></tr>";
    }

    if($text!='') {

      $errortype = array (
        E_ERROR          => "Error",
        E_WARNING        => "Warning",
        E_PARSE          => "Parsing Error",
        E_NOTICE          => "Notice",
        E_CORE_ERROR      => "Core Error",
        E_CORE_WARNING    => "Core Warning",
        E_COMPILE_ERROR  => "Compile Error",
        E_COMPILE_WARNING => "Compile Warning",
        E_USER_ERROR      => "User Error",
        E_USER_WARNING    => "User Warning",
        E_USER_NOTICE    => "User Notice",
      );

      $pms .= "<tr><td>&nbsp;</td></tr><tr><td colspan='3'><b>PHP error debug</b></td></tr>";

      $pms .= "<tr><td valign='top'>&nbsp;&nbsp;Error: </td>";
      $pms .= "<td colspan='2'>$text</td><td>&nbsp;</td>";
      $pms .= "</tr>";

      $pms .= "<tr><td valign='top'>&nbsp;&nbsp;Error type/ Nr.: </td>";
      $pms .= "<td colspan='2'>".$errortype[$nr]." - $nr</b></td><td>&nbsp;</td>";
      $pms .= "</tr>";

      $pms .= "<tr><td>&nbsp;&nbsp;File: </td>";
      $pms .= "<td colspan='2'>$file</td><td>&nbsp;</td>";
      $pms .= "</tr>";

      $pms .= "<tr><td>&nbsp;&nbsp;Line: </td>";
      $pms .= "<td colspan='2'>$line</td><td>&nbsp;</td>";
      $pms .= "</tr>";
      if($source!='') {
        $pms .= "<tr><td valign='top'>&nbsp;&nbsp;Line $line source: </td>";
        $pms .= "<td colspan='2'>$source</td><td>&nbsp;</td>";
        $pms .= "</tr>";
      }
    }

    $pms .= "<tr><td>&nbsp;</td></tr><tr><td colspan='3'><b>Parser timing</b></td></tr>";

    $pms .= "<tr><td>&nbsp;&nbsp;MySQL: </td>";
    $pms .= "<td><i>[^qt^] s</i></td><td>(<i>[^q^] Requests</i>)</td>";
    $pms .= "</tr>";

    $pms .= "<tr><td>&nbsp;&nbsp;PHP: </td>";
    $pms .= "<td><i>[^p^] s</i></td><td>&nbsp;</td>";
    $pms .= "</tr>";

    $pms .= "<tr><td>&nbsp;&nbsp;Total: </td>";
    $pms .= "<td><i>[^t^] s</i></td><td>&nbsp;</td>";
    $pms .= "</tr>";

    $pms .= "</table>";
    $pms .= "</body></html>";

    $this->documentContent = $pms;
    $this->outputContent();

    exit;
  }

  // Parsing functions used in this class are based on/ inspired by code by Sebastian Bergmann.
  // The regular expressions used in this class are taken from the ModLogAn (http://jan.kneschke.de/projects/modlogan/) project.
  function log() {
    // if we are tracking visitors and this is not the 404 error page, log the hit
    if($this->config['track_visitors'] && $this->documentIdentifier != $this->config['error_page']) {
      $basepath=dirname(__FILE__); // $basedir added by Dean [0613]
      include_once($basepath."/manager/includes/visitor_logging.inc.php");
    }
  }

/***************************************************************************************/
/* END: Error Handler and Logging Functions
/***************************************************************************************/

/***************************************************************************************/
/* START: Etomite API functions                                                        */
/***************************************************************************************/

  function getAllChildren($id=0, $sort='menuindex', $dir='ASC', $fields='id, pagetitle, longtitle, description, parent, alias', $limit="", $showhidden=false) {
  // returns a two dimensional array of $key=>$value data for all existing documents regardless of activity status
  // $id = id of the document whose children have been requested
  // $sort = the field to sort the result by
  // $dir = sort direction (ASC|DESC)
  // $fields = comma delimited list of fields to be returned for each record
  // $limit = maximun number of records to return (default=all)
  // $showhidden = setting to [true|1] will override the showinmenu flag setting (default=false)
    $limit = ($limit != "") ? "LIMIT $limit" : "";
    $tbl = $this->db."site_content";
    if($showhidden == 0) $showinmenu = "AND $tbl.showinmenu=1";
    $sql = "SELECT $fields FROM $tbl WHERE $tbl.parent=$id $showinmenu ORDER BY $sort $dir $limit;";
    $result = $this->dbQuery($sql);
    $resourceArray = array();
    for($i=0;$i<@$this->recordCount($result);$i++)  {
      array_push($resourceArray,@$this->fetchRow($result));
    }
    return $resourceArray;
  }

  function getActiveChildren($id=0, $sort='menuindex', $dir='', $fields='id, pagetitle, longtitle, description, parent, alias, showinmenu', $limit="", $showhidden=false) {
  // returns a two dimensional array of $key=>$value data for active documents only
  // $id = id of the document whose children have been requested
  // $sort = the field to sort the result by
  // $dir = sort direction (ASC|DESC)
  // $fields = comma delimited list of fields to be returned for each record
  // $limit = maximun number of records to return (default=all)
  // $showhidden = setting to [true|1] will override the showinmenu flag setting (default=false)
    $limit = ($limit != "") ? "LIMIT $limit" : "";
    $tbl = $this->db."site_content";
    if($showhidden == 0) $showinmenu = "AND $tbl.showinmenu=1";
    $sql = "SELECT $fields FROM $tbl WHERE $tbl.parent=$id AND $tbl.published=1 AND $tbl.deleted=0 $showinmenu ORDER BY $sort $dir $limit;";
    $result = $this->dbQuery($sql);
    $resourceArray = array();
    for($i=0;$i<@$this->recordCount($result);$i++)  {
      array_push($resourceArray,@$this->fetchRow($result));
    }
    return $resourceArray;
  }

  function getDocuments($ids=array(), $published=1, $deleted=0, $fields="*", $where='', $sort="menuindex", $dir="ASC", $limit="", $showhidden=false) {
  // Modified getDocuments function which includes LIMIT capabilities - Ralph
  // returns $key=>$values for an array of document id's
  // $id = the identifier of the document whose data is being requested
  // $fields = a comma delimited list of fields to be returned in a $key=>$value array (defaults to all)
  // $where = an optional WHERE clause to be used inthe query
  // $sort = the field to sort the result by
  // $dir = sort direction (ASC|DESC)
  // $fields = comma delimited list of fields to be returned for each record
  // $limit = maximun number of records to return (default=all)
  // $showhidden = setting to [true|1] will override the showinmenu flag setting (default=false)
    if(count($ids)==0) {
      return false;
    } else {
      $limit = ($limit != "") ? "LIMIT $limit" : "";
      $tbl = $this->db."site_content";
      if($showhidden == 0) $showinmenu = "AND $tbl.showinmenu=1";
      $sql = "SELECT $fields FROM $tbl WHERE $tbl.id IN (".join($ids, ",").") AND $tbl.published=$published AND $tbl.deleted=$deleted $showinmenu $where ORDER BY $sort $dir $limit;";
      $result = $this->dbQuery($sql);
      $resourceArray = array();
      for($i=0;$i<@$this->recordCount($result);$i++)  {
      array_push($resourceArray,@$this->fetchRow($result));
      }
      return $resourceArray;
    }
  }

  function getDocument($id=0, $fields="*") {
  // returns $key=>$values for a specific document
  // $id is the identifier of the document whose data is being requested
  // $fields is a comma delimited list of fields to be returned in a $key=>$value array (defaults to all)
  // Modified 2008-04-14 [v1.0] to disregard showinmenu setting
    if($id==0) {
      return false;
    } else {
      $tmpArr[] = $id;
      $docs = $this->getDocuments($tmpArr, 1, 0, $fields, $where, $sort="menuindex", $dir="ASC", $limit="1", $showhidden=true);
      if($docs!=false) {
        return $docs[0];
      } else {
        return false;
      }
    }
  }

  function getPageInfo($id=-1, $active=1, $fields='id, pagetitle, description, alias') {
  // returns a $key=>$value array of information for a single document
  // $id is the identifier of the document whose data is being requested
  // $active boolean (0=false|1=true) determines whether to return data for any or only an active document
  // $fields is a comma delimited list of fields to be returned in a $key=>$value array
    if($id==0) {
      return false;
    } else {
      $tbl = $this->db."site_content";
      $activeSql = $active==1 ? "AND $tbl.published=1 AND $tbl.deleted=0" : "" ;
      $sql = "SELECT $fields FROM $tbl WHERE $tbl.id=$id $activeSql";
      $result = $this->dbQuery($sql);
      $pageInfo = @$this->fetchRow($result);
      return $pageInfo;
    }
  }

  function getParent($id=-1, $active=1, $fields='id, pagetitle, description, alias, parent') {
  // returns document information for a given document identifier
  // $id is the identifier of the document whose parent is being requested
  // $active boolean (0=false|1=true) determines whether to return any or only an active parent
  // $fields is a comma delimited list of fields to be returned in a $key=>$value array
  // Now works properly when an $id is passed or when parent id is the root of the doc tree
  // Last Modified: 2007-10-09 By Ralph to correct ongoing issues
    if($id==-1 || $id=="") {
      $id = $this->documentObject['parent'];
    }
    if($id==0) {
      return false;
    } else {
      $tbl = $this->db."site_content";
      $activeSql = $active==1 ? "AND $tbl.published=1 AND $tbl.deleted=0" : "";
      $sql = "SELECT $fields FROM $tbl WHERE $tbl.id=$id $activeSql";
      $result = $this->dbQuery($sql);
      $parent = @$this->fetchRow($result);
      return $parent;
    }
  }

  function getSnippetName() {
  // returns the textual name of the calling snippet
    return $this->currentSnippet;
  }

  function clearCache() {
  // deletes all cached documents from the ./assets/acahe directory
    $basepath=dirname(__FILE__);
    if (@$handle = opendir($basepath."/assets/cache")) {
      $filesincache = 0;
      $deletedfilesincache = 0;
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
          $filesincache += 1;
          if (preg_match ("/\.etoCache/", $file)) {
            $deletedfilesincache += 1;
            unlink($basepath."/assets/cache/".$file);
          }
        }
      }
      closedir($handle);
      return true;
    } else {
      return false;
    }
  }

  function makeUrl($id, $alias='', $args='') {
  // Modified by mikef
  // Last Modified: 2006-04-08 by Ralph Dahlgren
  // returns a properly formatted URL as of 0.6.1 Final
  // $id is a valid document id and is optional when sending an alias
  // $alias can now be sent without $id but may cause failures if the alias doesn't exist
  // $args is a URL compliant text string of $_GET key=value pairs
  // Examples: makeURL(45,'','?cms=Etomite') OR makeURL('','my_alias','?cms=Etomite')
  // ToDo: add conditional code to create $args from a $key=>$value array

    // make sure $id data type is not string
    if(!is_numeric($id) && $id!="") {
      $this->messageQuit("`$id` is not numeric and may not be passed to makeUrl()");
    }
    // assign a shorter base URL variable
    $baseURL=$this->config['www_base_path'];
    // if $alias was sent in the function call and the alias exists, use it
    if($this->config['friendly_alias_urls']==1 && isset($this->documentListing[$alias])) {
        $url = $baseURL.$this->config['friendly_url_prefix'].$alias.$this->config['friendly_url_suffix'];
    }
    // $alias wasn't sent or doesn't exist so try to get the documents alias based on id if it exists
    elseif($this->config['friendly_alias_urls']==1 && $this->aliases[$id]!="") {
      $url = $baseURL.$this->config['friendly_url_prefix'].$this->aliases[$id].$this->config['friendly_url_suffix'];
    }
    // only friendly URL's are enabled or previous alias attempts failed
    elseif($this->config['friendly_urls']==1) {
      $url = $baseURL.$this->config['friendly_url_prefix'].$id.$this->config['friendly_url_suffix'];
    }
    // for some reason nothing else has workd so revert to the standard URL method
    else {
      $url = $baseURL."index.php?id=$id";
    }
    // make sure only the first argument parameter is preceded by a "?"
    if(strlen($args)&&strpos($url, "?")) $args="&amp;".substr($args,1);
    return $url.$args;
  }

  function getConfig($name='') {
  // returns the requested configuration setting_value to caller
  // based on $key=>$value records stored in system_settings table
  // $name can be any valid setting_name
  // Example: getConfig('site_name')
    if(!empty($this->config[$name])) {
      return $this->config[$name];
    } else {
      return false;
    }
  }

  function getVersionData() {
  // returns a $key=>$value array of software package information to caller
    include("manager/includes/version.inc.php");
    $version = array();
    $version['release'] = $release;// Current Etomite release
    $version['code_name'] = $code_name;// Current Etomite codename
    $version['version'] = $small_version; // Current Etomite version
    $version['patch_level'] = $patch_level; // Revision number/suffix
    $version['full_appname'] = $full_appname; // Etomite Content Management System + $version + $patch_level + ($code_name)
    $version['full_slogan'] = $full_slogan; // Current Etomite slogan
    return $version;
  }

  function makeList($array, $ulroot='root', $ulprefix='sub_', $type='', $ordered=false, $tablevel=0, $tabstr="") {
  // returns either ordered or unordered lists based on passed parameters
  // $array can be a single or multi-dimensional $key=>$value array
  // $ulroot is the lists root CSS class name for controlling list-item appearance
  // $ulprefix is the prefix to send with recursive calls to this function
  // $type can be used to specifiy the type of the list-item marker (examples:disc,square,decimal,upper-roman,etc...)
  // $ordered determines whether the list is alphanumeric or symbol based (true=alphanumeric|false=symbol)
  // $tablevel is an internally used variable for determining depth of indentation on recursion
  // $tabstr can be used to send an alternative indentation string in place of the default tab character (added in 0.6.1 RTM)
  // [0614] Modified by Ralph for better functionality when sending multi-dimensional arrays

    // first find out whether the value passed is an array
    if(!is_array($array)) {
      return "<ul><li>Bad list</li></ul>";
    }
    if(!empty($type)) {
      $typestr = " style='list-style-type: $type'";
    } else {
      $typestr = "";
    }
    $tabs = "";
    for($i=0; $i<$tablevel; $i++) {
      $tabs .= $tabstr;
    }
    $listhtml = $ordered==true ? $tabs."<ol class='$ulroot'$typestr>" : $tabs."<ul class='$ulroot'$typestr>";
    foreach($array as $key=>$value) {
      if(is_array($value)) {
        $listhtml .= $tabs."<li>";
        if($ordered) $listhtml .= $key;
        $listhtml .= $this->makeList($value, $ulprefix.$ulroot, $ulprefix, $type, $ordered, $tablevel+1, $tabstr).$tabs."</li>";
      } else {
        $listhtml .= $tabs."<li>".$value."</li>";
      }
    }
    $listhtml .= $ordered==true ? $tabs."</ol>" : $tabs."</ul>" ;
    return $listhtml;
  }

  function userLoggedIn() {
  // returns an array of user details if logged in else returns false
  // array components returned are self-explanatory
    $userdetails = array();
    if(isset($_SESSION['validated'])) {
      $userdetails['loggedIn']=true;
      $userdetails['id']=strip_tags($_SESSION['internalKey']);
      $userdetails['username']=strip_tags($_SESSION['shortname']);
      return $userdetails;
    } else {
      return false;
    }
  }

  function getKeywords($id=0) {
  // returns a single dimensional array of document specific keywords
  // $id is the identifier of the document for which keywords have been requested
    if($id==0 || $id=="") {
      $id=$this->documentIdentifier;
    }
    $tbl = $this->db;
    $sql = "SELECT keywords.keyword FROM ".$tbl."site_keywords AS keywords INNER JOIN ".$tbl."keyword_xref AS xref ON keywords.id=xref.keyword_id WHERE xref.content_id = $id";
    $result = $this->dbQuery($sql);
    $limit = $this->recordCount($result);
    $keywords = array();
    if($limit > 0)   {
      for($i=0;$i<$limit;$i++) {
        $row = $this->fetchRow($result);
        $keywords[] = $row['keyword'];
      }
    }
    return $keywords;
  }

  function runSnippet($snippetName, $params=array()) {
  // returns the processed results of a snippet to the caller
  // $snippetName = name of the snippet to process
  // $params = array of $key=>$value parameter pairs passed to the snippet
    return $this->evalSnippet($this->snippetCache[$snippetName], $params);
  }

  function getChunk($chunkName) {
  // returns the contents of a cached chunk as code
  // $chunkName = textual name of the chunk to be returned
    return base64_decode($this->chunkCache[$chunkName]);
  }

  function putChunk($chunkName) {
  // at present this is only an alias of getChunk() and is not used
    return $this->getChunk($chunkName);
  }

  function parseChunk($chunkName, $chunkArr, $prefix="{", $suffix="}") {
  // returns chunk code with marker tags replaced with $key=>$value values
  // $chunkName = the textual name of the chunk to be parsed
  // $chunkArr = a single dimensional $key=>$value array of tags and values
  // $prefix and $suffix = tag begin and end markers which can be customized when called
  // Modified 2007-09-28 by Ralph to allow $key=>array($keys=>$values) to be
  // sent which will be processed by looping through code wrapped within {tag}{/tag} pairs.
  // Example: {tag}<tr><td>{col1}</td><td>{col2}</td></tr>{/tag}
    if(!is_array($chunkArr) || count($chunkArr) < 1) {
      return false;
    }
    $chunk = $this->getChunk($chunkName);
    foreach($chunkArr as $key => $value)
    {
      if(!is_array($value))
      {
        $chunk = str_replace($prefix.$key.$suffix, $value, $chunk);
      }
      else
      {
        if(preg_match("|".$prefix.$key.$suffix."(.+)".$prefix.'/'.$key.$suffix."|s", $chunk, $match)
        && count($value) > 0)
        {
          $loopData = '';
          foreach($value as $row)
          {
            $loopTemp = $match['1'];
            foreach($row as $loopKey => $loopValue)
            {
              $loopTemp = str_replace($prefix.$loopKey.$suffix, $loopValue, $loopTemp);
            }
            $loopData .= $loopTemp;
          }
          $chunk = str_replace($match['0'], $loopData, $chunk);
        }
      }
    }
    return $chunk;
  }

  function getUserData() {
  // returns user agent related (browser) info in a $key=>$value array using the phpSniff class
  // can be used to perform conditional operations based on visitors browser specifics
  // items returned: ip,ua,browser,long_name,version,maj_ver,min_vermin_ver,letter_ver,javascript,platform,os,language,gecko,gecko_ver,html,images,frames,tables,java,plugins,css2,css1,iframes,xml,dom,hdml,wml,must_cache_forms,avoid_popup_windows,cache_ssl_downloads,break_disposition_header,empty_fil,e_input_value,scrollbar_in_way
    include_once("manager/includes/etomiteExtenders/getUserData.extender.php");
    return $tmpArray;
  }

  function getSiteStats() {
  // returns a single dimensional $key=>$value array of the visitor log totals
  // array $keys are  today, month, piDay, piMonth, piAll, viDay, viMonth, viAll, visDay, visMonth, visAll
  // today = date in YYYY-MM-DD format
  // month = two digit month (01-12)
  // pi = page impressions per Day, Month, All
  // vi = total visits
  // vis = unique visitors
    $tbl = $this->db."log_totals";
    $sql = "SELECT * FROM $tbl";
    $result = $this->dbQuery($sql);
    $tmpRow = $this->fetchRow($result);
    return $tmpRow;
  }

  //
  // START: Database abstraction layer related functions
  //

  function dbConnect() {
  // function to connect to the database
    $tstart = $this->getMicroTime();
    if(@!$this->rs = mysql_connect($this->dbConfig['host'], $this->dbConfig['user'], $this->dbConfig['pass'])) {
      $this->messageQuit("Failed to create the database connection!");
    } else {
      mysql_select_db($this->dbConfig['dbase']);
      $tend = $this->getMicroTime();
      $totaltime = $tend-$tstart;
      if($this->config['dumpSQL']) {
        $this->queryCode .= "<fieldset style='text-align:left'><legend>Database connection</legend>".sprintf("Database connection was created in %2.4f s", $totaltime)."</fieldset><br />";
      }
      $this->queryTime = $this->queryTime+$totaltime;
    }
  }

  function dbQuery($query) {
  // function to query the database
    // check the connection and create it if necessary
    if(empty($this->rs)) {
      $this->dbConnect();
    }
    $tstart = $this->getMicroTime();
    if(@!$result = mysql_query($query, $this->rs)) {
      $this->messageQuit("Execution of a query to the database failed", $query);
    } else {
      $tend = $this->getMicroTime();
      $totaltime = $tend-$tstart;
      $this->queryTime = $this->queryTime+$totaltime;
      if($this->config['dumpSQL']) {
        $this->queryCode .= "<fieldset style='text-align:left'><legend>Query ".($this->executedQueries+1)." - ".sprintf("%2.4f s", $totaltime)."</legend>".$query."</fieldset><br />";
      }
      $this->executedQueries = $this->executedQueries+1;
      if(count($result) > 0) {
        return $result;
      } else {
        return false;
      }
    }
  }

  function recordCount($rs) {
  // function to count the number of rows in a record set
    return mysql_num_rows($rs);
  }

  function fetchRow($rs, $mode='assoc') {
  // [0614] object mode added by Ralph
    if($mode=='assoc') {
      return mysql_fetch_assoc($rs);
    } elseif($mode=='num') {
      return mysql_fetch_row($rs);
    } elseif($mode=='both') {
      return mysql_fetch_array($rs, MYSQL_BOTH);
    } elseif($mode=='object') {
      return mysql_fetch_object($rs);
    } else {
      $this->messageQuit("Unknown get type ($mode) specified for fetchRow - must be empty, 'assoc', 'num', 'both', or 'object'.");
    }
  }

  function affectedRows() {
  // returns the number of rows affected by the last query
    return mysql_affected_rows($this->rs);
  }

  function insertId() {
  // returns auto-increment id of the last insert
    return mysql_insert_id($this->rs);
  }

  function dbClose() {
  // function to close a database connection
    mysql_close($this->rs);
  }

  function getIntTableRows($fields="*", $from="", $where="", $sort="", $dir="ASC", $limit="", $push=true, $addPrefix=true) {
  // function to get rows from ANY internal database table
  // This function works much the same as the getDocuments() function. The main differences are that it will accept a table name and can use a LIMIT clause.
  // $fields = a comma delimited string: $fields="name,email,age"
  // $from = name of the internal Etomite table which data will be selected from without database name or table prefix ($from="user_messages")
  // $where = any optional WHERE clause: $where="parent=10 AND published=1 AND type='document'"
  // $sort = field you wish to sort by: $sort="id"
  // $dir = ASCending or DESCending sort order
  // $limit = maximum results returned: $limit="3" or $limit="10,3"
  // $push = ( true = [default] array_push results into a multi-demensional array | false = return MySQL resultset )
  // $addPrefix = whether to check for and/or add $this->dbConfig['table_prefix'] to the table name
  // Returns FALSE on failure.
    if($from=="") return false;
    // added multi-table abstraction capability
    if(is_array($from)) {
      $tbl = "";
      foreach ($from as $_from) $tbl .= $this->db.$_from.", ";
      $tbl = substr($tbl,0,-2);
    } else {
      $tbl = ($this->dbConfig['table_prefix'] != ''
              && strpos($from,$this->dbConfig['table_prefix']) === 0
              || !$addPrefix)
              ? $this->dbConfig['dbase'].".".$from
              : $this->db.$from;
    }
    $where = ($where != "") ? "WHERE $where" : "";
    $sort = ($sort != "") ? "ORDER BY $sort $dir" : "";
    $limit = ($limit != "") ? "LIMIT $limit" : "";
    $sql = "SELECT $fields FROM $tbl $where $sort $limit;";
    $result = $this->dbQuery($sql);
    if(!$push) return $result;
    $resourceArray = array();
    for($i=0;$i<@$this->recordCount($result);$i++)  {
      array_push($resourceArray,@$this->fetchRow($result));
    }
    return $resourceArray;
  }

  function putIntTableRow($fields="", $into="", $addPrefix=true) {
  // function to put a row into ANY internal database table
  // INSERT's a new table row into ANY internal Etomite database table. No data validation is performed.
  // $fields = a $key=>$value array: $fields=("name"=>$name,"email"=$email,"age"=>$age)
  // $into = name of the internal Etomite table which will receive the new data row without database name or table prefix: $into="user_messages"
  // $addPrefix = whether to check for and/or add $this->dbConfig['table_prefix'] to the table name
  // Returns FALSE on failure.
    if(($fields=="") || ($into=="")){
      return false;
    } else {
      $tbl = ($this->dbConfig['table_prefix'] != ''
              && strpos($from,$this->dbConfig['table_prefix']) === 0
              || !$addPrefix)
              ? $this->dbConfig['dbase'].".".$into
              : $this->db.$into;
      $sql = "INSERT INTO $tbl SET ";
      foreach($fields as $key=>$value) {
        $sql .= "`".$key."`=";
        if (is_numeric($value)) $sql .= $value.",";
        else $sql .= "'".$value."',";
      }
      $sql = rtrim($sql,",");
      $sql .= ";";
      $result = $this->dbQuery($sql);
      return $result;
    }
  }

  function updIntTableRows($fields="", $into="", $where="", $sort="", $dir="ASC", $limit="", $addPrefix=true) {
  // function to update a row into ANY internal database table
  // $fields = a $key=>$value array: $fields=("name"=>$name,"email"=$email,"age"=>$age)
  // $into = name of the internal Etomite table which will receive the new data row without database name or table prefix: $into="user_messages"
  // $where = any optional WHERE clause: $where="parent=10 AND published=1 AND type='document'"
  // $sort = field you wish to sort by: $sort="id"
  // $dir = ASCending or DESCending sort order
  // $limit = maximum results returned: $limit="3" or $limit="10,3"
  // $addPrefix = whether to check for and/or add $this->dbConfig['table_prefix'] to the table name
  // Returns FALSE on failure.
    if(($fields=="") || ($into=="")){
      return false;
    } else {
      $where = ($where != "") ? "WHERE $where" : "";
      $sort = ($sort != "") ? "ORDER BY $sort $dir" : "";
      $limit = ($limit != "") ? "LIMIT $limit" : "";
      $tbl = ($this->dbConfig['table_prefix'] != ''
              && strpos($from,$this->dbConfig['table_prefix']) === 0
              || !$addPrefix)
              ? $this->dbConfig['dbase'].".".$into
              : $this->db.$into;
      $sql = "UPDATE $tbl SET ";
      foreach($fields as $key=>$value) {
        $sql .= "`".$key."`=";
        if (is_numeric($value)) $sql .= $value.",";
        else $sql .= "'".$value."',";
      }
      $sql = rtrim($sql,",");
      $sql .= " $where $sort $limit;";
      $result = $this->dbQuery($sql);
      return $result;
    }
  }

  function getExtTableRows($host="", $user="", $pass="", $dbase="", $fields="*", $from="", $where="", $sort="", $dir="ASC", $limit="", $push=true) {
  // function to get table rows from an external MySQL database
  // Performance is identical to getIntTableRows plus additonal information regarding the external database.
  // $host is the hostname where the MySQL database is located: $host="localhost"
  // $user is the MySQL username for the external MySQL database: $user="username"
  // $pass is the MySQL password for the external MySQL database: $pass="password"
  // $dbase is the MySQL database name to which you wish to connect: $dbase="extdata"
  // $fields should be a comma delimited string: $fields="name,email,age"
  // $from is the name of the External database table that data rows will be selected from: $from="contacts"
  // $where can be any optional WHERE clause: $where="parent=10 AND published=1 AND type='document'"
  // $sort can be set to whichever field you wish to sort by: $sort="id"
  // $dir can be set to ASCending or DESCending sort order
  // $limit can be set to limit results returned: $limit="3" or $limit="10,3"
  // $push = ( true = [default] array_push results into a multi-demensional array | false = return MySQL resultset )
  // Returns FALSE on failure.
    if(($host=="") || ($user=="") || ($pass=="") || ($dbase=="") || ($from=="")){
      return false;
    } else {
      $where = ($where != "") ? "WHERE  $where" : "";
      $sort = ($sort != "") ? "ORDER BY $sort $dir" : "";
      $limit = ($limit != "") ? "LIMIT $limit" : "";
      $tbl = $dbase.".".$from;
      $this->dbExtConnect($host, $user, $pass, $dbase);
      $sql = "SELECT $fields FROM $tbl $where $sort $limit;";
      $result = $this->dbQuery($sql);
      if(!$push) return $result;
      $resourceArray = array();
      for($i=0;$i<@$this->recordCount($result);$i++)  {
        array_push($resourceArray,@$this->fetchRow($result));
      }
      return $resourceArray;
    }
  }

  function putExtTableRow($host="", $user="", $pass="", $dbase="", $fields="", $into="") {
  // function to update a row into an external database table
  // $host = hostname where the MySQL database is located: $host="localhost"
  // $user = MySQL username for the external MySQL database: $user="username"
  // $pass = MySQL password for the external MySQL database: $pass="password"
  // $dbase = MySQL database name to which you wish to connect: $dbase="extdata"
  // $fields = a $key=>$value array: $fields=("name"=>$name,"email"=$email,"age"=>$age)
  // $into = name of the external database table which will receive the new data row: $into="contacts"
  // $where = optional WHERE clause: $where="parent=10 AND published=1 AND type='document'"
  // $sort = whichever field you wish to sort by: $sort="id"
  // $dir = ASCending or DESCending sort order
  // $limit = limit maximum results returned: $limit="3" or $limit="10,3"
  // Returns FALSE on failure.
    if(($host=="") || ($user=="") || ($pass=="") || ($dbase=="") || ($fields=="") || ($into=="")){
      return false;
    } else {
      $this->dbExtConnect($host, $user, $pass, $dbase);
      $tbl = $dbase.".".$into;
      $sql = "INSERT INTO $tbl SET ";
      foreach($fields as $key=>$value) {
        $sql .= "`".$key."`=";
        if (is_numeric($value)) $sql .= $value.",";
        else $sql .= "'".$value."',";
      }
      $sql = rtrim($sql,",");
      $result = $this->dbQuery($sql);
      return $result;
    }
  }

  function updExtTableRows($host="", $user="", $pass="", $dbase="", $fields="", $into="", $where="", $sort="", $dir="ASC", $limit="") {
  // function to put a row into an external database table
  // INSERT's a new table row into an external database table. No data validation is performed.
  // $host = hostname where the MySQL database is located: $host="localhost"
  // $user = MySQL username for the external MySQL database: $user="username"
  // $pass = MySQL password for the external MySQL database: $pass="password"
  // $dbase = MySQL database name to which you wish to connect: $dbase="extdata"
  // $fields = a $key=>$value array: $fields=("name"=>$name,"email"=$email,"age"=>$age)
  // $into = name of the external database table which will receive the new data row: $into="user_messages"
  // Returns FALSE on failure.
    if(($fields=="") || ($into=="")){
      return false;
    } else {
      $this->dbExtConnect($host, $user, $pass, $dbase);
      $tbl = $dbase.".".$into;
      $where = ($where != "") ? "WHERE $where" : "";
      $sort = ($sort != "") ? "ORDER BY $sort $dir" : "";
      $limit = ($limit != "") ? "LIMIT $limit" : "";
      $sql = "UPDATE $tbl SET ";
      foreach($fields as $key=>$value) {
        $sql .= "`".$key."`=";
        if (is_numeric($value)) $sql .= $value.",";
        else $sql .= "'".$value."',";
      }
      $sql = rtrim($sql,",");
      $sql .= " $where $sort $limit;";
      $result = $this->dbQuery($sql);
      return $result;
    }
  }

  function dbExtConnect($host, $user, $pass, $dbase) {
  // function used to connect to external database
  // This function is called by other functions and should not need to be called directly.
  // $host = hostname where the MySQL database is located: $host="localhost"
  // $user = MySQL username for the external MySQL database: $user="username"
  // $pass = MySQL password for the external MySQL database: $pass="password"
  // $dbase = MySQL database name to which you wish to connect: $dbase="extdata"
    $tstart = $this->getMicroTime();
    if(@!$this->rs = mysql_connect($host, $user, $pass)) {
      $this->messageQuit("Failed to create connection to the $dbase database!");
    } else {
      mysql_select_db($dbase);
      $tend = $this->getMicroTime();
      $totaltime = $tend-$tstart;
      if($this->config['dumpSQL']) {
        $this->queryCode .= "<fieldset style='text-align:left'><legend>Database connection</legend>".sprintf("Database connection to %s was created in %2.4f s", $dbase, $totaltime)."</fieldset><br />";
      }
      $this->queryTime = $this->queryTime+$totaltime;
    }
  }

  function dbExtQuery($host, $user, $pass, $dbase, $query) {
  // function to query an external database
  // This function can be used to perform queries on any external MySQL database.
  // $host = hostname where the MySQL database is located: $host="localhost"
  // $user = MySQL username for the external MySQL database: $user="username"
  // $pass = MySQL password for the external MySQL database: $pass="password"
  // $dbase = MySQL database name to which you wish to connect: $dbase="extdata"
  // $query = SQL query to be performed: $query="DELETE FROM sometable WHERE somefield='somevalue';"
  // Returns error on fialure.
    $tstart = $this->getMicroTime();
    $this->dbExtConnect($host, $user, $pass, $dbase);
    if(@!$result = mysql_query($query, $this->rs)) {
      $this->messageQuit("Execution of a query to the database failed", $query);
    } else {
      $tend = $this->getMicroTime();
      $totaltime = $tend-$tstart;
      $this->queryTime = $this->queryTime+$totaltime;
      if($this->config['dumpSQL']) {
        $this->queryCode .= "<fieldset style='text-align:left'><legend>Query ".($this->executedQueries+1)." - ".sprintf("%2.4f s", $totaltime)."</legend>".$query."</fieldset><br />";
      }
      $this->executedQueries = $this->executedQueries+1;
      return $result;
    }
  }

  function intTableExists($table) {
  // Modified 2008-05-10 [v1.1] by Ralph to use FROM clause
  // function to determine whether or not a specific database table exists
  // $table = the table name, including prefix, to check for existence
  // example: $table = "etomite_new_table"
  // Returns boolean TRUE or FALSE
    if($table == null) return false;
    $query = "SHOW TABLE STATUS FROM ".$this->dbConfig['dbase']." LIKE '".$table."'";
    $rs = $this->dbQuery($query);
    return ($row = $this->fetchRow($rs)) ? true : false;
  }

  function extTableExists($host, $user, $pass, $dbase, $table) {
  // Added 2006-04-15 by Ralph Dahlgren
  // function to determine whether or not a specific database table exists
  // $host = hostname where the MySQL database is located: $host="localhost"
  // $user = MySQL username for the external MySQL database: $user="username"
  // $pass = MySQL password for the external MySQL database: $pass="password"
  // $dbase = MySQL database name to which you wish to connect: $dbase="extdata"
  // $table = the table name to check for existence: $table="some_external_table"
  // Returns boolean TRUE or FALSE
    $query = "SHOW TABLE STATUS LIKE '".$table."'";
    $rs = $this->dbExtQuery($host, $user, $pass, $dbase, $query);
    return ($row = $this->fetchRow($rs)) ? true : false;
  }

  //
  // END: Database abstraction layer related functions
  //

  function getFormVars($method="",$prefix="",$trim="",$REQUEST_METHOD) {
  // function to retrieve form results into an associative $key=>$value array
  // This function is intended to be used to retrieve an associative $key=>$value array of form data which can be sent directly to the putIntTableRow() or putExttableRow() functions. This function performs no data validation. By utilizing $prefix it is possible to // retrieve groups of form results which can be used to populate multiple database tables. This funtion does not contain multi-record form capabilities.
  // $method = form method which can be POST or GET and is not case sensitive: $method="POST"
  // $prefix = used to specifiy prefixed groups of form variables so that a single form can be used to populate multiple database // tables. If $prefix is omitted all form fields will be returned: $prefix="frm_"
  // $trim = boolean value ([true or 1]or [false or 0]) which tells the function whether to trim off the field prefixes for a group // resultset
  // $RESULT_METHOD is sent so that if $method is omitted the function can determine the form method internally. This system variable cannot be assigned a user-specified value.
  // Returns FALSE if form method cannot be determined
    $results = array();
    $method = strtoupper($method);
    if($method == "") $method = $REQUEST_METHOD;
    if($method == "POST") $method = &$_POST;
    elseif($method == "GET") $method = &$_GET;
    elseif($method == "FILES") $method = &$_FILES;
    else return false;
    reset($method);
    foreach($method as $key=>$value) {
      if(($prefix != "") && (substr($key,0,strlen($prefix)) == $prefix)) {
        if($trim) {
          $pieces = explode($prefix, $key,2);
          $key = $pieces[1];
          $results[$key] = $value;
        }
        else $results[$key] = $value;
      }
      elseif($prefix == "") $results[$key] = $value;
    }
    return $results;
  }

  function arrayValuesToList($rs,$col) {
  // Converts a column of a resultset array into a comma delimited list (col,col,col)
  // $rs = query resultset OR an two dimensional associative array
  // $col = the target column to compile into a comma delimited string
  // Returns error on fialure.
    if(is_array($col)) return false;
    $limit = $this->recordCount($rs);
    $tmp = "";
    if($limit > 0) {
      for ($i = 0; $i < $limit; $i++) {
        $row = $this->fetchRow($rs);
        $tmp[] = $row[$col];
      }
      return implode(",", $tmp);
    } else {
      return false;
    }
  }

  function mergeCodeVariables($content="",$rs="",$prefix="{",$suffix="}",$oddStyle="",$evenStyle="",$tag="") {
  //  parses any string data for template tags and populates from a resultset or single associative array
  //  $content = the string data to be parsed
  //  $rs = the resultset or associateve array which contains the data to check for possible insertion
  //  $prefix & $suffix = the tags start and end characters for search and replace purposes
  //  $oddStyle & $evenStyle = CSS info sent as style='inline styles' or class='className'
  //  $tag = the HTML tag to use as a container for each template object record
    if((!is_array($rs)) || ($content == "")) return false;
    if(!is_array($rs[0])) $rs = array($rs);
    $i = 1;
    foreach($rs as $row) {
      //$rowStyle = fmod($i,2) ? $oddStyle : $evenStyle;
      $_SESSION['rowStyle'] = ($_SESSION['rowStyle'] == $oddStyle) ? $evenStyle : $oddStyle;
      $tmp = $content;
      $keys = array_keys($row);
      foreach($keys as $key) {
        $tmp = str_replace($prefix.$key.$suffix, $row[$key], $tmp);
      }
      if((($oddStyle > "") || ($evenStyle > "")) && ($tag > "")) {
        //$output .= "\n<$tag ".$rowStyle.">$tmp</$tag>\n";
        $output .= "\n<$tag ".$_SESSION['rowStyle'].">$tmp</$tag>\n";
      } else {
        $output .= "$tmp\n";
      }
      $i++;
    }
    return $output;
  }

  function getAuthorData($internalKey){
  // returns a $key=>$value array of information from the user_attributes table
  // $internalKey which correlates with a documents createdby value.
  // Uasge: There are several ways in which this function can be called.
  //   To call this function from within a snippet you could use
  //   $author = $etomite->getAuthorData($etomite->documentObject['createdby'])
  //   or $author = $etomite->getAuthorData($row['createdby']) or $author = $etomite->getAuthorData($rs[$i]['createdby']).
  //   Once the $key=>$value variable, $author, has been populated you can access the data by using code similar to
  //   $name = $author['fullname'] or $output .= $author['email'] for example.
  //   There is also a snippet named GetAuthorData which uses the format:
  //   [[GetAuthorData?internalKey=[*createdby*]&field=fullname]]
  // Last Modified: 2008-04-17 [v1.0] by Ralph A. Dahlgren
  // * fixed to return false if user record not found
    $tbl = $this->db."user_attributes";
    $sql = "SELECT * FROM $tbl WHERE $tbl.internalKey = ".$internalKey;
    $result = $this->dbQuery($sql);
    $limit = $this->recordCount($result);
    if($limit < 1) {
      return false;
    } else {
      $user = $this->fetchRow($result);
      return $user;
    }
  }

  //
  // Permissions and Authentication related functions
  //

  function checkUserRole($action="",$user="",$id="") {
  //  determine document permissions for a user
  //  $action = any role action name (edit_document,delete_document,etc.)
  //  $user = user id or internalKey
  //  $id = id of document in question
  //  because user permissions are stored in the session data the users role is not required
  // Returns error on fialure.
    if(($this->config['use_udperms'] == 0) || ($_SESSION['role'] == 1)) return true;
    if($user == "") $user = $_SESSION['internalKey']; // Modified 2006-08-04 Ralph
    if($id == "") $id = $this->documentIdentifier;
    if($user == "" || $id == "" || $_SESSION['role'] == "") return false;
    if(($action != "") && ($_SESSION['permissions'][$action] != 1)) return false;
    if(($document == 0) && ($this->config['udperms_allowroot'] == 1)) return true;

    if($_SESSION['permissions'][$action] == 1) {
      return true;
    } else {
      return false;
    }
  }

  function checkPermissions($id="")
  {
  //  determines user permissions for the current document
  // Returns error on fialure.
  // $id = id of document whose permissions are to be checked against the current user
  // Modified 2007-03-07 by Randy Casburn for improved overall performance
    $user = $_SESSION['internalKey'];
    $document = ($id!="") ? $id : $this->documentIdentifier;
    $role = $_SESSION['role'];

    if($_SESSION['internalKey']=="") return false;
    if($role==1) return true;  // administrator - grant all document permissions
    if($document==0 && $this->config['udperms_allowroot']==0) return false;

    if($this->config['use_udperms']==0
    || $this->config['use_udperms']==""
    || !isset($this->config['use_udperms']))
    {
      return true; // user document permissions aren't in use
    }

    // Added by Ralph 2006-07-07 to handle visitor permissions checks properly
    // Modified by Randy and nalagar in [0614]
    if($this->config['use_uvperms']==0
    || $this->config['use_uvperms']==""
    || !isset($this->config['use_uvperms']))
    {
      return true; // visitor document permissions aren't in use
    }
    // Returns true (1) or false (0) depending on if
    // the user is/is not in any group that has permissions
    // to access this document
    $sql = "SELECT count(".$this->db."member_groups.member) as Auth
    FROM ".$this->db."document_groups,
        ".$this->db."membergroup_access,
        ".$this->db."member_groups
    WHERE
        ".$this->db."document_groups.document = ".$document." AND
        ".$this->db."document_groups.document_group = ".$this->db."membergroup_access.documentgroup AND
        ".$this->db."membergroup_access.membergroup = ".$this->db."member_groups.user_group AND
        ".$this->db."member_groups.member = ".$user.";";
    $rs = $this->dbQuery($sql);
    $checkPermissions = $this->fetchRow($rs);
    // Query will only return the value of 1 or 0
    // 1 = the user is in a group that has permission to access this document
    // 0 = the user is NOT in a group that has permission to access this document
    if($checkPermissions['Auth']) {
      return true;
    }

    // if all else fails, return false just to be safe
    return false;
  }

  function userLogin($username,$password,$rememberme=0,$url="",$id="",$alias="",$use_captcha=0,$captcha_code="") {
  // Performs user login and permissions assignment
  // And combination of the following variables can be sent
  // Defaults to current document
  // $url   = and fully qualified URL (no validation performed)
  // $id    = an existing document ID (no validation performed)
  // $alias = any document alias (no validation performed)

    // include the crypto thing
    include_once("./manager/includes/crypt.class.inc.php");

    // include_once the error handler
    include_once("./manager/includes/error.class.inc.php");
    $e = new errorHandler;

    if($use_captcha==1) {
      if($_SESSION['veriword']!=$captcha_code) {
        unset($_SESSION['veriword']);
        $e->setError(905);
        $e->dumpError();
        $newloginerror = 1;
      }
    }
    unset($_SESSION['veriword']);

    $username = htmlspecialchars($username);
    $givenPassword = htmlspecialchars($password);

    $sql = "SELECT ".$this->db."manager_users.*, ".$this->db."user_attributes.* FROM ".$this->db."manager_users, ".$this->db."user_attributes WHERE ".$this->db."manager_users.username REGEXP BINARY '^".$username."$' and ".$this->db."user_attributes.internalKey=".$this->db."manager_users.id;";
    $rs = $this->dbQuery($sql);
    $limit = $this->recordCount($rs);

    if($limit==0 || $limit>1)
    {
        $e->setError(900);
        $e->dumpError();
    }

    $row = $this->fetchRow($rs);

    $_SESSION['shortname']         = $username;
    $_SESSION['fullname']          = $row['fullname'];
    $_SESSION['email']             = $row['email'];
    $_SESSION['phone']             = $row['phone'];
    $_SESSION['mobilephone']       = $row['mobilephone'];
    $_SESSION['internalKey']       = $row['internalKey'];
    $_SESSION['failedlogins']      = $row['failedlogincount'];
    $_SESSION['lastlogin']         = $row['lastlogin'];
    $_SESSION['role']              = $row['role'];
    $_SESSION['nrlogins']          = $row['logincount'];

    if($row['failedlogincount']>=$this->config['max_attempts'] && $row['blockeduntil']>time())
    {
        session_destroy();
        session_unset();
        $e->setError(902);
        $e->dumpError();
    }

    if($row['failedlogincount']>=$this->config['max_attempts'] && $row['blockeduntil']<time())
    {
      $sql = "UPDATE ".$this->db."user_attributes SET failedlogincount='0', blockeduntil='".(time()-1)."' where internalKey=".$row['internalKey'].";";
      $rs = $this->dbQuery($sql);
    }

    if($row['blocked']=="1")
    {
      session_destroy();
      session_unset();
      $e->setError(903);
      $e->dumpError();
    }

    if($row['blockeduntil']>time())
    {
      session_destroy();
      session_unset();
      $e->setError(904);
      $e->dumpError();
    }

    if($row['password'] != md5($givenPassword))
    {
        session_destroy();
        session_unset();
        $e->setError(901);
        $newloginerror = 1;
        $e->dumpError();
    }

    $sql="SELECT * FROM ".$this->db."user_roles where id=".$row['role'].";";
    $rs = $this->dbQuery($sql);
    $row = $this->fetchRow($rs);
    $_SESSION['permissions'] = $row;
    $_SESSION['frames'] = 0;
    $_SESSION['validated'] = 1;

    if($url=="") {
      $url = $this->makeURL($id,$alias);
    }
    $this->sendRedirect($url);
  }

  function userLogout($url="",$id="",$alias="") {
  // Use the managers logout routine to end the current session
  // And combination of the following variables can be sent
  // Defaults to index.php in the current directory
  // $url   = any fully qualified URL (no validation performed)
  // $id    = an existing document ID (no validation performed)
  // $alias = any document alias (no validation performed)
    if($url == "") {
      if($alias == "") {
        $id = ($id != "") ? $id : $this->documentIdentifier;
        $rs = $this->getDocument($id,'alias');
        $alias = $rs['alias'];
      } else {
        $id = 0;
      }
      $url = $this->makeURL($id,$alias);
    }
    if($url != "") {
      include_once("manager/processors/logout.processor.php");
    }
  }

  function getCaptchaNumber($length, $alt='Captcha Number', $title='Security Code') {
  // returns a Captcha Number image to caller and stores value in $_SESSION['captchNumber']
  // $length = number of digits to return
  // $alt = alternate text if image cannot be displayed
  // $title = message to display for onhover event
    if($length < 1) return false;
    return '<img src="./manager/includes/captchanumbers/captchaNumber.php?size='.$length.'" alt="'.$alt.'" title="'.$title.'" />';
  }

  function validCaptchaNumber($number) {
  // returns Captcha Number validation back to caller - boolean (true|false)
  // $number = number entered by user for validation (example: $_POST['captchaNumber'])
    $result = (isset ($_SESSION['captchaNumber']) && $_SESSION['captchaNumber'] == $number) ? true : false;
    return $result;
  }

  function getCaptchaCode($alt='CaptchaCode', $title='Security Code', $width="148", $height="80", $refresh=false) {
  // returns a CaptchaCode image to caller and stores value in $_SESSION['captchCode']
  // $alt = alternate text if image cannot be displayed
  // $title = message to display for onhover event
  // $width & height = desired width and height of returned image
  // $refresh = boolean [true|false] flag to turn on|off link creation [v1.0] - Ralph
  // $dummy = rand();
    $code = '<img src="manager/includes/captchaCode.php?dummy='.rand().'&amp;sessid='.session_id().'&amp;realm=IN_ETOMITE_PARSER" width="'.$width.'" height="'.$height.'" alt="'.$_lang["login_captcha_message"].'" title="'.$title.'" />';
    if($refresh)
    {
      $code = "<a href=\"\">$code</a>";
    }
    return $code;
  }

  function validCaptchaCode($captchaCode) {
  // returns CaptchaCode validation back to caller - boolean (true|false)
  // $captchaCode = code entered by user for validation (example: $_POST['captchaCode'])
    $result = ($_SESSION['veriword'] == $captchaCode) ? true : false;
    return $result;
  }

  //
  // END: Permissions and Authentication related functions
  //

/***************************************************************************************/
/* END: Etomite API functions
/***************************************************************************************/

// End of etomite class.
}

/***************************************************************************
 Filename: index.php
 Function: This file loads and executes the parser.
/***************************************************************************/

// before we do anything, let's help avoid XSS attacks
$_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);

// first, set some settings, and do some stuff
$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $tstart = $mtime;
@ini_set('session.use_trans_sid', false);
@ini_set("url_rewriter.tags","");
// header for weird cookie stuff. Blame IE.
//header('P3P: CP="NOI NID ADMa OUR IND UNI COM NAV"');
ob_start();
// set error reporting level (can be changed in a production environment)
error_reporting(E_ALL);
// let scripts know that it is the parser calling
define("IN_ETOMITE_PARSER", "true");
// let scripts know that it is the manager calling
//define("IN_ETOMITE_SYSTEM", "true");

// get the required includes and/or additional classes
// contents of manager/includes/config.inc.php can be copied and pasted here for a small speed increase
@include("manager/includes/config.inc.php");
// If config.inc.php doesn't exist or isn't complete, display installer link and die
if(empty($database_type) || !file_exists("manager/includes/config.inc.php"))
{
 die("Please run the Etomite <a href=\"./install/\">install utility</a>!");
}
// if the form class will not be used this include can be rearked for a small speed increase
include("manager/includes/form_class.php");
// create a customized session
startCMSSession();
// initiate a new document parser and additional classes
$etomite = new etomite;
// set some options
$etomite->printable = "Printable Page"; // Name of Printable Page template
// the following settings are for blocking search bot page hit logging
$etomite->useblockLogging = true;
$etomite->blockLogging = "/(google|bot|msn|slurp|spider|agent|validat|miner|walk|crawl|robozilla|search|combine|theophrastus|larbin|dmoz)/i";
// these settings allow for fine tuning the parser recursion
$etomite->snippetParsePasses = 5; # Original default: 3
$etomite->nonCachedSnippetParsePasses = 5; # Original default: 2
// feed the parser the execution start time
$etomite->tstart = $tstart;
// execute the parser
$etomite->executeParser();
// flush the content buffer
ob_end_flush();
// ANY SETTINGS YOU DIDN'T FIND HERE HAVE BEEN MOVED TO THE CONFIGURATION PANEL
// END: index.php -- Etomite parser
?>
