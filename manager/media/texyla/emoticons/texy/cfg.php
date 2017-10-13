<?php
# smajlíky texy (výchozí) - konfigurák

# kde hledat smajlíky
# cesta pro html
# $texy->emoticonModule->root = 'texyla/emoticons/texy/';
$texy->emoticonModule->root = '/etomite/manager/media/texyla/emoticons/texy/';
# cesta pro php (kvůli rozměrům souborů)
$texy->emoticonModule->fileRoot = dirname(__FILE__);

# ikony
$texy->emoticonModule->icons = array(
	':-)' => 'smile.gif',
	':-(' => 'sad.gif',
	';-)' => 'wink.gif',
	':-D' => 'biggrin.gif',
	'8-O' => 'eek.gif',
	'8-)' => 'cool.gif',
	':-?' => 'confused.gif',
	':-x' => 'mad.gif',
	':-P' => 'razz.gif',
	':-|' => 'neutral.gif'
);
?>