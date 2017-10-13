/***************************************************************************************************
 *	Texyla
 *  Version:            0.4.3.3 multilang
 *  Latest update: 		23. března 2008
 ***************************************************************************************************
 *  Encoding:           utf-8
 ***************************************************************************************************
 *	Vytvořil: 			Jan Marek
 *						Petr Vaněk aka krteczek
 *						Lukáš Voda
 *
 *	Web: 				http://texyla.jaknato.com
 *
 *  Licence:            Texyla je k dispozici pod GPL licencí
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
 **************************************************************************************************/

var TexylaCharset = '';

// Konstruktor
function Texyla (options) {
	// Nastavení
	this.options = options;
	
	// Buď najdeme textareu podle ID, nebo již máme objekt Textarea
	this.textarea = typeof(options.textarea) === 'string' ? document.getElementById(options.textarea) : options.textarea;
	
	// Tlačítková lišta: string -> vybere z přednastavených, objekt -> uživatelská lišta
	this.options.toolbar = typeof(options.toolbar) === "string" ? Texyla.toolbars[options.toolbar] : options.toolbar;
	
	// smajlíky: string -> vybere z přednastavených, objekt -> uživatelské smajly
	this.options.emoticons = typeof(options.emoticons) === 'string' ? Texyla.emoticons[options.emoticons] : options.emoticons;

	//výběr jazykové verze Texyly
	this.lng = typeof(options.lng) === 'string' ? Texyla.lng[options.lng] : Texyla.lng.cs;
	
	// Addresy
	this.addr = options.addr;

	// přidá k adrese složky smajlíků složku konkrétního typu smajlů
	this.addr.emoticons += this.options.emoticons.folder;
	
	// Zjistí Internet Explorer
	this.IE = this.isIE();
	
	// Zařídí, aby Texyly neměly společné podobjekty
	this.Buttons = this.Buttons(this);
	this.Texy = this.Texy();
	this.Dom = this.Dom();
	this.View = this.View();
	this.Windows = this.Windows();
	
	// Vytvoří v podobjektech odkaz na hlavní objekt Texyly
	this.Dom.Texyla = this;
	this.View.Texyla = this;
	this.Texy.Texyla = this;
	this.Windows.Texyla = this;
	this.Windows.img.Texyla = this;
	this.Windows.table.Texyla = this;
	this.Windows.emoticon.Texyla = this;
	this.Windows.symbol.Texyla = this;
	
	// Spustí texylovatění
	this.Dom.init();
};

// Funkce pro zjištění umístění adresáře s texylou
Texyla.getAddrBase = function () {
	var scripts = document.getElementsByTagName("head")[0].getElementsByTagName("script");
	var src = scripts[scripts.length - 1].src;
	var file = /\/[\w-]+\.js$/;
	var addrBase = src.replace(file, '') + '/';
	
	return addrBase;
};

// Umístění adresáře s Texylou
Texyla.addrBase = Texyla.getAddrBase();

// Nastavení - konfigurátor
Texyla.configurator = {
	// Společné základní nastavení
	defaultCfg: function(textarea) {	
		return {
			// Textarea do které se vkládá Texy
			textarea: textarea,
			
			//lng: 'cs',
			// Lišta
			toolbar: null,
			
			// Povolit náhled HTML kódu
			allowHtmlPreview: true,
			
			// Typ ikonek - silk | old
			iconType: 'silk',
			
			// Formát ikonek - silk -> png, old -> gif
			iconFormat: 'png',
			
			// Vzhled (default | win_xp)
			theme: 'default',
			
			// Hezký vzhled tlačítek
			coolButtons: true,
			
			// 'auto' -> zachová původní šířku | číslo -> šířka editoru v pixelech
			editorWidth: 'auto',
			
			// odsazení textarey
			textareaMargin: 6,
			
			// Funkce, která se volá při odpovědi na XHR, vrací string - HTML
			AjaxProcessor: null,
			
			// Funkce, která modifikuje nastavení XHR
			AjaxPreProcessor: null,
			
			// Konfigurace Texy, dostupné jsou: admin, forum, oneline
			texyCfg: null,
			
			// smajlíky
			emoticons: 'texy',
			
			// adresáře
			addr: {
				// témata vzhledů
				css: Texyla.addrBase + 'themes/',
				// ikonky tlačítek apod.
				icons: Texyla.addrBase + 'icons/',
				// smajlíky
				emoticons: Texyla.addrBase + 'emoticons/',
				// náhled
				ajax: Texyla.addrBase + 'texyla.php'
			},
			
			// Odeslat formulář po klávesové zkratce
			submitOnCtrlS: true,
			
			// Syntax kurzívy: '*' -> *kurzíva*, '//' -> //kurzíva//
			emSyntax: '*',
			
			// První pohled (edit, preview, html - což je sice blbost, ale funguje to)
			defaultView: 'edit',
			
			// Odesílací tlačítko přímo v texyle (true, false, 'preview' -> jen při náhledu)
			submitButton: false,
			
			// Tlačítko syntaxe při editaci
			syntaxButton: true,
			
			// Schovat elementy (např. submit, nápovědu pro texy apod.), false->neschovat nic, element, pole elementů
			hideElements: false,
			
			// Symboly
			symbols: ['@', '&', ['<', '&lt;'], ['>', '&gt;']]
		};
	},
	
	// nastavení pro administrační rozhraní
	admin: function(textarea) {
		// základ
		var options = Texyla.configurator.defaultCfg(textarea);
		
		// lišta admin
		options.toolbar = 'admin';
		// konfigurák Texy: admin
		options.texyCfg = 'admin';
		
		return options;
	},
	
	// nastavení pro fóra
	forum: function(textarea) {
		// základ
		var options = Texyla.configurator.defaultCfg(textarea);
		
		// lišta fórum
		options.toolbar = 'forum';
		// zakáže HTML náhled
		options.allowHtmlPreview = false;
		// konfigurák Texy: forum
		options.texyCfg = 'forum';
		// zakáže odeslání formuláře po klávesové zkratce
		options.submitOnCtrlS = false;
		
		return options;
	}
};

// Lišty pro Texylu
Texyla.toolbars = {
	// plná verze lišty
	full: [
		'h1', 'h2', 'h3', 'h4',
		null,
		'bold', 'italic',
		null,
		'center', ['left', 'right', 'justify'],
		null,
		'ul', 'ol',
		null,
		'link', 'img', 'table', 'emoticon', 'symbol',
		null,
		'div', ['html', 'blockquote', 'text', 'comment'],
		null,
		'code',	['code_html', 'code_css', 'code_js', 'code_php', 'code_sql'], 'inlineCode',
		null,
		['sup', 'sub', 'del', 'acronym', 'hr', 'notexy', 'web']
	],
	// admin
	admin: [
		'h2', 'h3', 'h4',
		null,
		'bold', 'italic',
		null,
		'center', ['left', 'right', 'justify'],
		null,
		'ul', 'ol',
		null,
		'link', 'img', 'table', 'emoticon',
		null,
		'comment',
		null,
		['div', 'code', 'inlineCode', 'html', 'notexy', 'web']
	],
	// forum lišta
	forum: [
		'bold', 'italic', null, 'ul', 'ol', null, 'link', null, 'emoticon', null, ['web']
	],
	// lišta pro webmajstrovo fórum
	webmaster: [
		'bold', 'italic',
		null,
		'ul', 'ol',
		null,
		'link', 'emoticon',
		null,
		'code',	['code_html', 'code_css', 'code_js', 'code_php', 'code_sql'], 'inlineCode',
		null,
		'web'
	]
};

// smajlíky
Texyla.emoticons = {
	// texy smajlíky, výchozí
	texy: {
		// složka
		folder: 'texy/',
		// formát souborů
		format: 'gif',
		// šířka
		width: 15,
		// výška
		height: 15,
		// seznam ikonek
		icons: {
			':-)': 'smile',
			':-(': 'sad',
			';-)': 'wink',
			':-D': 'biggrin',
			'8-O': 'eek',
			'8-)': 'cool',
			':-?': 'confused',
			':-x': 'mad',
			':-P': 'razz',
			':-|': 'neutral'
		}
	},
	
	// silk smajlíci
	silk: {
		// složka
		folder: 'silk/',
		// formát souborů
		format: 'png',
		// šířka
		width: 16,
		// výška
		height: 16,
		// seznam ikonek
		icons: {
			':-)': 'smile',
			':-(': 'unhappy',
			';-)': 'wink',
			':-D': 'grin',
			':-O': 'surprised',
			':-P': 'tongue'
		}
	}
};

// Texty
Texyla.lng = {
			// popisy tlačítek
			btn_h1: "Největší nadpis",
			btn_h2: "Velký nadpis",
			btn_h3: "Střední nadpis",
			btn_h4: "Nejmenší nadpis",
			btn_bold: "Tučně",
			btn_italic: "Kurzíva",
			btn_del: "Přeškrtnuto",
			btn_center: "Zarovnání na střed",
			btn_left: "Zarovnání vlevo",
			btn_right: "Zarovnání vpravo",
			btn_justify: "Zarovnání do bloku",
			btn_ul: "Seznam",
			btn_ol: "Číslovaný seznam",
			btn_blockquote: "Bloková citace",
			btn_sub: "Dolní index",
			btn_sup: "Horní index",
			btn_link: "Odkaz",
			btn_img: "Obrázek",
			btn_table: "Tabulka",
			btn_acronym: "Vysvětlení zkratky",
			btn_hr: "Čára",
			btn_code: "Kód",
			btn_code_html: "Kód html",
			btn_code_css: "Kód CSS",
			btn_code_js: "Kód javascript",
			btn_code_php: "Kód php",
			btn_code_sql: "Kód SQL",
			btn_comment: "Komentář",
			btn_div: "Blok div",
			btn_text: "Text",
			btn_inlineCode: "Inline kód",
			btn_html: "HTML",
			btn_notexy: "Inline text",
			btn_web: "Web editoru Texyla",
			btn_emoticon: "Smajlík",
			btn_symbol: "Symbol",
			
			// funkce
			texy_heading_text: "Text nadpisu",
			texy_link_url: "Adresa odkazu",
			texy_acronym_title: "Titulek",
			
			// pohledy
			view_edit: "Upravit",
			view_preview: "Náhled",
			view_html: "HTML",
			view_syntax: "Texy syntaxe",
			view_wait: "Prosím čekejte",
			view_empty: "Textové pole je prázdné!",
			view_submit: "Odeslat",
			
			// obrázek
			img_heading: "Vložit obrázek",
			img_src: "Adresa obrázku",
			img_alt: "Popis",
			img_align: "Zarovnání",
			img_al_none: "žádné",
			img_al_left: "vlevo",
			img_al_right: "vpravo",
			img_al_center: "na střed",
			img_descr: "Zobrazit jako popisek",
			
			// tabulka
			tab_heading: "Vložit tabulku",
			tab_cols: "Počet sloupců",
			tab_rows: "Počet řádek",
			tab_th: "Hlavička",
			tab_th_none: "žádná",
			tab_th_top: "nahoře",
			tab_th_left: "vlevo",
			
			// smajlíci
			emoticon_heading: "Vložit smajlík",
			
			// symboly
			symbol_heading: "Vložit symbol",
			
			// okna
			win_ins: "Vložit",
			win_close: "Zavřít"
};


Texyla.prototype = {	
	// Pole, ve kterém editujeme Texy
	textarea: null,
	
	// Internet Explorer
	IE: null,
	
	isIE: function() {
		// Opera, Firefox
		if (this.textarea.selectionStart || this.textarea.selectionStart === 0) {
			TexylaCharset = document.characterSet;
			return false;
			
		// IE
		} else {
			if (document.selection) {
				TexylaCharset = document.charset;
				return true;
			}
		}
	},
	
	// Tlačítka	
	Buttons: function(_this) {
		var lng = Texyla.lng;
		return {
			// název tlačítka
				// ikonka, název, funkce
			h1:
				{icon:"h1", name:lng.btn_h1, func: function() {_this.Texy.heading('#');}},
			h2:
				{icon:"h2", name:lng.btn_h2, func: function() {_this.Texy.heading('*');}},
			h3:
				{icon:"h3", name:lng.btn_h3, func: function() {_this.Texy.heading('=');}},
			h4:
				{icon:"h4", name:lng.btn_h4, func: function() {_this.Texy.heading('-');}},
			bold:
				{icon:"bold", name:lng.btn_bold, func: function() {_this.Texy.phrase('**', '**');}},
			italic:
				{icon:"italic", name:lng.btn_italic, func: function() {_this.Texy.em();}},
			del:
				{icon:"del", name:lng.btn_del, func: function() {_this.Texy.phrase('--', '--');}},
			center:
				{icon:"center", name:lng.btn_center, func: function() {_this.Texy.align('<>');}},
			left:
				{icon:"left", name:lng.btn_left, func: function() {_this.Texy.align('<');}},
			right:
				{icon:"right", name:lng.btn_right, func: function() {_this.Texy.align('>');}},
			justify:
				{icon:"justify", name:lng.btn_justify, func: function() {_this.Texy.align('=');}},
			ul:
				{icon:"ul", name:lng.btn_ul, func: function() {_this.Texy.list('ul');}},
			ol:
				{icon:"ol", name:lng.btn_ol, func: function() {_this.Texy.list('ol');}},
			blockquote:
				{icon:"blockquote", name:lng.btn_blockquote, func: function() {_this.Texy.list('bq');}},
			sub:
				{icon:"sub", name:lng.btn_sub, func: function() {_this.Texy.phrase('__', '__');}},
			sup:
				{icon:"sup", name:lng.btn_sup, func: function() {_this.Texy.phrase('^^', '^^');}},
			link:
				{icon:"link", name:lng.btn_link, func: function() {_this.Texy.link();}},
			img:
				{icon:"img", name:lng.btn_img, func: function() {_this.Windows.img.open(this);}},
			table:
				{icon:"table", name:lng.btn_table, func: function() {_this.Windows.table.open(this);}},
			acronym:
				{icon:"acronym", name:lng.btn_acronym, func: function() {_this.Texy.acronym();}},
			hr:
				{icon:"hr", name:lng.btn_hr, func: function() {_this.Texy.line();}},
			code:
				{icon:"tag", name:lng.btn_code, func: function() {_this.Texy.block('code');}},
			code_html:
				{icon:"code_html", name:lng.btn_code_html, func: function() {_this.Texy.block('code html');}},
			code_css:
				{icon:"code_css", name:lng.btn_code_css, func: function() {_this.Texy.block('code css');}},
			code_js:
				{icon:"code_js", name:lng.btn_code_js, func: function() {_this.Texy.block('code js');}},
			code_php:
				{icon:"code_php", name:lng.btn_code_php, func: function() {_this.Texy.block('code php');}},
			code_sql:
				{icon:"code_sql", name:lng.btn_code_sql, func: function() {_this.Texy.block('code sql');}},
			inlineCode:
				{icon:"inline_code", name:lng.btn_inlineCode, func: function() {_this.Texy.phrase('`', '`');}},
			html:
				{icon:"html", name:lng.btn_html, func: function() {_this.Texy.block('html');}},
			notexy:
				{icon:"notexy", name:lng.btn_notexy, func: function() {_this.Texy.phrase("''", "''");}},
			web:
				{icon:"web", name:lng.btn_web, func: function() {window.open('http://texyla.jaknato.com/');}},
			emoticon:
				{icon:"emoticon", name:lng.btn_emoticon, func: function() {_this.Windows.emoticon.open(this);}},
			symbol:
				{icon:"symbol", name:lng.btn_symbol, func: function() {_this.Windows.symbol.open(this);}},
			div:
				{icon:"div", name:lng.btn_div, func: function() {_this.Texy.block('div');}},
			comment:
				{icon:"comment", name:lng.btn_comment, func: function() {_this.Texy.block('comment');}},
			text:
				{icon:"text", name:lng.btn_text, func: function() {_this.Texy.block('text');}}
		};
	},
	
	// funkce pro práci s výběrem a pro vkládání Texy
	Texy: function() {
		return {
			// oddělovač řádků
			lineFeed: '\r\n',
			// jestli jsme si jisti s formátem oddělovače řádků
			lineFeedKnown: false,
			
			/*selection: {
				txt: null,
				len: null,
				start: null,
				end: null,
				cursor: null
			}, */
			
			// obalí výběr (firstTexy + výběr + secondText)
			tag: function (firstText, secondText) {
				this.changeSelection(false, firstText, secondText);
			},
			
			// nahradí výběr proměnnou replacement
			replaceSelection: function (replacement) {
				this.changeSelection(true, replacement, null);
			},
			
			// odstraní případnou jednu mezeru vpravo z výběru a zavolá funkci this.tag()
			// FF fix (po dvojkliku na slovo vybere i mezeru za ním)
			phrase: function (firstText, secondText) {
				this.doSelect();
				
				var seltxt = this.selection.txt;
				if (seltxt.substring(seltxt.length, seltxt.length-1) == " ") {
					this.select(this.selection.start,this.selection.len - 1);
				}
				
				this.tag(firstText, secondText);
			},
			
			// změna výběru
			changeSelection: function (replacement, firstText, secondText) {
				this.doSelect();
				
				// Kolik je odrolováno
				var scrolled = this.Texyla.textarea.scrollTop;
				
				// Změněný text
				// replacement = true -----> vyber je nahrazen promennou firstText
				// replacement = false ----> vyber je obalen firstText a secondText
				var changedText = replacement ? firstText : (firstText + this.selection.txt + secondText);
				
				// Změna textu v textaree
				var taval = this.Texyla.textarea.value;
				this.Texyla.textarea.value = taval.substring(0, this.selection.start) + changedText + taval.substring(this.selection.end);
				
				// Vybrat
				// Pri vyberu zohlední: a) je-li vyber nahrazovan b) je-li obalen vyber ci kurzor
				var from = this.selection.start + ((replacement || !this.selection.cursor) ? 0 : firstText.length);
				var length = replacement ? firstText.length : (
					this.selection.cursor ? 0 : firstText.length + this.selection.len + secondText.length
				);
				this.select(from, length);
				
				// Odrolovat na původní pozici
				this.Texyla.textarea.scrollTop = scrolled;
			},
		
			// Funkce zjistí pravděpodobnou podobu formátu nového řádku.
			getLineFeedFormat: function() {
				if (!this.lineFeedKnown) {
					// Pokusí se ho nalézt:
					var unix = this.Texyla.textarea.value.indexOf('\n');
					var mac = this.Texyla.textarea.value.indexOf('\r');
					var win = this.Texyla.textarea.value.indexOf('\r\n');
					
					if (unix >= 0) {
						this.lineFeed = '\n';
					}
					if (mac >= 0) {
						this.lineFeed = '\r';
					}
					if (win >= 0) {
						this.lineFeed = '\r\n';
					}
					
					// V případě úspěchu nastaví proměnnou this.lineFeedKnown na true a funkce již později hledání neopakuje.
					if (unix >= 0 || mac >= 0 || win >= 0) {
						this.lineFeedKnown = true;
					}
					
					// Jinak se nový řádek nastaví provizorně podle prohlížeče.
					if (!this.lineFeedKnown) {
						// O, IE -> win
						if (document.selection) {
							this.lineFeed = '\r\n';
						// FF -> unix
						} else {
							this.lineFeed = '\n';
						}
					}
				}
			},
			
			// Ulož vlastnosti výběru
			saveSelectionProperties: function() {
				this.Texyla.textarea.focus();
				
				var start, end, selectedText, cursor;
				
				// IE
				if (this.Texyla.IE) {
					var backup = this.Texyla.textarea.value;
					
					var ieSelection = document.selection.createRange();
					var bookmark = "[~Z~A~L~O~Z~K~A~]";
					selectedText = ieSelection.text;
					
					ieSelection.text = bookmark + selectedText;
					start = this.Texyla.textarea.value.indexOf(bookmark);
					end = start + selectedText.length;
					
					this.Texyla.textarea.value = backup;
					
				// O, FF
				} else { 
					start = this.Texyla.textarea.selectionStart;
					end = this.Texyla.textarea.selectionEnd;
					selectedText = this.Texyla.textarea.value.substring(start, end);
				}
				
				// Aktualizuje promennou this.selection
				cursor = (end === start);
				this.selection = {
					txt: selectedText,
					len: selectedText.length,
					start: start,
					end: end,
					cursor: cursor
				};
			},
			
			doSelect: function() {
				this.saveSelectionProperties();
				this.select(this.selection.start, this.selection.len); //IE
				this.getLineFeedFormat();
			},
			
			select: function(from, length) {
				if (this.Texyla.IE) {
					var lfCount = this.Texyla.textarea.value.substring(0, from).split("\r\n").length - 1;
					from -= lfCount;
					this.Texyla.textarea.focus();
					this.Texyla.textarea.select();
					var ieSelected = document.selection.createRange();
					ieSelected.collapse(true);
					ieSelected.moveStart("character", from);
					ieSelected.moveEnd("character", length);
					ieSelected.select();
					this.Texyla.textarea.focus();
				} else {
					this.Texyla.textarea.selectionStart = from;
					this.Texyla.textarea.selectionEnd = from + length;
				}
				
				this.Texyla.textarea.focus();
			},
			
			selectBlock: function() {
				this.doSelect();
				var workFrom = this.Texyla.textarea.value.substring(0, this.selection.start).lastIndexOf(this.lineFeed);
				if (workFrom !== -1) {
					workFrom += this.lineFeed.length;
				}
				var from = Math.max(0, workFrom);
				
				var ta = this.Texyla.textarea;
				var workLength = ta.value.substring(workFrom, this.selection.start).length + this.selection.len;
				var fromSelectionEnd = ta.value.substring(this.selection.end, ta.value.length);
				var lineFeedPos = fromSelectionEnd.indexOf(this.lineFeed);
				workLength += lineFeedPos === -1 ? fromSelectionEnd.length : lineFeedPos;
				this.select(from, workLength);
				this.doSelect();
			},
			
			// konkrétní fce
			
			// zarovnání
			align: function(type) {
				this.doSelect();
			
				var start = '.' + type + this.lineFeed,
					newPar = this.lineFeed + this.lineFeed,
					found = this.Texyla.textarea.value.substring(0, this.selection.start).lastIndexOf(newPar),
					beforePar = found + newPar.length;
				
				if (found ==- 1) {
					this.Texyla.textarea.value = start + this.Texyla.textarea.value;
				} else {
					this.Texyla.textarea.value = this.Texyla.textarea.value.substring(0, beforePar) + start + this.Texyla.textarea.value.substring(beforePar);
				}
				this.select(this.selection.start + start.length, this.selection.len);
			},
			
			// vytvoří seznam - číslovaný (type == "ol"), s odrážkami (type == "ul"), blockquote (type == "bq")
			list: function(type) {
				this.selectBlock();
				
				var lines = this.selection.txt.split(this.lineFeed);
				var lineCount = this.selection.cursor ? 3 : lines.length;
				var replacement = '';
				
				for (var i = 1; i <= lineCount; ++i) {
					// UL
					if (type === 'ul') {
						replacement += '- ';
					// OL
					} else if (type === 'ol') {
						replacement += i+') ';
					// Blockquote
					} else if (type === 'bq') {
						replacement += '> ';
					}
					if (this.selection.cursor && i === 1) {
						var firstLength = replacement.length;
					}
					if (!this.selection.cursor) {
						replacement += lines[i - 1];
					}
					if (i !== lineCount) {
						replacement += this.lineFeed;
					}
				}
				
				if (this.selection.cursor) {
					this.tag(replacement.substring(0, firstLength), replacement.substring(firstLength));
				} else {
					this.replaceSelection(replacement);
				}
			},
			
			// Vrátí string, kde bude 'length'krát za sebou 'type'
			headingCreate: function(type, length) {
				var underline = '';
				for (var i=0; i < Math.max(3,length); ++i) {
					underline += type;
				}
				return underline;
			},
			
			// vytvoří nadpis, podtrhne podle type
			heading: function(type) {
				this.selectBlock();
				// Nový nadpis
				if (this.selection.cursor) {
					var headingText = prompt(Texyla.lng.texy_heading_text, "");
					if (headingText) {
						this.tag(
							headingText + this.lineFeed + this.headingCreate(type, headingText.length) + this.lineFeed,
							''
						);
					}
				// Vyrobí nadpis z výběru
				} else {
					this.tag(
						'',
						this.lineFeed + this.headingCreate(type, this.selection.len)
					);
				}
			},
			
			// odkaz
			link: function() {
				var addr = prompt(Texyla.lng.texy_link_url, 'http://');
				if (addr) {
					this.phrase('"', '":' + addr);
				}
			},
			
			// acronym
			acronym: function() {
				this.doSelect();
				var title = prompt(this.Texyla.texy_acronym_title, '');
				if (title) {
					// Nejsou potřeba uvozovky. př.: slovo((titulek))
					if (this.selection.txt.match(/^[a-zA-ZěščřžýáíéúůĚŠČŘŽÝÁÍÉÚŮ]{2,}$/)) {
						this.tag('','((' + title + '))');
						
					// Jsou potřeba uvozovky. př.: "třeba dvě slova"((titulek))
					} else {
						this.phrase('"', '"((' + title + '))');
					}
				}
			},
			
			// čára
			line: function() {
				this.doSelect();
				var lineText = this.lineFeed + this.lineFeed + '-------------------' + this.lineFeed + this.lineFeed;
				if (this.selection.cursor) {
					this.tag(lineText, '');
				} else {
					this.replaceSelection(lineText);
				}
			},
			
			// blok
			block: function(what) {
				this.getLineFeedFormat();
				this.tag('/--' + what + this.lineFeed, this.lineFeed + '\\--');
			},
			
			// kurzíva
			em: function () {
				var em = this.Texyla.options.emSyntax;
				this.phrase(em, em);
			},
			
			// obrázek
			img: function(src, alt, align, descr) {
				this.getLineFeedFormat();
				
				// Zarovnání na střed
				var imgT = align == '<>' ? this.lineFeed + '.<>' + this.lineFeed : '';
				
				// Začátek
				imgT += '[* ' + src + ' ';
				
				// Popis
				imgT += alt ? '.('+ alt +') ' : '';
				
				// Zarovnání
				imgT += (align != '<>' ? align : '*') + ']';
				
				// Popisek
				imgT += descr ? ' *** ' + alt : '';
				
				this.replaceSelection(imgT);
			},
			
			// tabulka
			table: function(cols, rows, header) {
				this.getLineFeedFormat();
				var tabTxt = this.lineFeed;
				
				for (var i = 0; i < rows; ++i) {
					// Hlavička nahoře
					if (header === 'n' && i < 2) {
						tabTxt += '|';
						for (var j = 0; j < cols; ++j) {
							tabTxt += '--------';
						}
						tabTxt += this.lineFeed;
					}
					
					// Buňky
					for (j = 0; j < cols; ++j) {
						// Hlavička vlevo
						if (header === 'l' && j === 0) {
							tabTxt += "|* \t";
							
						// Buňka bez hlavičky
						} else {
							tabTxt += "| \t"; 
						}
						if (i === 0 && j === 0) {
							var firstLength = tabTxt.length - 1;
						}
					}
					tabTxt += '|' + this.lineFeed;
				}
				tabTxt += this.lineFeed;
				
				// Vloží tabulku
				this.tag(tabTxt.substring(0, firstLength), tabTxt.substring(firstLength));
			}
		};
	},
	
	
	Dom: function() {
		return {
			//Kontejner na texylu
			//container: null,
			
			// Načtení texyly
			init: function() {
				var options = this.Texyla.options;
				
				// načíst CSS
				Texyla.loadStylesheet(this.Texyla.addr.css + 'base.css');
				Texyla.loadStylesheet(this.Texyla.addr.css + this.Texyla.options.theme +'/'+ this.Texyla.options.theme + '.css');
				
				// Textarea
				var ta = this.Texyla.textarea;
				
				// Automatická šířka
				if (options.editorWidth == 'auto') {
					options.editorWidth = ta.offsetWidth;
				}
				
				// Kontejner na Texylu
				var skinContainer = ta.parentNode.insertBefore(
					_('div.' + options.theme,
						this.container = _('div.Texyla')
					),
					ta
				);
				this.container.style.width = options.editorWidth + 'px';

				// Vytvořit bloky
				this.block('edit');
				this.block('preview');
				if (options.allowHtmlPreview) {
					this.block('html');
				}
				
				// Tlačítka pod textareou
				this.createBottomBar();
				
				// Zobrazit výchozí pohled
				this.Texyla.View.switchView(ta.value == '' ? 'edit' : options.defaultView);
				
				// Klávesové zkratky
				// přidá fci this.shortcuts textaree na keydown
				var _this = this;
				_event(ta, 'keydown', function(e) {_this.shortcuts(e)});
				
				// schovat elementy
				_display(this.Texyla.options.hideElements, false);
			},
			
			// Vytvoří blok (edit, preview, html)
			block: function(name) {
				var View = this.Texyla.View;
				
				// pomocná proměnná
				var divName = {
					preview: "preview",
					html: "htmlPreview"
				};
				
				var icon = {
					edit: "edit",
					preview: "view",
					html: "source"
				};
				
				
				// Vložit
				var block =  _app(
					this.container,
					
					_('div', name == "edit" ? [
							this.createTopBar(),
							_('div.textareaParent',
								this.Texyla.textarea
							)
						] : [
							_('div.heading', [this.img(icon[name]), Texyla.lng['view_' + name] ]),
							View[divName[name] + "Div"] = _('div.' + divName[name])
						]
					)
				);
				
				// margin textareay
				if (name == "edit" && this.Texyla.options.textareaMargin) {
					this.Texyla.textarea.style.width = ( this.Texyla.options.editorWidth - 2 * this.Texyla.options.textareaMargin ) + 'px';
					this.Texyla.textarea.style.margin = this.Texyla.options.textareaMargin + 'px 0';
				}
				
				// Přidá pohled
				View.views.push({
					block: block,
					control: this.button(Texyla.lng['view_' + name], icon[name], function() {View.switchView(name);}),
					btn: []
				});
			},
			
			// Zobrazení a nápověda
			createBottomBar: function() {
				var left;
				_app(this.container, _('div.bottomBar', [
					left = _('div.bottomLeftBar'),
					this.bottomRightBar = _('div.bottomRightBar'),
					_('div.cleaner')
				]));
				
				// Tlačítka vlevo
				for (var i in this.Texyla.View.views) {
					_app(left, this.Texyla.View.views[i].control);
				}
				
				// Tlačítka vpravo
				
				var buttons = {
					syntax: this.button(Texyla.lng.view_syntax, "help", function() {window.open('http://texy.info/cs/syntax/');}),
					submit: this.button(Texyla.lng.view_submit, "tick", function () {_this.Texyla.textarea.form.submit();})
				};
				
				var _this = this;
				function btn(id, type, requirement) {
					if (requirement) {
						_this.Texyla.View.views[id].btn.push(buttons[type]);
					}
				}
				
				var submitOp = this.Texyla.options.submitButton;
				
				// Edit
				btn(0, 'syntax', this.Texyla.options.syntaxButton);
				btn(0, 'submit', submitOp === true);
				btn(1, 'submit', submitOp);
				btn(2, 'submit', submitOp && this.Texyla.options.allowHtmlPreview);
			},
			
			// Vytvoří tlačítkovou lištu
			createTopBar: function() {
				var domBar = _('ul.toolbar');
				
				// Tlačítka
				var toolbar = this.Texyla.options.toolbar;
				var buttons = this.Texyla.Buttons;
				
				for (var i=0; i<toolbar.length; i++) {
					// Oddělovač
					if (toolbar[i] === null) {
						_app(domBar, _('li.separator'));
						continue;
					}
					
					// Ikonka
					if (typeof(toolbar[i]) == "string") {
						var span;
						
						// Vložit
						_app(domBar, _('li',
							this.hover(
								span = _('span.link', {title: buttons[toolbar[i]].name},
									this.img(buttons[toolbar[i]].icon)
								)
							)
						));
						
						span.onclick = buttons[toolbar[i]].func;
						continue;
					}
					
					// Menu
					if (typeof(toolbar[i]) == "object") {
						var menu, item;
						var menuArray = toolbar[i];
						
						_app(domBar, this.hover(
							_('li.' + (typeof(toolbar[i-1]) == "string" ? "btnmenu" : "menu"),
								menu = _('ul')
							)
						));
						
						// Položky
						for (var j=0;j<menuArray.length;j++) {
							item = _app(menu, this.hover(
								_('li', [
									this.img(buttons[menuArray[j]].icon),
									_('span', buttons[menuArray[j]].name)
								])
							));
							
							item.onclick = buttons[menuArray[j]].func;
						}
						continue;
					}
				}
				
				return domBar;
			},
			
			// Hover efekt
			hover: function(el) {
				// při najetí myší přiřadí nebo ubere třídu hover
				_event(el, 'mouseover', function() {el.className += (el.className ? ' ' : '') + 'hover';});
				_event(el, 'mouseout', function() {el.className = el.className.replace(/ ?hover/,'');});
				
				return el;
			},
			
			// Tlačítko
			button: function(title, icon, func) {
				// Hezké tlačítko
				if (this.Texyla.options.coolButtons == true) {
					return this.hover(
						_('span.coolbtn', {onclick: func}, [
							_('span.coolbtn-left'),
							_('span.coolbtn-middle', [
								this.img(icon),
								title
							]),
							_('span.coolbtn-right')
						])
					);
					
				// Normální tlačítko
				} else {
					return _('button', {type: 'button', onclick: func}, [this.img(icon), " " + title]);
				}
			},
			
			img: function(file, title) {				
				return this.pngHack(
					_('img', {
						src: this.Texyla.addr.icons + this.Texyla.options.iconType + "/" + file + "." + this.Texyla.options.iconFormat,
						width: 16,
						height: 16,
						alt: title ? title : '',
						title: title ? title : ''
					})
				);
			},
			
			pngHack: function (img) {
				//pro IE se nacpe průhledný gif s filtrem png
				var pngIeHack = this.Texyla.IE && /MSIE\s(5\.5|6\.)/.test(navigator.userAgent) && /png$/.test(img.src);
				
				if (pngIeHack) {
					img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + img.src + "')";
					img.src = this.Texyla.addr.icons + 'pixel.gif';
				}
				
				return img;
			},
			
			//funkce, která umožní vkládání tabulátoru
			shortcuts: function(e) {
				var pressedKey = e.charCode || e.keyCode || -1;
				
				var action = false;
				
				// tučně (Ctrl + B nebo např. Shift + Ctrl + B)
				if (e.ctrlKey && pressedKey == 66 && !e.altKey) {
					this.Texyla.Texy.phrase('**','**');
					action = true;				
				}
				
				// kurzíva (Ctrl + I nebo např. Alt + Ctrl + I)
				if (e.ctrlKey && pressedKey == 73) {
					this.Texyla.Texy.em();
					action = true;
				}
				
				// tabulátor (tab)
				if (pressedKey == 9) {
					this.Texyla.Texy.tag('\t','');
					action = true;
				}
				
				// Odeslat formulář (Ctrl + S nebo např. Shift + Ctrl + S), musí být povolené v nastavení
				if ((e.ctrlKey && pressedKey == 83) && this.Texyla.options.submitOnCtrlS) {
					this.Texyla.textarea.form.submit();
					action = true;
				}
				
				// zruší defaultní akce
				if (action) {
					// Firefox & Opera (ale ta na to docela sere)
					if (e.preventDefault && e.stopPropagation) {
						e.preventDefault();
						e.stopPropagation();
						
					// IE
					} else {
						window.event.cancelBubble = true;
						window.event.returnValue = false;
					}
				}				
			},
			
			formInput: function(options) {
				var td, label;
				
				// řádek bez inputku
				var dom = _('tr', [
					_('th',
						label = _('label', {'for': 'winInp' + (++Texyla.Window.maxInput)}, options.name)
					),
					td = _('td')
				]);
				
				// inputek
				switch (options.type) {
					case 'input':
						var inp = _app(td, _('input', options.attributes));
						inp.onfocus = function() { this.select() };
					break;
					case 'select':
						var inp = _app(td, _('select', options.attributes));
						// options
						for (var i = 0; i < options.options.length; i++) {
							var opt = _app(inp, _('option', { value: options.options[i][0] }, options.options[i][1]) );
							if (options.options[i][2] == true) {
								opt.selected = true;
							}
						}
					break;
				}	
				
				// label for
				inp.setAttribute('id', 'winInp' + Texyla.Window.maxInput);
				
				return {
					dom: dom,
					inp: inp
				}
			}
		}
	},
	
	View: function() {
		return {
			views: [],
			
			/*
			// Náhled zpracovaného Texy
			previewDiv: null,
			
			// Div s náhledem HTML
			htmlPreviewDiv: null,
			
			// Tlačítka vpravo dole
			bottomRightBar: null,
			
			// Poslední zpracované Texy
			lastPreviewTexy: null,
			*/
			
			switchView: function(viewName) {			
				var changed = this.lastPreviewTexy !== this.Texyla.textarea.value;
				
				if (this.Texyla.textarea.value === '' && viewName !== 'edit') {
					alert(Texyla.lng.view_empty);
					this.Texyla.textarea.focus();
					return false;
				}
				
				if (changed && viewName !== 'edit') {
					var msg = _app(_empty(this.previewDiv), _('p.wait', Texyla.lng.view_wait));
					
					if (this.Texyla.options.allowHtmlPreview) {
						_app(_empty(this.htmlPreviewDiv), msg.cloneNode(true));
					}
					
					this.lastPreviewTexy = this.Texyla.textarea.value;
					
					this.getPreview();
				}
				
				var viewId = {
					// Upravit
					edit: 0,
					
					// Náhled
					preview: 1,
					
					// HTML zdroj
					html: 2
				};
				
				// Bloky
				for (var i=0; i < this.views.length; i++) {
					_display(this.views[i].block, i == viewId[viewName]);
				}
				
				// Tlačítka dole vlevo
				for (var i=0; i<this.views.length; i++) {
					_display(this.views[i].control, i != viewId[viewName], 'inline');
				}
				
				// Tlačítko dole vpravo
				var bar = this.Texyla.Dom.bottomRightBar;
				var btn = this.views[viewId[viewName]].btn;
				
				_empty(bar);
				for (var i=0; i<btn.length; i++) {
					_app(bar, btn[i]);
				}
			},
			
			getPreview: function() {
				var _this = this;
				function onLoad(res) {
					return _this.onPreviewLoad(res);
				}
				var options = { onComplete: onLoad, jsonRes: true };
				var vars = { texylaContent: this.Texyla.textarea.value, texylaTexyCfg: this.Texyla.options.texyCfg};
				if (this.Texyla.options.AjaxPreProcessor) {
					this.Texyla.options.AjaxPreProcessor(options);
				}
				new Texyla.Ajax(this.Texyla.addr.ajax, options, vars);
			},
			
			onPreviewLoad: function(res) {
				if (this.Texyla.options.AjaxProcessor) {
					res = this.Texyla.options.AjaxProcessor(res);
				}
				this.previewDiv.innerHTML = res;
				
				// Zobrazí zdrojový kód (HTML)
				if (this.Texyla.options.allowHtmlPreview) {
					_app(
						_empty(this.htmlPreviewDiv),
						_('pre', res.replace(/\n/g, this.Texyla.Texy.lineFeed))
					);
				}
			}
		}
	},
	
	Windows: function () {
		return {
			// otevřená okna
			open: {
				img: false,
				table: false,
				emoticon: false,
				symbol: false
			},
			
			// obrázek
			img: {
				/*window: null,
				src: null,
				alt: null,
				align: null,
				descr: null,*/
				
				open: function(btnClicked) {
					if (this.Texyla.Windows.open.img == true) {
						this.window.focus();
						return false;
					}
					this.Texyla.Windows.open.img = true;
					
					var _this = this;
					
					// IE fix
					this.Texyla.Texy.doSelect();
					
					var properties = {
						heading: Texyla.lng.img_heading,
						content: null,
						func: function() {
							_this.Texyla.View.switchView('edit');
							_this.window.close();
							_this.Texyla.Texy.img(_this.src.value, _this.alt.value, _this.align[_this.align.selectedIndex].value, _this.descr.checked);
						},
						_this: this,
						open: 'img',
						btnClicked: btnClicked
					};
					
					// Obsah
					var tbody;
					properties.content = _('table', 
						tbody = _('tbody')
					);
					
					var attr = {type: 'text', className: 'textField'};
					
					// Adresa
					var tr = this.Texyla.Dom.formInput({name: Texyla.lng.img_src, type: 'input', attributes: attr});
					_app(tbody, tr.dom);
					this.src = tr.inp;
					
					// Alt
					tr = this.Texyla.Dom.formInput( {name: Texyla.lng.img_alt, type: 'input', attributes: attr} );
					_app(tbody, tr.dom);
					this.alt = tr.inp;
					
					// Popisek
					_app(tbody, _('tr', [
						_('td'),
						_('td',
							_('label', [
								this.descr = _('input', {type: 'checkbox'}),
								Texyla.lng.img_descr	// Zobrazit jako popisek
							])
						)
					]));
					
					// Hlavička
					tr = this.Texyla.Dom.formInput({
						name: Texyla.lng.img_align,
						type: 'select',
						options: [
							['*', Texyla.lng.img_al_none, true],
							['<', Texyla.lng.img_al_left, false],
							['>', Texyla.lng.img_al_right, false],
							['<>', Texyla.lng.img_al_center, false]
						]
					});
					_app(tbody, tr.dom);
					this.align = tr.inp;
					
					this.window = new Texyla.Window(properties);
				}
			},
			
			// smajlík
			emoticon: {
				//window: null,
				
				open: function(btnClicked) {
					if (this.Texyla.Windows.open.emoticon == true) {
						this.window.focus();
						return false;
					}
					this.Texyla.Windows.open.emoticon = true;
					
					var _this = this;
					
					var properties = {
						heading: Texyla.lng.emoticon_heading,
						content: null,
						func: null,
						_this: this,
						open: 'emoticon',
						btnClicked: btnClicked
					};
					
					// Obsah
					var emoticons = this.Texyla.options.emoticons;
					
					function insEmoticon(txt) {
						return function() {
							_this.Texyla.Texy.replaceSelection(txt);
							_this.window.close();
						}
					}
					
					properties.content = _('div.emoticons');
					
					var emCt = 0;
					for (var i in emoticons.icons) {
						//jednou za 5 smajlíků odřádkuje
						if (emCt%5 == 0 && emCt > 0) {
							_app(properties.content, _('br'));
						}
						emCt++;
						
						//vloží obrázek
						_app(
							properties.content,
							
							this.Texyla.Dom.hover(
								_('span', {onclick: insEmoticon(i)},
									this.Texyla.Dom.pngHack(
										_('img', {
											width: emoticons.width,
											height: emoticons.height,
											alt: i,
											title: i,
											src: this.Texyla.addr.emoticons + emoticons.icons[i] + '.' + emoticons.format
										})
									)
								)
							)
						);
					}
					
					_app(properties.content, _('div.cleaner'));
					
					this.window = new Texyla.Window(properties);
				}
			},
			
			// symbol
			symbol: {
				//window: null,
				
				open: function(btnClicked) {
					if (this.Texyla.Windows.open.symbol == true) {
						this.window.focus();
						return false;
					}
					this.Texyla.Windows.open.symbol = true;
					
					var _this = this;
					
					var properties = {
						heading: Texyla.lng.symbol_heading,
						content: null,
						func: null,
						_this: this,
						open: 'symbol',
						btnClicked: btnClicked
					};
					
					// Obsah
					var symbols = this.Texyla.options.symbols;
					
					function insSymbol(txt) {
						return function() {
							_this.Texyla.Texy.replaceSelection(txt);
							_this.window.close();
						}
					}
					
					properties.content = _('div.emoticons');
					
					var emCt = 0, symb, symbIns;
					for (var i in symbols) {
						//jednou za 10 symbolů odřádkuje
						if (emCt%10 == 0 && emCt > 0) {
							_app(properties.content, _('br'));
						}
						emCt++;
						
						if (typeof(symbols[i]) == 'string') {
							symb = symbols[i];
							symbIns = symbols[i];
						} else {
							symb = symbols[i][0];
							symbIns = symbols[i][1];
						}								
						
						//vloží odkaz
						_app(
							properties.content,
							this.Texyla.Dom.hover( _('span', {onclick: insSymbol(symbIns)}, symb) )
						);
					}
					
					_app(properties.content, _('div.cleaner'));
					
					this.window = new Texyla.Window(properties);
				}
			},
			
			// tabulka
			table: {
				/*window: null,
				
				// formulářové prvky
				cols: null,
				rows: null,
				select: null,
				
				// rychlá tabulka
				select: null,
				table: null,*/
				
				open: function(btnClicked) {
					if (this.Texyla.Windows.open.table == true) {
						this.window.focus();
						return false;
					}
					this.Texyla.Windows.open.table = true;
					
					var _this = this;
					
					// IE fix
					this.Texyla.Texy.doSelect();
					
					var properties = {
						heading: Texyla.lng.tab_heading,
						content: null,
						func: function() {
							_this.Texyla.View.switchView('edit');
							_this.window.close();
							_this.Texyla.Texy.table(_this.cols.value, _this.rows.value, _this.header[_this.header.selectedIndex].value);
						},
						_this: this,
						open: 'table',
						btnClicked: btnClicked
					};
					
					// Obsah
					var tbody;
					var table = _('table.table',
						tbody = _('tbody')
					);
					
					var func = function() { _this.setColor(_this.cols.value, _this.rows.value); };					
					var attr = {type: 'number', value: 2, min: 1, maxlength: 2, className: 'number', onkeyup: func, onchange:func};
					
					// Sloupce
					var tr = this.Texyla.Dom.formInput( {name: Texyla.lng.tab_cols, type: 'input', attributes: attr} );
					_app(tbody, tr.dom);
					this.cols = tr.inp;
					
					// Řádky
					tr = this.Texyla.Dom.formInput( {name: Texyla.lng.tab_rows, type: 'input', attributes: attr} );
					_app(tbody, tr.dom);
					this.rows = tr.inp;
					
					// Hlavička
					tr = this.Texyla.Dom.formInput({
						name: Texyla.lng.tab_th,
						type: 'select',
						options: [
							['', Texyla.lng.tab_th_none, false],
							['n', Texyla.lng.tab_th_top, true],
							['l', Texyla.lng.tab_th_left, false]
						]
					});
					_app(tbody, tr.dom);
					this.header = tr.inp;
					
					// rychlá tabulka
					this.table = _('div.tabBackground', [
						this.select = _('div.tabSelection'),
						_('div.tabControl', {
							onmousemove: function(e) { var event = e || window.event; _this.doSelect(event); },
							onmouseover: function() { _this.tabSelect = true },
							onclick: function() { _this.tabSelect = !_this.tabSelect; _this.cols.focus(); },
							ondblclick: properties.func
						})
					]);
						
					properties.content = [table, this.table];
					
					this.window = new Texyla.Window(properties);
				},
				
				// Velikost čtverečku
				tabRectangle: 8,
				
				// Povolena změna rozměrů tabulky
				tabSelect: true,
				
				getDimensions: function(event) {
					var posEl = Texyla.getPosition(this.table);
					var mouseX = event.x + document.documentElement.scrollLeft || event.pageX;
					var mouseY = event.y + document.documentElement.scrollTop || event.pageY;
					var posX = mouseX - posEl.left;
					var posY = mouseY - posEl.top;
					
					// Rozměry
					return {
						cols: Math.max(1, Math.ceil(posX / this.tabRectangle)),
						rows: Math.max(1, Math.ceil(posY / this.tabRectangle))
					};
				},
				
				doSelect: function(event) {
					if (this.tabSelect) {
						var dimensions = this.getDimensions(event);
						this.setColor(dimensions.cols, dimensions.rows);
						this.cols.value = dimensions.cols;
						this.rows.value = dimensions.rows;
					}
				},
				
				setColor: function(cols, rows) {
					this.select.style.width = (Math.min(cols, 10) * this.tabRectangle) + 'px';
					this.select.style.height = (Math.min(rows, 10) * this.tabRectangle) + 'px';
				}
			}
		}
	}
};

Texyla.Window = function (properties) {
	var _this = this;
	this.Texyla = properties._this.Texyla;
	this.open = properties.open;
	var form;
	
	// okno
	_app(
		document.body,
		this.skinCont = _('div.' + this.Texyla.options.theme,
			this.win = _('div.TexylaPopup', {onmousedown: function () {_this.focus();}},
				form = _('form', [
					_display(_('button', {type: 'submit'}), false),
					
					// hlavička
					_('div.heading', [
						// ikona
						this.Texyla.Dom.img(this.open),
						// dragger
						_('div',{
							// funkce na přetažení okna
							onmousedown: function(event) { _this.dragStart(event || window.event); return false; },
							onmouseup: function() { _this.dragEnd() },
							onmouseout: function(event) { _this.doDrag(event || window.event) }
						}, properties.heading), // text
						// zavřít
						this.Texyla.Dom.hover(
							_('span.close', {title: Texyla.lng.win_close, onclick: function () {_this.close();}})
						)
					]),
					
					// obsah
					_('div.winForm', properties.content),
					
					// dolní lišta
					(
						properties.func ?
							_('div.bottomBar', [
								this.Texyla.Dom.button(Texyla.lng.win_ins, "tick", properties.func),
								_('div.cleaner')
							])
						:
							false
					)
				])
			)
		)
	);
	
	form.onsubmit = function() {properties.func(); return false;};
	
	var leftPos = Math.min(document.body.offsetWidth - this.win.offsetWidth, Texyla.getPosition(properties.btnClicked).left);
	this.win.style.left = Math.max(0, leftPos) + 'px';
	this.win.style.top = Texyla.getPosition(this.Texyla.textarea).top + 5 + 'px';
	
	//focus
	var firstInput = this.win.getElementsByTagName('input')[0];
	if (firstInput) {
		firstInput.focus();
	}
};
Texyla.Window.maxZIndex = 0;
Texyla.Window.maxInput = 0;

Texyla.Window.prototype = {
	lastX: 0,
	lastY: 0,
	dragging: false,
	
	close: function() {
		this.Texyla.Windows.open[this.open] = false;
		document.body.removeChild(this.skinCont);
		
		var sel = this.Texyla.Texy.selection;
		this.Texyla.Texy.select(sel.start, sel.len);
		/*
			tady to občas hlásí nějakou chybu (IE). start má hodnotu null nebo není objekt
		*/
	},
	
	focus: function() {
		this.win.style.zIndex = ++Texyla.Window.maxZIndex;
	},
	
	dragStart: function(event) {
		var _this = this;
		this.dragging = true;
		this.lastX = event.x || event.pageX;
		this.lastY = event.y || event.pageY;
		document.body.onmousemove = function(event) {
			_this.doDrag(event || window.event);
			return false;
		};
	},
	
	dragEnd: function() {
		document.body.onmousemove = null;
		this.dragging = false;
	},
	
	doDrag: function(event) {
		if (!this.dragging) {
			return;
		}
		var mouseX = event.x || event.pageX;
		var mouseY = event.y || event.pageY;
		
		this.win.style.left = (parseInt(this.win.style.left) + mouseX - this.lastX) + 'px';
		this.win.style.top = (parseInt(this.win.style.top) + mouseY - this.lastY) + 'px';
		
		this.lastX = mouseX;
		this.lastY = mouseY;
	}
};

// Zjištění pozice elementu na stránce, počítá i s offsetParent
Texyla.getPosition = function(el) {
	var left = 0;
	var top = 0;
	while (el) {
		left += el.offsetLeft;
		top += el.offsetTop;
		el = el.offsetParent;
	}
	return {
		left: left,
		top: top
	};
};

Texyla.Ajax = function(url, options, params) {
	var _this = this;
	this.xmlhttp = Texyla.Try(
		function() { return new XMLHttpRequest() },
		function() { return new ActiveXObject('Msxml2.XMLHTTP') },
		function() { return new ActiveXObject('Microsoft.XMLHTTP') }
	);
	this.onComplete = options.onComplete;
	this.options = options;
	function onStateChange() {
		if (_this.xmlhttp.readyState == 4) {
			var res = _this.xmlhttp.responseText;
			_this.onComplete(res);
		}
	};
	this.onStateChange = this;
	this.xmlhttp.onreadystatechange = onStateChange;
	
	// zjištění kódóvání dokumentu
	var nurl = url + '?str=' + (new Date().getTime()) + '&amp;texylaCharset=' + TexylaCharset;
	//alert('url je: ' + nurl);
	this.xmlhttp.open('post', nurl, true);
	this.xmlhttp.setRequestHeader(
		'Content-Type', 'application/x-www-form-urlencoded'
	);
	post = 'texylaAjax=1';
	esc = encodeURIComponent || escape;
	for (i in params) {
		if (params.hasOwnProperty(i)) {
			post += '&' + i + '=' + esc(params[i]);
		}
	}
	this.xmlhttp.send(post);
};

Texyla.Try = function() {
	var retval;
	for (i = 0; i < arguments.length; ++i) {
		try {
			retval = new arguments[i];
		} catch(e) {
			continue;
		}
		return retval;
	}
};

// Načte css soubor, pokud již není načten
Texyla.loadStylesheet = function(href) {
	var stylesheets = document.styleSheets;
	for (var i = 0; i < stylesheets.length; i++) {
		if (href == stylesheets[i].href) {
			return false;
		}
	}
	//alert(stylesheets.length);
	var link = _('link');
	link.setAttribute('href', href);
	link.setAttribute('type', 'text/css');
	link.setAttribute('rel', 'stylesheet');
	document.getElementsByTagName('head')[0].appendChild(link);
};

function _(name, attr, content) {
	// Element
	var el, rgxp;
	
	if ((content === undefined) && (attr !== undefined) && (attr instanceof Array || attr.tagName || typeof(attr) == "string")) {
		content = attr;
		attr = false;
	}

	// Element se třídou
	rgxp = /^(\w+)\.([\w-]+)$/;
	if (rgxp.test(name)) {
		el = document.createElement(name.match(rgxp)[1]);
		el.className = name.match(rgxp)[2];
	
	// Element bez třídy
	} else {
		el = document.createElement(name);
	}
	
	if (attr) {
		for (var prop in attr) {
			if (attr.hasOwnProperty(prop) && attr[prop]) {
				var val = attr[prop];
				
				// javascript
				rgxp = /^on(\w+)$/;
				if (rgxp.test(prop)) {
					_event(el, prop.match(rgxp)[1], val);
					continue;
				}
				
				// třída
				if (prop == 'className') {
					el.className = val;
					continue;
				}
				
				// input type="number"
				var Opera = navigator.userAgent.indexOf('Opera') != -1;
				if (name=="input" && prop == "type" && val == "number" && !Opera) {
					val = "text";
					continue;
				}
				
				// normální vlastnost
				el.setAttribute(prop, val);
			}
		}
	}
	
	// vloží, objekt, text nebo pole objektů či textů
	_app(el, content);
	
	return el;
};
// vloží, objekt, text nebo pole objektů či textů
function _app(el, content) {
	if (el && content) {
		
		// vloží, objekt, text
		function appone (el, content) {
			if (content) {
				if (typeof(content)=='object') {
					el.appendChild(content);
				}
				
				if (typeof(content)=='string') {
					el.appendChild(document.createTextNode(content));
				}
			}
		}
		
		// jestli je to pole
		if (content instanceof Array) {
			// nahází to tam po jednom
			for (var i=0; i<content.length; i++) {
				if (content instanceof Array) {
					_app(el, content[i]);
				} else {
					appone(el, content[i]);
				}
			}
		
		// nebo jen jedna věc
		} else {
			appone(el, content);
		}
	}
	
	return content;
};

// vyprázdní element
function _empty(ref) {
	while (ref.hasChildNodes()) {
		ref.removeChild(ref.childNodes[0]);
	}
	
	return ref;
};

// schovávací a zobrazovací fce
function _display(el, display, style) {
	if (el) {
		// funkce výkonná
		function disp(el, display, style) {
			el.style.display = display ? (style ? style : 'block') : 'none';
		}
		
		// pole elementů
		if (el instanceof Array) {
			for (var i=0; i<el.length; i++) {
				disp(el[i], display, style);
			}
		
		// pouze jeden element
		} else {
			disp(el, display, style);
		}
	}
	
	return el;
};

function _event(el, type, func) {
	if (el.addEventListener) {
		el.addEventListener(type, func, false); // O, FF
		return el;
	} else if (el.attachEvent) {
		return el.attachEvent('on' + type, func); // IE
	}
};

// zkusí přednačíst základní css v defaultním umístění
Texyla.loadStylesheet(Texyla.configurator.defaultCfg().addr.css + 'base.css');
