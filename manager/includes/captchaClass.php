<?php
// captchaClass.php file modified for Etomite
// Last Modified: 2007-03-06 by Ralph A. Dahlgren
// 2007-03-06 - modified for better ttf directory calculation and to ignore subdirectories
// 2007-01-31 - modified to skip .svn dir when processing the ./ttf dir

## v1 Stable ##

######
## Verification Word
######
## This class generate an image with random text
## to be used in form verification. It has visual
## elements design to confuse OCR software preventing
## the use of BOTS.
##
#######
## Author: Huda M Elmatsani
## Email: justhuda at netrada.co.id
##
## 25/07/2004
#######
## Copyright (c) 2004 Huda M Elmatsani All rights reserved.
## This program is free for any purpose use.
########
##
## USAGE
## create some image with noise texture, put in image directory,
## rename to noise_#, see examples
## put some true type font into font directory,
## rename to font_#, see exmplae
## you can search and put free font you like
##
## see sample.php for test and usage
## sample URL: http://www.program-ruti.org/veriword/
####

class VeriWord {

  var $dir_font, $dir_noise, $word, $im_width, $im_height, $host, $user, $password, $dbase, $prefix;

  function VeriWord($width=200, $height=80, $host, $user, $password, $dbase, $prefix)
  {
    $this->dir_font = "fonts/";
    $this->dir_noise = "noises/";
    $this->im_width = $width;
    $this->im_height = $height;
    $this->host = $host;
	$this->user = $user;
    $this->password = $password;
    $this->dbase = $dbase;
	$this->prefix = $prefix;
    $this->set_veriword();
  }

  function set_veriword() {
    // create session variable for verification
    // you may change the session variable name
    $this->word = $this->pick_word();
    $_SESSION['veriword'] = $this->word;
  }

  function output_image() {
    // output the image as jpeg
    $this->draw_image();
    header("Content-type: image/jpeg");
    imagejpeg($this->im);
  }

  function pick_word() {
    // set default words
    $words="Array,BitCode,Chunk,Document,Etomite,Forum,Index,Javascript,Keyword,MySQL,Parser,Query,Random,Snippet,Template,Website";

    // connect to the database
    if(@$etomiteDBConn = mysql_connect($this->host, $this->user, $this->password)) {
      mysql_select_db($this->dbase);
      $sql = "SELECT * FROM ".$this->dbase.".".$this->prefix."system_settings WHERE setting_name='captcha_words'";
      $rs = mysql_query($sql);
      $limit = mysql_num_rows($rs);
      if($limit==1) {
        $row = mysql_fetch_assoc($rs);
        $words = $row['setting_value'];
      }
    }

    $arr_words = explode(",", $words);

    // pick one randomly for text verification
    return sprintf("%s",$arr_words[array_rand($arr_words)]);
  }

  function draw_text() {

    // pick one font type randomly from font directory
    // $text_font = $this->dir_font."".rand(1,3).".ttf";
    // added by Alex - read ttf dir
    $dirpath = realpath("./ttf")."/";
    $dir = dir($dirpath);
    $fontstmp = array();
    while ($file = $dir->read()) {
      if(!is_dir($file)) {
        $fontstmp[] = $dirpath.$file;
      }
    }
    $dir->close();
    $text_font = sprintf("%s",$fontstmp[array_rand($fontstmp)]);
    // end of ttf selection

    // angle for text inclination
    $text_angle = rand(-9,9);

    // initial text size
    $text_size = 30;

    // calculate text width and height
    $box = imagettfbbox ($text_size, $text_angle, $text_font, $this->word);
    $text_width = $box[2]-$box[0]; //text width
    $text_height = $box[5]-$box[3]; //text height

    // adjust text size
    $text_size  = round((20 * $this->im_width)/$text_width);

    // recalculate text width and height
    $box = imagettfbbox ($text_size, $text_angle, $text_font, $this->word);
    $text_width = $box[2]-$box[0]; //text width
    $text_height = $box[5]-$box[3]; //text height

    // calculate center position of text
    $text_x = ($this->im_width - $text_width)/2;
    $text_y = ($this->im_height - $text_height)/2;

    // create canvas for text drawing
    $im_text = imagecreate ($this->im_width, $this->im_height);
      $bg_color = imagecolorallocate ($im_text, 255, 255, 255);

    // pick color for text
    $text_color = imagecolorallocate ($im_text, 0, 51, 153);

    // draw text into canvas
    imagettftext($im_text,
              $text_size,
              $text_angle,
              $text_x,
              $text_y,
              $text_color,
              $text_font,
              $this->word);

    // remove background color
    imagecolortransparent($im_text, $bg_color);
    return $im_text;
    imagedestroy($im_text);
  }


  function draw_image() {

    // pick one background image randomly from image directory
    $img_file = $this->dir_noise."noise".rand(1,4).".jpg";

    // create "noise" background image from your image stock
    $noise_img = @imagecreatefromjpeg ($img_file);
    $noise_width = imagesx($noise_img);
    $noise_height = imagesy($noise_img);

    // resize the background image to fit the size of image output
    $this->im = imagecreatetruecolor($this->im_width,$this->im_height);
    imagecopyresampled ($this->im,
              $noise_img,
              0, 0, 0, 0,
              $this->im_width,
              $this->im_height,
              $noise_width,
              $noise_height);

    // put text image into background image
    imagecopymerge ($this->im,
              $this->draw_text(),
              0, 0, 0, 0,
              $this->im_width,
              $this->im_height,
              70 );

    // return the image
    return $this->im;
  }

  function destroy_image() {
      // cleanup memory stack
      imagedestroy($this->im);

  }

}
?>
