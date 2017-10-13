<?php
/**
	konfigurace texy!
	Tento config je určený pro fora, knihy návštěv a jiné podobné věci
	Je poměrně restriktivní, další věci lze povolit/zakázat editací
	Vlastnosti této konfigurace
     	- html převádí na entity
     	- jsou zakázané linkové definice
     	- jsou zakázané nadpisy
     	- odkazy jsou doplněné o rel="nofollow"

	Chcete-li změnit nastavení, učiňte tak, ale na podrobnosti se ptejte na http://texy.info
**/

TexyConfigurator::safeMode($texy);

# zapbutí/vypnutí xhtml syntaxe (<tag />)
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

# v případě HTML vypne odstranění volitelných koncových značek
$texy->htmlOutputModule->removeOptional = false;

# zakázání referencí

$texy->allowed['link/definition'] = false;
$texy->allowed['image/definition'] = false;


# zakázaní nadpisů
$texy->allowed['heading/surrounded'] = false;
$texy->allowed['heading/underlined'] = false;


# zalamování textu v odstavcích po enteru
$texy->mergeLines = false;


# fráze
$texy->allowed['phrase/ins'] = false;			// ++inserted++
$texy->allowed['phrase/del'] = false;			// --deleted--
$texy->allowed['phrase/sup'] = true;			// ^^superscript^^
$texy->allowed['phrase/sub'] = true;			// __subscript__
$texy->allowed['phrase/cite'] = false;			// ~~cite~~
$texy->allowed['deprecated/codeswitch'] = true;	// `=code


# vypnutí/zapnutí html tagů
$texy->allowed['tags'] = false;


# odkazy

# automatické doplňování rel="nofollow" k odkazům
$texy->linkModule->forceNoFollow = true;


# zakáže převádění všech typů adres (email, http, www, "...":, ...) na odkazy
# TexyConfigurator::disableLinks($texy);

# povolí / zakáže převody jednotlivých druhů odkazů
$texy->allowed['link/link'] = true;
$texy->allowed['link/url'] = true;
$texy->allowed['link/email'] = true;

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