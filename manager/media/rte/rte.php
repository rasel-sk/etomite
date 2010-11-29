<?php
// rte.php
// RTE
// Snippet to create a Rich Text Editor
// Use: Place a snippet call where the textarea should be in the form
//      [!RTE?rte=<field_name>&value=<data>!]
//      Usage requires inclusion of the onsubmit form attribute as noted below
// <form name="RTEDemo" action="" method="post" onsubmit="return submitForm();">


$rte = "ta";
$path = "./media/rte/";

//format content for preloading
$content['content'] = rteSafe($content['content']);

$script = <<<END
<script type="text/javascript" src="{$path}html2xhtml.js"></script>
<script type="text/javascript" src="{$path}richtext_compressed.js"></script>
<script type="text/javascript">
<!--
function submitForm() {
  //make sure hidden and iframe values are in sync for all rtes before submitting form
  updateRTEs();
  document.mutate.$rte.value = document.mutate.$rte.value.replace(/%5B%7E/g,'[~'); //FireFox encodes characters in URLs. Let's convert [~] back
  document.mutate.$rte.value = document.mutate.$rte.value.replace(/%7E%5D/g,'~]');
  document.mutate.$rte.value = document.mutate.$rte.value.replace(/<a href="[^"]+\[~/gi, "<a href=\"[~" ); //IE 6 tries to convert relative URLs to absolute but often fails. Let's make at least [~id~] relative again.
  return true;
}

//Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML)
initRTE("{$path}images/", "{$path}", "", true);
//-->
</script>
<noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>

<script type="text/javascript">
<!--
//build new richTextEditor
var $rte = new richTextEditor('$rte');
$rte.html = '$content[content]';
$rte.width = "100%";
$rte.height = 400;
$rte.readOnly = false;
$rte.toolbar1 = true;
$rte.toolbar2 = true;
$rte.cmdFormatBlock = true;
$rte.cmdFontName = true;
$rte.cmdFontSize = true;
$rte.cmdIncreaseFontSize = true;
$rte.cmdDecreaseFontSize = true;
$rte.cmdBold = true;
$rte.cmdItalic = true;
$rte.cmdUnderline = true;
$rte.cmdStrikethrough = true;
$rte.cmdSuperscript = true;
$rte.cmdSubscript = true;
$rte.cmdJustifyLeft = true;
$rte.cmdJustifyCenter = true;
$rte.cmdJustifyRight = true;
$rte.cmdJustifyFull = true;
$rte.cmdInsertHorizontalRule = true;
$rte.cmdInsertOrderedList = true;
$rte.cmdInsertUnorderedList = true;
$rte.cmdOutdent = true;
$rte.cmdIndent = true;
$rte.cmdForeColor = true;
$rte.cmdHiliteColor = true;
$rte.cmdInsertLink = true;
$rte.cmdInsertImage = true;
$rte.cmdInsertTable = true;
$rte.cmdSpellCheck = true;
$rte.cmdCut = true;
$rte.cmdCopy = true;
$rte.cmdPaste = true;
$rte.cmdUndo = true;
$rte.cmdRedo = true;
$rte.cmdRemoveFormat = true;
$rte.toggleSrc = true;
$rte.build();
//-->
</script>
END;

echo $script;

function rteSafe($strText) {
  //returns safe code for preloading in the RTE
  $tmpString = $strText;

  //convert all types of single quotes
  $tmpString = str_replace(chr(145), chr(39), $tmpString);
  $tmpString = str_replace(chr(146), chr(39), $tmpString);
  $tmpString = str_replace("'", "&#39;", $tmpString);

  //convert all types of double quotes
  $tmpString = str_replace(chr(147), chr(34), $tmpString);
  $tmpString = str_replace(chr(148), chr(34), $tmpString);

  //replace carriage returns & line feeds
  $tmpString = str_replace(chr(10), " ", $tmpString);
  $tmpString = str_replace(chr(13), " ", $tmpString);

  return $tmpString;
}

?>