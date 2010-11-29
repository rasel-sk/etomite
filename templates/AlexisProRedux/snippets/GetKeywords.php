// Snippet name: GetKeywords
// Snippet description: Fetches the keywords attached to the document.
// Revision: 1.00 ships with Etomite 0.6.1-Final

$keywords = $etomite->getKeywords();
if(count($keywords)>0) {
    $keys = join($keywords, ", ");
    return '<meta http-equiv="keywords" content="'.$keys.'" />';
} else {
    return false;
}
