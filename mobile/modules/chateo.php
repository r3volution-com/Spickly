	<?php	if (!isset($_SESSION["chat_member_id"])) {
			$_SESSION['chat_member_id'] = $id;
			$lt = explode(" ", $user_row["lastname"]);
			$_SESSION['chat_username'] = $user_row["firstname"]." ".$lt[0];
			$_SESSION['chat_member_id'] = $id;
			$lt = explode(" ", $user_row["lastname"]);
			$_SESSION['chat_username'] = $user_row["firstname"]." ".$lt[0];
		}
		 if ($opt != 1) { ?><li><a href="#">Chat</a>
			<ul id="chatlist">
				<?php if ($user_row["online"] == 1) echo "Conectando..."; else echo "Desconectado"; ?>
			</ul>
		</li><?php } ?>
		
		<div id="chat">
			<h1 id="chatbuttons">Chat - 
				<?php if ($user_row["status"] == 1) { ?>
					<a href="#" onclick="startStopChat(0, 'chatlist')">Desconectar</a> - <a href="#" onclick="refreshChat('chatlist')">Refrescar</a>
				<?php } else { ?>
					<a href="#" onclick="startStopChat(1, 'chatlist')">Conectar</a>
				<?php } ?>
			</h1>
			<hr>
			<ul id="chatlist">
				<?php if ($user_row["status"] == 1) echo "Conectando..."; else echo "Desconectado"; ?>
			</ul>
		</div>
		<script>
			function lostSessionChat() {
				startStopChat(0, 'chatlist');
				changeStatus(0);
			}
			var sliderpage=0;
			var mySound = new buzz.sound("resources/sound.mp3");
			setTimeout("refreshChat('chatlist')", 30000);
			setTimeout("lostSessionChat()", 900000);
		function changeStatus(type) {
			if (!checkCookie("chat_disconnected")) {
				$.ajax({ 
					type: "GET",  
		url: "chat.php?status="+type,  
					async: false,
					success: function (response) { 
						if (response != "ok") alert("Error en el chat");
					}
				}); 
			}
		}
		function refreshChat(placelist) {
			$.ajax({ 
				type: "GET", 
				url: "chat.php?refresh=1", 
				success: function (response) { 
					document.getElementById(placelist).innerHTML=response;
				}
			}); 
		}
function startStopChat(type, placelist) {
	$.ajax({ 
		type: "GET", 
		url: "chat.php?status="+type, 
		success: function (response) { 
			if (response != "ok") alert("Error en el chat");
			else {
				if (type == 1) {
					deleteCookie("chat_disconnected");
					document.getElementById("chatbuttons").innerHTML="Chat - <a href=\"#\" onclick=\"startStopChat(0, 'chatlist')\">Desconectar</a> - <a href=\"#\" onclick=\"refreshChat('chatlist')\">Refrescar</a>";
					refreshChat(placelist);
				} else {
					setCookie("chat_disconnected", 1, 7);
					document.getElementById("chatbuttons").innerHTML="Chat - <a href=\"#\" onclick=\"startStopChat(1, 'chatlist')\">Conectar</a>";
					document.getElementById(placelist).innerHTML="<li>Desconectado</li>";
				}
			}
		}
	}); 
}
		</script>