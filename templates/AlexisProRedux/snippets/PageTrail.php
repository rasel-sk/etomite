// Snippet name: PageTrail
// Snippet description: Outputs the page trail, based on Bill Wilson's script
// Revision: 1.00 ships with Etomite 0.6.1-Final

$sep = " &raquo; ";

// end config
$ptarr = array();
$pid = $etomite->documentObject['parent'];
$ptarr[] = "<a href='[~".$etomite->documentObject['id']."~]'>".$etomite->documentObject['pagetitle']."</a>";

while ($parent=$etomite->getParent($pid)) {
    $ptarr[] = "<a href='[~".$parent['id']."~]'>".$parent['pagetitle']."</a>";
    $pid = $parent['parent'];
}

$ptarr = array_reverse($ptarr);
return join($ptarr, $sep);
