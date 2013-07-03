<?php
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

include("common.php");

function getChatList($user) {
	$res = mysql_query("SELECT * FROM friends WHERE (member_id_send='".mysql_real_escape_string($user)."' OR member_id_receive='".mysql_real_escape_string($user)."') AND alive=1 ORDER BY alive") or die(mysql_error());
	if (!mysql_num_rows($res)) die ("<li>No tienes amigos conectados :(</li>");
	else {
		while ($row = mysql_fetch_array($res)) {
			if ($row["member_id_send"] != $user) $u_row = fetchUser($row["member_id_send"]); 
			if ($row["member_id_receive"] != $user) $u_row = fetchUser($row["member_id_receive"]); 
			if ($u_row["status"] == 1) { 
				$i++; ?>
				<li><a href="javascript:void(0)" onclick="javascript:chatWith('<?php echo $u_row["member_id"]; ?>', '<?php $lt = explode(" ", $u_row["lastname"]); echo $u_row["firstname"]." ".$lt[0]; ?>')"><?php echo $u_row["firstname"]." ".$u_row["lastname"]; ?></a></li>
				<?php 		
			} 
		} 
		if (!$i) die ("<li>No tienes amigos conectados</li>"); 
	}
}

if (isset($_GET["status"])){
	if ($_GET["status"]) {
		mysql_query("UPDATE members SET status=1 WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'") or die(mysql_error());
		die("ok");
	} else {
		mysql_query("UPDATE members SET status=0 WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'") or die(mysql_error());
		die("ok");
	}
}
if (isset($_GET["refresh"])) {
	if ($user_row["status"] == 1) {
		getChatList($user_row["member_id"]);
		exit();
		die("ok");
	} else die("<li>Desconectado</li>");
}

if ($_GET['action'] == "chatheartbeat") { chatHeartbeat(); } 
if ($_GET['action'] == "sendchat") { sendChat(); } 
if ($_GET['action'] == "closechat") { closeChat(); } 
if ($_GET['action'] == "startchatsession") { startChatSession(); } 

if (!isset($_SESSION['chatHistory'])) {
	$_SESSION['chatHistory'] = array();	
}

if (!isset($_SESSION['openChatBoxes'])) {
	$_SESSION['openChatBoxes'] = array();	
}

function chatHeartbeat() {
	
	$sql = "select * from chat where (chat.member_id_receive = '".mysql_real_escape_string($_SESSION['chat_member_id'])."' AND status = 0) order by id ASC";
	$query = mysql_query($sql);
	$items = '';

	$chatBoxes = array();

	while ($chat = mysql_fetch_array($query)) {

		if (!isset($_SESSION['openChatBoxes'][$chat['member_id_send']]) && isset($_SESSION['chatHistory'][$chat['member_id_send']])) {
			$items = $_SESSION['chatHistory'][$chat['member_id_send']];
		}
		if (!isset($_SESSION["chatnames_".$chat['member_id_send']])) {
			$row = fetchUser($chat['member_id_send']);
			$lt = explode(" ", $row["lastname"]);
			$_SESSION["chatnames_".$chat['member_id_send']] = $row["firstname"]." ".$lt[0];
			$to_name = $row["firstname"]." ".$lt[0];
		} else {
			$to_name = $_SESSION["chatnames_".$chat['member_id_send']];
		}
		$chat['message'] = sanitize($chat['message']);

		$items .= <<<EOD
					   {
			"s": "0",
			"f": "{$chat['member_id_send']}",
			"g": "{$to_name}",
			"m": "{$chat['message']}"
	   },
EOD;

	if (!isset($_SESSION['chatHistory'][$chat['member_id_send']])) {
		$_SESSION['chatHistory'][$chat['member_id_send']] = '';
	}

	$_SESSION['chatHistory'][$chat['member_id_send']] .= <<<EOD
						   {
			"s": "0",
			"f": "{$chat['member_id_send']}",
			"g": "{$to_name}",
			"m": "{$chat['message']}"
	   },
EOD;
		
		unset($_SESSION['tsChatBoxes'][$chat['member_id_send']]);
		$_SESSION['openChatBoxes'][$chat['member_id_send']] = $chat['sent'];
	}

	if (!empty($_SESSION['openChatBoxes'])) {
	foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
		if (!isset($_SESSION['tsChatBoxes'][$chatbox])) {
			$now = time()-strtotime($time);
			$time = date('g:iA M dS', strtotime($time));

			$message = "Sent at $time";
			if ($now > 180) {
				$items .= <<<EOD
{
"s": "2",
"f": "$chatbox",
"g": "Aqui nombre",
"m": "{$message}"
},
EOD;

	if (!isset($_SESSION['chatHistory'][$chatbox])) {
		$_SESSION['chatHistory'][$chatbox] = '';
	}

	$_SESSION['chatHistory'][$chatbox] .= <<<EOD
		{
"s": "2",
"f": "$chatbox",
"g": "Aqui nombre",
"m": "{$message}"
},
EOD;
			$_SESSION['tsChatBoxes'][$chatbox] = 1;
		}
		}
	}
}

	$sql = "update chat set status = 1 where chat.member_id_receive = '".mysql_real_escape_string($_SESSION['chat_member_id'])."' and status = 0";
	$query = mysql_query($sql);

	if ($items != '') {
		$items = substr($items, 0, -1);
	}
header('Content-type: application/json');
?>
{
		"items": [
			<?php echo $items;?>
        ]
}

<?php
			exit(0);
}

function chatBoxSession($chatbox) {
	
	$items = '';
	
	if (isset($_SESSION['chatHistory'][$chatbox])) {
		$items = $_SESSION['chatHistory'][$chatbox];
	}

	return $items;
}

function startChatSession() {
	$items = '';
	if (!empty($_SESSION['openChatBoxes'])) {
		foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
			$items .= chatBoxSession($chatbox);
		}
	}


	if ($items != '') {
		$items = substr($items, 0, -1);
	}

header('Content-type: application/json');
?>
{
		"userid": "<?php echo $_SESSION['chat_member_id'];?>",
		"username": "<?php echo $_SESSION['chat_username'];?>",
		"items": [
			<?php echo $items;?>
        ]
}

<?php


	exit(0);
}

function sendChat() {
	$from = $_SESSION['chat_member_id'];
	$to = $_POST['to'];
	if (!isset($_SESSION["chatnames_".$_POST['to']])) {
		$row = fetchUser($_POST["to"]);
		$lt = explode(" ", $row["lastname"]);
		$_SESSION["chatnames_".$_POST['to']] = $row["firstname"]." ".$lt[0];
		$to_name = $row["firstname"]." ".$lt[0];
	} else {
		$to_name = $_SESSION["chatnames_".$_POST['to']];
	}
	$message = $_POST['message'];

	$_SESSION['openChatBoxes'][$_POST['to']] = date('Y-m-d H:i:s', time());
	
	$messagesan = sanitize($message);

	if (!isset($_SESSION['chatHistory'][$_POST['to']])) {
		$_SESSION['chatHistory'][$_POST['to']] = '';
	}

	$_SESSION['chatHistory'][$_POST['to']] .= <<<EOD
					   {
			"s": "1",
			"f": "{$to}",
			"g": "{$to_name}",
			"m": "{$messagesan}"
	   },
EOD;


	unset($_SESSION['tsChatBoxes'][$_POST['to']]);
	if (!in_array($to, $friendArray)) die("ERROR");

	$sql = "insert into chat (chat.member_id_send,chat.member_id_receive,message,date) values ('".mysql_real_escape_string($from)."', '".mysql_real_escape_string($to)."','".mysql_real_escape_string($message)."',NOW())";
	$query = mysql_query($sql);
	echo "1";
	exit(0);
}

function closeChat() {

	unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
	
	echo "1";
	exit(0);
}

function sanitize($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n","<br>",$text);
	return $text;
}