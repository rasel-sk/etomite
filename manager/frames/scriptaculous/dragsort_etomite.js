//Drag & Drop Sortable Menu Tree v.0.4.3 for Etomite CMS, a mod by Johan Bjerklund (nalagar)
//Last updated: April 22, 2008 for eto v 1.0. Recycle bin image src is switched when a document is dragged over it.

//START DRAG & DROP FOR PARENT ID

document.onmousemove = mouseMove;
document.onmouseup   = mouseUp;

var dragObject  = null; //Our dragged item
var dragHelper = null; //Our 'Ghost'-item
var dropObject = null;
var mouseOffset = null; //NOT USED!
var mouseStart = null; //Here we store the mouse coordinates when a drag is initiated
var realDrag = false; //If an item is dragged more than 5px, we consider it a real drag
var lastTarget  = null;
var cursorTarget = null;
var cursorDefault = '';
var oldRecycleTitle = '';
var oldRecycleIcon = '';
var tree_save_changes = 'You are currently editing a document. Changes will be lost if you continue. Proceed?';
var tree_drop_recycle = 'Drop to delete document';

//var canEdit=false;

var savedSelection = null;  //LINK INTEGRATION GLOBAL HOLDING SELECTION IN EDITOR

scrolldelay=null;
scrollwait=100;
scrolldir=0;



function allowredraw() {

   return !(parent.main.document.mutate && parent.main.document.mutate.a && parent.main.document.mutate.a.value=='5') || (parent.main.document.mutate && parent.main.document.mutate.a && parent.main.document.mutate.a.value=='5' && confirm(tree_save_changes));

}



function pageScroll() {
        var y;
        if (self.pageYOffset) // all except Explorer
	    y = self.pageYOffset;
       else if (document.documentElement && document.documentElement.scrollTop) // Explorer 6 Strict
	    y = document.documentElement.scrollTop;
       else if (document.body) // all other Explorers
	    y = document.body.scrollTop;

    	window.scrollBy(0,scrolldir); // horizontal and vertical scroll increments
        if(parseInt(dragHelper.style.top)>0 && y > 0)
        dragHelper.style.top  = parseInt(dragHelper.style.top) + scrolldir;

        if(scrollwait>0)
    	window.setTimeout('pageScroll()',scrollwait);
}


function realposX(o) {
	var p = 0;
	for(; o.offsetParent; o = o.offsetParent ) p += o.offsetTop;
	if( ! p && o.x ) p = o.x;
	return p;
}


function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1)
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }


function mouseCoords(ev){
	if(ev.pageX || ev.pageY){
		return {x:ev.pageX, y:ev.pageY};
	}
	return {
		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
		y:ev.clientY + document.body.scrollTop  - document.body.clientTop
	};
}


function getMouseOffset(target, ev){
	ev = ev || window.event;
	var docPos    = getPosition(target);
	var mousePos  = mouseCoords(ev);
	return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};
}



function getPosition(e){
	var left = 0;
	var top  = 0;

	while (e.offsetParent){
		left += e.offsetLeft;
		top  += e.offsetTop;
		e     = e.offsetParent;
	}
	left += e.offsetLeft;
	top  += e.offsetTop;
	return {x:left, y:top};
}



function mouseMove(ev){
	ev           = ev || window.event;
        var target   = ev.target || ev.srcElement;
	var mousePos = mouseCoords(ev);
        if(oldRecycleIcon == '')
            oldRecycleIcon = document.getElementById('recycle').getAttribute('src');
        if(realDrag && target && target == document.getElementById('recycle')) {

            if(oldRecycleTitle==''){
                oldRecycleTitle = target.getAttribute('title');
                //oldRecycleIcon = target.getAttribute('src');
                }
            target.setAttribute('title',tree_drop_recycle);
            target.setAttribute('src','media/images/tree/trash_full_over.gif');
        }
        else {
		if(oldRecycleTitle!=''){
            	document.getElementById('recycle').setAttribute('title',oldRecycleTitle);
            	oldRecycleTitle = "";
            	document.getElementById('recycle').setAttribute('src',oldRecycleIcon);
            	//oldRecycleIcon = "";
		}
        }

        dropObject=null;
        for(var x=target; x ; x=x.parentNode) {
             if(x.tagName=='LI' && !dropObject ) {
                 dropObject=x;
              }
              if(x==dragObject) {
                 dropObject=null;
                 break;
              }
        }

        if( target.tagName=='LI'||(dropObject && dragObject && dropObject.getElementsByTagName('ul')[0]==dragObject.parentNode ) )
              dropObject=null;

	if(dragObject){

            var x,y;
            if (self.innerHeight) // all except Explorer
            {
	        x = self.innerWidth;
	        y = self.innerHeight;
            }
            else if (document.documentElement && document.documentElement.clientHeight) // Explorer 6 Strict Mode
            {
	        x = document.documentElement.clientWidth;
	        y = document.documentElement.clientHeight;
            }
            else if (document.body) // other Explorers
            {
	        x = document.body.clientWidth;
	        y = document.body.clientHeight;
            }

            if(ev.clientY && ev.clientY < 30) {
                   if(Math.abs(10*(ev.clientY))<300)
                        scrollwait=10*(ev.clientY);
                   else
                        scrollwait=10;
                   scrolldir=-1;
                   pageScroll();
            }
            else {
                       if(ev.clientY + 20 > y) {
                            if(30*Math.abs(y - ev.clientY)<300 && y - ev.clientY > 1)
                                  scrollwait=10*Math.abs(y - ev.clientY);
                            else
                                  scrollwait=10;
                            scrolldir=1;
                            pageScroll();
                       }
                       else {
                            if(ev.clientY && ev.clientY>0 && y && y > 0) {
                                 scrollwait=0;
                            }
                       }
             }

                if(!realDrag) {
                    if(mouseStart == null)
                        mouseStart = mousePos;
                    if( Math.abs(mouseStart.x - mousePos.x) >5 || Math.abs(mouseStart.y - mousePos.y) >5 ) {
                        dragHelper.style.display = 'block';
                        dragHelper.style.cssText = "filter:alpha(opacity=50); opacity: 0.50; -moz-opacity:0.50; z-index:99999;" + dragHelper.style.cssText;

                        realDrag = true;
                        //dragObject.style.visibility="hidden";
                    }
                }
                dragHelper.style.top =  mousePos.y -1;
                dragHelper.style.left =  mousePos.x +5;

                if(dropObject && dropObject.getElementsByTagName('DIV').length>0) {
                   if(dropObject.getElementsByTagName('A').length > 0)
                      dropObject.getElementsByTagName('A')[0].style.cssText='color: white;';
                   dropObject.getElementsByTagName('DIV')[0].style.cssText='background: #000099; color: white; margin-left:-2px; padding-left:2px';
                   dropObject.getElementsByTagName('SPAN')[0].style.cssText='color: white';
                   if(dropObject.getElementsByTagName('SPAN')[0].getElementsByTagName('SPAN').length>0)
                       dropObject.getElementsByTagName('SPAN')[0].getElementsByTagName('SPAN')[0].style.cssText='color: white';
                }
                //begin ny
                if(lastTarget && lastTarget!=dropObject && lastTarget.getElementsByTagName('DIV').length>0){
                    if(lastTarget.getElementsByTagName('A').length > 0)
                        lastTarget.getElementsByTagName('A')[0].style.cssText='';
        lastTarget.getElementsByTagName('DIV')[0].style.cssText='';
        lastTarget.getElementsByTagName('SPAN')[0].style.cssText='';
        if(lastTarget.getElementsByTagName('SPAN')[0].getElementsByTagName('SPAN').length>0)
            lastTarget.getElementsByTagName('SPAN')[0].getElementsByTagName('SPAN')[0].style.cssText='';
        }
        if(dropObject && dropObject.getElementsByTagName('DIV').length>0)
        lastTarget=dropObject;
                //end ny
	}
        if(cursorTarget) //Set a good cursor
             cursorTarget.style.cursor=cursorDefault;
        if(target&&realDrag&&target.style) { //We are dragging for parent
             cursorDefault=target.style.cursor;
             cursorTarget=target;
             target.style.cursor='pointer';
        }
        if(target&&drag_active&&target.style) {
             cursorDefault=target.style.cursor;
             cursorTarget=target;
             target.style.cursor='move';
        }
        return false;
}

function mouseUp(ev){
        ev           = ev || window.event;
        var target   = ev.target || ev.srcElement;
        if(cursorTarget) //Reset cursor
             cursorTarget.style.cursor=cursorDefault;
        if(!dragObject || !dropObject) //Fixes bug in IE where mouseup sometimes is fired in random order for objects
              realDrag=false;
        if( !realDrag && target && target.parentNode && target.parentNode.getAttribute && target.parentNode.getAttribute('doOnUp') && target.parentNode.getAttribute('doOnUp')!=''){  //ptmToggle() will sometimes not display the right icon when a node is expanded/collapsed in FF.
               ptmToggle(target.parentNode.getAttribute('doOnUp'));                                                                       //Why?????? If we try again here it works! ( code from function ptmToggleImage() )
               if( document.getElementById("ul." + target.parentNode.getAttribute('doOnUp'))  ){
                  state=document.getElementById("ul." + target.parentNode.getAttribute('doOnUp')).className;
                  img=target.src;
                  pth='';
                  if(target.src.lastIndexOf('/')>0) {
                      pth=target.src.substring(0,target.src.lastIndexOf('/'));
                      img=target.src.substring(target.src.lastIndexOf('/'));
                  }
                  if(state == 'closed'){
    		      if(img.split('open').length > 1)
      		          img = img.split('open')[0] + '.gif';
  		  	}
  		  	if(state == 'open'){
    		      	if(img.split('open').length == 1) 
      		     		img = img.split('.gif')[0] + 'open.gif';
  		  	}
                  target.src = pth + img;
              }
        }
        scrollwait=0;
        if(dragHelper)
            dragHelper.style.display = 'none';
        if(oldRecycleTitle!='') {
            if( allowredraw() ) {
            	document.getElementById('recycle').setAttribute('src',oldRecycleIcon);
            	document.getElementById('recycle').setAttribute('title',oldRecycleTitle);
            	if(dragObject&&dragObject.id) {
                    deletedocument(dragObject.id.split('_')[1]);
            	}
            }
        }
        if(lastTarget && lastTarget.getElementsByTagName('DIV').length>0) {
            lastTarget.getElementsByTagName('DIV')[0].style.cssText='';
        }
        if(lastTarget && lastTarget.getElementsByTagName('A').length > 0) {
            lastTarget.getElementsByTagName('A')[0].style.cssText='';
        }
        if(dragObject) {
            dragObject.getElementsByTagName('DIV')[0].style.cssText='';
        }
        if(dragObject && dropObject && dragObject.id!=dropObject.id) {

                /*
                if(!canEdit) {
                     alert('<?php echo $_lang['access_permission_denied'];?>');
                     dragObject = null;
                     dropObject= null;
                     realDrag = false;
                     drag_active = false;
                     return;
                } */
                //To have the right open/closed folders open we try to modify the tree so the ptm functions
                //stores the right values. NOT PERFECTED YET!

                dragObject.style.visibility='hidden';
                var p = dragObject.parentNode;
                p.classname='closed';
		p.removeChild( dragObject );
		if( p.getElementsByTagName( 'LI' ).length <= 0 )
			p.parentNode.removeChild( p );
                dropObject.style.backgroundColor = '';
                var ul = dropObject.getElementsByTagName( 'UL' )[0];
		if( ! ul ) {
			ul = document.createElement( 'UL' );
                        ul.cssText='display:none';
                        dropObject.appendChild( ul );
                        ul.classname='closed';
                        ul.id='ptm.99999999';
		}
		ul.appendChild( dragObject );
                ptmGetStates();
             //Set new parent and update tree by Ajax. We also show a hour glass cursor by displaying a transparent div over the tree.
             height_of_tree = "10000";
             if(document.getElementById('ul.ptm0').offsetHeight) {
                height_of_tree = document.getElementById('ul.ptm0').offsetHeight + 21;
             }
             else if(document.getElementById('ul.ptm0').style.pixelHeight) {
               height_of_tree = document.getElementById('ul.ptm0').style.pixelHeight + 21;
             }
             document.getElementById('workingmess').style.cursor = "wait";
             document.getElementById('workingmess').style.display = "block"; //inactivate the tree while waiting for response from server by showing a transparant div in front
             document.getElementById('workingmess').style.height = height_of_tree + "px";

             var bufferYscroll = document.body.scrollTop;
             var failNewParent = function(t) { alert('Error ' + t.status + ' -- ' + t.statusText); dropObject.parentNode.removeChild(ul); }
	     new Ajax.Updater({success:document.getElementById('ul.ptm0')},'index.php',{method:'post', parameters:'a=1&f=3&id=' + dragObject.id.split('_')[1] + '&newparent=' + dropObject.id.split('_')[1] + '&scrollTop=' + bufferYscroll, onFailure:failNewParent, evalScripts:true});

        }
        dragObject = null;
        dropObject= null;
        realDrag = false;
        drag_active = false;
        mouseStart = null;
}

function makeDraggable(item){
	if(!item ) return;
	item.onmousedown = function(ev){
                if(!dragObject) {
		   dragObject  = this; //ny
                   dropObject = null;
                   realDrag=false;
                   drag_active = false;
                }
		//mouseOffset = getMouseOffset(this, ev);
		return false;
	}
        if(dragHelper == null) {
           dragHelper = document.createElement('DIV');
	   dragHelper.style.cssText = 'position:absolute;display:none;';
           document.body.appendChild(dragHelper);
        }
        for(var i=0; i<dragHelper.childNodes.length; i++)
           dragHelper.removeChild(dragHelper.childNodes[i]);

	// Make a copy of the current item and put it in our drag helper.
	dragHelper.appendChild(item.getElementsByTagName('div')[0].cloneNode(true));
        dragArr=dragHelper.childNodes;
        for(c=0;dragArr[c];c++) {
            if(dragArr[c].style)
                dragArr[c].style.background =  'none';
        }
        realDrag=false;
        mouseStart = null;
}

// END DRAG AND DROP FOR PARENT ID

var drag_active=false; //if we are dragging something we don't want other arrows to be active
var dragged_doc_id = 0; //global variable holding the dragged document's document id (that later will be sent to the server for permission check)

function dragged(e) { //called when a page is dropped in a new place
   //try to figure out pixel height of the tree or else use a really big default value;
   height_of_tree = "10000";
   if(document.getElementById('ul.ptm0').offsetHeight) {
      height_of_tree = document.getElementById('ul.ptm0').offsetHeight + 21;
   }
   else if(document.getElementById('ul.ptm0').style.pixelHeight) {
      height_of_tree = document.getElementById('ul.ptm0').style.pixelHeight + 21;
   }
   //update the form elements of the hidden form and submit to server
   document.hiddenform.id.value=dragged_doc_id;
   document.hiddenform.listing.value=Sortable.serialize(e,{name:'list'});
   document.getElementById('workingmess').style.display = "block"; //inactivate the tree while waiting for response from server by showing a transparant div in front
   document.getElementById('workingmess').style.height = height_of_tree + "px";
   drag_active=false;
   Sortable.destroy(e); //perhaps better performance if we don't have many sortable lists running simultaniously?
   //update the title attributes so the new menu ids are displayed and change menu id if we are editing a document
   mid_arr = document.hiddenform.listing.value.replace('list[]=','').split('&list[]=');
   if(document.hiddenform.orderby.value == "DESC") {
      mid_arr.reverse();
   }
   var editid=-1;
   if(parent.main.document.mutate && parent.main.document.mutate.a && parent.main.document.mutate.a.value=='5')
       editid=parent.main.document.mutate.id.value;
   document.hiddenform.editid.value="";
   for( var y = 0; y < mid_arr.length; y++ ) {
      curtitle = document.getElementById("title_"+mid_arr[y]).getAttribute('title');
      curtitle = curtitle.substr(0,curtitle.search("Menu index: ")) + "Menu index: " + (y+1);
      document.getElementById("title_"+mid_arr[y]).setAttribute('title',curtitle);
      if(mid_arr[y]==editid)
          document.hiddenform.editid.value=y+1;
   }
   document.hiddenform.submit();
}


function init_drag(id_of_parent, set_doc_id) {                                      //called when mouse is over arrow handles
   if(!drag_active) {                                                               //Make sure we are not already dragging
      Sortable.create(id_of_parent,{handle:'handle',scroll:window,onUpdate:dragged}); //make parent node sortable
      dragged_doc_id = set_doc_id;
   }
}

//START ADD LINK INTEGRATION, SAVE POSITION OF SELECTED TEXT IN EDITORS TO GLOBAL AND linkToEditor() creates the links
//We need to perform several checks to make sure the editors is properly loaded, or we would get javascript errors...

function bufferEditorSelection() {
  content=null;
  curtext=null;
  savedSelection = null;
  if(!ca || ca!='parent')
     return;

  if(parent.main.FCKeditorAPI) {
           var oEditor = parent.main.FCKeditorAPI.GetInstance('ta') ;
           if(oEditor.EditorDocument && oEditor.EditorDocument.selection) {
                content = oEditor.EditorDocument.selection;
                if(content)
                     curtext=content.createRange().text;
                if((curtext && curtext!="") || (content.createRange().item && content.createRange().item(0)) )
                     savedSelection = content.createRange();
           }
           else {
                if(oEditor.EditorWindow && oEditor.EditorWindow.getSelection) {
                    content = oEditor.EditorWindow.getSelection();
                    if(content && content.toString() != "")
                         savedSelection = "Selection maintained";
                }
           }
           return;
   }
   if(parent.main.xinha_editors && parent.main.xinha_editors['ta'] && parent.main.xinha_editors['ta']._getSelection && (parent.main.xinha_editors['ta']._doc || parent.main.xinha_editors['ta']._iframe) ) {
           content = parent.main.xinha_editors['ta']._getSelection();
           if(!(parent.main.xinha_editors['ta']._selectionEmpty(content)))
                 savedSelection=parent.main.xinha_editors['ta']._createRange(content);
           return;
   }
   if(parent.main.tinyMCE && parent.main.tinyMCE.getInstanceById) {
           if(parent.main.tinyMCE.getInstanceById('ta') && parent.main.tinyMCE.getInstanceById('ta').getSelectedHTML!="")
                 savedSelection=parent.main.tinyMCE.getInstanceById('ta').selection.getBookmark(true);
           return;
   }
   if(parent.main.document.getElementById('ta') && parent.main.document.getElementById('ta').contentWindow) {
           var oEditor = parent.main.document.getElementById('ta').contentWindow;
           if(oEditor.document.selection) {
                 content = oEditor.document.selection;
                 if(content)
                      curtext=content.createRange().text;
                 if((curtext && curtext!="") || (content.createRange().item && content.createRange().item(0)) )
                      savedSelection = content.createRange();
           }
           else {
                 if(oEditor.getSelection) {
                     content = oEditor.getSelection();
                     if(content && content.toString() != "")
                          savedSelection = "Selection maintained";
                 }
           }
           return;
   }
}

function linkToEditor(id) {
    if(parent.main.FCKeditorAPI) {
        if(savedSelection=="Selection maintained") {
            var oEditor = parent.main.FCKeditorAPI.GetInstance('ta') ;
            oEditor.CreateLink('[~' + id + '~]');
            return true;
        }
        if(savedSelection) {
            var oEditor = parent.main.FCKeditorAPI.GetInstance('ta') ;
            savedSelection.select();
            oEditor.CreateLink('[~' + id + '~]');
            return true;
        }
    }
    if(parent.main.xinha_editors && parent.main.xinha_editors['ta']) {
           if(savedSelection) {
                       if(savedSelection.select) {
                           savedSelection.select();
                           if(!savedSelection.item) {
                                parent.main.xinha_editors['ta'].surroundHTML("<a href=\"[~" + id + "~]\">" , "</a>");
                           }
                           else { //Selection is an object and surroundHTML will fail in IE, use direct dom manipulation instead
                                thelink = parent.main.xinha_editors['ta']._doc.createElement("a");
                                thelink.href = "[~" + id + "~]";
                                thelink.appendChild(savedSelection.item(0).cloneNode(true));
                                savedSelection.item(0).parentNode.replaceChild(thelink,savedSelection.item(0));
                           }
                       }
                       else {
                           parent.main.xinha_editors['ta'].surroundHTML("<a href=\"[~" + id + "~]\">" , "</a>");
                       }
                       return true;
           }
   }
   if(parent.main.tinyMCE) {
         if(savedSelection) {
               if(!savedSelection.item) {
                    parent.main.tinyMCE.getInstanceById('ta').selection.moveToBookmark(savedSelection);
                    tmp = parent.main.tinyMCE.getInstanceById('ta').selection.getSelectedHTML();
                    if(tmp!="") {
	                 parent.main.tinyMCE.getInstanceById('ta').execCommand("createlink", false, "[~" + id + "~]");
                         return true;
                    }
                }
                else { //Selection is an object and built in functions will fail in IE, use direct dom manipulation instead
                    for(thelink=savedSelection.item(0); thelink && thelink.parentNode; thelink=thelink.parentNode) {}
                    thelink = thelink.createElement("a");
                    thelink.href = "[~" + id + "~]";
                    parent.main.tinyMCE.getInstanceById('ta').execCommand('mceAddUndoLevel');
                    thelink.appendChild(savedSelection.item(0).cloneNode(true));
                    savedSelection.item(0).parentNode.replaceChild(thelink,savedSelection.item(0));
                    parent.main.tinyMCE.getInstanceById('ta').execCommand('mceAddUndoLevel');
                    return true;
                }
         }
   }
   if(parent.main.document.getElementById('ta') && parent.main.document.getElementById('ta').contentWindow) {
        if(savedSelection=="Selection maintained" && parent.main.setRange && parent.main.insertHTML) {
            parent.main.document.getElementById('ta').contentWindow.document.execCommand("createlink", false, "[~" + id + "~]"); 
            return true;
        }
        if(savedSelection && savedSelection.select) {
            if(!savedSelection.item){
               savedSelection.select();
               savedSelection.execCommand("createlink", false, "[~" + id + "~]");
            }
            else {
               thelink = parent.main.document.getElementById('ta').contentWindow.document.createElement("a");
               thelink.href = "[~" + id + "~]";
               thelink.appendChild(savedSelection.item(0).cloneNode(true));
               savedSelection.item(0).parentNode.replaceChild(thelink,savedSelection.item(0));
            }
            return true;
        }
    }
   return false;
}
