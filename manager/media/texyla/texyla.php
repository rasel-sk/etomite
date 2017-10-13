<?php
/***************************************************************************************************
 * Texyla
 * Version:            	0.4.3.4 for PHP5
 * Author:			
 *						Petr Vaněk aka krteczek (krteczek@jaknato.com)
 *						Jan Marek
 *						Lukáš Voda
 * Latest update: 		18.května 2008
 ***************************************************************************************************
 *  Encoding:           UTF-8
 ***************************************************************************************************
 *	Vytvořil: 			Petr Vaněk aka krteczek
 *
 *	Web: 					http://texyla.jaknato.com
 *
 *  Licence:			Texyla je k dispozici pod GPL licencí
 *  					(její český překlad naleznete v souboru gpl.cs.html)
 *
 *  Tento program je volný software; můžete jej šířit a modifikovat podle
 *  ustanovení Obecné veřejné licence GNU, vydávané Free Software
 *  Foundation; a to buď verze 2 této licence anebo (podle vašeho uvážení)
 *  kterékoli pozdější verze.
 *
 *  Tento program je rozšiřován v naději, že bude užitečný, avšak BEZ
 *  JAKÉKOLI ZÁRUKY; neposkytují se ani odvozené záruky PRODEJNOSTI anebo
 *  VHODNOSTI PRO URČITÝ ÚČEL. Další podrobnosti hledejte ve Obecné
 *  veřejné licenci GNU.
 *
 *  Kopii Obecné veřejné licence GNU jste měl obdržet spolu s tímto
 *  programem; pokud se tak nestalo, napište o ni Free Software Foundation,
 *  Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *************************************************************************************************
	Změny v 0.4.3.4
		- zaimplementována nová verze 2.0 BETA 2 (Revision: 211, Date: 2008/05/12 02:48:48)
		- přidána možnost natvrdo přidávat do odkazů target="_blank"
		 - tato volba se hodí například pro různá fóra, knihy návštěv, ...
		 - přibyla funkce texylaAddTargetBlank() která mění <a href na <a target="_blank" href
		 - toto se řídí nastavením proměnné $addTargetBlank = true, kterou je nutno nastavit v texyla/cfg/forum.php
		 - pro administraci doporučuji nechat $addTargetBlank = false.
 *************************************************************************************************
	Změny v 0.4.3.2
		- zaimplementována nová verze Texy 2.0 BETA 2 (Revision: 205, Date: 2008/02/15 05:45:10)
		- upraven kód aby fungovalo Fshl (převzato z Texy/examples/syntax highlighting/demo-fshl.php)
		- upraveny chybové hlášky, funkce texylaErrorMsg je upravena, a vrací text, ten je následně 
				pomocí trigger_error() vypsán do stránky
		- Texyla je připravena na distribuci bez Texy a Fshl (kvůli licencím)(mělo by stačit smazat obsahy adresářů fshl a texy)
		- přehozeno pořadí seznamu změn v tomto souboru
 *************************************************************************************************
	Změny v 0.4.3 beta 1
		- zaimplementována nová verze Texy (2.0 beta 2 rev 175)
		- s tím začala být nepotřebná třída texyEmoticonsAddSize zpracovávající smajlíky
		- odstraněna třída FshlHandler, a nazhrazená funkcí TexylaFSHLBlockHandler
		- odstraněny implemntace výše zmíněných tříd v texyle
		- opraven bug v konfigurácích,
*************************************************************************************************
	Změny v 0.4.3 alpha
		- odstraněna kontrola na iconv v sekci webalize (byla zbytečná)
		- dopsané vysvětlivky k jednotlivým částem kódu
		- implementována podpora FSHL (obarvovače kódu)
		- přesunut adresar s konfiguračními soubory
		- fshl jde zapnout/vypnout v konfiguračních souborech
		- smajlíci jdou zapnout/vypnout v konfiguračních souborech
		- Hlídá se existence třídy FshlHandler a FshlParser
*************************************************************************************************
	Změny v 0.4.2:
	***************
		- odstraněna hláška o odeslání prázdného formuláře (dělalo to neplechu 
			u zpracování v php, místo vrácení prázdného obsahu to vrátilo tento text)  
		- odstraněný kód je nahrazen podobnou kontrolou hned na začátku fce texyla, s tím,
			že je okamžitě vráceno to co bylo posláno
			
 *************************************************************************************************
	
		Rozhodl jsem se přepsat php jádro texyly tak aby bylo snadno použitelné i při 
		zpracování údajů z formuláře(ů) v samotném php.
		
		Takže jsou dvě možnosti použití texyla.php:
		
			1. zavolá si ho texyla.js pomocí httprequestu (tak jako doteď)
			2. tento soubor je možno includovat do vašich php scriptů:
					include('./texyla/texyla.php');				
				na místě kde to potřebujete a zavolat funkci texyla:
					texyla(text, konfigurace, kodovani, jednoradkovy) 
				s těmito parametry:
					text 				=> text který má být zpracovám texy
					konfigurace 	=> jaká má být použita konfigurace pro texy
												Možnosti: 'admin', 'forum', 'oneline', 'webalize'
					kodovani			=> v jakém kódování je text ke zpracování
												Příklady: 'utf-8', 'windows-1250', 'iso-8859-2'
											'utf-8' je defaultní kódování, není třeba uvádět, jedině když
											použijete zároveň i čtvrtý parametr (jednořádkový
					jednořádkový	=> pro rozlišení ošetření jednořádkových textů (témata, jména, emaily...)
												Možnosti: true, false
											false je defaultní hodnota, není třeba uvádět

************************************************************************************************/
//error_reporting(E_ALL);

# definice chybových hlášek
if(!defined('TEXYLA_CLASS_NOT_TEXY_FOUND')){define('TEXYLA_FILE_NOT_FOUND', 1);}
if(!defined('TEXYLA_OLD_PHP')){define('TEXYLA_OLD_PHP',2);}
if(!defined('TEXYLA_ACCESS_DENIED')){define('TEXYLA_ACCESS_DENIED',3);}
if(!defined('TEXYLA_ICONV_MISSING')){define('TEXYLA_ICONV_MISSING', 5);}
if(!defined('TEXYLA_TEXY_NOT_FOUND')){define('TEXYLA_TEXY_NOT_FOUND', 6);}
if(!defined('TEXYLA_FSHL_NOT_FOUND')){define('TEXYLA_FSHL_NOT_FOUND', 7);}
if(!defined('TEXYLA_FILE_CFG_NOT_FOUND')){define('TEXYLA_FILE_CFG_NOT_FOUND', 8);}
# cesty
$pathTexy = dirname(__FILE__) . '/texy/texy.compact.php';
$pathFSHL = dirname(__FILE__) . '/fshl/fshl.php';

# include texyif(!file_exists($pathTexy))
	{
		# texy.compact.php není nahráno v adresáři texy
		trigger_error(texylaErrorMsg(TEXYLA_TEXY_NOT_FOUND), E_USER_ERROR);
	}
else
	{
		require_once($pathTexy);
	}

# include fshl

if(!file_exists($pathFSHL))
	{
		# trigger_error(vsprintf(texylaErrorMsg(TEXYLA_FSHL_NOT_FOUND), array(__LINE__)), E_USER_ERROR);	}
else
	{
		require_once($pathFSHL);
	}

if(!empty($_POST['texylaAjax']))
	{
		# zpracováváme požadavky texyla.js volané přes httprequest (AJAX)		# Odstraníme případné ošetření zpětnými lomítky "\" způsobené zaplou 		# directivou MagicQuotesGpc. Doporučuji použít vždy,
		removeMagicQuotesGpc();

		# zavoláme funkci texyla a předáme jí obsah textarey, konfiguraci 		# texy, kodovani neposila, js automaticky posílá text v utf-8, takže nemusíme 		# posílat kodování, a automaticky předpokládáme, že se jedná o text z textarea, 		# čili je to $oneLine === false což je defaultní nastavení.		# výsledek zobrazíme (=== odešleme)
		# protože to někde dělalo neplechu, přibyla do adresy položka texylaCharset, 
		# pomocí ní se nastavuje kódóvání znaků v kterém je poslaný text
		$charset = 'utf-8';
		if(!empty($_GET['texylaCharset']))
			{	
				$charset = $_GET['texylaCharset'];
			}
		header('Content-type: text/html; charset=' . $charset);
		die(texyla(@$_POST['texylaContent'], @$_POST['texylaTexyCfg'], $charset));
	}

function texyla($content, $texyCfg, $charset = 'utf-8', $oneLine = false)
	{
		if(empty($content))
			{
				# protože je obsah prázdný vrátíme ho prázdný zpátky bez zpracování
				return $content;
			}
		if((!function_exists('iconv')) && ($charset != 'utf-8'))
			{
				# ověření existence fce iconv(), která se používá pro zmenu 
				# kodování vstupního textu při jiném než utf-8 kodování
				trigger_error(texylaErrorMsg(TEXYLA_ICONV_MISSING), E_USER_ERROR);
			}
		# jaky pouzit configurační soubor pro zpracování vstupního textu
		# defaultní hodnota je forum, takže není třeba uvádět.
		$texyCfg = texylaTexyCfg($texyCfg);
		
		# sestaveni cesty ke konfigu
		$texyCfgFile = dirname(__FILE__) . '/cfg/' . $texyCfg . '.php';
		
		if(!is_bool($oneLine))
			{
				# pokud není bool nastavíme na false (blokový text)
				$oneLine = false;
			}

		if(!class_exists('Texy'))
			{	
				# Neexistuje třída Texy
				trigger_error(texylaErrorMsg(TEXYLA_CLASS_NOT_TEXY_FOUND, $texyFile), E_USER_ERROR);
			}
			
		# iniciace texy
		$texy = new Texy();
		
	
		# nastavíme kódování v kterém je zpracováváný text
		# default je utf-8, není třeba uvádět při volání funkce texyla() [pokud nenásleduje další parametr]
		$texy->encoding = $charset;
		
		# proměnná, podle které se řídí přidávání hlášky  <!-- by Texy2! --> na konec zpracovávaného textu
		# $GLOBALS['Texy::$advertisingNotice'] = false;

		# verze pro php5
				Texy::$advertisingNotice = false;
		
		# Odstranění diakritiky z textu, vytvoření 	
		if($texyCfg == 'webalize')
			{
				/*
					*******************************************************************************************
					!!! Toto je v Testovací fázi !!! Bugreporty prosím na http://texyla.jaknato.com/khostu.php
					*******************************************************************************************
					Jedná se o odstranění diakritiky...
					Výsledek obsahuje pouze a-z, čísla a spojovník.
					Není třeba načítat konfiguraci, 
					Pokud používáte jiné kódování než je utf-8, je text překodován na utf-8 a je z něj 
					odstraněna diakritika, výsledný text je čisté ASCII, takže se zpštně nepřevádí na 
					původní kódóvání.
					Poznámky dgx k webalize:
					========================
						Je však možné povolit i další ASCII znaky:
	
							$nadpis = "článek/PHP 5.2.1 a funkce is_array()";
							echo Texy::webalize($nadpis); // standardní chování
	
							→ clanek-php-5-2-1-a-funkce-is-array
	
							$addChar = '/_.';// navíc povolíme znaky: / _ .
							echo Texy::webalize($nadpis,$addChar);
							
							→ clanek/php-5.2.1-a-funkce-is_array
	
							Ještě dodám, že funkce funguje korektně i při chybné implementaci iconv (glibc).
				*/
				$content = (strtolower($charset) == 'utf-8' ? $content : iconv($charset, 'utf-8', $content));
				$addChar = '';
				return Texy::webalize($content, $addChar);			
			}	
		# kontrola existence konfiguračního souboru
		if(!file_exists($texyCfgFile))
			{
				# neexistuje (nebyl nalezen/includován) soubor s konfigurací pro texy

				return texylaErrorMsg(TEXYLA_FILE_CFG_NOT_FOUND, $texyCfgFile);
			}
		
		# includujeme soubor s nastavením pro Texy!
		
		if(!include($texyCfgFile))
			{
				# Nepodařilo se načíst konfigurační soubor, 
				# nejspíš špatné práva pro přístup k souboru
				return texylaErrorMsg(TEXYLA_ACCESS_DENIED, $texyFile);
			}
			
		
		# kontrola existence pomocné třídy FshlHandler (propojení Texy s fshlParser) 
		# a třídy fshlParser (ta se stará o samotné obarvení kodu)
		if((function_exists('TexylaFSHLBlockHandler')) && (class_exists('fshlParser')) && (!empty($useFSHL)) && ($useFSHL === true))
			{
				# iniciace pomocné třídy starající se o obarvení zdrojovách kódů
				# Autor třídy je: Juraj 'hvge' Durech
				$texy->addHandler('block', 'TexylaFSHLBlockHandler');
			}
		
		# Provedeme zpracování poslaného obsahu pomocí Texy!
		
		if(!empty($addTargetBlank) && $addTargetBlank === true)
			{
				return preg_replace_callback("~<a href~iU", "texylaAddTargetBlank", $texy->process($content, $oneLine));
			}
		return $texy->process($content, $oneLine);

	}
# end function texyla

function texylaAddTargetBlank($text)
	{
		# funkce přidává target="_blank" do odkazů 
		return '<a target="_blank" href';
	}

function texylaTexyCfg($cfg)
	{
		# vrátí název konfigu konfiguračnímu souboru pro texy
		switch($cfg)
			{
				case 'admin': return 'admin'; break;
				case 'oneline': return 'oneline'; break;
				case 'webalize': return 'webalize'; break;
				default: return 'forum';
			}
	}

function texylaErrorMsg($numMsg, $cesta = '')
	{
		# slouží k zobrazení chybových hlášek texyly.

		$msg = '';
		switch ($numMsg)
			{
				case 1: $msg .= '<p>Neexistuje třída Texy()!</p>'; break;
				case 2: $msg .= '<p>Nepodporovaná verze php.</p>'; break;
				case 3: $msg .= '<p>Nejsou správně nastavena přístupová práva k souboru ' . $cesta . '</p>'; break;
				case 5: $msg .= '<p>Chybí funkce iconv, která slouží k změně znakové sady zpracovávaného textu. Nelze pokračovat.</p>'; break;
				case 6: 
					$msg .= '<p>Adresář <b>./texyla/texy/</b> neobsahuje soubor <b>texy.compat.php</b>, který je důležitý pro zpracování textu (obsahuje třídu Texy). </p>';
					$msg .= '<p>Pokud nemáte Texy, stáhněte si ji ze stránek <a href="http://texy.info/download">http://texy.info/download</a>.</p>';
					$msg .= '<p>Věnujte prosím pozornost licenci, pokud chcete Texy používat v closed source systémech, kontaktujte autora Texy, kterým je David Grudl.</p>';
					$msg .= '<p>Po rozbalení staženého balíku najděte adresář <b>texy.compact</b>, a v něm obsažený soubor <b>texy.compact.php</b> nakopírujte do adresáře Texyly <b>./texyla/texy/</b>';	
				break;
				case 7: 
					$msg .= '<p>Adresář <b>./texyla/fshl/</b> neobsahuje soubor <b>fshl.php</b>, který obsahuje třídu fshlParser.</p><p>Tato třída se stará o obarvování publikovaných zdrojových kódů</p>';
					$msg .= '<p>Pokud nemáte Fshl a chcete ho použít, stáhněte si ho ze stránek <a href="http://www.hvge.sk/scripts/fshl/">http://www.hvge.sk/scripts/fshl/</a>.</p>';
					$msg .= '<p>Věnujte prosím pozornost licenci, pokud chcete Fshl používat v closed source systémech, kontaktujte autora Fshl, kterým je Juraj `hvge` Ďurech.</p>';
					$msg .= '<p>Po rozbalení staženého balíku nakopírujte jeho obsah do připraveného adresáře <b>./texyla/fshl/</b>';	
					$msg .= '<p>Nechcete-li Fshl používat, zakomentujte řádek č.: %s v souboru texyla.php</p>';
				break;
				case 8 :
					$msg .= '<p>Nepodařilo se načíst soubor s konfigurací pro Texy, nelze pokračovat.';
				break;
				default: $msg .= '<p>Pokoušíte se volat neznámé chybové hlášení. Hodnota je: ' . $numMsg .'</p>';
			}
		die($msg);
	}

function TexylaFSHLBlockHandler($invocation, $blocktype, $content, $lang, $modifier)
	{
		/**
		 * User handler for code block by dgx
		 *
		 * @param TexyHandlerInvocation  handler invocation
		 * @param string  block type
		 * @param string  text to highlight
		 * @param string  language
		 * @param TexyModifier modifier
		 * @return TexyHtml
		 */
		if ($blocktype !== 'block/code')
			{
				return $invocation->proceed();
			}

		$lang = strtoupper($lang);
		if ($lang == 'JAVASCRIPT') 
			{
				$lang = 'JS';
			}
		#$parser = new fshlParser('HTML_UTF8', P_TAB_INDENT);
		$parser = new fshlParser('HTML_UTF8', 100);
		if (!$parser->isLanguage($lang)) 
			{
				return $invocation->proceed();
			}

		$texy = $invocation->getTexy();
		$content = Texy::outdent($content);
		$content = $parser->highlightString($lang, $content);
		$content = $texy->protect($content, Texy::CONTENT_BLOCK);

		$elPre = TexyHtml::el('pre');
		if ($modifier) 
			{
				$modifier->decorate($texy, $elPre);
			}				
		$elPre->attrs['class'] = strtolower($lang);

		$elCode = $elPre->create('code', $content);

		return $elPre;
	}

function removeMagicQuotesGpc()
{
	/*****
	*	Tato funkce slouží k odstranění ošetření způsobeného
	*	direktivou magic_quotes_gpc (jedná se o zpětná lomítka přidávána
	*	před uvozovky a apostrofy).
	*	Byla publikována Jakubem Vránou na http://php.vrana.cz/vypnuti-magic_quotes_gpc.php
	*****/
	if(get_magic_quotes_gpc())
		{
			$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST, &$_FILES);
			while (list($key, $val) = each($process))
				{
					foreach ($val as $k => $v)
						{
							unset($process[$key][$k]);
							if (is_array($v))
								{
									$process[$key][($key < 5 ? $k : stripslashes($k))] = $v;
									$process[] =& $process[$key][($key < 5 ? $k : stripslashes($k))];
								}
								else
								{
									$process[$key][stripslashes($k)] = stripslashes($v);
				
				   			}
						}
				}
		}
}
?>
