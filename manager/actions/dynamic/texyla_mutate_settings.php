<?php
/*****************************************************************
	Texyla setting for etomite admin (manager)
	
	Zde si nastavte jak se má chovat editor Texyla na textarea
	podrobnosti o nastavení naleznete na http://texyla.jaknato.com	

	Modified 2008-06-13 by Petr Vaněk aka krteczek to added Texyla
******************************************************************/

$contentTexy = ((!isset($syntax) || $content['content'] != '') ? htmlspecialchars($content['texy']) : '');

$txtTexylaSettings = <<< EEE
<!-- Standard TextArea Box -->
<div style="width:100%">
	<textarea id="ta" name="ta" style="width:100%; " onChange="documentDirty=true;">{$contentTexy}</textarea>
</div>
<script type="text/javascript">
	options = Texyla.configurator.admin("ta");
	options.toolbar = ["h1", "h2", "h3", ["h4"], null, "bold", "italic", ["del", "sub", "sup", "acronym", "inlineCode", "hr"], null, "center", ["left", "right", "justify"], null, "ul", "ol", ["blockquote"], null, "img", "table", "link", null, "code_html", "code_php", ["code", "code_css", "code_js", "code_sql"], null, "div", "html", "comment", "notexy", ["text"], null, "emoticon", "symbol"]
	options.submitButton = true;
	options.symbols = ['&', '@', ['<', '&lt;'], ['>', '&gt'], '{', '}', '[', ']', '%','‰', 'α', 'β', 'π', 'µ', 'Ω', '∑', '°', '∞', '≠', '±', '×', '÷', '≥', '≤', '®', '™', '€'];
	new Texyla(options);
</script>  
EEE;
echo $txtTexylaSettings;

?>