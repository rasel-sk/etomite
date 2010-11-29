// ** I18N

// Calendar ZH language
// Author: muziq, <muziq@sina.com>
// Encoding: GB2312 or GBK
// Distributed under the same terms as the calendar itself.

// full day names
Calendar._DN = new Array
("ÐÇÆÚÈÕ",
 "ÐÇÆÚһ",
 "ÐÇÆڶþ",
 "ÐÇÆÚÈý",
 "ÐÇÆÚËÄ",
 "ÐÇÆÚÎå",
 "ÐÇÆÚÁù",
 "ÐÇÆÚÈÕ");

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
("ÈÕ",
 "һ",
 "¶þ",
 "Èý",
 "ËÄ",
 "Îå",
 "Áù",
 "ÈÕ");

// full month names
Calendar._MN = new Array
("һÔÂ",
 "¶þÔÂ",
 "ÈýÔÂ",
 "ËÄÔÂ",
 "ÎåÔÂ",
 "ÁùÔÂ",
 "ÆßÔÂ",
 "°ËÔÂ",
 "¾ÅÔÂ",
 "ʮÔÂ",
 "ʮһÔÂ",
 "ʮ¶þÔÂ");

// short month names
Calendar._SMN = new Array
("һÔÂ",
 "¶þÔÂ",
 "ÈýÔÂ",
 "ËÄÔÂ",
 "ÎåÔÂ",
 "ÁùÔÂ",
 "ÆßÔÂ",
 "°ËÔÂ",
 "¾ÅÔÂ",
 "ʮÔÂ",
 "ʮһÔÂ",
 "ʮ¶þÔÂ");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "°ïÖú";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"ѡÔñÈÕÆÚ:\n" +
"- µã»÷ \xab, \xbb °´ťѡÔñÄê·Ý\n" +
"- µã»÷ " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " °´ťѡÔñÔ·Ý\n" +
"- ³¤°´ÒÔÉϰ´ť¿ɴӲ˵¥ÖпìËÙѡÔñÄê·ݻòÔ·Ý";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"ѡÔñʱ¼ä:\n" +
"- µã»÷Сʱ»ò·ÖÖӿÉʹ¸ÄÊýֵ¼Óһ\n" +
"- °´סShift¼üµã»÷Сʱ»ò·ÖÖӿÉʹ¸ÄÊýֵ¼õһ\n" +
"- µã»÷Í϶¯Êó±ê¿ɽøÐпìËÙѡÔñ";

Calendar._TT["PREV_YEAR"] = "ÉÏһÄê (°´ס³ö²˵¥)";
Calendar._TT["PREV_MONTH"] = "ÉÏһÔÂ (°´ס³ö²˵¥)";
Calendar._TT["GO_TODAY"] = "תµ½½ñÈÕ";
Calendar._TT["NEXT_MONTH"] = "ÏÂһÔÂ (°´ס³ö²˵¥)";
Calendar._TT["NEXT_YEAR"] = "ÏÂһÄê (°´ס³ö²˵¥)";
Calendar._TT["SEL_DATE"] = "ѡÔñÈÕÆÚ";
Calendar._TT["DRAG_TO_MOVE"] = "Í϶¯";
Calendar._TT["PART_TODAY"] = " (½ñÈÕ)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "×î×ó±ßÏÔʾ%s";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "¹رÕ";
Calendar._TT["TODAY"] = "½ñÈÕ";
Calendar._TT["TIME_PART"] = "(Shift-)µã»÷Êó±ê»òÍ϶¯¸ıäֵ";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%A, %b %eÈÕ";

Calendar._TT["WK"] = "ÖÜ";
Calendar._TT["TIME"] = "ʱ¼ä:";
