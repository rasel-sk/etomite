/* ptm.js - Persistent Tree Menu */
/* Author: Ralph A. Dahlgren  */
/* Rev. Date: 2005-06-16 : Initial Code Release */
/* Rev. Date: 2005-09-30 : Modified code to allow for handling multiple folder types */
/* Rev. Date: 2006-10-06 : Modified by Nalagar to support urls containing the words "open" and "closed" */

var uls,i,pNodes;
var imgPath = "media/images/tree/";
var sessionName = "ptmRC3";

function ptmToggle(id)
{
  ul = "ul." + id;
  img = "img." + id;
  ulElement = document.getElementById(ul);
  imgElement = document.getElementById(img);
  if(ulElement)
  {
    if(ulElement.className == 'closed')
    {
      ulElement.className = "open";
      imgElement.src = ptmToggleImage(imgElement.src,ulElement.className);
    }
    else if(ulElement.className == 'open')
    {
      ulElement.className = "closed";
      imgElement.src = ptmToggleImage(imgElement.src,ulElement.className);
    }
  }
  ptmGetStates();
}

function ptmCollapse()
{
  uls=document.getElementsByTagName('ul');
  for (i=0;i<uls.length;i++)
  {
    img = uls[i].id.replace(/ul./,'img.');
    ulElement = document.getElementById(uls[i].id);
    imgElement = document.getElementById(img);
    if(ulElement)
    {
      if((ulElement.className == 'open') || (ulElement.className == 'closed'))
      {
        ulElement.className = "closed";
        imgElement.src = ptmToggleImage(imgElement.src,ulElement.className);
      }
    }
  }
  ptmGetStates();
}

function ptmExpand()
{
  uls=document.getElementsByTagName('ul');
  for (i=0;i<uls.length;i++)
  {
    img = uls[i].id.replace(/ul./,'img.');
    ulElement = document.getElementById(uls[i].id);
    imgElement = document.getElementById(img);
    if(ulElement)
    {
      if((ulElement.className == 'closed') || (ulElement.className == 'open'))
      {
        ulElement.className = "open";
        imgElement.src = ptmToggleImage(imgElement.src,ulElement.className);
      }
    }
  }
  ptmGetStates();
}

function ptmCreateCookie(sessionName,value,days)
{
  if (days)
  {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else var expires = "";
  document.cookie = sessionName+"="+value+expires+"; path=/";
}

function ptmReadCookie(sessionName)
{
  var nameEQ = sessionName + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++)
  {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

function ptmEraseCookie(sessionName)
{
  ptmCreateCookie(sessionName,"",-1);
}

function ptmGetStates()
{
  var s="";
  uls=document.getElementsByTagName('ul');
  for (i=0;i<uls.length;i++)
  {
    ulElement = document.getElementById(uls[i].id);
    if(ulElement)
    {
      if(ulElement.className == 'closed')
      {
        s=s+"0";
      }
      else if(ulElement.className == 'open')
      {
        s=s+"1"
      }
    }
  }
  ptmCreateCookie(sessionName,s,0);
}

function ptmResetStates()
{
  var s="";
  result=ptmReadCookie(sessionName);
  if(!result) return;
  nodes=result.split('');
  uls=document.getElementsByTagName('ul');
  var c=0;
  var i=0;
  while (i<uls.length)
  {
    ulElement = document.getElementById(uls[i].id);
    if(ulElement.className == "closed" || ulElement.className == "open")
    {
      img = uls[i].id.replace(/ul./,'img.');
      imgElement = document.getElementById(img);
      if(nodes[c] == "1")
      {
        ulElement.className = "open";
        imgElement.src = ptmToggleImage(imgElement.src,ulElement.className);
      }
      else if(nodes[c] == "0")
      {
        ulElement.className = "closed";
        imgElement.src = ptmToggleImage(imgElement.src,ulElement.className);
      }
      c++;
    }
    i++;
  }
}

function ptmToggleImage(img,state)
{
  pth='';
  if(img.lastIndexOf('/')>0) {
    pth=img.substring(0,img.lastIndexOf('/'));
    img=img.substring(img.lastIndexOf('/'));
  }
  if(state == 'closed')
  {
    if(img.split('open').length > 1)
    {
      img = img.split('open')[0] + '.gif';
    }
  }
  if(state == 'open')
  {
    if(img.split('open').length == 1)
    {
      img = img.split('.gif')[0] + 'open.gif';
    }
  }
  return pth + img;
}
