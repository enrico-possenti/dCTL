/**
 +----------------------------------------------------------------------+
 | A digital tale (C) 2009 Enrico Possenti :: dCTL                      |
 +----------------------------------------------------------------------+
 | Author:  NoveOPiu di Enrico Possenti <info@noveopiu.com>             |
 | License: Creative Commons License v3.0 (Attr-NonComm-ShareAlike      |
 |          http://creativecommons.org/licenses/by-nc-sa/3.0/           |
 +----------------------------------------------------------------------+
 | A js file for "dCTL"                                                 |
 +----------------------------------------------------------------------+
*/

window.onload = externalLinks();
window.onload = hideUnvisible;

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* GLOBAL VARS */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
         //scroller height
        var sheight=36;
        var sshift=0.85;
        var stime=100;

        //scroller's speed
        var sspeed=1;
        var resumesspeed=sspeed;

        var ns6div;
        var sizeup;

         // Begin the ticker code

        function NewsTicker_start() {

									if (document.all) {
										iemarquee(ticker);
									} else {
										if (document.getElementById) {
											ns6marquee(document.getElementById('ticker'));
										};
									};
        }

        function iemarquee(whichdiv){
                var iediv = eval(whichdiv);
                iediv.style.pixelTop = sheight;
                iediv.innerHTML = news_text;
                var sizeup = iediv.offsetHeight;
                ieslide();
        }

        function ieslide(){
                if (iediv.style.pixelTop>=sizeup*(-sshift)) {
                        iediv.style.pixelTop -= sspeed;
                        setTimeout("ieslide()",stime);
                }
                else{
                        iediv.style.pixelTop=sheight;
                        ieslide();
                }
        }

        function ns6marquee(whichdiv){
	ns6div=eval(whichdiv);
	ns6div.style.top=sheight + "px";
	ns6div.innerHTML=news_text;
	sizeup=ns6div.offsetHeight;
	ns6slide();
        }

        function ns6slide(){

if (parseInt(ns6div.style.top)>=sizeup*(-sshift)) {
                        var theTop = parseInt(ns6div.style.top) - sspeed;
                        ns6div.style.top = theTop + "px";
                        setTimeout("ns6slide()",stime);
                }
                else
 {
                        ns6div.style.top = sheight + "px";
                        ns6slide();
                }
        }



/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
var rememberObj = new Array();
var rememberObjClass = new Array();
rememberObj[1] = new Object();
rememberObjClass[1] = '';
rememberObj[2] = new Object();
rememberObjClass[2] = '';
rememberObj[3] = new Object();
rememberObjClass[3] = '';
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
var secs;
var timerID = null;
var timerRunning = false;
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
var NS = (navigator.appName.indexOf("Explorer") == -1)?true:false;
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/* METHODS */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/******************************************
* Ajax load XML file script -- By Eddie Traversa (http://dhtmlnirvana.com/)
* Script featured on Dynamic Drive (http://www.dynamicdrive.com/)
* Keep this notice intact for use
******************************************/
// USE: javascript:indexAjax(targetId, url[])

function indexAjax(action, id, coll, pack) {
 doProgress();
	var my_arguments = new Array();
	var url = "indexAjax.php?action=" + action + "&id=" + id + "&collection_id=" + coll + "&package_id=" + pack;
	var param;
	for (param=4; param<arguments.length; param++) {
	 if (arguments[param] != '') {
			my_arguments[param] = '&'+arguments[param];
			url = url + my_arguments[param];
		};
	};
	if (document.getElementById) {
		var x = (window.ActiveXObject) ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
	};
	if (x) {
		x.onreadystatechange = function() {
// 		  readyState
// 		  * 0 = uninitialized
//     * 1 = loading
//     * 2 = loaded
//     * 3 = interactive
//     * 4 = complete
			if (x.readyState == 4 && x.status == 200) {
				el = document.getElementById(id);
				el.innerHTML = x.responseText;

				if (id == 'side_d_explorer') {
				 var idx = 'dx_' + basename(my_arguments[4]); // url=
					window.location.hash = '#' + idx;
     document.getElementById("dctl_explorer_document2").scrollTop -= 50;
     entag(3, document.getElementById(idx), true);
				};

			if (id == 'side_c_explorer') {
				var posx = basename(my_arguments[4].replace(/.*=/, '/')); // url=
				var page = dirname(my_arguments[4].replace(/.*=/, '')); // url=
				window.location.hash = '#' + posx;
				document.getElementById("dctl_explorer_document1").scrollTop -= 50;
				if (posx != page) entag(2, document.getElementById(posx), true);
			};
/*
if (action == 'ajax_loadLinks') {
 $('#'+id+' .simpleTree').simpleTree({
   basePath: '../img/',
		autoclose: true,
		animate: true, drag:		false
   });
   
			};
*/

killProgress();
			};
		};
		x.open("GET", url, true);
		x.send(null);
 };
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function basename (path) { return path.replace( /.*\//, "" ); }
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function dirname (path) { return path.replace( "/"+basename (path), "" ); }
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function zoomImage(elementId, zoomer) {
 var obj = document.getElementById(elementId);
 var sizeH = obj.height;
 var sizeW = obj.width;
 if ((zoomer == "+") & (sizeW < 3600) & (sizeH < 3600)) {
		obj.height = sizeH*4/3;
		obj.width = sizeW*4/3;
 };
 if ((zoomer == "-") & (sizeW > 200) & (sizeH > 200)) {
		obj.height = sizeH*3/4;
		obj.width = sizeW*3/4;
 };
 if (zoomer == "0") {
  obj.location = obj.src;
  var maxH = document.getElementById("dctl_explorer_document2").clientHeight-10;
  var maxW = document.getElementById("dctl_explorer_document2").clientWidth-10;
  if(obj.height > maxH) {
   obj.width = maxH * obj.width / obj.height;
   obj.height = maxH;
  };
  if(obj.width > maxW) {
   obj.height = maxW * obj.height / obj.width;
   obj.width = maxW;
  };
 };
};

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function getBreadCrumb() {
	var path = "";
	var href = document.location.href;
	var s = href.split("/");
	for (var i=2;i<(s.length-1);i++) {
	path+="<a href=\""+href.substring(0,href.indexOf("/"+s[i])+s[i].length+1)+"/\">"+s[i]+"</a>/ ";
	}
	i=s.length-1;
	path+="<a href=\""+href.substring(0,href.indexOf(s[i])+s[i].length)+"\">"+s[i]+"</a>";
	var url = window.location.protocol + "//" + path;
	document.writeln(url);
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function InitializeTimer() {
    // Set the length of the timer, in seconds
    secs = 5;
    StopTheClock();
    StartTheTimer();
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function StopTheClock() {
    if(timerRunning)
        clearTimeout(timerID);
    timerRunning = false;
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function StartTheTimer() {
    if (secs==0) {
        StopTheClock();
        // Here's where you put something useful that's
        // supposed to happen after the allotted time.
        // For example, you could display a message:
        document.getElementById('dctl_explorer_linker1').innerHTML = '';
    } else {
        secs = secs - 1;
        timerRunning = true;
        timerID = self.setTimeout("StartTheTimer()", 1000);
    }
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function startTimer() {
 InitializeTimer();
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function delay(millis) {
 var date = new Date();
 var curDate = null;
 do {
  curDate = new Date();
 } while(curDate-date < millis);
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function expandTag(theTag0) {
 theTag1 = theTag0.replace('-0', '-1');
 theTag2 = theTag0.replace('-0', '-2');
 theExpand = document.getElementById(theTag1).className == 'tei_tag_hide';
 if (theExpand) {
  document.getElementById(theTag0).className = 'dctl_entag';
  document.getElementById(theTag1).className = 'tei_tag';
  document.getElementById(theTag2).className = 'tei_tag';
 } else {
  document.getElementById(theTag1).className = 'tei_tag_hide';
  document.getElementById(theTag2).className = 'tei_tag_hide';
 };
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function recordHistory() {
 curr_URL = document.getElementsByTagName("meta")['archive:URL'].content;
 prev_URL = document.getElementsByTagName("meta")['archive:HISTORY'].content;
 theCookie = "?" + curr_URL;
 if (prev_URL) theCookie += prev_URL;
 if (document.cookie) {
  setCookie ("dCTL", theCookie);
 }
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function entag (theSelector, theObject, theStatus) {
 if (theObject) {
 if(rememberObj[theSelector] != theObject) {
  rememberObj[theSelector].className = rememberObjClass[theSelector];
  rememberObjClass[theSelector] = theObject.className;
 if (theStatus) {
   if (theSelector == 1) {
				theObject.className = "current";
   } else {
				theObject.className = "dctl_entag";
			};
   rememberObj[theSelector].className = rememberObjClass[theSelector];
  } else {
   theObject.className = "";
  };
  rememberObj[theSelector] = theObject;
 };
 };
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function fixDate(date) {
  var base = new Date(0);
  var skew = base.getTime();
  if (skew > 0)
    date.setTime(date.getTime() - skew);
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setCookie(name, value, expires, path, domain, secure) {
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/**
 * Gets the value of the specified cookie.
 *
 * name  Name of the desired cookie.
 *
 * Returns a string containing value of specified cookie,
 *   or null if cookie does not exist.
 */
function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    } else {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
/**
 * Deletes the specified cookie.
 *
 * name      name of the cookie
 * [path]    path of the cookie (must be same as path used to create cookie)
 * [domain]  domain of the cookie (must be same as domain used to create cookie)
 */
function deleteCookie(name, path, domain) {
    if (getCookie(name)) {
        document.cookie = name + "=" +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function externalLinks() {
 var i, obj;
 for (i=0; (obj = document.getElementsByTagName("a")[i]); i++) {
  switch(obj.getAttribute("class")) {
   case 'external':
   case 'link_ext':
   case 'link_pop':
   case 'link_dload':
    obj.target = '_new';
   break;
  };
 };
 for (i=0; (obj = document.getElementsByTagName("form")[i]); i++) {
  switch(obj.getAttribute("class")) {
   case 'external':
   case 'link_ext':
   case 'link_pop':
   case 'link_dload':
    obj.target = '_new';
   break;
  };
 };
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function toggleTab (theCaller, theTabbed, theTabber, theCurrent) {
 var theL = document.getElementById(theTabber).childNodes;
 var theT = document.getElementById(theTabbed).childNodes;
 var theT2 = new Array();
 var theL2 = new Array();
 theCurrent--;
 var i = 0;
 for(var j=0; j<theT.length; j++) {
  var theLabel = theL[j];
  var theObject = theT[j];
		if (theObject) {
		 if (theObject.style) {
		  if (theObject.nodeName == "SPAN")  {
	 	  theT2[i] = theObject;
	 	  theL2[i] = theLabel;
	 	  theObject.style.display = 'none';
	 	  i++;
				};
   };
	 };
		if (theLabel) {
			if (theLabel.className) {
				theLabel.className = theLabel.className.replace(' current', '');
			};
		};
 };

 toggleVisibility(theCaller, theT2[theCurrent]);

	var theLabel = theL2[theCurrent];
	if (theLabel) {
		theLabel.className += ' current';
	};

}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function toggleVisibility(theCaller) {
 for (var i=1; i<arguments.length; i++) {
		if (arguments[i].style) {
		 var theObject = arguments[i];
		} else {
				var theObject = document.getElementById(arguments[i]);
		};
		if (theObject) {
		 if (theObject.style) {
		  theObject.style.display = (theObject.style.display != 'none' ? 'none' : '' );
		  var theS = theCaller.childNodes[0].src; // img
		  if (theObject.style.display == 'none') {
		   theS = theS.replace('collapse', 'expand');
		  } else {
		   theS = theS.replace('expand', 'collapse');
		  };
		  theCaller.childNodes[0].src = theS;
   };
	 };
	};
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function hideUnvisible() {
 var i, obj;
 for (i=0; (obj = document.getElementsByTagName('table')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
 for (i=0; (obj = document.getElementsByTagName('tr')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
 for (i=0; (obj = document.getElementsByTagName('ul')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
 for (i=0; (obj = document.getElementsByTagName('span')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
 for (i=0; (obj = document.getElementsByTagName('div')[i]); i++) {
	 if (obj.className.indexOf('_hidden') != -1) {
   obj.style.display = 'none';
  };
 };
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
// window.onload = hideUnvisible;
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function doProgress() {
	var obj = document.getElementById("progress");
	obj.style.height = "1.0em";
	obj.style.visibility = 'visible';
	return true;
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function killProgress() {
	var obj = document.getElementById("progress");
 obj.style.visibility = 'hidden';
	return true;
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function highlight (theName, status) {
  if (status) {
   document.getElementById(theName).className = "highlight";
  } else {
   document.getElementById(theName).className = "small";
  };
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function submitform (theForm) {
  document.getElementById(theForm).submit();
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function addContent (theObj, theText) {
 var theValue = document.getElementById(theObj).value;
 if (theValue.indexOf(theText) == -1) {
		document.getElementById(theObj).value = theValue+' '+theText;
	};
};
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function replace(string,text,by) {
// Replaces text with by in string
    var strLength = string.length, txtLength = text.length;
    if ((strLength == 0) || (txtLength == 0)) return string;
    var i = string.indexOf(text);
    if ((!i) && (text != string.substring(0,txtLength))) return string;
    if (i == -1) return string;
    var newstr = string.substring(0,i) + by;
    if (i+txtLength < strLength)
        newstr += replace(string.substring(i+txtLength,strLength),text,by);
    return newstr;
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function fitPic() {
	iWidth = (NS)?window.innerWidth:document.body.clientWidth;
	iHeight = (NS)?window.innerHeight:document.body.clientHeight;
	iWidth = document.images[0].width - iWidth;
	iHeight = document.images[0].height - iHeight;
	window.resizeBy(iWidth, iHeight);
	self.focus();
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
function openPic() {
 max = arguments.length;
 shiftw = 0;
 for (i=0; i<max; i++) {
		aPic = arguments[i];
		aName = arguments[i+1];
		i++;
		aName = replace(aName,' ','_');
		aName = replace(aName,'-','_');
		aName = replace(aName,"'",'_');
		aName = replace(aName,'"','_');
		// if (NS)
		 // theWin = // window.open('',aName,'width=600,height=600'+',top='+(parseInt(screen.availTop)+shiftw)+',left='+(parseInt(screen.availLeft)+shiftw))
		// else
		 theWin = window.open('',aName,'width=600,height=600'+',top='+(shiftw)+',left='+(shiftw));
		theWin.document.writeln('<html>');
		theWin.document.writeln('<head>');
		theWin.document.writeln('<title>'+aName+'</title>');
		theWin.document.writeln('</head>');
		theWin.document.writeln('<body style="background:url('+aPic+') no-repeat">');
		theWin.document.writeln('<p/>');
		theWin.document.writeln('</body></html>');
		shiftw = shiftw+20;
	}
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */


/***********************************************
***********************************************/
