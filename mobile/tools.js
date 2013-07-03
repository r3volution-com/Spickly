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
function getSuggest(suggest, placelist) { 
	$.ajax({ 
		type: "GET", 
		url: "friends.php?fs="+suggest, 
		contentType: "application/json; charset=utf-8", 
		dataType: "json", 
		success: function (response) { 
			document.getElementById(placelist).innerHTML="";
			var p = eval( response );
			for (i=0; p.resultado[i]; i++) document.getElementById(placelist).innerHTML+="<li><a href='in.php?p=profile&id="+p.resultado[i].id+"'>"+p.resultado[i].nombre+"</a></li>";
		}
	}); 
}
function getSuggestToInput(suggest, placelist, placename, placeid) { 
	$.ajax({ 
		type: "GET", 
		url: "friends.php?fs="+suggest, 
		contentType: "application/json; charset=utf-8", 
		dataType: "json", 
		success: function (response) { 
			document.getElementById(placelist).innerHTML="";
			var p = eval(response);
			for (i=0; p.resultado[i]; i++) document.getElementById(placelist).innerHTML+="<li><a href='#' onclick='putSuggestToInput(\""+placelist+"\",\""+placename+"\", \""+placeid+"\", \""+p.resultado[i].nombre+"\",\""+p.resultado[i].id+"\")'>"+p.resultado[i].nombre+"</a></li>";
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
		url: "index.php?cs="+type, 
		async: false
	}); 
}
function sendSpacer() {
	document.getElementById('spacertext').innerHTML=sendAjax('in_actions.php?a=cspacer&t='+encodeURIComponent(document.getElementById('textspacer').value));
	document.getElementById('textspacer').value='';
}
function sendComment() {
	document.getElementById('comment_container').innerHTML=sendAjax('in_actions.php?a=ccomment&t='+encodeURIComponent(document.getElementById('spicktext').value)+'&u='+document.getElementById('spickid').value+'&r='+document.getElementById('responseid').value)+document.getElementById('comment_container').innerHTML;
	document.getElementById('spicktext').value='';
}
function sendSpacerComment(id) {
	document.getElementById('commentspacer_container').innerHTML=sendAjax('in_actions.php?a=cspacercom&t='+encodeURIComponent(document.getElementById('spickspacertext').value)+'&s='+id)+document.getElementById('commentspacer_container').innerHTML;
	document.getElementById('spickspacertext').value='';
}
function sendCommentPhoto(id) {
	document.getElementById('comment_container').innerHTML=sendAjax('in_actions.php?a=cphotocom&t='+encodeURIComponent(document.getElementById('spicktext').value)+'&f='+id)+document.getElementById('comment_container').innerHTML;
	document.getElementById('spicktext').value='';
}
function sendPageComment(id) {
	document.getElementById('comment_container').innerHTML=sendAjax('in_actions.php?a=cpagecom&t='+encodeURIComponent(document.getElementById('spicktext').value)+'&u='+id)+document.getElementById('comment_container').innerHTML;
	document.getElementById('spicktext').value='';
}
function sendText(id) {
	document.getElementById('textarea').innerHTML=sendAjax('in_actions.php?a=epage&t='+encodeURIComponent(document.getElementById('text').value)+'&u='+id)+document.getElementById('textarea').innerHTML;
	document.getElementById('text').value='';
}
function deleteSpacer(id) {
	document.getElementById('spacerlist_'+id).innerHTML=sendAjax('in_actions.php?a=dspacer&d='+id);
}
function deleteComment(id) {
	document.getElementById('commentlist_'+id).innerHTML=sendAjax('in_actions.php?a=dcomment&d='+id);
}
function deleteSpacerComment(id) {
	document.getElementById('commentspacerlist_'+id).innerHTML=sendAjax('in_actions.php?a=dspacercom&d='+id);
}
function deleteCommentPhoto(id) {
	document.getElementById('commentlist_'+id).innerHTML=sendAjax('in_actions.php?a=dphotocom&d='+id);
}
function deleteMp(id) {
	document.getElementById('mp_'+id).innerHTML=sendAjax('in_actions.php?a=dmp&d='+id);
}
function deletePageComment(id) {
	document.getElementById('commentlist_'+id).innerHTML=sendAjax('in_actions.php?a=dpagecom&d='+id);
}
function formResponse(id, userid) {
	document.getElementById('spickform').style.display="block";
	document.getElementById('spickid').value=userid;
	document.getElementById('responseid').value=id;
	document.getElementById('spicktext').value="spickly.es/pr_"+userid+" ";
	document.getElementById('spicktext').focus();
}