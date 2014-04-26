function deletePlayer(theWrapper, thePlaceholder, thePlayerId) {
	swfobject.removeSWF(thePlayerId);
	var tmp=document.getElementById(theWrapper);
	if (tmp) { tmp.innerHTML = "<div id='" + thePlaceholder + "'></div>"; }
}

function createPlayer(id, page, theFile, width, height, theSkin, theAutostart) {
		var theAutostart = typeof(theAutostart) != 'undefined' ? theAutostart : false;
		
		var theSetup = {
			'flashplayer': mod_youtubeplaylist_player,
			'skin': theSkin,
			'autostart': theAutostart,
			'width': width,
			'height': height,
			'overstretch':'auto',
			'repeat': 'list',
			'quality':'true',
			'controlbar' : 'over',
			'playlist.size': theOptions[id+'playlist_size'],
			'playlist.position': theOptions[id+'playlist_position']			
			};
			if(theOptions[id+'hdon'] == 'true') { 
				theSetup.plugins = {'hd-1': {}};
				}
			if(theOptions[id+'playlist_position'] == 'over') { 
				theSetup.playlist = 'over';
				}
			if(page!='video') { 
				theSetup.playlistfile = theFile;
				} else theSetup.file = theFile;
				
			
	if( page != 'video' && theOptions[id+'playlist_pos'] != 'undefined' && 0) { 
			//theSetup.controlbar = 'over';
		}

    //http://www.longtailvideo.com/support/jw-player/jw-player-for-flash-v5/12540/javascript-api-reference
	jwplayer(id+'-placeholder').setup(theSetup);
	}

function Playingnext(id){
	state = jwplayer(id+'-placeholder').getState();
	if(state == 'BUFFERING' || state == 'PLAYING') theOptions[id+'theAutostart'] = true;
	else theOptions[id+'theAutostart'] = false;
	initPlayer(id, theOptions[id+'page'], theOptions[id+'theSearch'], theOptions[id+'width'], theOptions[id+'height'], theOptions[id+'theSkin'], theOptions[id+'theAutostart'], 'next');
}
function Playingpreviuos(id) {		
	state = jwplayer(id+'-placeholder').getState();
	if(state == 'BUFFERING' || state == 'PLAYING') theOptions[id+'theAutostart'] = true;
	else theOptions[id+'theAutostart'] = false;
	initPlayer(id, theOptions[id+'page'], theOptions[id+'theSearch'], theOptions[id+'width'], theOptions[id+'height'], theOptions[id+'theSkin'], theOptions[id+'theAutostart'], 'previous');
}
function initPlayer(id, page, theSearch, width, height, theSkin, theAutostart, ypage) {
		if(page == 'combination') { //ACtion for Droplist ONLY
			var psearch = theSearch.split('|');
			page = psearch[1];
			theSearch = psearch[0];
			alert(page + '  '+ theSearch);
		}
		var yindex = 1;
		var yresult = 50;
		theOptions['id'] = id;
		//theOptions[id+'yresult'];
		//theOptions[id+'ypage'];
		theOptions[id+'page'] = page;
		theOptions[id+'theSearch'] = theSearch;
		theOptions[id+'width'] = width;
		theOptions[id+'height'] = height;
		theOptions[id+'theSkin'] = theSkin;
		theOptions[id+'theAutostart'] = theAutostart;
		
		switch (ypage) {
			case "next":		
				yresult = theOptions[id+'yresult'];				
				ypage = theOptions[id+'ypage'];
				ypage++;
				theOptions[id+'ypage'] = ypage;
				yindex = (ypage - 1)*yresult + 1;
				//alert('Ypage: '+ ypage+'  '+yresult + '  ' + yindex);
			break;
			case "previous":		
				yresult = theOptions[id+'yresult'];				
				ypage = 1;
				if(theOptions[id+'ypage'] > 1) ypage = theOptions[id+'ypage'] = theOptions[id+'ypage'] - 1;
				yindex = (ypage - 1)*yresult + 1;
			break;			
			default:
				theOptions[id+'ypage'] = 1;
				yresult = theOptions[id+'yresult'];
				yindex = 1;			
		}

		if(theSearch == '...') return;
		switch (page) {
			case "video":
			theFile = "http://www.youtube.com/v/" + theSearch;
			break
			case "playlist":
			theFile = "http://gdata.youtube.com/feeds/api/playlists/"+ theSearch +"?start-index="+ yindex + "&max-results="+yresult+"&v=2";
			//showBoxDiv(id, theSearch);
			break
			case "user":
			showBoxDiv(id, theSearch);
			theFile = "http://gdata.youtube.com/feeds/api/users/"+ theSearch +"/uploads?start-index="+ yindex + "&max-results="+yresult+"&v=2";
			break
			default:
			theFile = page + theSearch;
		}
		
		if(theSkin !='0') theSkin = mod_youtubeplaylist_skin + theSkin;
		else theSkin = '';		

	currentFile = theFile;
	currentSkin = theSkin;
	deletePlayer(id+'-wrapper', id+'-placeholder', id+'-player');
	createPlayer(id, page, currentFile, width, height, currentSkin, theAutostart, theOptions);
}

function showBoxDiv(id, theBlock) {
theOldBlock = theCurrentPlayingId[id].toLowerCase();
theCurrentPlayingId[id]	 = theBlock;
theBlock = theBlock.toLowerCase();
//alert('Old:'+theOldBlock+'  Curr:'+theBlock);
if(document.getElementById(id+"-"+theBlock)!=null){
	if(document.getElementById(id+"-"+theBlock).style.display=="none") {
			document.getElementById(id+"-"+theBlock).style.display="block";
		}
	}	
if((theBlock != theOldBlock) && document.getElementById(id+"-"+theOldBlock)!=null){
	if(document.getElementById(id+"-"+theOldBlock).style.display=="block") {
		document.getElementById(id+"-"+theOldBlock).style.display="none";
	   }
	}

}

function theResults (form) {
		initPlayer(currentUrl, form.inputbox.value, currentSkin, false);
}
	

	/* function submitenter(myfield,e)	{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;

	if (keycode == 13)
   {
	theResults(myform);
	   return false;
   }
	else
	   return true;

	} */
	

function getUrl() {
	var ln = player1.getConfig().item;
	var lnk = (player1.getPlaylist()[ln].file);
	alert("Current item URL: <br />" + lnk + "<br />");
}
	

function showSearch() {
	myform.inputbox = currentSearch;
}

// ----- ajax functions --------------  
function getHTTPObject() { 
  var xhr = false; 
  if (window.XMLHttpRequest) { 
    xhr = new XMLHttpRequest(); 
  } else if (window.ActiveXObject) { 
    try { 
      xhr = new ActiveXObject("Msxml2.XMLHTTP"); 
    } catch(e) { 
      try { 
        xhr = new ActiveXObject("Microsoft.XMLHTTP"); 
      } catch(e) { 
        xhr = false; 
      } 
    } 
  } 
  return xhr; 
}
function grabFile(id, file) {
	ajaxcontentId = id;
	var theFile = paramsfilefolder + file;
  var request = getHTTPObject(); 
  if (request) { 
    request.onreadystatechange = function() { 
      parseResponse(request); 
    }; 
    request.open("GET", theFile, true); 
    request.send(null); 
  } 
} 
 
function parseResponse(request) { 
  if (request.readyState == 4) { 
    if (request.status == 200 || request.status == 304) {       
        loadExternalContent(request.responseText);         
    } 
  } 
} 
 
function loadExternalContent(markup){ 
    document.getElementById(ajaxcontentId).innerHTML = markup; 
}