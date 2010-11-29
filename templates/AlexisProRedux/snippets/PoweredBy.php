// Snippet name: PoweredBy
// Snippet description: A little link to Etomite.
// Revision: 1.00 ships with Etomite 0.6.1-Final

$version = $etomite->getVersionData();
return '<a href="http://www.etomite.com" title="Etomite Website">Powered by Etomite <b>'.$version['version'].$version['patch_level'].'</b> <i>('.$version['code_name'].')</i>.</a>';
