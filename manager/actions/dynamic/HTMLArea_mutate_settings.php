<?php
#HTML Editor initialisation
?>

<script type="text/javascript">
    _editor_lang = "en";
    _editor_url = "./media/editor/";
 </script>

  <script type="text/javascript" src="./media/editor/editor.js"></script>
  <style type="text/css">@import url(./media/editor/editor.css);</style>

  <script type="text/javascript" >
  // load up the plugins...
  <?php if($im_plugin==1) { ?>
    HTMLArea.loadPlugin("ImageManager");
  <?php } ?>
  <?php if($fm_plugin==1) { ?>
    HTMLArea.loadPlugin("InsertFile");
  <?php } ?>
    HTMLArea.loadPlugin("EnterParagraphs");
  <?php if($to_plugin==1) { ?>
    HTMLArea.loadPlugin("TableOperations");
  <?php } ?>
  <?php if($cm_plugin==1) { ?>
    HTMLArea.loadPlugin("ContextMenu");
  <?php } ?>
    HTMLArea.loadPlugin("ListType");
  </script>

  <textarea id="ta" name="ta" style="width:100%; border:1px solod #000000;" onChange="documentDirty=true;"><?php echo htmlspecialchars($content['content']); ?></textarea>

  <script type="text/javascript">
  function initEditor() {

  var config = new HTMLArea.Config();

  <?php if($strict_editor==1) { ?>
  config.toolbar = [
      [ "formatblock", "space",
        "bold", "italic", "underline", "strikethrough", "separator",
        "subscript", "superscript", "separator",
        "copy", "cut", "paste", "space", "undo", "redo",
        "orderedlist", "unorderedlist", "separator",
        "inserthorizontalrule", "createlink", "insertimage", "inserttable", "htmlmode"]
    ];
  <?php } ?>

    editor = new HTMLArea("ta");

  <?php if($to_plugin==1) { ?>
    editor.registerPlugin(TableOperations);
  <?php } ?>
    editor.registerPlugin(EnterParagraphs);
  <?php if($cm_plugin==1) { ?>
    editor.registerPlugin(ContextMenu);
  <?php } ?>
  <?php if($fm_plugin==1) { ?>
    editor.registerPlugin(InsertFile);
  <?php } ?>
    editor.registerPlugin(ListType);


    editor.generate();
    return false;
  }

  <?php if ($strip_base_href==0) { ?>
  HTMLArea.prototype.stripBaseURL = function(string) { return string; }
  <?php } ?>

  document.onload=initEditor();

  </script>
  <?php ?>