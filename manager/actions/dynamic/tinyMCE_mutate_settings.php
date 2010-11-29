<?php
// tinyMCE_mutate_settings.php
// TinyMCE WYSIWYG Editor Initialization
// Created by Cris Deagon
// Modified 2008-04-04 [v1.0] by Ralph Dahlgren
// Patched 2008-04-24 by Nalagar for proper resize
// Modified 2008-05-08 by Ralph to fix minor bugs

$cssPath = !empty($tiny_css_path) ? $tiny_css_path : "";
$cssSelectors = !empty($tiny_css_selectors) ? $tiny_css_selectors : "theme_advanced_styles";
$cfg['ilibs_dir'] = array('../assets/images/');
$rel = (empty($strip_base_href)) ? "false" : "true";
?>

<textarea id="ta" rows="25" cols="80" name="ta" style="width:80%; margin:0;" onchange="documentDirty=true;"><?php echo  htmlspecialchars($content['content']); ?></textarea>

<script type="text/javascript" src="media/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">
  var EtoRoot = "<?php echo $ETOMITE_PAGE_BASE['www']; ?>";
  var rel = <?php echo $rel; ?>;
  tinyMCE.init({
    auto_reset_designmode : true,
    relative_urls : true,
    document_base_url : "<?php echo $ETOMITE_PAGE_BASE['relative']; ?>",
    mode : "textareas",
    theme : "advanced",
    relative_urls : rel,
    remove_script_host : false,
    convert_urls : true,
    plugins : "table, advhr, advimage, advlink, emotions, iespell, insertdatetime, preview, zoom, flash, searchreplace, print, contextmenu, paste, directionality, fullscreen",
    theme_advanced_buttons1_add_before : "newdocument, separator",
    theme_advanced_buttons1_add : "fontselect, fontsizeselect",
    theme_advanced_buttons2_add : "separator, insertdate, inserttime, preview, separator, forecolor, backcolor",
    theme_advanced_buttons2_add_before: "cut, copy, paste, pastetext, pasteword, separator, search, replace, separator",
    theme_advanced_buttons3_add_before : "tablecontrols, separator",
    theme_advanced_buttons3_add : "emotions, iespell, flash, advhr, separator, print, separator, ltr, rtl, separator, fullscreen",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    content_css : "<?php echo $cssPath; ?>",
    plugin_insertdate_dateFormat : "<?php echo $date_format; ?>",
    plugin_insertdate_timeFormat : "<?php echo $time_format; ?>",
    extended_valid_elements : "hr[class|width|size|noshade], font[face|size|color|style] ,span[class|align|style]",
    external_link_list_url : "example_data/example_link_list.js",
    external_image_list_url : "example_data/example_image_list.js",
    flash_external_list_url : "example_data/example_flash_list.js",
    // file_browser_callback : "mcFileManager.filebrowserCallBack",
    theme_advanced_resize_horizontal : false,
    theme_advanced_resizing : true,
    init_instance_callback : 'resizeEditorBox'
  });

  function fileBrowserCallBack(field_name, url, type, win) {
    return; // Nothing installed so we just return
    // This is where you insert your custom filebrowser logic
    alert("Filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);
    // Insert new URL, this would normaly be done in a popup
    win.document.forms[0].elements[field_name].value = "someurl.htm";
  }

  resizeEditorBox = function (editor) {
      // Have this function executed via TinyMCE's init_instance_callback option!
      // requires TinyMCE3.x
      var container = editor.contentAreaContainer, /* new in TinyMCE3.x -
          for TinyMCE2.x you need to retrieve the element differently! */
          formObj = document.mutate, // this might need some adaptation to your site
          dimensions = {
              x: 0,
              y: 0,
              maxX: 0,
              maxY: 0
          }, doc, docFrame;

      dimensions.x = formObj.offsetLeft; // get left space in front of editor
      dimensions.y = formObj.offsetTop; // get top space in front of editor

      dimensions.x += formObj.offsetWidth; // add horizontal space used by editor
      dimensions.y += formObj.offsetHeight; // add vertical space used by editor

      // get available width and height
      if (window.innerHeight) {
          dimensions.maxX = window.innerWidth;
          dimensions.maxY = window.innerHeight;
      } else {
      // check if IE for CSS1 compatible mode
          doc = (document.compatMode && document.compatMode == "CSS1Compat")
              ? document.documentElement
              : document.body || null;
          dimensions.maxX = doc.offsetWidth - 4;
          dimensions.maxY = doc.offsetHeight - 4;
      }

      // extend container by the difference between available width/height and used width/height
      docFrame = container.firstChild;//if we use firstChild instead of children[0] (used in example) it works
      docFrame.style.width = container.style.width = (dimensions.maxX -45) + 'px';//subtract hardcoded margins from maximum to maximize width
      docFrame.style.height = container.style.height = (dimensions.maxY -230) + 'px';//subtract hardcoded margins from maximum to maximize height

  }

var OldOnResize = window.onresize; // save any existing assignment

window.onresize = function ()
{
  resizeEditorBox(tinyMCE.getInstanceById('ta'));
  if(OldOnResize != null && typeof(OldOnResize) == 'function')
  {
    OldOnResize();
  }
}

</script>

<?php /* END */ ?>
