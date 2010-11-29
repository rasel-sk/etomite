// ** I18N

// Calendar EN language
// Author: Mihai Bazon, <mihai_bazon@yahoo.com>
// Translation: Yourim Yi <yyi@yourim.net>
// Encoding: EUC-KR
// lang : ko
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names

Calendar._DN = new Array
("ÀϿäÀÏ",
 "¿ù¿äÀÏ",
 "ȭ¿äÀÏ",
 "¼ö¿äÀÏ",
 "¸ñ¿äÀÏ",
 "±ݿäÀÏ",
 "Åä¿äÀÏ",
 "ÀϿäÀÏ");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("ÀÏ",
 "¿ù",
 "ȭ",
 "¼ö",
 "¸ñ",
 "±Ý",
 "Åä",
 "ÀÏ");

// full month names
Calendar._MN = new Array
("1¿ù",
 "2¿ù",
 "3¿ù",
 "4¿ù",
 "5¿ù",
 "6¿ù",
 "7¿ù",
 "8¿ù",
 "9¿ù",
 "10¿ù",
 "11¿ù",
 "12¿ù");

// short month names
Calendar._SMN = new Array
("1",
 "2",
 "3",
 "4",
 "5",
 "6",
 "7",
 "8",
 "9",
 "10",
 "11",
 "12");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "calendar ¿¡ ´ëÇؼ­";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"\n"+
"ÃֽÅ ¹öÀü; ¹Þ8½÷xé http://www.dynarch.com/projects/calendar/ ¿¡ ¹湮Çϼ¼¿ä\n" +
"\n"+
"GNU LGPL ¶óÀ̼¾½º·Î ¹èÆ÷µ˴ϴÙ. \n"+
"¶óÀ̼¾½º¿¡ ´ëÇÑ Àڼ¼ÇÑ ³»¿ë: http://gnu.org/licenses/lgpl.html ; ÀÐ8¼¼¿ä." +
"\n\n" +
"³¯¥ ¼±ÅÃ:\n" +
"- ¿¬µµ¸¦ ¼±ÅÃÇϷxé \xab, \xbb ¹öư; »ç¿ëÇմϴÙ\n" +
"- ´Þ; ¼±ÅÃÇϷxé " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " ¹öư; ´©¸£¼¼¿ä\n" +
"- °è¼Ó ´©¸£°í ÀÖ8¸é ' °ªµé; ºü¸£°Ô ¼±ÅÃÇϽÇ ¼ö Àֽ4ϴÙ.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"½ð£ ¼±ÅÃ:\n" +
"- ¸¶¿콺·Î ´©¸£¸é ½ð£ÀÌ Áõ°¡ÇմϴÙ\n" +
"- Shift Ű¿Í ÇԲ² ´©¸£¸é °¨¼ÒÇմϴÙ\n" +
"- ´©¸¥ »óÅ¿¡¼­ ¸¶¿콺¸¦ ¿òÁ÷À̸é { ´õ ºü¸£°Ô °ªÀÌ º¯ÇմϴÙ.\n";

Calendar._TT["PREV_YEAR"] = "Áö³­ ÇØ (±æ°Ô ´©¸£¸é ¸ñ·Ï)";
Calendar._TT["PREV_MONTH"] = "Áö³­ ´Þ (±æ°Ô ´©¸£¸é ¸ñ·Ï)";
Calendar._TT["GO_TODAY"] = "¿4Ã ³¯¥·Î";
Calendar._TT["NEXT_MONTH"] = "´Ù= ´Þ (±æ°Ô ´©¸£¸é ¸ñ·Ï)";
Calendar._TT["NEXT_YEAR"] = "´Ù= ÇØ (±æ°Ô ´©¸£¸é ¸ñ·Ï)";
Calendar._TT["SEL_DATE"] = "³¯¥¸¦ ¼±ÅÃÇϼ¼¿ä";
Calendar._TT["DRAG_TO_MOVE"] = "¸¶¿콺 µ巡±׷Î À̵¿ Çϼ¼¿ä";
Calendar._TT["PART_TODAY"] = " (¿4Ã)";
Calendar._TT["MON_FIRST"] = "¿ù¿äÀÏ; ÇÑ ÁÖÀÇ ½ÃÀÛ ¿äÀϷÎ";
Calendar._TT["SUN_FIRST"] = "ÀϿäÀÏ; ÇÑ ÁÖÀÇ ½ÃÀÛ ¿äÀϷÎ";
Calendar._TT["CLOSE"] = "´ݱâ";
Calendar._TT["TODAY"] = "¿4Ã";
Calendar._TT["TIME_PART"] = "(Shift-)Ŭ¸¯ ¶ǴÂ µ巡±× Çϼ¼¿ä";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%b/%e [%a]";

Calendar._TT["WK"] = "ÁÖ";
