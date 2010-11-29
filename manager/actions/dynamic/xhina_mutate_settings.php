<?php
// xinha_mutate_settings.php
// XHINA Editor Initialisation
// Last Modified 2008-05-02 [v1.0] by Ralph A. Dahlgren
?>
<textarea id="ta" name="ta" rows="30" cols="100" onChange="documentDirty=true;" style="width:98%;"><?php echo htmlspecialchars($content['content']); ?></textarea>

<script type="text/javascript">
  _editor_url  = document.location.href.replace(/index.php.*/, 'media/xinha/');
  _editor_lang = "en";
  _editor_skin = "<?php echo $xSkin='' ? '' : $xSkin; ?>";
</script>
<script type="text/javascript" src="./media/xinha/htmlarea.js"></script>
<script>
  xinha_editors = null;
  xinha_init    = null;
  xinha_config  = null;
  xinha_plugins = null;
  xinha_init =  function()
  {
    xinha_plugins = xinha_plugins ? xinha_plugins : [''];
    if(!HTMLArea.loadPlugins(xinha_plugins, xinha_init)) return;
    xinha_editors = xinha_editors ? xinha_editors : ['ta'];
    xinha_config = xinha_config ? xinha_config() : new HTMLArea.Config();
// begin Ian's custom xinha config
    xinha_config.height = '400px';
    // begin toolbar config
    xinha_config.toolbar =
    [
      ["formatblock","bold","italic","strikethrough","separator"],
      ["justifyleft","justifycenter","justifyright","justifyfull","separator"],
      ["insertorderedlist","insertunorderedlist","outdent","indent","separator"],
      ["inserthorizontalrule","createlink","insertimage","inserttable","separator"],
      ["undo","redo"], (HTMLArea.is_gecko ? [] : ["cut","copy","paste"]),["separator"],
      ["killword","clearfonts","removeformat","toggleborders","separator"],
      ["selectall","print","separator","htmlmode","showhelp"
      <?php echo $xp_Stylist==1 ? ",\"stylist\"" : ""; ?>]
    ];
    // end toolbar config

    // path to custom site stylesheet for real-time development styling
    //xinha_config.pageStyleSheets = ['manager/media/xinha/xinha.css'];
    xinha_config.pageStyleSheets = ['<?php echo !empty($xp_Stylist_path) ? $xp_Stylist_path : "manager/media/xinha/xinha.css" ?>'];
    xinha_editors = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);
    HTMLArea.startEditors(xinha_editors);
  }
// end Ian's custom xinha config
  xinha_config = function()
  {
    var config = new HTMLArea.Config();
    config.baseHref = document.location.href.replace(/manager.*/, '');

<?php if ($strip_base_href==0) { ?>
    // we must alter the default config to turn off stripping the base href
    config.stripBaseHref = false;
<?php } else { ?>
    config.stripBaseHref = true;
<?php } ?>

<?php if($xp_Stylist==1 && !empty($xp_Stylist_path)) { ?>
    // add to the config if we are using stylist
    if(typeof Stylist != 'undefined')
    {
      // We can load an external stylesheet like this - NOTE : YOU MUST GIVE AN ABSOLUTE URL
      // otherwise it won't work!
      config.stylistLoadStylesheet('<?php echo !empty($xp_Stylist_path) ? $xp_Stylist_path : "../assets/site/example.css" ?>');
  // Apply the stylesheet to the editor's content window
      config.pageStyle = '@import url(<?php echo !empty($xp_Stylist_path) ? $xp_Stylist_path : "../assets/site/example.css" ?>);';
      // Add a button to toggle the stylist panel.
      config.registerButton({
        id       : "stylist",
        tooltip  : "Toggle Styles Panel",
        image    : "./media/images/icons/stylist.gif",
        textMode : false,
        action   : function(editor) { editor._toggleStylist(editor); }
      })

      config.toolbar[1].splice(1, 0, "separator");
      config.toolbar[1].splice(1, 0, "stylist");

      HTMLArea.prototype._toggleStylist = function(editor)
      {
        if(editor._stylistVisible == true)
        {
          editor.hidePanel(editor._stylist);
          editor._stylistVisible = false;
        }
        else
        {
          editor.showPanel(editor._stylist);
          editor._stylistVisible = true;
        }
      }

      HTMLArea.prototype._stylistVisible = true;
    }
<?php } ?>
    return config;
  }

// load the plugins
xinha_plugins = [];
<?php if($im_plugin==1) { ?>
  xinha_plugins.push('ImageManager');
<?php } ?>
<?php if($fm_plugin==1) { ?>
  xinha_plugins.push('InsertFile');
<?php } ?>
<?php if($xp_Stylist==1 && !empty($xp_Stylist_path)) { ?>
  xinha_plugins.push('Stylist');
<?php } ?>

<?php
// scan the xinha plugin directory for available plugins
// skip a few plugins because the either don't work well with etomite,
// or require extra settings we can't facilitate
// **NOTE: The skip_plugins list must match the one found in mutate_settings.dynamic.action.php.
$skip_plugins = array
(
  "ImageManager",
  "Stylist",
  "InsertFile",
  "InsertPicture",
  "HorizontalRule",
  "Linker",
  "InsertAnchor",
  "FullScreen",
  "EnterParagraphs"
);
$dir = "./media/xinha/plugins";
if($handle = opendir($dir))
{
  while(false !== ($file = readdir($handle)))
  {
    if($file != "." && $file != ".." && !in_array($file,$skip_plugins))
    {
      $plugins[] = $file;
    }
  }
  closedir($handle);
}
// if a plugin has been selected, add it
foreach($plugins as $p)
{
  $var = 'xp_'.$p;
  if ($$var==1) echo "\txinha_plugins.push('$p');\n";
}
?>

  window.onload = xinha_init;
  top.topFrame.document.getElementById('workText').innerHTML="";
</script>

<?php // END ?>