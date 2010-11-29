<?php
include("config.inc.php");
include("captchaClass.php");
$vword = new VeriWord(148,80);
$vword->output_image();
$vword->destroy_image();
?>
