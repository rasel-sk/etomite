<?php
/**
	konfigurace texy!
	Tento config je určený pro zpracování jednořádkových vstupů, jako je jmeno, příjmení, email, adresa... 
	Je poměrně restriktivní, další věci lze povolit/zakázat editací
	Vlastnosti této konfigurace
     	- veškeré html převádí na entity
     	- jsou zakázané linkové definice
     	- jsou zakázané nadpisy
     	- odkazy jsou doplněné o rel="nofollow"

	Chcete-li změnit nastavení, učiňte tak, ale na podrobnosti se ptejte na http://texy.info
**/

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

# použijeme safe mod
TexyConfigurator::safeMode($texy);

# v případě HTML vypne odstranění volitelných koncových značek
$texy->htmlOutputModule->removeOptional = false;


# zakázání referencí

$texy->allowed['link/definition'] = false;
$texy->allowed['image/definition'] = false;


# zakázaní nadpisů

$texy->allowed['heading/surrounded'] = false;
$texy->allowed['heading/underlined'] = false;


# zalamování textu v odstavcích po enteru
# false => nebude spojovat řádky, vloží místo enteru <br>
# true => řádky po jednom enteru spojí

$texy->mergeLines = false;


# fráze
# Protože se jedná o konfigurák pro jednořádkový text nastavte si, 
# co chcete převádět a co už ne

$texy->allowed['phrase/strong+em'] = FALSE;  // ***strong+emphasis***
$texy->allowed['phrase/strong'] = FALSE;     // **strong**
$texy->allowed['phrase/em'] = FALSE;         // //emphasis//
$texy->allowed['phrase/em-alt'] = FALSE;     // *emphasis*
$texy->allowed['phrase/span'] = FALSE;       // "span"
$texy->allowed['phrase/span-alt'] = FALSE;   // ~span~
$texy->allowed['phrase/acronym'] = FALSE;    // "acro nym"((...))
$texy->allowed['phrase/acronym-alt'] = FALSE;// acronym((...))
$texy->allowed['phrase/code'] = FALSE;       // `code`
$texy->allowed['phrase/notexy'] = FALSE;     // ''....''
$texy->allowed['phrase/quote'] = FALSE;      // >>quote<<:...
$texy->allowed['phrase/quicklink'] = FALSE;  // ....:LINK
$texy->allowed['phrase/sup-alt'] = FALSE;    // superscript^2
$texy->allowed['phrase/sub-alt'] = FALSE;    // subscript_3

$texy->allowed['phrase/ins'] = FALSE;       // ++inserted++
$texy->allowed['phrase/del'] = FALSE;		// --deleted--
$texy->allowed['phrase/sup'] = FALSE;		//^^superscript^^
$texy->allowed['phrase/sub'] = FALSE;       // __subscript__
$texy->allowed['phrase/cite'] = FALSE;      // ~~cite~~
$texy->allowed['deprecated/codeswitch'] = FALSE;// `=...


# vypnutí/zapnutí html tagů
$texy->allowedTags = FALSE;


# nastavení pro odkazy
# automatické doplňování rel="nofollow" k odkazům
$texy->linkModule->forceNoFollow = true;


# zakáže převádění adres na odkazy

//TexyConfigurator::disableLinks($texy);


# povolí / zakáže převody jednotlivých druhů odkazů

$texy->allowed['link/link'] = true;
$texy->allowed['link/url'] = true;
$texy->allowed['link/email'] = true;


# Zapnutí/vypnutí obarvovače kódu FSHL (booleans => true/false)
$useFSHL = false;

# Smajlíky

# zapnout/vypnout zpracování smailíků
$texy->allowed['emoticon'] = true;

# includujeme nastavení smajlíků
# smajlíky texy
# include dirname(__FILE__) . '/../emoticons/texy/cfg.php';
# nebo smajlíci silk
# include dirname(__FILE__) . '/../emoticons/silk/cfg.php';



# $addTargetBlank pokud je true, přidává do všech odkazů automaticky target="_blank" 
# booleans true/false
# v administraci není třeba, tam si to může admin řídit podle svých potřeb
$addTargetBlank = false; 
?>