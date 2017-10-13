<?php
# smajlíky silk - konfigurák

# kde hledat smajlíky
# cesta pro html
$texy->emoticonModule->root = '/manager/media/texyla/emoticons/silk/';
# cesta pro php (kvůli rozměrům souborů)
$texy->emoticonModule->fileRoot = dirname(__FILE__);

# ikony
$texy->emoticonModule->icons = array(
	':-)' => 'smile.png',
	':-(' => 'unhappy.png',
	';-)' => 'wink.png',
	':-D' => 'grin.png',
	':-O' => 'surprised.png',
	':-P' => 'tongue.png'
);
?>