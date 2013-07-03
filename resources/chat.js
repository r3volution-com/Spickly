/*

Copyright (c) 2009 Anant Garg (anantgarg.com | inscripts.com)

This script may be used for non-commercial purposes only. For any
commercial purposes, please contact the author at 
anant.garg@inscripts.com

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

*/

var windowFocus = true;
var userid;
var username;
var chatHeartbeatCount = 0;
var minChatHeartbeat = 1000;
var maxChatHeartbeat = 33000;
var chatHeartbeatTime = minChatHeartbeat;
var originalTitle;
var blinkOrder = 0;

var chatboxFocus = new Array();
var newMessages = new Array();
var newMessagesWin = new Array();
var chatBoxes = new Array();

$(document).ready(function(){
	originalTitle = document.title;
	startChatSession();

	$([window, document]).blur(function(){
		windowFocus = false;
	}).focus(function(){
		windowFocus = true;
		document.title = originalTitle;
	});
});

function restructureChatBoxes() {
	align = 0;
	for (x in chatBoxes) {
		chatboxid = chatBoxes[x];

		if ($("#chatbox_"+chatboxid).css('display') != 'none') {
			if (align == 0) {
				$("#chatbox_"+chatboxid).css('right', '20px');
			} else {
				width = (align)*(225+7)+20;
				$("#chatbox_"+chatboxid).css('right', width+'px');
			}
			align++;
		}
	}
}

function chatWith(chatid, chatuser) {
	createChatBox(chatid, chatuser);
	$("#chatbox_"+chatid+" .chatboxtextarea").focus();
}

function createChatBox(chatboxid,chatboxuser,minimizeChatBox) {
	if ($("#chatbox_"+chatboxid).length > 0) {
		if ($("#chatbox_"+chatboxid).css('display') == 'none') {
			$("#chatbox_"+chatboxid).css('display','block');
			restructureChatBoxes();
		}
		$("#chatbox_"+chatboxid+" .chatboxtextarea").focus();
		return;
	}

	$(" <div />" ).attr("id","chatbox_"+chatboxid)
	.addClass("chatbox")
	.html('<div class="chatboxhead"><div class="chatboxtitle">'+chatboxuser+'</div><div class="chatboxoptions"><a href="javascript:void(0)" onclick="javascript:toggleChatBoxGrowth(\''+chatboxid+'\')">-</a> <a href="javascript:void(0)" onclick="javascript:closeChatBox(\''+chatboxid+'\')">X</a></div><br clear="all"/></div><div class="chatboxcontent"></div><div class="chatboxinput"><textarea class="chatboxtextarea" onkeydown="javascript:return checkChatBoxInputKey(event,this,\''+chatboxid+'\');"></textarea></div>')
	.appendTo($( "body" ));
			   
	$("#chatbox_"+chatboxid).css('bottom', '0px');
	
	chatBoxeslength = 0;

	for (x in chatBoxes) {
		if ($("#chatbox_"+chatBoxes[x]).css('display') != 'none') {
			chatBoxeslength++;
		}
	}

	if (chatBoxeslength == 0) {
		$("#chatbox_"+chatboxid).css('right', '20px');
	} else {
		width = (chatBoxeslength)*(225+7)+20;
		$("#chatbox_"+chatboxid).css('right', width+'px');
	}
	
	chatBoxes.push(chatboxid);

	if (minimizeChatBox == 1) {
		minimizedChatBoxes = new Array();

		if ($.cookie('chatbox_minimized')) {
			minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		}
		minimize = 0;
		for (j=0;j<minimizedChatBoxes.length;j++) {
			if (minimizedChatBoxes[j] == chatboxid) {
				minimize = 1;
			}
		}

		if (minimize == 1) {
			$('#chatbox_'+chatboxid+' .chatboxcontent').css('display','none');
			$('#chatbox_'+chatboxid+' .chatboxinput').css('display','none');
		}
	}

	chatboxFocus[chatboxid] = false;

	$("#chatbox_"+chatboxid+" .chatboxtextarea").blur(function(){
		chatboxFocus[chatboxid] = false;
		$("#chatbox_"+chatboxid+" .chatboxtextarea").removeClass('chatboxtextareaselected');
	}).focus(function(){
		chatboxFocus[chatboxid] = true;
		newMessages[chatboxid] = false;
		$('#chatbox_'+chatboxid+' .chatboxhead').removeClass('chatboxblink');
		$("#chatbox_"+chatboxid+" .chatboxtextarea").addClass('chatboxtextareaselected');
	});

	$("#chatbox_"+chatboxid).click(function() {
		if ($('#chatbox_'+chatboxid+' .chatboxcontent').css('display') != 'none') {
			$("#chatbox_"+chatboxid+" .chatboxtextarea").focus();
		}
	});

	$("#chatbox_"+chatboxid).show();
}


function chatHeartbeat(){

	var itemsfound = 0;
	
	if (windowFocus == false) {
 
		var blinkNumber = 0;
		var titleChanged = 0;
		for (x in newMessagesWin) {
			if (newMessagesWin[x] == true) {
				++blinkNumber;
				if (blinkNumber >= blinkOrder) {
					document.title = x+' says...';
					titleChanged = 1;
					mySound.play();
					break;	
				}
			}
		}
		
		if (titleChanged == 0) {
			document.title = originalTitle;
			blinkOrder = 0;
		} else {
			++blinkOrder;
		}

	} else {
		for (x in newMessagesWin) {
			newMessagesWin[x] = false;
		}
	}

	for (x in newMessages) {
		if (newMessages[x] == true) {
			if (chatboxFocus[x] == false) {
				//FIXME: add toggle all or none policy, otherwise it looks funny
				$('#chatbox_'+x+' .chatboxhead').toggleClass('chatboxblink');
			}
		}
	}
	
	$.ajax({
	  url: "chat.php?action=chatheartbeat",
	  cache: false,
	  dataType: "json",
	  success: function(data) {

		$.each(data.items, function(i,item){
			if (item)	{ // fix strange ie bug

				chatboxid = item.f;

				if ($("#chatbox_"+chatboxid).length <= 0) {
					createChatBox(chatboxid);
				}
				if ($("#chatbox_"+chatboxid).css('display') == 'none') {
					$("#chatbox_"+chatboxid).css('display','block');
					restructureChatBoxes();
				}
				
				if (item.s == 1) {
					item.f = userid;
				}

				if (item.s == 2) {
					$("#chatbox_"+chatboxid+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+item.m+'</span></div>');
				} else {
					newMessages[chatboxid] = true;
					newMessagesWin[chatboxid] = true;
					$("#chatbox_"+chatboxid+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+item.g+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+item.m+'</span></div>');
				}

				$("#chatbox_"+chatboxid+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxid+" .chatboxcontent")[0].scrollHeight);
				itemsfound += 1;
			}
		});

		chatHeartbeatCount++;

		if (itemsfound > 0) {
			chatHeartbeatTime = minChatHeartbeat;
			chatHeartbeatCount = 1;
		} else if (chatHeartbeatCount >= 10) {
			chatHeartbeatTime *= 2;
			chatHeartbeatCount = 1;
			if (chatHeartbeatTime > maxChatHeartbeat) {
				chatHeartbeatTime = maxChatHeartbeat;
			}
		}
		
		setTimeout('chatHeartbeat();',chatHeartbeatTime);
	}});
}

function closeChatBox(chatboxid) {
	$('#chatbox_'+chatboxid).css('display','none');
	restructureChatBoxes();

	$.post("chat.php?action=closechat", { chatbox: chatboxid} , function(data){	
	});

}

function toggleChatBoxGrowth(chatboxid) {
	if ($('#chatbox_'+chatboxid+' .chatboxcontent').css('display') == 'none') {  
		
		var minimizedChatBoxes = new Array();
		
		if ($.cookie('chatbox_minimized')) {
			minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		}

		var newCookie = '';

		for (i=0;i<minimizedChatBoxes.length;i++) {
			if (minimizedChatBoxes[i] != chatboxid) {
				newCookie += chatboxid+'|';
			}
		}

		newCookie = newCookie.slice(0, -1)


		$.cookie('chatbox_minimized', newCookie);
		$('#chatbox_'+chatboxid+' .chatboxcontent').css('display','block');
		$('#chatbox_'+chatboxid+' .chatboxinput').css('display','block');
		$("#chatbox_"+chatboxid+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxid+" .chatboxcontent")[0].scrollHeight);
	} else {
		
		var newCookie = chatboxid;

		if ($.cookie('chatbox_minimized')) {
			newCookie += '|'+$.cookie('chatbox_minimized');
		}


		$.cookie('chatbox_minimized',newCookie);
		$('#chatbox_'+chatboxid+' .chatboxcontent').css('display','none');
		$('#chatbox_'+chatboxid+' .chatboxinput').css('display','none');
	}
	
}

function checkChatBoxInputKey(event,chatboxtextarea,chatboxid) {
	 
	if(event.keyCode == 13 && event.shiftKey == 0)  {
		message = $(chatboxtextarea).val();
		message = message.replace(/^\s+|\s+$/g,"");

		$(chatboxtextarea).val('');
		$(chatboxtextarea).focus();
		$(chatboxtextarea).css('height','44px');
		if (message != '') {
			$.post("chat.php?action=sendchat", {to: chatboxid, message: message} , function(data){
				message = message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");
				$("#chatbox_"+chatboxid+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+username+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+message+'</span></div>');
				$("#chatbox_"+chatboxid+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxid+" .chatboxcontent")[0].scrollHeight);
			});
		}
		chatHeartbeatTime = minChatHeartbeat;
		chatHeartbeatCount = 1;

		return false;
	}

	var adjustedHeight = chatboxtextarea.clientHeight;
	var maxHeight = 94;

	if (maxHeight > adjustedHeight) {
		adjustedHeight = Math.max(chatboxtextarea.scrollHeight, adjustedHeight);
		if (maxHeight)
			adjustedHeight = Math.min(maxHeight, adjustedHeight);
		if (adjustedHeight > chatboxtextarea.clientHeight)
			$(chatboxtextarea).css('height',adjustedHeight+8 +'px');
	} else {
		$(chatboxtextarea).css('overflow','auto');
	}
	 
}

function startChatSession(){  
	$.ajax({
	  url: "chat.php?action=startchatsession",
	  cache: false,
	  dataType: "json",
	  success: function(data) {
 
		userid = data.userid;
		username = data.username;

		$.each(data.items, function(i,item){
			if (item)	{ // fix strange ie bug

				chatboxid = item.f;
				chatboxname = item.g;

				if ($("#chatbox_"+chatboxid).length <= 0) {
					createChatBox(chatboxid,chatboxname,1);
				}
				
				if (item.s == 1) {
					item.f = userid;
					item.g = username;
				}

				if (item.s == 2) {
					$("#chatbox_"+chatboxid+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+item.m+'</span></div>');
				} else {
					$("#chatbox_"+chatboxid+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+item.g+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+item.m+'</span></div>');
				}
			}
		});
		
		for (i=0;i<chatBoxes.length;i++) {
			chatboxid = chatBoxes[i];
			$("#chatbox_"+chatboxid+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxid+" .chatboxcontent")[0].scrollHeight);
			setTimeout('$("#chatbox_"+chatboxid+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxid+" .chatboxcontent")[0].scrollHeight);', 100); // yet another strange ie bug
		}
	
	setTimeout('chatHeartbeat();',chatHeartbeatTime);
		
	}});
}

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};