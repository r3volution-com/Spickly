function setCookie(c_name,value,exdays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}
function getCookie(c_name) {
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++){
		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		x=x.replace(/^\s+|\s+$/g,"");
		if (x==c_name)  return unescape(y);
	}
}
function deleteCookie(c_name) {
	document.cookie = c_name +'=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
}
function checkCookie(cookie){
	var username=getCookie(cookie);
	if (username!=null && username!="") return 1;
	else return 0;
}
function sendAjax(s_url) {
	var resp = "";
	$.ajax({
		url : s_url,
		type : 'GET',
        async: false,
		success : function(res){
			resp = res;
		}				
	});
	return resp;
}
function changeStyle(element){
	var new_style = $(element).attr('class');
	var selected = $(element).parent().siblings('.selected').get(0);
	selected = $(selected);
	var old_style = selected.find('a').attr('class');
	selected.removeClass('selected');
	$(element).parent().addClass('selected');
	$('body').removeClass(old_style).addClass(new_style);
}
function ver(image){
document.getElementById('image').innerHTML = "<img src='"+image+"'>" 
}

function getSuggest(suggest, placelist) { 
	$.ajax({ 
		type: "GET", 
		url: "in_actions.php?a=searchfriend&term="+suggest, 
		contentType: "application/json; charset=utf-8", 
		dataType: "json", 
		success: function (response) { 
			document.getElementById(placelist).innerHTML="";
			var p = eval( response );
			for (i=0; p[i]; i++) document.getElementById(placelist).innerHTML+="<li><a href='in.php?p=profile&id="+p[i].id+"'>"+p[i].value+"</a></li>";
		}
	}); 
}
function getSuggestToInput(suggest, placelist, placename, placeid) { 
	$.ajax({ 
		type: "GET", 
		url: "in_actions.php?a=searchfriend&term="+suggest, 
		contentType: "application/json; charset=utf-8", 
		dataType: "json", 
		success: function (response) { 
			document.getElementById(placelist).innerHTML="";
			var p = eval(response);
			for (i=0; p[i]; i++) document.getElementById(placelist).innerHTML+="<li><a href='#' onclick='putSuggestToInput(\""+placelist+"\",\""+placename+"\", \""+placeid+"\", \""+p[i].value+"\",\""+p[i].id+"\")'>"+p[i].value+"</a></li>";
		}
	});
}
function putSuggestToInput(placelist, placename, placeid, name, id) {
	document.getElementById(placelist).innerHTML = "";
	document.getElementById(placename).value = name;
	document.getElementById(placeid).value = id;
}
function changeStatus(type) {
	$.ajax({ 
		type: "GET", 
		url: "out_actions.php?a=status&t="+type, 
		async: false,
		success: function (response) { 
			if (response != "ok") alert("Error al cambiar el estado");
		}
	}); 
}

function array2json(arr) {
    var parts = [];
    var is_list = (Object.prototype.toString.apply(arr) === '[object Array]');

    for(var key in arr) {
    	var value = arr[key];
        if(typeof value == "object") { //Custom handling for arrays
            if(is_list) parts.push(array2json(value)); /* :RECURSION: */
            else parts[key] = array2json(value); /* :RECURSION: */
        } else {
            var str = "";
            if(!is_list) str = '"' + key + '":';

            //Custom handling for multiple data types
            if(typeof value == "number") str += value; //Numbers
            else if(value === false) str += 'false'; //The booleans
            else if(value === true) str += 'true';
            else str += '"' + value + '"'; //All other things
            // :TODO: Is there any more datatype we should be in the lookout for? (Functions?)

            parts.push(str);
        }
    }
    var json = parts.join(",");
    
    if(is_list) return '[' + json + ']';//Return numerical JSON
    return '{' + json + '}';//Return associative JSON
}

/*function deltag(id){
	imagen = document.getElementById(id);	
	if (!imagen){
		alert("El elemento selecionado no existe");
	} else {
		padre = imagen.parentNode;
		padre.removeChild(imagen);
	}
}*/
function deltag(id) {
    return (elem=document.getElementById(id)).parentNode.removeChild(elem);
}

function cwindows(func_after){
	var newdiv = document.createElement('div');
	newdiv.setAttribute('id',"notice");
	newdiv.setAttribute('class',"notice");
	document.getElementById('page').appendChild(newdiv);														
	document.getElementById('notice').innerHTML="<h2>Aviso</h2> <p>¿Seguro que desea hacerlo?</p> <input type=\"button\" class=\"boton\" value=\"No\" onclick=\"deltag('notice')\"/> <input type=\"button\" class=\"boton\" value=\"Si\" onclick=\""+func_after+";deltag('notice')\" />";
}


function comment(id, type) {	
	var newdiv = document.createElement('div');
	var br = document.createElement('br');

	var fecha= new Date()
	var s = fecha.getSeconds();
	var m = fecha.getMinutes();
	var h =	fecha.getHours();
	var d = fecha.getDate();
	var mo = fecha.getMonth();
	var y = fecha.getFullYear();
	var datefull = s+":"+m+":"+h+" "+d+"-"+mo+"-"+y;
	
	var divid = 'new_comment_'+s+'_'+m;
	newdiv.setAttribute('id',divid);
	document.getElementById('new_comment_'+id+'').appendChild(newdiv);	

	var text = sendAjax('in_actions.php?a=comment&t='+encodeURIComponent(document.getElementById('spicktext_'+id).value)+'&f='+id+'&type='+type);
	if (type == 3) {
		newdiv.setAttribute('class',"commentspacer");
		document.getElementById('new_comment_'+id+'').appendChild(br);	
		newdiv.innerHTML="<div id=\"allcomment\"><div id=\"eliminar\" onclick=\"cwindows(deleteSpacerComment());\">X</div> <img src=\""+my_info["prf_img"]+" \" width=\"40px\" height=\"40px\" class=\"prf_img_mini\" > <a href=\"in.php?p=profile&id="+my_info["id"]+"\">"+my_info["name"]+" "+my_info["lname"]+"</a><br> <h5>"+datefull+"</h5>"+text+"</div>";
	} else {
		document.getElementById('new_comment_'+id+'').appendChild(newdiv);	
		newdiv.innerHTML=" <a href=\"in.php?p=profile&id="+my_info["id"]+"\">"+my_info["name"]+" "+my_info["lname"]+"</a><br> "+text+"<h6 style='margin:0px;padding:0px;'>"+datefull+"</h6>";
	}

	document.getElementById('spicktext_'+id+'').value='';
}
function delcomment (id, type) {
	switch (type) {
		case 1: 
			var opt = "photos";
		break;
		case 2:
			var opt = "pages";
		break;
		case 3:
			var opt = "events";
		break;
		case 4:
			var opt = "spacers";
		break;
	}
	if (sendAjax('in_actions.php?a=dcomment&d='+id+'&type='+type)) deltag('commentspacerlist_'+id);
}

function deleteNewComment(id) {
	var comment = document.getElementById(id);
	var fatherdiv = comment.parentNode;
	fatherdiv.removeChild(comment);
}

function sendSearch() {
	document.getElementById('result').innerHTML=sendAjax('in_actions.php?a=search_pages&topic='+encodeURIComponent(document.getElementById('topic_search').value));
}

function sendNota() {
	document.getElementById('new_nota').innerHTML=sendAjax('in_actions.php?a=fastnote&t='+encodeURIComponent(document.getElementById('textnota').value));
	document.getElementById('textnota').value='';
}
//AMIGOS EN HOME
function sendaceptarA(f) {
	document.getElementById('inv_'+f).innerHTML=sendAjax('in_actions.php?a=addfriend&f='+f);
	document.getElementById('numpet').innerHTML = parseInt(document.getElementById('numpet').innerHTML) - 1;
}
function senddenegarA(f) {
	document.getElementById('inv_'+f).innerHTML=sendAjax('in_actions.php?a=delfriend&nf='+f);
	document.getElementById('numpet').innerHTML = parseInt(document.getElementById('numpet').innerHTML) - 1;
}
//AMIGOS EN FRIENDS
function sendanadirA(f) {
	document.getElementById('addamigo').value=sendAjax('in_actions.php?a=addfriend&f='+f);
}
function senddelA(f) {
	document.getElementById('delamigo').value=sendAjax('in_actions.php?a=delfriend&nf='+f);
}
//PAGINAS EN PAGES
function sendseguirP(id) {
	document.getElementById('check').innerHTML=sendAjax('in_actions.php?a=invpage&idg='+id+'&new');
}
function senddejarP(id, ids) {
	document.getElementById('check').innerHTML=sendAjax('in_actions.php?a=invpage&idg='+id+'&ids='+ids+'&del=2');
}
//PAGINAS EN HOME
function senddenegarP(id, ids) {
	document.getElementById('checkpage_'+id).innerHTML=sendAjax('in_actions.php?a=invpage&idg='+id+'&ids='+ids+'&alv=2');
}
function sendaceptarP(id, ids) {
	document.getElementById('checkpage_'+id).innerHTML=sendAjax('in_actions.php?a=invpage&idg='+id+'&ids='+ids+'&alv=1');
}
//EVENTOS En HOME
function sendvoyE(id, ids) {
	document.getElementById('checkevent_'+id).innerHTML=sendAjax('in_actions.php?a=event&idg='+id+'&ids='+ids+'&alv=1');
}
function sendquizasE(id, ids) {
	document.getElementById('checkevent_'+id).innerHTML=sendAjax('in_actions.php?a=event&idg='+id+'&ids='+ids+'&alv=2');
}
function sendnvoyE(id, ids) {
	document.getElementById('checkevent_'+id).innerHTML=sendAjax('in_actions.php?a=event&idg='+id+'&ids='+ids+'&alv=3');
}
//OTROS
function sendVote(vote, id, ide) {
	document.getElementById('check').innerHTML=sendAjax('in_actions.php?a=event&vote='+vote+'&idg='+id);
	document.getElementById('graf').innerHTML=sendAjax('in_actions.php?a=event&refresh&id='+ide);
}
function sendEventComment(id) {
	document.getElementById('pgcomment_container').innerHTML=sendAjax('in_actions.php?a=event&t='+encodeURIComponent(document.getElementById('spicktext').value)+'&u='+id)+document.getElementById('comment_container').innerHTML;
	document.getElementById('spicktext').value='';
}
function like(id, idimg, type) {
	document.getElementById('likecheck').innerHTML=sendAjax('in_actions.php?a=like&type='+type+'&idimg='+idimg);
}
function sendComment() {
	document.getElementById('pgcomment_container').innerHTML=sendAjax('in_actions.php?a=ccomment&t='+encodeURIComponent(document.getElementById('spicktext').value)+'&u='+document.getElementById('spickid').value+'&r='+document.getElementById('responseid').value)+document.getElementById('pgcomment_container').innerHTML;
	document.getElementById('spicktext').value='';
}
function sendText(id) {
	document.getElementById('textarea').innerHTML=sendAjax('in_actions.php?a=epage&t='+encodeURIComponent(document.getElementById('text').value)+'&u='+id)+document.getElementById('textarea').innerHTML;
	document.getElementById('text').value='';
}
function sendSpacer() {
	document.getElementById('pgspacertext').innerHTML=sendAjax('in_actions.php?a=cspacer&t='+encodeURIComponent(document.getElementById('textspacer').value));
	document.getElementById('textspacer').value='';
}
function deleteSpacer(id) {
	if (sendAjax('in_actions.php?a=dspacer&d='+id) == "") deltag('spacerlist_'+id);
	else alert("Error");
}
function deleteComment(id) {
	if (sendAjax('in_actions.php?a=dcomment&d='+id) == "") deltag('commentlist_'+id);
}
function deleteMp(id) {
	if (sendAjax('in_actions.php?a=dmp_&d='+id) == "") deltag('mp_'+id);
}
function deleteFriend(id) {
	if (sendAjax('in_actions.php?a=delfriend&nf='+id) == "") deltag('fr_'+id);
}
function formResponse(id, userid) {
	document.getElementById('spickform').style.display="block";
	document.getElementById('spickid').value=userid;
	document.getElementById('responseid').value=id;
	document.getElementById('spicktext').value="spickly.es/pr_"+userid+" ";
	document.getElementById('spicktext').focus();
}

var selectedindex = 0;
function selectImage(index) {
	 document.getElementById('image').value=index;
	 document.getElementById('image_'+index).style.border='3px solid red';
	 if (selectedindex) document.getElementById('image_'+selectedindex).style.border='0px none';
	 selectedindex = index;
}
function prevPage() {
	sliderpage=sliderpage-1; 
	if (sliderpage == 1) document.getElementById('prevpage').style.display = 'none';
	$.ajax({
		url: "in_actions.php?a=sphoto&s="+sliderpage,
		type: 'GET',
		success: function(res){
			document.getElementById('slidercontent').innerHTML = res;
		}
	});
}
function nextPage() {
	sliderpage=sliderpage+1; 
	document.getElementById('prevpage').style.display = 'block';
	$.ajax({
		url: "photoslider.php?a=sphoto&s="+sliderpage,
		type: 'GET',
		success: function(res){
			document.getElementById('slidercontent').innerHTML = res;
			if (!res) document.getElementById('nextpage').style.display = 'none';
		}
	});
}
var selecteduser = 0;
function selectUser(index) {
	document.getElementById('user').value=index;
	document.getElementById('user_'+index).style.border='3px solid red';
	if (selecteduser) document.getElementById('user_'+selecteduser).style.border='0px none';
	selecteduser = index;
}
function setVideoToScreen(video) {
	document.getElementById('videocontainer').innerHTML='<object width="640" height="390"><param name="movie" value="http://www.youtube.com/v/'+video+'"><param name="allowFullScreen" value="true"><param name="allowscriptaccess" value="always"><embed src="http://www.youtube.com/v/'+video+'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="390"></object>';
	document.getElementById('videobg').style.display="block";
}
function setFriendType(option, ids) {
	sendAjax('in_actions.php?a=ftype&t='+option+'&id='+ids);
}
function pagination (type, page, id) {
	if (id===undefined) document.getElementById('pg'+type).innerHTML=sendAjax('in_actions.php?a=pn'+type+'&pg='+page);
	else document.getElementById('pg'+type).innerHTML=sendAjax('in_actions.php?a=pn'+type+'&pg='+page+'&id='+id);
}
function mostrarupload(capa) {
	document.getElementById('upload').style.display="none";
	document.getElementById(capa).style.display="block";
}
function muestraoculta(id){
	if (document.getElementById){ 
		var el = document.getElementById('upload'); 
		el.style.display = (el.style.display == 'block') ? 'none' : 'block'; 
	}
}
window.onload = function(){
	muestraoculta('upload');
}
function ocultardiv() {
	$('#resultado').fadeOut(4000);
}
// USAR ESTA FUNCION SI QUEREIS MOSTRAR O OCULTAR ALGO (ROBER) 
function ocultardivs(capas) {
	document.getElementById('filelist').style.display="none";
	document.getElementById(capa).style.display="block";
}
