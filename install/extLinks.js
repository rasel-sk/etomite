window.onload=function(){

  function extLinks(class) {
    if (!document.getElementsByTagName) return;
    var anchors = document.getElementsByTagName("a");
    for (var i=0; i<anchors.length; i++) {
      var anchor = anchors[i];
      if (anchor.getAttribute("href")
      && anchor.className.indexOf(class) >= 0)
      {
        anchor.target = "_blank";
      }
    }
  }

  extLinks('external');

}
