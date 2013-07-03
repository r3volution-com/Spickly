<?php 
include("common.php");
	if (!function_exists("checkEmail")) {
		function checkEmail($email){
			$exp = "/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/"; 
			if(preg_match($exp,$email)){ 
				$my_temp_variable=explode("@",$email);
				$my_temp_variable=array_pop($my_temp_variable);
				if(!checkdnsrr($my_temp_variable,"MX")) return false; 
				$qry = "SELECT * FROM members WHERE email='".$email."' ";
				$result = mysql_query($qry) or die(mysql_error());
				if(mysql_num_rows($result)) return false;
				else return true;
			} else return false; 
		} 
	}
if (isset($_GET["a"]) && $_GET["a"]) {
	switch ($_GET["a"]) {
		///////////////////////////////////////////////////////////////
		////////////////////Sistema de etiquetas//////////////////////
		//////////////////////////////////////////////////////////////
		case "tag":
			if ($_GET["ac"] == "get") {
				$res = mysql_query("SELECT * FROM photos WHERE id=".mysql_real_escape_string($_GET["image-id"])."") or die(mysql_error()); 
				$row = mysql_fetch_array($res);
				echo '{
				"Image" : [
				{
				"id":'.$row["id"].',
				"Tags":[';
				$tres = mysql_query("SELECT * FROM tags WHERE photos_id=".mysql_real_escape_string($_GET["image-id"])."") or die(mysql_error()); 
				for ($i = 0; $trow = mysql_fetch_array($tres); $i++) {
					if ($i > 0) echo ",";
					echo '
					{
					"id":'.$trow["member_id_receive"].',
					"pid":'.$_REQUEST['image-id'].',
					"text":"'.getUsername($trow["member_id_receive"]).'",
					"left":'.$trow["left"].',
					"top":'.$trow["top"].',
					"width":'.$trow["width"].',
					"height":'.$trow["height"].',
					"url": "profile.php?id='.$trow["member_id_receive"].'",
					"isDeleteEnable": true
					}';
				}
				die(']
				}
				],
				"options":{
					"literals": {
						"removeTag": "Borrar"
					},
					"tag":{
						"flashAfterCreation": true
					}
				}
				}
				');
			} else if ($_GET["ac"] == "put") {
				if (!in_array($_REQUEST['name_id'], $friendArray) && $_REQUEST['name_id'] != $user_row["member_id"]) die("ERROR: ".$_REQUEST['name_id']);
				//Hacer que no se pueda etiquetar 2 veces a una persona en la misma foto
				mysql_query("INSERT INTO tags (member_id_send, member_id_receive, photos_id, `left` , `top`, `width`, `height`) VALUES (".$user_row["member_id"].", ".$_REQUEST['name_id'].", ".$_GET["image_id"].", ".$_REQUEST['left'].", ".$_REQUEST['top'].", ".$_REQUEST['width'].", ".$_REQUEST['height'].")") or die(mysql_error());
				die('{
				"result":true,
				"tag": {
					"id":'.$_REQUEST['name_id'].',
					"pid":'.$_REQUEST['photo_id'].',
					"text": "'.$_REQUEST['name'].'",
					"left": '.$_REQUEST['left'].',
					"top": '.$_REQUEST['top'].',
					"width": '.$_REQUEST['width'].',
					"height": '.$_REQUEST['height'].',
					"url": "profile.php?id='.$_REQUEST['name_id'].'",
					"isDeleteEnable": true
				}
				}');
			} else if ($_GET["ac"] == "del") {
				mysql_query("DELETE FROM tags WHERE member_id_send=".$user_row["member_id"]." AND member_id_receive=".$_GET["tag-id"]." AND photos_id=".$_GET["image-id"]);
				die('{"result":true,"message":"ooops"}');
			} else {
				die("ERROR");
			}
		break;	
		///////////////////////////////////////////////////////////////
		///////////////////////////Buscador////////////////////////////
		///////////////////////////////////////////////////////////////
		case "search_pages";
			$search = $_GET["topic"];
			$res = mysql_query("SELECT * FROM pages WHERE topic LIKE '%".$search."%' ") or die(mysql_error()); 
			while($row = mysql_fetch_array($res)){
				echo '<a href="in.php?p=pages&id='.$row["id"].'">'.$row["topic"].'</a>';
				echo "<br>";
			}
		break;
		///////////////////////////////////////////////////////////////
		///////////////////Invitaciones a paginas//////////////////////
		///////////////////////////////////////////////////////////////
		case "invpage":			
			if (isset($_POST["idp"]) ) {	// Invitar a un amigo	
				$ids = $_GET["ids"]; // Id del amigo " cuando acepta invitacion"		
				$id = $_GET["idg"]; // Id de la pagina
				$user_row2 = $_POST["user"]; // Id del amigo "cuando invitas"
				if ($user_row2 != $user_row["member_id"]) {			
					$res = mysql_query("SELECT * FROM members_has_pages WHERE member_id_receive='".mysql_real_escape_string($user_row2)."' AND pages_id='".$id."' ") or die("17: ".mysql_error());
					$row = mysql_fetch_array($res);
					if (mysql_num_rows($res) == 0) {
						mysql_query("INSERT INTO members_has_pages (member_id_send, member_id_receive, pages_id) VALUES ('".mysql_real_escape_string($user_row["member_id"])."','".mysql_real_escape_string($user_row2)."', '$id');") or die("20: ".mysql_error());
						echo "Invitacion enviada correctamente";
					} else echo "Ya esta invitado, no lo puedes volver a invitar";							
				} else echo "No te puedes invitar a ti mismo";
			} elseif (isset($_GET["alv"]) ) {	// Aceptar o Denegar una invitacion 					
				$user_row2 = $_GET["ids"]; // Id de la persona que te ha invitado
				$id = $_GET["idg"]; // Id de la pagina
				$alive = $_GET["alv"]; // Aceptar-1 o Denegar-2
				if ($user_row2 != $user_row["member_id"]) {			
					$res = mysql_query("SELECT * FROM members_has_pages WHERE member_id_receive='".mysql_real_escape_string($user_row["member_id"])."' AND pages_id='".$id."' AND alive=0 ") or die("17: ".mysql_error());
					$row = mysql_fetch_array($res);
					if (mysql_num_rows($res) && $row["member_id_send"] != $user_row["member_id"]) {			
						mysql_query("UPDATE members_has_pages SET alive=".$alive." WHERE member_id_receive='".mysql_real_escape_string($user_row["member_id"])."' AND pages_id='".$id."' AND alive=0") or die("23: ".mysql_error());
						echo "Peticion aceptada o denegada";
					} else echo "No puedes aceptarla, la has enviado tu.";						
				} else echo "No puedes aceptarla, la has envitado tu.";
			} else if (isset($_GET["del"]) ) { //Borrar la vinculacion						
				$user_row2 = $_GET["ids"]; // Id de la persona que te ha invitado
				$id = $_GET["idg"]; // Id de la pagina
				$resp = mysql_query("SELECT * FROM pages WHERE id='".$id."' ") or die("17: ".mysql_error());
				$rowp = mysql_fetch_array($resp);
				if ($rowp["member_id_send"] != $user_row["member_id"]) {		
					$res = mysql_query("SELECT * FROM members_has_pages WHERE (member_id_send='".mysql_real_escape_string($user_row2)."' AND member_id_receive='".mysql_real_escape_string($user_row["member_id"])."') OR (member_id_send='".mysql_real_escape_string($user_row["member_id"])."' AND member_id_receive='".mysql_real_escape_string($user_row2)."') AND pages_id='".$id."' ") or die("17: ".mysql_error());
					$row = mysql_fetch_array($res);
					if (mysql_num_rows($res) && $row["member_id_send"] != $user_row["member_id"]) {			
						mysql_query("DELETE FROM members_has_pages WHERE pages_id = '".mysql_real_escape_string($id)."' AND member_id_receive='".mysql_real_escape_string($user_row["member_id"])."' ") or die("No puedes vincularte con esta pagina");
						echo "Te has desvinculado de esta pagina";
					} else echo "No se ha podido ejecutar la orden";
				} else echo "Eres tu el dueÒo de la pagina";
			} else if (isset($_GET["new"])) { //Te vinculas a la pagina sin invitacion
				$id = $_GET["idg"]; // Id de la pagina
				$res = mysql_query("SELECT * FROM members_has_pages WHERE (member_id_send='".mysql_real_escape_string($user_row["member_id"])."' OR member_id_receive='".mysql_real_escape_string($user_row["member_id"])."') AND pages_id='".$id."' AND alive=2 ") or die("17: ".mysql_error());
				$row = mysql_fetch_array($res);
				if (!mysql_num_rows($res)) {			
					mysql_query("INSERT INTO members_has_pages (member_id_send, member_id_receive, pages_id, alive) VALUES (0, '".$user_row["member_id"]."', '".$id."', 1) ") or die("No has podido vincularte ha esta pagina");
					echo "Has empezado a seguir esta pagina";
				} else {
					mysql_query("UPDATE members_has_pages SET alive=1, member_id_send=0 WHERE member_id_receive=".mysql_real_escape_string($user_row["member_id"])." AND pages_id=$id") or die("23: ".mysql_error());
					echo "Estas invitado- Modificando las entrada ...  ";
				}	
			} else die ("Error");			
		break;
		///////////////////////////////////////////////////////////////
		/////////////////////Sistema de Eventos////////////////////////
		///////////////////////////////////////////////////////////////
		case "event": 	
			if (isset($_POST["pregunta"])) {
				$id_evento = $_GET["id"];
				$consulta = "INSERT INTO polls (topic, event_id) VALUES ('".($_POST['pregunta'])."', '".$id_evento."' )";
				mysql_query($consulta) or die(mysql_error());	
				$optionq = $_POST['option'];
				echo $optionq[0]."<0";	
				echo $optionq[1]."<1";	
				echo $optionq[2]."<2";	
				$result = count($_POST['option']);
				echo $result;
				$i = 0;
				$res = mysql_query("SELECT * FROM polls WHERE topic='".($_POST['pregunta'])."' ") or die("17: ".mysql_error());
				$row = mysql_fetch_array($res);
				while ($i < $result) {
					$consultar = "INSERT INTO poll_options (poll_id, text) VALUES ('".$row[id]."', '".$optionq[$i]."')"; // '".implode($option)."'
					mysql_query($consultar) or die(mysql_error());
					++$i; // Esta instrucciÛn hace que el valor de $i se incremente en 1 
				}	
			}
			else if (isset($_GET["vote"]) ) {	
				$id = $_GET["idg"]; // Id de la encuesta
				$option_id = $_GET["vote"];
				$res = mysql_query("SELECT * FROM poll_votes WHERE member_id_send='".mysql_real_escape_string($user_row["member_id"])."' AND poll_id='".$id."' ") or die("17: ".mysql_error());
				$row = mysql_fetch_array($res);
				if (!mysql_num_rows($res)) {			
					mysql_query("INSERT INTO poll_votes (member_id_send, poll_id, option_id) VALUES ('".$user_row["member_id"]."', '".$id."', '".$option_id."') ") or die("No has podido vincularte ha esta pagina");
					echo "Votando...";
				} else {
					echo "Actualizando voto...";
					mysql_query("UPDATE poll_votes SET option_id='".$option_id."' WHERE poll_id='".mysql_real_escape_string($row["poll_id"])."' ") or die("23: ".mysql_error());
				}							
			}
			else if(isset($_GET["refresh"])){
				// Mejorar sistema de SELECT un bucle dentro de otro no me gusta XD
				$pageid = $_GET["id"];
				$resencu = mysql_query("SELECT * FROM polls WHERE event_id='".$pageid."' ") or die(mysql_error());  
				$i=0; 
				while ($rowencu = mysql_fetch_array($resencu)) { 
					++$i; 
					?>
					<div id="<?php echo $rowencu["id"];?>"<? if($i == 1) { ?> style="display: block;"  <?} else{ ?> style="display: none;" <? }   ?> > 
						<div id="graphic_bar">
							<table border="1" class="graphic_bar"> 		
								<tr style="height:100px; text-align:center;"> 									
									<a href="#" onclick="document.getElementById('<?php echo $rowencu["id"]?>').style.display='none';document.getElementById('<?php echo $rowencu["id"] - 1?>').style.display='block';"> ‚Üê </a>
									<a href="#" onclick="document.getElementById('<?php echo $rowencu["id"]?>').style.display='none';document.getElementById('<?php echo $rowencu["id"] + 1?>').style.display='block';"> ‚Üí </a><p>
									<?php echo $rowencu["topic"]."<br>"; 
									$resopt = mysql_query("SELECT * FROM poll_options WHERE poll_id='".$rowencu["id"]."' ") or die(mysql_error());
									if (!mysql_num_rows($resopt)) echo "Consulta sin respuestas";
									while ($rowopt = mysql_fetch_array($resopt)) {
										$resoptx = mysql_query("SELECT * FROM poll_votes WHERE option_id='".$rowopt["id"]."' ") or die(mysql_error());  
										$resoptr = mysql_query("SELECT * FROM poll_votes ") or die(mysql_error());  
										$cantvx = mysql_num_rows($resoptx); //Cantidad de resultados de X
										$cantvr = mysql_num_rows($resoptr); //Cantidad de resultados en total
										if($cantvx == 0) $result = 0;
										else $result = $cantvx * '100' / $cantvr;
										echo $result."%";
										?>
										<td>
											<div id="graphic_bar_yes" style="height: <?php echo $result;?>px;" class="ggraphic_bar">
												<input type="submit" value="<?php echo $rowopt["text"]; ?>" onclick="sendVote(<?php echo $rowopt["id"]; ?>,<?php echo $rowencu["id"];?>,<?php echo $pageid; ?>)" ><br>
											</div>
										</td> 
									<?php } ?>
								</tr> 						
							</table>
						</div>	
					</div>			
				<?php }			
			}
			// Invitaciones de asistencia a los eventos								
			else if (isset($_POST["ids"]) ) {	// Invitar a un amigo	
				$ids = json_decode(stripcslashes($_POST["ids"]), true); // Id del amigo " cuando acepta invitacion"
				foreach ($ids as $id) echo $id;
				if ($user_row2 != $user_row["member_id"]) {			
					$res = mysql_query("SELECT * FROM members_has_events WHERE member_id_receive='".mysql_real_escape_string($user_row2)."' AND events_id='".$id."' ") or die("17: ".mysql_error());
					$row = mysql_fetch_array($res);
					if (mysql_num_rows($res) == 0) {
						mysql_query("INSERT INTO members_has_events (member_id_send, member_id_receive, events_id) VALUES ('".mysql_real_escape_string($user_row["member_id"])."','".mysql_real_escape_string($user_row2)."', '$id');") or die("20: ".mysql_error());
						echo 'Inivitacion enviada';
						echo $user_row2 ;
					} else echo "Ya esta invitado, no lo puedes volver a invitar";
				} else echo "No te puedes invitar a ti mismo";
			} elseif (isset($_GET["alv"]) ) {	// Aceptar o Denegar una invitacion 					
				$user_row2 = $_GET["ids"]; // Id de la persona que te ha invitado
				$id = $_GET["idg"]; // Id de la pagina
				$alive = $_GET["alv"]; // Aceptar-1 o Denegar-2
				if ($user_row2 != $user_row["member_id"]) {			
					$res = mysql_query("SELECT * FROM members_has_events WHERE member_id_receive='".mysql_real_escape_string($user_row["member_id"])."' AND events_id='".$id."' AND alive=0 ") or die("17: ".mysql_error());
					$row = mysql_fetch_array($res);
					if (mysql_num_rows($res) && $row["member_id_send"] != $user_row["member_id"]) {			
						mysql_query("UPDATE members_has_events SET alive=".$alive." WHERE member_id_receive='".mysql_real_escape_string($user_row["member_id"])."' AND events_id='".$id."' AND alive=0 ") or die("23: ".mysql_error());
						echo "Peticion aceptada o denegada";
					} else echo "No puedes aceptarla, la has enviado tu";							
				} else echo "No puedes aceptarla,la has envitado tu ";
			} elseif (isset($_GET["del"]) ) { //Borrar la vinculacion						
				$user_row2 = $_GET["ids"]; // Id de la persona que te ha invitado
				$id = $_GET["idg"]; // Id del evento
				$resp = mysql_query("SELECT * FROM events WHERE id='".$id."' ") or die("17: ".mysql_error());
				$rowp = mysql_fetch_array($resp);
				echo $rowp["member_id_send"];
				if ($rowp["member_id_send"] != $user_row["member_id"]) {			
					$res = mysql_query("SELECT * FROM members_has_events WHERE (member_id_send='".mysql_real_escape_string($user_row2)."' AND member_id_receive='".mysql_real_escape_string($user_row["member_id"])."') OR (member_id_send='".mysql_real_escape_string($user_row["member_id"])."' AND member_id_receive='".mysql_real_escape_string($user_row2)."') AND events_id='".$id."' ") or die("17: ".mysql_error());
					$row = mysql_fetch_array($res);
					if (mysql_num_rows($res) && $row["member_id_send"] != $user_row["member_id"]) {			
						mysql_query("DELETE FROM members_has_events WHERE pages_id = '".mysql_real_escape_string($id)."' AND member_id_receive='".mysql_real_escape_string($user_row["member_id"])."' ") or die("No puedes vincularte con esta pagina");
						echo "Te has desvinculado de esta pagina";
					} else echo "No se ha podido ejecutar la orden";
				} else echo "Eres tu el dueÒØ†§e la pagina";
			} elseif (isset($_GET["new"]) ) { //Te vinculas a la pagina sin invitacion <--- Hay ke borrarlo
				$id = $_GET["idg"]; // Id de la pagina
				$res = mysql_query("SELECT * FROM members_has_events WHERE (member_id_send='".mysql_real_escape_string($user_row["member_id"])."' OR member_id_receive='".mysql_real_escape_string($user_row["member_id"])."') AND pages_id='".$id."' AND alive=2 ") or die("17: ".mysql_error());
				$row = mysql_fetch_array($res);
				if (!mysql_num_rows($res) ) {			
					mysql_query("INSERT INTO members_has_events (member_id_send, member_id_receive, events_id, alive) VALUES (0, '".$user_row["member_id"]."', '".$id."', 1) ") or die("No has podido vincularte ha esta pagina");
					echo "Has empezado a seguir esta pagina";
				} else {
					mysql_query("UPDATE members_has_events SET alive=1, member_id_send=0 WHERE id=".mysql_real_escape_string($row["id"]) ) or die("23: ".mysql_error());
					echo "Estas invitado- Modificando las entrada ...  ";
				}	
			}
			else if (isset($_GET["t"]) && isset($_GET["u"])) {
				mysql_query("INSERT INTO comment_event (member_id_send, events_id, comment) VALUES (".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID']).", ".mysql_real_escape_string($_GET["u"]).",'".parseText($_GET["t"],1)."');") or die(mysql_error());
				echo '<div id="commentlist">'.$nick.'<br/>'.parseText($_GET['t']).'<br/><h6 style="margin:0px;padding:0px;">'.date('h:i:s d-m-Y').'</h6><hr></div>';
			} 
			else if (isset($_GET["d"]) && $_GET["d"]) mysql_query("DELETE FROM comment_event WHERE id=".mysql_real_escape_string($_GET["d"])." AND member_id_send=".$user_row["member_id"])or die ("No puedes borrar este comentario");										
			else die("OPCION INVALIDA");
		break;
		///////////////////////////////////////////////////////////////
		////////////////////Sistema de comentarios/////////////////////
		///////////////////////////////////////////////////////////////		
		case "comment": 
			if (isset($_GET["t"]) && isset($_GET["f"]) && $_GET["type"] <= 5) {
				$type = array("photos", "pages", "events", "spacers");
				$text = parseText($_GET["t"]);
				mysql_query("INSERT INTO comment_".$type[$_GET["type"]]." (member_id_send, ".$type[$_GET["type"]]."_id, text) VALUES (".$user_row["member_id"].", '".($_GET["f"])."', '".addslashes($text)."') ") or die(mysql_error());			
				die($text);
			} else die ("Error");
		break;
		case "dcomment": 
			if (isset($_GET["type"]) && $_GET["type"] <= 5 && isset($_GET["d"]) && $_GET["d"]) {
				$type = array("photos", "pages", "events", "spacers");
				mysql_query("DELETE FROM comment_".$type[$_GET["type"]]." WHERE id=".mysql_real_escape_string($_GET["d"])." AND member_id_send=".$user_row["member_id"]) or die(mysql_error());			
			} else die ("Error");
		break;
		case "cucomment":
			if (isset($_GET["t"]) && isset($_GET["u"])) {
				if ($_GET["u"] == $user_row["member_id"]) die("ERROR");
				if (!in_array($_GET["u"], $friendArray)) die("ERROR");
				$text = parseText($_GET['t']);
				if (!$_GET["r"]) $query = "INSERT INTO comment_users (member_id_send, member_id_receive, comment) VALUES (".$user_row["member_id"].", ".mysql_real_escape_string($_GET["u"]).",'".addslashes($text)."');";
				else $query = "INSERT INTO comment_users (member_id_send, member_id_receive, parent_comment, comment) VALUES (".$user_row["member_id"].", ".mysql_real_escape_string($_GET["u"]).", ".mysql_real_escape_string($_GET["r"]).", '".addslashes($text)."');";
				mysql_query($query) or die($query);
				if (!$_GET["r"]){
					$resc = mysql_query("SELECT * FROM comment_users WHERE member_id_send = ".mysql_real_escape_string($user_row['member_id'])." ORDER BY date DESC LIMIT 1") or die(mysql_error());
					$rowc = mysql_fetch_array($resc);
					die ('<div id="commentlist_'.$rowc["id"].'"><a href="in.php?p=profile&id='.$id.'">'.$nick.'</a><br/>'.$text.'<br/><h6 style="margin:0px;padding:0px;">'.date('h:i:s d-m-Y').' <a href="javascript:void(0)" onclick="deleteComment('.$rowc["id"].')">Eliminar</a> </h6></div>');
				} else {
					$row = fetchUser($_GET["u"]);
					die ('Has respondido a '.$row["firstname"].'<br>');
				}
			} else die ("Error");
			break;
		case "ducomment":
			if (isset($_GET["d"]) && $_GET["d"]) mysql_query("DELETE FROM comment_users WHERE id=".mysql_real_escape_string($_GET["d"])." AND member_id_send=".$user_row["member_id"])or die ("No puedes borrar este comentario");
			else die ("Error");
			break;
		///////////////////////////////////////////////////////////////
		//////Crear, editar y borrar paginas y eventos/////////////////
		///////////////////////////////////////////////////////////////	
		case "cpage":
			if (isset($_POST["titulo"])) {
				if (isset($_POST["image"]) && $_POST["image"]) $image = $_POST["image"];
				else $image = 0;
				$consulta = "INSERT INTO pages (member_id_send, topic, text, photos_id, category) VALUES (".$user_row["member_id"].", '".parseText($_POST['texto'],0, 1)."', '".parseText($_POST['texto'],1, 1)."', ".$image.", '".parseText($_POST['category'],0,1)."')";
				mysql_query($consulta) or die(mysql_error());
				$consulta = "INSERT INTO members_has_pages (member_id_send, member_id_receive, pages_id, alive) VALUES (0,  '".$user_row["member_id"]."', '".mysql_insert_id()."', 1 )";
				mysql_query($consulta) or die(mysql_error()); 
				$res = mysql_query("SELECT * FROM pages WHERE member_id_send = ".$user_row["member_id"]." order by id DESC") or die(mysql_error());
				$row = mysql_fetch_array($res);
				header('location: in.php?p=pages&id='.$row["id"]);
			} else die ("Error");
			break;
		case "cevent":
			if (isset($_POST["titulo"])) {
				if (isset($_POST["image"]) && $_POST["image"]) $image = $_POST["image"];
				else $image = 0;
				$consulta = "INSERT INTO events (member_id_send, topic, text, photos_id, date, place, contact) VALUES (".$user_row["member_id"].", '".parseText($_POST['texto'],0, 1)."', '".parseText($_POST['texto'],1,1)."', ".$image.", '".$_POST['dia']." ".$_POST['hora']."', '".$_POST['lugar']."', '".$_POST['tlf']."')";
				mysql_query($consulta) or die(mysql_error());
				$consulta = "INSERT INTO members_has_events (member_id_send, member_id_receive, events_id, alive) VALUES (0, '".$user_row["member_id"]."', '".mysql_insert_id()."', 1)";
				mysql_query($consulta) or die(mysql_error());
				$res = mysql_query("SELECT * FROM events WHERE member_id_send = ".$user_row["member_id"]." order by id DESC") or die(mysql_error());
				$row = mysql_fetch_array($res);
				header('location: in.php?p=events&id='.$row["id"]);
			} else die ("Error");
			break;
		case "epage":
			if (isset($_GET["t"]) && isset($_GET["u"])) mysql_query("UPDATE pages SET text='".parseText($_GET["t"],1)."' WHERE id='".mysql_real_escape_string($_GET["u"])."' ") or die(mysql_error());
			else die ("Error");
			break;
		case "eevent":
			if (isset($_GET["t"]) && isset($_GET["u"])) mysql_query("UPDATE events SET text='".parseText($_GET["t"],1)."' WHERE id='".mysql_real_escape_string($_GET["u"])."' ") or die(mysql_error());
			else die ("Error");
			break;
		case "dpage": 
			if (isset($_GET["d"])) {
				mysql_query("DELETE FROM pages WHERE id = '".mysql_real_escape_string($_GET["d"])."' ") or die("No puedes borrar esta paina");
				mysql_query("DELETE FROM members_has_pages WHERE pages_id = '".mysql_real_escape_string($_GET["d"])."' ") or die("No puedes borrar los usuarios vinculados a esta pagina");
				mysql_query("DELETE FROM comment_pages WHERE pages_id = '".mysql_real_escape_string($_GET["d"])."' ") or die("No puedes borrar los comentarios de esta pagina");
			} else die ("Error");
			break;
		case "devent": 
			if (isset($_GET["d"])) {
				mysql_query("DELETE FROM events WHERE id = '".mysql_real_escape_string($_GET["d"])."'") or die("No puedes borrar este evento");
				mysql_query("DELETE FROM poll_id WHERE events_id = '".mysql_real_escape_string($_GET["d"])."'") or die("No puedes borrar los usuarios vinculados a este evento");
				mysql_query("DELETE FROM comment_events WHERE events_id = '".mysql_real_escape_string($_GET["d"])."'") or die("No puedes borrar los comentarios de este evento");
			} else die ("Error");
			break;
		///////////////////////////////////////////////////////////////
		////////////////////////Sistema de MPS/////////////////////////
		///////////////////////////////////////////////////////////////	
		case "cmp":
			if (isset($_POST["receiver"])) {
				$row = fetchUser($_POST["receiver"]);
				if ($row["member_id"] && ($row["member_id"] != $user_row["member_id"])) {
					mysql_query("INSERT INTO mps (member_id_send, member_id_receive, topic, message) VALUES ('".$user_row["member_id"]."','".$row["member_id"]."', '".mysql_real_escape_string(htmlentities($_POST["topic"]))."', '".parseText($_POST["message"],1, 1)."');") or die(mysql_error());
					header("location: in.php?p=mp");
				} else {
					header("location: in.php?p=mp");
				}
			} else die ("Error");
			break;
		case "dmp":
			if (isset($_GET["d"]) && $_GET["d"]) mysql_query("DELETE FROM mp WHERE (member_id_send=".$user_row["member_id"]." OR member_id_receive=".$user_row["member_id"].") AND id = '".mysql_real_escape_string($_GET["d"])."' ") or die("No puedes borrar este MP");
			else die ("Error");
			break;
		///////////////////////////////////////////////////////////////
		//////////////////////Sistema de Spacers///////////////////////
		///////////////////////////////////////////////////////////////	
		case "cspacer":
			if (isset($_GET["t"]) && $_GET["t"]) {
				mysql_query("INSERT INTO spacers (member_id_send, text) VALUES (".$user_row["member_id"].", '".parseText($_GET["t"],1, 1)."');") or die(mysql_error());
				die (parseText($_GET["t"],1, 0));
			} else die ("Error");
			break;
		case "dspacer":
			if (isset($_GET["d"]) && $_GET["d"]) {
				mysql_query("DELETE FROM comment_spacers WHERE spacers_id=".mysql_real_escape_string($_GET["d"])) or die (mysql_error());
				mysql_query("DELETE FROM spacers WHERE member_id_send = ".$user_row["member_id"]." AND id = '".mysql_real_escape_string($_GET["d"])."' ") or die(mysql_error()."No puedes borrar este spacer");
			} else die ("Error");
			break;
		///////////////////////////////////////////////////////////////
		//////////////////////Sistema de FastNote//////////////////////
		///////////////////////////////////////////////////////////////	
		case "fastnote":
			if (isset($_GET["t"]) && $_GET["t"]) {
				mysql_query("INSERT INTO notes (member_id_send, text) VALUES (".$user_row["member_id"].", '".parseText($_GET["t"],1, 1)."') ;") or die(mysql_error());
				die (parseText($_GET["t"],1, 0));
			} else die ("Error");
		break;
		///////////////////////////////////////////////////////////////
		///////////////////////Sistema de Likes////////////////////////
		///////////////////////////////////////////////////////////////
		//ATENCION: SOLO PERMITE DARLE LIKE A FOTOS, AMPLIARLO PARA QUE SOPORTE SPACERS
		case "like":
			if (isset($_GET["type"]) ) {
				$res = mysql_query("SELECT * FROM like_photos WHERE member_id_send = ".mysql_real_escape_string($user_row['member_id'])." AND photos_id = '".mysql_real_escape_string($_GET["idimg"])."'  ") or die(mysql_error());
				if (!mysql_num_rows($res)) {	
					mysql_query("INSERT INTO like_photos (member_id_send, photos_id) VALUES (".$user_row["member_id"].", '".mysql_real_escape_string($_GET["idimg"])."' ) ;") or die(mysql_error());
					echo "Has votado correctamente";
					die ("+1 Like"); 
				} else {
					echo "Ya has likeado no puedes volver a votar.<p>";
				}
			} else die ("Error");
		break;
		///////////////////////////////////////////////////////////////
		///////////////////////Sistema de Fotos////////////////////////
		///////////////////////////////////////////////////////////////
		case "pphoto":
			if(isset($_GET['setimg']) && $_GET['setimg']){
				mysql_query("UPDATE members SET prf_img=".mysql_real_escape_string($_GET['setimg'])." WHERE member_id='".$user_row["member_id"]."' ") or die(mysql_error());
				echo "Hecho";
				unset($_SESSION["user_row"]); 
			} else die ("Error");
			break;
		case "ephoto":
			if (isset($_POST["nombre"])) mysql_query("UPDATE images SET name='".mysql_real_escape_string($_POST["nombre"])."' WHERE  id='".mysql_real_escape_string($_POST["id_img"])."' ") or die(mysql_error());
			 else die ("Error");
			break;
		case "sphoto":
			if (isset($_GET["s"]) && $_GET["s"]) {
				$page = $_GET["s"]*3;
				$res2 = mysql_query("SELECT * FROM photos WHERE member_id_send='".$user_row["member_id"]."' ORDER BY id DESC LIMIT $page,3") or die(mysql_error());      
				if (mysql_num_rows($res2)) {
					while ($row = mysql_fetch_array($res2)) echo '<a href="javascript:void(0)" onclick="selectImage(\''.$row["id"].'\');"><img src="tmp/small/'.$row["image"].'" width="100" height="100" style="margin:3px;"></a>'; 
				} 
			} else die ("Error");
			break;
		case "dphoto": 
			if (isset($_GET["id"]) && $_GET["id"]) {
				$res = mysql_query("SELECT * FROM photos WHERE id='".mysql_real_escape_string($_GET["id"])."' AND member_id_send=".$user_row["member_id"]) or die(mysql_error());
				if (mysql_num_rows($res)) {
					$row = mysql_fetch_array($res);
					mysql_query("DELETE FROM comment_photos WHERE photos_id='".mysql_real_escape_string($_GET["id"])."'") or die("No puedes borrar los comentarios de esta pagina");
					mysql_query("DELETE FROM photos WHERE id='".mysql_real_escape_string($_GET["id"])."'") or die("No se pudo borrar esta imagen");
					unlink("tmp/".$row["image_url"]);
					unlink("tmp/small/".$row["image_url"]);
				} else die ("SELECT * FROM photos WHERE id='".mysql_real_escape_string($_GET["id"])."' AND member_id_send=".$user_row["member_id"]." "."Error: id erronea");
			} else die ("Error: debe enviarse una ID");
		break;
		case "uphoto": 
			$upload_dir  = 'tmp';
			$upload_small_r  = 'tmp/small';
			$array = array();
			$num_files = count($_FILES['user_file']['name']);
			for ($i=0; $i < $num_files; $i++) {
				$array[$i]["id"] = $i;
				$partes_ruta = pathinfo($upload_dir . "/" . basename($_FILES['user_file']['name'][$i]));
				$upload_name = $_SESSION['SESS_MEMBER_ID'].time().$i.".".$partes_ruta['extension'];
				$upload_file = $upload_dir . "/" .$upload_name;
				$upload_small = $upload_small_r . "/" .$upload_name;
				$ext = strtolower($partes_ruta['extension']);
				if (!preg_match("/(gif|jpg|jpeg|png|bmp)$/i",$_FILES['user_file']['name'][$i])) {
					$array[$i]["status"] = "El archivo ".$_FILES['user_file']['name'][$i]."que ha introducido no es una imagen valida";
				} else {
					if (is_uploaded_file($_FILES['user_file']['tmp_name'][$i])) {
						if (filesize($_FILES['user_file']['tmp_name'][$i]) < 5*1024*1024) {
							if (move_uploaded_file($_FILES['user_file']['tmp_name'][$i], $upload_file)) {
								image_resize($upload_file, $upload_file, 1280, 720);
								$tam=getimagesize($upload_file);  
								if(($tam[0] > 200 || $tam[1] > 200) && $ext != "gif") { 
									image_resize($upload_file, $upload_small, 200, 200);
								} else {
									copy($upload_file, $upload_small);
								}
								mysql_query("INSERT INTO photos (member_id_send, image_url) VALUES ('".$_SESSION['SESS_MEMBER_ID']."', '".$upload_name."');") or die(mysql_error());
								$array[$i]["pid"] = mysql_insert_id($link);
								$array[$i]["status"] = "OK";
							} else $array[$i]["status"] = "El archivo ".$_FILES['user_file']['name'][$i]." no se ha subido correctamente";
						} else $array[$i]["status"] = "El archivo ".$_FILES['user_file']['name'][$i]." que ha introducido supera el tamaÒo maximo permitido (5MB)";
					} else $array[$i]["status"] = "El archivo ".$_FILES['user_file']['name'][$i]." no se ha subido correctamente";
				}
			}
			die(json_encode($array));
		break;
		///////////////////////////////////////////////////////////////
		///////////////////////Sistema de Perfil///////////////////////
		///////////////////////////////////////////////////////////////
		case "eprofile":
			if (isset($_POST["enviar"])) {
				$age = $_POST['ano'].'-'.$_POST['mes'].'-'.$_POST['dia'];
				$query = "UPDATE members SET birthdate='$age', city='".mysql_real_escape_string($_POST["city"])."', iam=".mysql_real_escape_string($_POST["iam"])." WHERE member_id=".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID']);
				mysql_query($query) or die(mysql_error()." in query ".$query);
				unset($_SESSION["user_row"]);
				header("location: in.php");
			} else die ("Error");
			break;	
		case "edata":
			if (isset($_POST['enviar'])) {
				$nombre = $_POST['nombre'];
				$apellido = $_POST['apellidos'];
				$email = $_POST['email'];
				$pass = $_POST['pass'];
				$pass2 = $_POST['pass2'];
				$consulta = "UPDATE members SET firstname='$nombre', lastname='$apellido'" ;
				if ($email && checkEmail($email)) $consulta.= ", email='".$email."'";
				if ($pass && (strlen($password)) > 25 || strlen($password) < 6) {
					if ($pass == $pass2) $consulta .= ", passwd='".md5($pass)."'";
					else die("Las contraseÒas no coinciden");
				}
				$consulta .= " WHERE member_id='".$_SESSION['SESS_MEMBER_ID']."' ";
				$res = mysql_query($consulta) or die(mysql_error());
				unset($_SESSION["user_row"]);
				header("location: in.php?p=option");
			}
		break;
		case "emoreinfo":
			if (isset($_POST['enviar2'])) {
				if (isset($_POST["bio"])) $res = mysql_query("UPDATE members SET bio='".$_POST["bio"]."' WHERE member_id='".$_SESSION['SESS_MEMBER_ID']."'") or die(mysql_error());
				if (isset($_POST["ilikeit"])) $res = mysql_query("UPDATE members SET ilikeit='".$_POST["ilikeit"]."' WHERE member_id='".$_SESSION['SESS_MEMBER_ID']."'") or die(mysql_error());
				unset($_SESSION["user_row"]);
				header("location: in.php?p=option");
			}
		break;
		///////////////////////////////////////////////////////////////
		///////////////////////Sistema de Amigos///////////////////////
		///////////////////////////////////////////////////////////////		
		case "searchfriend":
			if (isset($_GET["term"]) && $_GET["term"]) {
				$myterm = mysql_real_escape_string($_GET["term"]);
				header("Content-Type: application/json" );
				$arr = array();
				if ($friendArray) {
					if (isset($_GET["canme"]) && $_GET["canme"]){
						$lil_arr["id"] = $user_row["member_id"];
						$lil_arr["label"] = utf8_encode($user_row["firstname"]. " " .$user_row["lastname"]);
						$lil_arr["value"] = utf8_encode($user_row["firstname"]. " " .$user_row["lastname"]);
						$arr[] = $lil_arr;
					}
					$consulta = "SELECT * FROM members WHERE member_id IN (".implode(",", $friendArray).") AND (firstname LIKE '$myterm%' OR lastname LIKE '$myterm%')";
					$res = mysql_query($consulta) or die(mysql_error());
					while ($row=mysql_fetch_array($res)) {
						$lil_arr["id"] = $row["member_id"];
						$lil_arr["label"] = utf8_encode($row["firstname"]. " " .$row["lastname"]);
						$lil_arr["value"] = utf8_encode($row["firstname"]. " " .$row["lastname"]);
						$arr[] = $lil_arr;
					}
				} else {
					$arr[]["value"] = utf8_encode("No tienes amigos");
				}
				die( json_encode($arr));
			}
		break;
		case "addfriend":
			if (isset($_GET["f"]) && $_GET["f"] != 0) {
				$user_row2 = fetchUser($_GET["f"]);
				if ($user_row2["member_id"] != $user_row["member_id"]) {			
					$res = mysql_query("SELECT * FROM friends WHERE (member_id_send='".mysql_real_escape_string($user_row2["member_id"])."' AND member_id_receive='".mysql_real_escape_string($user_row["member_id"])."') OR (member_id_send='".mysql_real_escape_string($user_row["member_id"])."' AND member_id_receive='".mysql_real_escape_string($user_row2["member_id"])."')") or die("17: ".mysql_error());
					$row = mysql_fetch_array($res);			
					if (mysql_num_rows($res) == 0) {
						mysql_query("INSERT INTO friends (member_id_send, member_id_receive) VALUES ('".mysql_real_escape_string($user_row["member_id"])."','".mysql_real_escape_string($user_row2["member_id"])."');") or die("20: ".mysql_error());
						echo "Tu invitacion se ha enviado correctamente";
					} else if (mysql_num_rows($res) && $row["member_id_send"] != $user_row["member_id"]) {			
						mysql_query("UPDATE friends SET alive=1 WHERE member_id_send='".mysql_real_escape_string($row["member_id_send"])."' AND member_id_receive='".mysql_real_escape_string($row["member_id_receive"])."'") or die("23: ".mysql_error());
						?>
						<html>
							<head>
							<style>
							#resultado {
								position:absolute;
								top:-38%;
								left:5%;
								width:50%;
								height:20%;
								opacity:1;
								background-color:green;
								border:1px solid green;
								border-radius:20px;
								color:white;
								text-align:center;
							}

							#resultado p {
								margin-top:30px;
								font-size:30px;
							}
							</style>	
							</head>
							<body>
								<div id="resultado">
									<p>Amigo agregado</p>
									</div>
							</body>
								</html><?
						//Creamos un array con la lista de amigos
						$res = mysql_query("SELECT * FROM friends WHERE (member_id_send=".mysql_real_escape_string($user_row["member_id"])." OR member_id_receive=".mysql_real_escape_string($user_row["member_id"]).") AND alive=1") or die(mysql_error());
						if (mysql_num_rows($res)) {
							while ($row = mysql_fetch_array($res)) {
								if ($row["member_id_send"] != $user_row["member_id"]) $u_row = $row["member_id_send"];
								else if ($row["member_id_receive"] != $user_row["member_id"]) $u_row = $row["member_id_receive"]; 
								$friendArray[] = $u_row;
							}
							sort($friendArray);
						} else $friendArray = 0;
						$_SESSION["user_row"]["friendlist"] = $friendArray;
					} else echo "ERROR";
				} else echo "ERROR";
			}
		break;
		case "delfriend":
			if (isset($_GET["nf"]) && $_GET["nf"] != 0) {
				$user_row2 = fetchUser($_GET["nf"]);
				if (in_array($user_row2["member_id"], $friendArray)) {
					$query = "DELETE FROM friends WHERE (member_id_send='".mysql_real_escape_string($user_row2["member_id"])."' AND member_id_receive='".mysql_real_escape_string($user_row["member_id"])."') OR (member_id_send='".mysql_real_escape_string($user_row["member_id"])."' AND member_id_receive='".mysql_real_escape_string($user_row2["member_id"])."')";
					mysql_query($query) or die($query.": ".mysql_error());
					//Creamos un array con la lista de amigos
					$res = mysql_query("SELECT * FROM friends WHERE (member_id_send=".mysql_real_escape_string($user_row["member_id"])." OR member_id_receive=".mysql_real_escape_string($user_row["member_id"]).") AND alive=1") or die(mysql_error());
					if (mysql_num_rows($res)) {
						while ($row = mysql_fetch_array($res)) {
							if ($row["member_id_send"] != $user_row["member_id"]) $u_row = $row["member_id_send"];
							else if ($row["member_id_receive"] != $user_row["member_id"]) $u_row = $row["member_id_receive"]; 
							$friendArray[] = $u_row;
						}
						sort($friendArray);
					} else $friendArray = 0;
					$_SESSION["user_row"]["friendlist"] = $friendArray;
					die("ok");
				} else echo "No es tu amigo";
			}
		break;
		case "ftype":
			if (isset($_GET["t"]) && ((isset($_GET["id"]) && $_GET["id"]) && in_array($_GET["id"], $friendArray))) {
				mysql_query("UPDATE friends SET ftype=".mysql_real_escape_string($_GET["t"])." WHERE id=".mysql_real_escape_string($_GET["id"])) OR die ("ERROR: ".mysql_error());
				die ("OK");
			} else die ("ERROR");
		break;
		///////////////////////////////////////////////////////////////
		/////////////////////Sistema de Paginacion/////////////////////
		///////////////////////////////////////////////////////////////
		case "pnalbum":
			if (isset($_GET["pg"])) $pg = $_GET["pg"];
			else die("ERROR");
			if (isset($_GET["id"])) $user = $_GET["id"];
			else $user = $user_row["member_id"];
			$pag = new Pagination("SELECT * FROM photos WHERE member_id_send='".mysql_real_escape_string($user)."' ORDER BY id DESC");
			$pag->pgSelectItemperPage(9);
			$pag->pgSelectType("album");
			$pag->pgSelectActualPage($pg);
			$res = $pag->pgDoPagination();
			if (!mysql_num_rows($res)) echo "No tienes fotos, subelas";
			else {
				while ($row = mysql_fetch_array($res)) echo '<a href="in.php?p=photo&id='.$row["id"].'&m=1" title="'.$row["name"].'"><img class="photoalbum" src="tmp/small/'.$row["image_url"].'" width="200" height="200" style="margin:3px;"></a>'; 
			} 
			echo $pag->pgShowAjaxPagination (", ".$user);
		break;
		case "pnfastnotes":
			if (isset($_GET["pg"])) $pg = $_GET["pg"];
			else die("ERROR");
			$pag2 = new Pagination("SELECT * FROM notes WHERE member_id_send='".mysql_real_escape_string($user_row["member_id"])."' ORDER BY id DESC") or die(mysql_error());   
			$pag2->pgSelectItemperPage(10); 
			$pag2->pgSelectActualPage($pg);
			$pag2->pgSelectType("fastnotes");
			$res = $pag2->pgDoPagination();
			if (!mysql_num_rows($res)) echo "No tienes Notas rapidas";
			else {
				echo '<div id="comments_notes"><div id="pgfastnotes">';
				while ($row = mysql_fetch_array($res)) {
					$fecha = date_create($row["date"]);
					$fecha = date_format($fecha, 'H:i:s d-m-Y');
					echo "<span id='notatext'>".$row["text"].'<br/>'.$fecha."<hr></span>"; 
				}
				echo $pag2->pgShowAjaxPagination();
				echo "</div></div>";
			} 
		break;
		case "pnspickphoto":
			if (isset($_GET["pg"])) $pg = $_GET["pg"];
			else die("ERROR");
			$pag3 = new Pagination("SELECT * FROM comment_photos WHERE to_photo_id='".mysql_real_escape_string($_GET["id"])."' ORDER BY id DESC") or die(mysql_error()); 
			$pag3->pgSelectItemperPage(8); 
			$pag3->pgSelectActualPage($pg);
			$pag3->pgSelectType("spickphoto");
			$res = $pag3->pgDoPagination();
			if (!mysql_num_rows($res["query_result"])) echo "No tienes Comentarios";
			else {
				while ($line = mysql_fetch_assoc($res["query_result"])) {
					$u_row = fetchUser($line['member_id_send']);
					echo '<div id="commentlist_'.$line["id"].'"> <a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"]." ".$u_row["lastname"].'</a> <br/>'.$line['comment'].'<br/><h6 style="margin:0px;padding:0px;">'.$line["date"].'</h6>';
					if ($line['member_id_send'] == $user_row["member_id"]) echo ' - <a href="javascript:void(0)" onclick="deleteCommentPhoto(\''.$line['id'].'\')">Eliminar</a><hr width="80%">';
					echo '</div>';
				}	
				echo $pag3->pgShowAjaxPagination();
			} 
		break;
		case "pnmpin":
			if (isset($_GET["pg"])) $pg = $_GET["pg"];
			else die("ERROR");
			$resIn = new Pagination("SELECT * FROM mps WHERE member_id_receive='".$user_row["member_id"]."' ORDER BY date DESC");
			$resIn->pgSelectItemperPage(5); 
			$resIn->pgSelectType("mpin");
			$resIn->pgSelectActualPage($pg);
			$resin = $resIn->pgDoPagination();	 
			?>
					<table border="0" width="100%" id="bordertable" cellspacing="0">
						<tr>
							<th style="position: relative; text-align: left;"><span>Asunto</span></th>
							<th style="position: relative; text-align: left;"><span>De</span></th>
							<th style="position: relative; text-align: left;"><span>Estado</span></th>
							<th style="position: relative; text-align: left;"><span>Fecha</span></th>
							<th style="position: relative; text-align: left;"><span>Borrar</span></th>
						</tr>
					<?php 
						while ($row = mysql_fetch_array($resin)) {
							$raw = fetchUser($row["member_id_send"]);
							if ($row["readed"] == 1) $readed = '<span style="color:#00AB00;">Leido</span>';  
							else $readed = '<span style="color:red;">No leido</span>'; 
							echo '<tr id="mp_'.$row["id"].'">
								<td onclick="mpLink('.$row['id'].')"><span>'.$row['topic'].'</span></td>
								<td onclick="mpLink('.$row['id'].')"><span>'.$raw["firstname"]." ".$raw["lastname"].'</span></td>
								<td onclick="mpLink('.$row['id'].')"><span>'.$readed.'</span></td>
								<td onclick="mpLink('.$row['id'].')"><span>'.$row["date"].'</span></td>
								<td><a href="#" style="color:red;" onclick="deleteMp('.$row['id'].')">Eliminar</a></td>
								</tr></span>';
						}
						echo $resIn->pgShowAjaxPagination (); ?>
					</table>
			<?php 
		break;
		case "pnmpout":
			if (isset($_GET["pg"])) $pg = $_GET["pg"];
			else die("ERROR");
			$resOut = new Pagination("SELECT * FROM mps WHERE member_id_send='".$user_row["member_id"]."' ORDER BY date DESC");
			$resOut->pgSelectItemperPage(5); 
			$resOut->pgSelectActualPage($pg);
			$resOut->pgSelectType("mpout");
			$resout = $resOut->pgDoPagination();	
			?>
				<table border="0" width="100%" id="bordertable" cellspacing="0">
						<tr>
							<th style="position: relative; text-align: left;"><span>Asunto</span></th>
							<th style="position: relative; text-align: left;"><span>Para</span></th>
							<th style="position: relative; text-align: left;"><span>Fecha</span></th>
							<th style="position: relative; text-align: left;"><span>Borrar</span></th>
						</tr>
						<?php 
						while ($row = mysql_fetch_array($resout)) { 
							$raw = fetchUser($row["member_id_receive"]);
							echo '<tr id="mp_'.$row["id"].'">
								<td onclick="mpLink('.$row['id'].')"><span>'.$row['topic'].'</span></td>
								<td onclick="mpLink('.$row['id'].')"><span>'.$raw["firstname"]." ".$raw["lastname"].'</span></td>
								<td onclick="mpLink('.$row['id'].')"><span>'.$row["date"].'</span></td>
								<td><a href="#" style="color:red;" onclick="deleteMp('.$row['id'].')">Eliminar</a></td>
								</tr>';
						}
						echo $resOut->pgShowAjaxPagination();?>
				</table>
			<?php
		break;
		case "pnpage":
			if (isset($_GET["pg"])) $pg = $_GET["pg"];
			else die("ERROR");
			$res = pagination("SELECT * FROM comment_pages WHERE pages_id=".mysql_real_escape_string($_GET["id"])." ORDER BY date DESC", 10, $pg);				
			
			if (!mysql_num_rows($res["query_result"])) echo "No tienes Comentarios";
			else {
				while ($row = mysql_fetch_array($res["query_result"])) {
					$fecha = date_create($row["date"]);
					$fecha = date_format($fecha, 'H:i:s d-m-Y');
					$u_row = fetchUser($row['member_id_send']);
					echo '<div id="commentlist_'.$row["id"].'"> <a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"]." ".$u_row["lastname"].'</a> <br/>'.$row['comment'].'<br/><h6 style="margin:0px;padding:0px;">'.$fecha.'</h6><hr></div>';
					if ($row["member_id_send"] == $user_row["member_id"]) echo ' - <a href="#" onclick="deletePageComment(\''.$row['id'].'\')">Eliminar</a>';
				}
				echo doPagination($res["n_pages"], 0, 0, $pg, "page", ", ".$_GET["id"]);
			}
		break;
		case "pnevento":
			if (isset($_GET["pg"])) $pg = $_GET["pg"];
			else die("ERROR");
			if (isset($_GET["id"])) $user = $_GET["id"];
			$pag5 = new Pagination("SELECT * FROM comment_events WHERE events_id='".mysql_real_escape_string($user)."' ORDER BY date DESC") or die(mysql_error()); 
			$pag5->pgSelectItemperPage(8); 
			$pag5->pgSelectActualPage($pg);
			$pag5->pgSelectType("evento");
			$res = $pag5->pgDoPagination();
			if (!mysql_num_rows($res)) echo "No tienes Comentarios";
			else {
				while ($line = mysql_fetch_assoc($res)) {
					$u_row = fetchUser($line['member_id_send']);
					echo '<div id="commentlist_'.$line["id"].'"> <a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"]." ".$u_row["lastname"].'</a> <br/>'.$line['text'].'<br/><h6 style="margin:0px;padding:0px;">'.$line["date"].'</h6>';
					if ($line['member_id_send'] == $user_row["member_id"]) echo ' - <a href="javascript:void(0)" onclick="deleteEventComment(\''.$line['id'].'\')">Eliminar</a><hr width="80%">';
					echo '</div>';
				}	
				echo $pag5->pgShowAjaxPagination();
			} 
		break;
		case "pnspickline":
			if (isset($_GET["pg"])) $pg = $_GET["pg"];
			else die("ERROR");
			$user = $_GET["id"];
			$pag3 = new Pagination("SELECT id, member_id_send, parent_comment, text, date FROM comment_users WHERE member_id_receive = ".mysql_real_escape_string($user)." UNION SELECT id, member_id_send, 1, text, date FROM spacers WHERE member_id_send = ".mysql_real_escape_string($user)." ORDER BY date DESC") or die(mysql_error());   
			$pag3->pgSelectItemperPage(10); 
			$pag3->pgSelectActualPage($pg);
			$pag3->pgSelectType("spickline");
			$res = $pag3->pgDoPagination();	
			while ($row = mysql_fetch_array($res)) {
				$fecha = date_create($row["date"]);
				$fecha = date_format($fecha, 'H:i:s d-m-Y');
				if ($row["member_id_send"] == $user){
					echo '<div id="spacerlist_'.$row["id"].'" class="spacerlist"><a name="spacer_'.$row["id"].'"></a>';
					if ($row["member_id_send"] == $user_row["member_id"]) echo  '<div class="delspacer" onclick="deleteSpacer(\''.$row['id'].'\')">X</div>';
					echo $row['text'].'<h5>'.$fecha.'</h5><br>';
					?>
				<form style="margin: 0px">
					<textarea id="spickspacertext" name="space" rows="2" cols="40" maxlength="130" onkeyup="document.getElementById('cuenta_<?php echo $row["id"]; ?>').value=130-this.value.length" ></textarea><input type="button" name="cuenta" class="cuenta" size="3" id="cuenta_<?php echo $row["id"]; ?>" value="130"  style="position: relative; bottom: 14px;"/>
					<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('spickspacertext').value != '') sendSpacerComment('<?php echo $row['id']; ?>')" style="position: relative; bottom: 15px;">  
				</form><?php
						$pag4 = new Pagination("SELECT id, member_id_send, text, date FROM comment_spacers WHERE spacers_id = ".mysql_real_escape_string($row['id'])." ORDER BY date DESC");
						$pag4->pgSelectItemperPage(10); 
						$pag4->pgSelectType("commentspacer_container");
						$res2 = $pag4->pgDoPagination();		
						echo '<div id="pgcommentspacer_container" style="margin-left: 20px">';
						if (mysql_num_rows($res2)) {
							while ($drow = mysql_fetch_array($res2)) {
								$fecha = date_create($drow["date"]);
								$fecha = date_format($fecha, 'H:i:s d-m-Y');
								$u_row = fetchUser($drow["member_id_send"]);
								echo "<div id='commentspacerlist_".$drow['id']."'>".$u_row["firstname"]." ".$u_row["lastname"].' a las '.$fecha.': '.$drow['text'];
								if ($u_row["member_id"] == $user_row["member_id"]) echo ' - <a href="javascript:void(0)" onclick="deleteSpacerComment(\''.$drow['id'].'\')">Eliminar</a>';
								echo "</div>";
							}
							echo $pag4->pgShowAjaxPagination (", ".$user);
						}
						echo "</div></div>";
					} else {
						$u_row = fetchUser($row['member_id_send']);
						if ($row["member_id_send"] != $user_row["member_id"]) $postextras = ' - <a href="javascript:void(0)" onclick="formResponse(\''.$row['id'].'\', \''.$u_row["member_id"].'\')">Responder</a>';
						else $postextras = ' - <a href="javascript:void(0)" onclick="deleteComment(\''.$row['id'].'\')">Eliminar</a>';
						if ($row["parent_comment"]) { 
							$rest = mysql_query("SELECT * FROM comment_users WHERE id=".$row["parent_comment"]);
							$rowt = mysql_fetch_array($rest);
							$preextras = '<h6>RP: '.$rowt["text"].'</h6>';
						} else $preextras = "";
						echo '<div id="commentlist_'.$row["id"].'"><a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"]." ".$u_row["lastname"].'</a> <br/>'.$preextras.$row['text'].'<br/><h5 style="margin:0px;padding:0px;">'.$fecha.$postextras.'</h5><hr></div>';
					}
			}
			echo $pag3->pgShowAjaxPagination (", ".$user);
		break;
		case "pnfriends":
			if (isset($_GET["pg"])) $pg = $_GET["pg"];
			else die("ERROR");
			$pag = new Pagination("SELECT * FROM members WHERE member_id IN (".implode(",", $friendArray).")") or die (mysql_error());
			$pag->pgSelectItemperPage(10); 
			$pag->pgSelectActualPage($pg);
			$pag->pgSelectType("friends");
			$res = $pag->pgDoPagination();	
			while ($u_row = mysql_fetch_array($res)) {	?>	
				<table id="fr_<?php echo $u_row["member_id"]; ?>">
					<tr>
						<td id="bordert" align="left">
							<div id="info_friends">
								<?php  
								if ($u_row["prf_img"] != 0) {
									$resa = mysql_query("SELECT * FROM photos WHERE id=".mysql_real_escape_string($u_row["prf_img"])) or die(mysql_error());   
									if (!mysql_num_rows($resa)) $image="resources/images/default.png";
									else { 
										$rowa = mysql_fetch_array($resa);
										$image="tmp/small/".$rowa["image_url"]; 
									}
								} else $image="resources/images/default.png";
								?>
							</div>
							<div id="info_container">
								<?php
								echo "<a href=in.php?p=profile&id=".$u_row["member_id"]."><h2>".$u_row["firstname"]." ".$u_row["lastname"]."</h2></a>";
								echo '<img src="'.$image.'" >';
								?>
							</div>
							<div id="info_container2">
								<?php
								if ($u_row["birthdate"] != "0000-00-00") echo '<b>Edad:</b>'.CalculaEdad($u_row["birthdate"]).'<br>';
								if ($u_row["ciudad"]) echo '<b>Ciudad:</b> '.$u_row["city"].'<br>';
								echo "<b>Sexo:</b>"; if ($u_row["sex"] == 0) echo "No especificado"; else if ($u_row["sex"] == 1) echo "Hombre"; else echo "Mujer";
								?> 
								<br><select id="select_groups" onchange="setFriendType(this.value, <?php echo $row["id"]; ?>);">
									<option value="0" <?php if ($row["ftype"] == 0) echo "selected"; ?>>Sin Grupo</option>
									<option value="1" <?php if ($row["ftype"] == 1) echo "selected"; ?>>Amigos</option>
									<option value="2" <?php if ($row["ftype"] == 2) echo "selected"; ?>>Familia</option>
									<option value="3" <?php if ($row["ftype"] == 3) echo "selected"; ?>>Compa&ntilde;eros de Trabajo</option>
								</select>
							</div>
						</td>
					</tr>
					<?php if (!isset($_GET["u"])) { ?>
					<tr>
						<td class="delete">
							<a href="javascript:void(0);" onclick="deleteFriend(<?php echo $u_row['member_id']; ?>);">Eliminar amigo</a>
						</td>
					</tr>
					<?php } ?>
				</table>
				<hr>
<?php		}
			echo $pag->pgShowAjaxPagination();
		break;
		case "getcal":
			if ((isset($_GET["month"]) && $_GET["month"]) && (isset($_GET["year"]) && $_GET["year"])) {
				$myarray = array();
				$res = mysql_query("SELECT DAY(birthdate) as bday, member_id, firstname, lastname FROM members WHERE member_id IN (".implode(",", $friendArray).") AND MONTH(birthdate) = ".$_GET["month"]);
				if (mysql_num_rows($res)) {
					while ($row = mysql_fetch_array($res)) {
						$myarray[] = array("day" => $row["bday"], "type" => 1, "data" => array("uid" => $row["member_id"], "name" => $row["firstname"]." ".$row["lastname"]));
					}
				}
				$res = mysql_query("SELECT DAY(date) as eday, id, topic, member_id_send, photos_id FROM events WHERE id IN (SELECT events_id FROM members_has_events WHERE member_id_receive=".$user_row["member_id"]." AND alive=1) AND MONTH(date) = ".$_GET["month"]." AND YEAR(date) = ".$_GET["year"]);
				if (mysql_num_rows($res)) {
					while ($row = mysql_fetch_array($res)) {
						$u_row = fetchUser($row["member_id_send"]);
						$myarray[] = array("day" => $row["eday"], "type" => 2, "data" => array("eid" => $row["id"], "topic" => $row["topic"], "imgid" => $row["photos_id"], "imgurl" => getProfilePhotoByPhotoId($row["photos_id"]), "ownerid" => $row["member_id_send"], "ownername" => $u_row["firstname"]." ".$u_row["lastname"]));
					}
				}
				die(json_encode($myarray));
			} else die("ERROR");
		break;
		default:
			die ("ERROR: opcion inexistente");
		break;
	}
	die();
}
