<?php
/**
	konfigurace texy!
	admin verze nastavení je nejbenevolentnější nastavení,
	kde je vše povoleno

	Chcete-li změnit nastavení, učiňte tak, ale na podrobnosti se ptejte na http://texy.info
**/
# zaputí/vypnutí xhtml syntaxe (<tag />)
# $texy->htmlOutputModule->xhtml = false; odstraněno Davidem Grudlem a nahrazeno níže ukázaným
# rev. 208
#
#    * use $texy->setOutputMode(...) to switch between HTML/XHTML, strict/loose modes. 
#    * Parameter is one of these constants: 
#			Texy::HTML4_TRANSITIONAL 
# 			Texy::HTML4_STRICT
# 			Texy::XHTML1_TRANSITIONAL
#			Texy::XHTML1_STRICT
#    * Texy::$strictDTD & $texy->htmlOutputModule->xhtml are deprecated
#
# rev. 206
#
#    * new constants Texy::FILTER_ANCHOR & Texy::FILTER_IMAGE
$texy->setOutputMode(Texy::HTML4_TRANSITIONAL);


# fráze
$texy->allowed['phrase/ins'] = true;			// ++inserted++
$texy->allowed['phrase/del'] = true;			// --deleted--
$texy->allowed['phrase/sup'] = true;			// ^^superscript^^
$texy->allowed['phrase/sub'] = true;			// __subscript__
$texy->allowed['phrase/cite'] = true;			// ~~cite~~
$texy->allowed['deprecated/codeswitch'] = true;	// `=code

# nadpisy
$texy->headingModule->balancing = TEXY_HEADING_FIXED;

# zalamování textu v odstavcích po enteru
$texy->mergeLines = false;


# obrázky
# nastaví kořen ve kterém php hledá obrázky kvůli zjištění rozměrů na složku images sousedící se složkou texyla
$texy->imageModule->fileRoot = dirname(__FILE__) . '/../../../assets/images/';


# Zapnutí/vypnutí obarvovače kódu FSHL (booleans => true/false)
$useFSHL = true;

# Smajlíky

# zapnout/vypnout zpracování smailíků
$texy->allowed['emoticon'] = true;

# includujeme nastavení smajlíků
# smajlíky texy
include dirname(__FILE__) . '/../emoticons/texy/cfg.php';
# nebo smajlíci silk
# include dirname(__FILE__) . '/../emoticons/silk/cfg.php';


# $addTargetBlank pokud je true, přidává do všech odkazů automaticky target="_blank" 
# booleans true/false
# v administraci není třeba, tam si to může admin řídit podle svých potřeb
$addTargetBlank = false; 
?>