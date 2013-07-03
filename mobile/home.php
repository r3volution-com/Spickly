<?php if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>");
 ?>
 <div id="page">
	<div id="sidebar">
		<div id="personalinfo" class="sideblock">
			<b><?php echo $user_row["firstname"]." ".$user_row["lastname"]; ?></b>
			<div id="perfil_photo">
				<?php 
				if ($user_row["prf_img"] != 0) {
					$res = mysql_query("SELECT * FROM photos WHERE id=".mysql_real_escape_string($user_row["prf_img"])) or die(mysql_error());   
					if (!mysql_num_rows($res)) $image="../resources/images/default.png";
					else { 
						$row = mysql_fetch_array($res);
						$image="../tmp/small/".$row["image_url"]; 
					}
				} else $image="../resources/images/default.png";
				echo '<img src="'.$image.'" width="200px" height="200px">'; 
				?>
			</div>
			<div id="chat">
				<h1 id="chatbuttons">Chat</h1>
				<hr>
				<ul id="chatlist">
					<li>Chat desactivado temporalmente...</li>
				</ul>
			</div>
		</div>
		<div id="patrocinados" class="sideblock">
			<h2>Patrocinadores</h2>
			<hr>
			<!--<div id="forocoches">
				<a href="http://www.forocoches.com" target="_blank"><img align="center" style="width:200px; height:100px;" src="../resources/images/patrocinados/forocoches.gif"></a>
				<h4>Forocoches, el foro para los amantes del motor</h4>
				<hr>
				</div>-->
				<a href="partners.php">¿Quieres ser Patrocinador?</a>
		</div>
	</div>
	<div id="spacer">
		<?php
		if (($user_row['member_id'] == $_SESSION['SESS_MEMBER_ID'])) {
			?>
			<h1>Spacer <small>¿En qué piensas?</small></h1>
			<hr>
			<form style="margin: 0px">
				<textarea id="textspacer" placeholder="¿En que piensas?" name="space" rows="2" cols="60" maxlength="130" onkeyup="document.getElementById('cuenta').value=130-this.value.length" ></textarea><input type="text" name="cuenta" size="3" id="cuenta" value="130" style="position: relative; bottom: 14px;" />
				<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('textspacer').value != '') sendSpacer()" style="position: relative; bottom: 14px;">  
			</form>

			<?
		} else {
			echo '<h1>Spacer <small>¿En qué piensa?</small></h1>';
		}
		?>
		<?php
		$space = mysql_query("SELECT id, text FROM spacers WHERE member_id_send='".mysql_real_escape_string($user_row['member_id'])."'ORDER BY date DESC LIMIT 1") or die(mysql_error());
		$row = mysql_fetch_array($space);
		if (mysql_num_rows($space)) echo "<b>Ultimo spacer:</b> <span id='pgspacertext'>".$row["text"]."</span>";
		else echo "<b>Aun no tienes ningun Spacer!</b>";
		?>
	</div>
	<div id="container">
			<?php
			$user = $user_row["member_id"];
			$myarray = array();
			$res = mysql_query("SELECT * FROM members_has_pages WHERE member_id_receive='".mysql_real_escape_string($user)."' AND alive=0 ORDER BY alive") or die(mysql_error());
			if (mysql_num_rows($res)) {
				$myarray[] = array("type" => 1, "date" => 0, "data" => mysql_num_rows($res));
				while ($row = mysql_fetch_array($res)) {
					$res2 = mysql_query("SELECT * FROM pages WHERE id=".$row["pages_id"]);
					$row2 = mysql_fetch_array($res2);
					$myarray[] = array("type" => 1, "date" => $row["date"], "from" => $row["member_id_send"], "data" => array("pageid" => $row["pages_id"], "topic" => $row2["topic"], "pid" => $row2["photos_id"]));   
				}
			}
			$res = mysql_query("SELECT * FROM friends WHERE member_id_receive=".$user_row["member_id"]." AND alive=0") or die(mysql_error());
			if (mysql_num_rows($res)) {
				$myarray[] = array("type" => 2, "date" => 0, "data" => mysql_num_rows($res));
				while ($row = mysql_fetch_array($res)) {
					if ($row["member_id_send"] != $user_row["member_id"]) $u = $row["member_id_send"]; 
					if ($row["member_id_receive"] != $user_row["member_id"]) $u = $row["member_id_receive"]; 
					$myarray[] = array("type" => 2, "date" => $row["date"], "from" => $u);
				}
			}
			$res = mysql_query("SELECT * FROM comment_users WHERE member_id_receive=".$user_row["member_id"]." AND date > '".$user_row["last_visit"]."'") or die(mysql_error());
			if (mysql_num_rows($res)) {
				$myarray[] = array("type" => 3, "date" => 0, "data" => mysql_num_rows($res));
				while ($row = mysql_fetch_array($res)) $myarray[] = array("type" => 3, "date" => $row["date"], "from" => $row["member_id_send"], "data" => $row["text"]);  
			}
			$res = mysql_query("SELECT comment_spacers.member_id_send as member_id, comment_spacers.text, comment_spacers.date as datec, spacers.id as spaid FROM comment_spacers, spacers WHERE spacers.member_id_send=".$user_row["member_id"]." AND spacers_id=spacers.id AND comment_spacers.member_id_send!=".$user_row["member_id"]." AND comment_spacers.date > '".$user_row["last_visit"]."'") or die(mysql_error());
			if (mysql_num_rows($res)) {
				$myarray[] = array("type" => 4, "date" => 0, "data" => mysql_num_rows($res));
				while ($row = mysql_fetch_array($res)) $myarray[] = array("type" => 4, "date" => $row["datec"], "from" => $row["member_id"], "data" => array("id" => $row["spaid"], "text" => $row["text"]));
	
			}
			$res = mysql_query("SELECT comment_photos.date as dates, comment_photos.member_id_send as userid, comment_photos.text as commentext, photos.id as imgid, photos.image_url as imgurl FROM comment_photos, photos WHERE photos_id=photos.id AND photos.member_id_send=".$user_row["member_id"]." AND comment_photos.date > '".$user_row["last_visit"]."'") or die(mysql_error());
			if (mysql_num_rows($res)) {
				$myarray[] = array("type" => 5, "date" => 0, "data" => mysql_num_rows($res));
				while ($row = mysql_fetch_array($res)) $myarray[] = array("type" => 5, "date" => $row["dates"], "from" => $row["userid"], "data" => array("text" => $row["commentext"], "image_id" => $row["imgid"], "image" => $row["imgurl"]));   
			}	
			$res = mysql_query("SELECT * FROM mps WHERE member_id_receive=".$user_row["member_id"]." AND date > '".$user_row["last_visit"]."'") or die(mysql_error());
			if (mysql_num_rows($res)) {
				$myarray[] = array("type" => 6, "date" => 0, "data" => mysql_num_rows($res));
				while ($row = mysql_fetch_array($res)) $myarray[] = array("type" => 6, "date" => $row["date"], "from" => $row["member_id_send"], "data" => array("id" => $row["id"], "topic" => $row["topic"]));
			}
			if ($friendArray) {
				$res = mysql_query("SELECT * FROM members WHERE extract(month from birthdate) = extract(month from current_date) AND extract(day from birthdate) > extract(day from current_date) AND member_id IN (".implode(",", $friendArray).") ORDER BY birthdate DESC") or die(mysql_error());
				if (mysql_num_rows($res)) {
					$myarray[] = array("type" => 7, "date" => 0, "data" => mysql_num_rows($res));
					while ($row = mysql_fetch_array($res)) $myarray[] = array("type" => 7, "date" => $row["birthdate"], "data" => array("id" => $row["member_id"], "name" => $row["firstname"]." ".$row["lastname"]));
				}
			}
			$res = mysql_query("SELECT *, tags.date as tagdate, tags.member_id_send as tagto FROM tags, photos WHERE tags.member_id_receive=".$user_row["member_id"]." AND photos.id=tags.photos_id AND tags.date > '".$user_row["last_visit"]."' ORDER BY tags.date DESC") or die(mysql_error());
			if (mysql_num_rows($res)) {
				$myarray[] = array("type" => 8, "date" => 0, "data" => mysql_num_rows($res));
				while ($row = mysql_fetch_array($res)) $myarray[] = array("type" => 8, "date" => $row["tagdate"], "from" => $row["tagto"], "data" => array("photo_id" => $row["photos_id"], "photo_url" => $row["image_url"], "photo_name" => $row["name"]));
			}
			$res = mysql_query("SELECT * FROM members_has_events WHERE member_id_receive='".mysql_real_escape_string($user)."' AND alive=0 ORDER BY alive") or die(mysql_error());
			if (mysql_num_rows($res)) {
				$myarray[] = array("type" => 9, "date" => 0, "data" => mysql_num_rows($res));
				while ($row = mysql_fetch_array($res)) {
					$res2 = mysql_query("SELECT * FROM events WHERE id=".$row["events_id"]);
					$row2 = mysql_fetch_array($res2);
					$myarray[] = array("type" => 9, "date" => $row["date"], "from" => $row["member_id_send"], "data" => array("pageid" => $row["events_id"], "topic" => $row2["topic"], "pid" => $row2["photos_id"]), "id_from" => $row["member_id_send"]);   
				}
			}
			
			echo '<h1>Mis Novedades</h1><hr><div id="news">';
			if (!empty($myarray)) {
				foreach($myarray as $key) {
					if ($key["type"] == 1) {
						$imagesp = getPagePhotoByPhotoId($key["data"]["pid"]);
						if (!$key["date"]) echo '<h4>Tiene '.$key["data"].' invitaciones a paginas nuevas: </h4><br>';
						else { echo "<img src='".$imagesp."' width='40' height='40'>&nbsp;<a href='in.php?p=pages&id=".$key["data"]["pageid"]."'><b>".$key["data"]["topic"]."</b></a> ";  
							?>
							<div id="checkpage_<?php echo $key["data"]["pageid"]; ?>">
								<input type="button" class="boton" value="Aceptar" onclick="sendaceptarP(<?php echo $key["data"]["pageid"]; ?>, <?php echo $key["from"]; ?>)" style="position: relative;">  
								<input type="button" class="boton" value="Denegar" onclick="senddenegarP(<?php echo $key["data"]["pageid"]; ?>, <?php echo $key["from"]; ?>)" style="position: relative;">  
							</div>
							<?php
							}
					}  else if ($key["type"] == 2) {
						if (!$key["date"]) echo '<h4>Tienes <span id="numpet">'.$key["data"].'</span> peticiones de amistad nuevas.</h4><br>';
						else {
							$u_row = fetchUser($key["from"]); 
							?>
								<table id="inv_<?php echo $u_row['member_id']; ?>">
									<tr>
										<td id="bordert">
											<?php  
											$image = getProfilePhotoByPhotoId($u_row['prf_img']);
											echo "<a href=in.php?p=profile&id=".$u_row["member_id"]."><h2>".$u_row["firstname"]." ".$u_row["lastname"]."</h2></a>";
											echo '<img src="'.$image.'" width="80px" height="80px" style="float: left;" >';
											if ($u_row["edad"]) echo 'Edad: '.$u_row["edad"].'<br>';
											if ($u_row["ciudad"]) echo 'Ciudad: '.$u_row["ciudad"].'<br>';
											echo "Sexo: "; if ($u_row["sexo"] == 0) echo "No especificado"; else if ($u_row["sexo"] == 1) echo "Hombre"; else echo "Mujer";?> 
										</td>
									</tr>
									<tr>
										<td>
											<input type="button" class="boton" value="Aceptar peticion" onclick="sendaceptarA(<?php echo $u_row['member_id']; ?>); ocultardiv();">  
											<input type="button" class="boton" value="Rechazar peticion" onclick="senddenegarA(<?php echo $u_row['member_id']; ?>); ocultardiv();">  
										</td>
									</tr>
								</table><br>
						<?php }
					} else if ($key["type"] == 3) {
						if (!$key["date"]) echo '<h4>Tienes '.$key["data"].' comentarios nuevos</h4><br>';
						else { 
							$u_row = fetchUser($key["from"]);
							echo '<a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"].' '.$u_row["lastname"].'</a><h6>'.$key["date"].'</h6>'.$key["data"].'<br/><br/>';
						}
					} else if ($key["type"] == 4) {
						if (!$key["date"]) echo '<h4>Tienes '.$key["data"].' comentarios en tu spacer nuevos</h4><br>';
						else { 
							$u_row = fetchUser($key["from"]);
							echo '<a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"].' '.$u_row["lastname"].'</a><h6><a href="in.php?p=profile&id='.$user_row["member_id"].'#spacer_'.$key["data"]["id"].'">Ver Spacer</a> - '.$key["date"].'</h6>'.$key["data"]["text"].'<br/><br/>';
						}
					} else if ($key["type"] == 5) {
						if (!$key["date"]) echo '<h4>Tienes '.$key["data"].' comentarios en fotos nuevos</h4><br>';
						else { 
							$u_row = fetchUser($key["from"]);
							echo '<a href="in.php?p=photo&id='.$key["data"]["image_id"].'&m=1"><img width="50" height="50" src="../tmp/small/'.$key["data"]["image"].'"/></a><a href=in.php?p=profile&id='.$u_row["member_id"].'>'.$u_row["firstname"].' '.$u_row["lastname"].'</a><h6>'.$key["date"].'</h6>'.$key["data"]["text"].'<br/><br/>';
						}
					} else if ($key["type"] == 6) {
						$u_row = fetchUser($key["from"]);
						if (!$key["date"]) echo '<h4>Tienes '.$key["data"].' mensajes nuevos: </h4><br>';
						else echo '<a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"].$u_row["lastname"].'</a> - <h6 style="display: inline;">'.$key["date"].'</h6>: <a href="in.php?p=mp&t=1&mp='.$key["data"]["id"].'">'.$key["data"]["topic"].'</a><br>';
					} else if ($key["type"] == 7) {
						if (!$key["date"]) echo '<h4>Tienes '.$key["data"].' cumpleaños proximos: </h4>';
						else echo "<a href='in.php?p=profile&id=".$key["data"]["id"]."'>".$key["data"]["name"]."</a> nacio el ".$key["date"]."<br>";
					} else if ($key["type"] == 8) {
						if (!$key["date"]) echo '<h4>Tienes '.$key["data"].' tags nuevas: </h4>';
						else {
							$u_row = fetchUser($key["from"]);
							echo "<a href='in.php?p=profile&id=".$key["from"]."'>".$u_row["firstname"]."</a> te ha etiquetado en la foto <a href='in.php?p=photo&id=".$key["data"]["photo_id"]."&m=1'><img width='50' height='50' title='".$key["data"]["photo_name"]."' src='../tmp/small/".$key["data"]["photo_url"]."'/></a> el ".$key["date"]."<br>";
						}
					} else if ($key["type"] == 9) {
						if (!$key["date"]) echo '<h4>Tiene '.$key["data"].' invitaciones a eventos nuevos: </h4><br>';
						else { 
							$imagesp = getPagePhotoByPhotoId($key["data"]["pid"]);
							$usendrow = fetchUser($key["from"]);
							echo "<a href='in.php?p=eventos&id=".$key["data"]["pageid"]."'><img src='$imagesp' width='40' height='40'/><b>".$key["data"]["topic"]."</b></a><br>Enviado por <a href=''>".$usendrow["firstname"]." ".$usendrow["lastname"]."</a>";  
							?>	
							<div id="checkevent_<?php echo $key["data"]["pageid"]; ?>">
								<input type="button" class="boton" value="Aceptar" onclick="sendvoyE(<?php echo $key["data"]["pageid"]; ?>, <?php echo $key["from"]; ?>)" style="position: relative;">  
								<input type="button" class="boton" value="Quizas" onclick="sendquizasE(<?php echo $key["data"]["pageid"]; ?>, <?php echo $key["from"]; ?>)" style="position: relative;">  
								<input type="button" class="boton" value="Rechazar" onclick="sendnvoyE(<?php echo $key["data"]["pageid"]; ?>, <?php echo $key["from"]; ?>)" style="position: relative;">  
							</div>
							<?php
						}			
					}
				}
			} else echo "No tienes novedades<br>";
			?>
		</div>
		<div id="news">
			<h1>Novedades de mis amigos</h1>
			<hr>
			<?php 
				//print_r($friendArray); die();
				if (!$friendArray) echo "Aun no tienes amigos :(";
				else {
					//Array novedades: member_id, date, type, data(otro array o string)
					$array = array();
					//--Spacers
					$res1 = mysql_query("SELECT * FROM spacers WHERE member_id_send IN (".implode(",", $friendArray).") AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) ORDER BY date DESC");
					if (mysql_num_rows($res1)) {
						for ($i==0;$row1 = mysql_fetch_array($res1);$i++) {
							$array[] = array("member_id" => $row1["member_id_send"], "date" => $row1["date"], "type" => 1, "data" => $row1["text"]);
						}
					}
					//Comentarios en Spacers
					$res2 = mysql_query("SELECT *, spacers.member_id_send as spacer_from, comment_spacers.member_id_send as comment_from, spacers.text as stext, comment_spacers.text as ctext FROM comment_spacers, spacers WHERE spacers.member_id_send IN (".implode(",", $friendArray).") AND spacers_id=spacers.id AND comment_spacers.date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) ORDER BY comment_spacers.date DESC");
					if (mysql_num_rows($res2)) {
						while ($row2 = mysql_fetch_array($res2)) {
							$array[] = array("member_id" => $row2["spacer_from"], "date" => $row2["date"], "type" => 2, "data" => array("from" => $row2["comment_from"], "to_spacer" => $row2["stext"], "text" => $row2["ctext"]));
						}
					}
					//--Imagenes
					$res3 = mysql_query("SELECT member_id_send, count(*) as cuenta FROM photos WHERE member_id_send IN (".implode(",", $friendArray).") AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) GROUP BY member_id_send") or die(mysql_error());
					if (mysql_num_rows($res3)) {
						while ($row3 = mysql_fetch_array($res3)) {
							if ($row["cuenta"] > 0) $array[] = array("member_id" => $row3["member_id_send"], "date" => 0, "type" => 3, "data" => $row3["cuenta"]);
						}
					}
					$res3 = mysql_query("SELECT * FROM photos WHERE member_id_send IN (".implode(",", $friendArray).") AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY)") or die(mysql_error());
					if (mysql_num_rows($res3)) {
						while ($row3 = mysql_fetch_array($res3)) {
							$array[] = array("member_id" => $row3["member_id_send"], "date" => $row3["date"], "type" => 3, "data" => array("id" => $row3["id"], "name" => $row3["name"], "url" => $row3["image_url"]));
						}
					}
					//--Comentarios
					$res4 = mysql_query("SELECT member_id_receive, count(*) as cuenta FROM comment_users WHERE member_id_receive IN (".implode(",", $friendArray).") AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) GROUP BY member_id_receive ORDER BY date DESC") or die(mysql_error());
					if (mysql_num_rows($res4)) {
						while ($row4 = mysql_fetch_array($res4)) {
							if ($row4["cuenta"] > 0) $array[] = array("member_id" => $row4["member_id_receive"], "date" => 0, "type" => 4, "data" => $row4["cuenta"]);
						}
					}
					$res4 = mysql_query("SELECT * FROM comment_users WHERE member_id_receive IN (".implode(",", $friendArray).") AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) ORDER BY date DESC") or die(mysql_error());
					if (mysql_num_rows($res4)) {
						while ($row4 = mysql_fetch_array($res4)) {
							$array[] = array("member_id" => $row4["member_id_receive"], "date" => $row4["date"], "type" => 4, "data" => array("from" => $row4["member_id_send"], "text" => $row4["text"]));
						}
					}
					//--tags
					$res5 = mysql_query("SELECT tags.member_id_receive as tagto, count(*) as cuenta FROM tags, photos WHERE tags.member_id_receive IN (".implode(",", $friendArray).") AND photos.id=tags.photos_id AND tags.date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) GROUP BY tags.member_id_send ORDER BY tags.date DESC") or die("C - ".mysql_error());			
					if (mysql_num_rows($res5)) {
						while ($row5 = mysql_fetch_array($res5)) {
							if ($row5["cuenta"] > 0) $array[] = array("member_id" => $row5["tagto"], "date" => 0, "type" => 5, "data" => $row5["cuenta"]);
						}
					}
					$res5 = mysql_query("SELECT tags.member_id_receive as tagto, tags.date as tagdate, tags.member_id_send as tagfrom, photos_id, image_url, name FROM tags, photos WHERE tags.member_id_receive IN (".implode(",", $friendArray).") AND photos.id=tags.photos_id AND tags.date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) ORDER BY tags.date DESC") or die("L - ".mysql_error());
					if (mysql_num_rows($res5)) {
						while ($row5 = mysql_fetch_array($res5)) {
							$array[] = array("member_id" => $row5["tagto"], "date" => $row5["tagdate"], "type" => 5, "data" => array("from" => $row5["tagfrom"], "to_photo" => $row5["photos_id"], "photo_url" => $row5["image_url"], "photo_name" => $row5["name"]));
						}
					}
					//Amigos SEND
					$res6 = mysql_query("SELECT member_id_send, count(*) as cuenta FROM friends WHERE (member_id_send IN (".implode(",", $friendArray).") AND alive=1) AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) GROUP BY member_id_send") or die(mysql_error());
					if (mysql_num_rows($res6)) {
						while ($row6 = mysql_fetch_array($res6)) {
							if ($row6["cuenta"]) $array[] = array("member_id" => $row6["member_id_send"], "date" => 0, "type" => 6, "data" => $row6["cuenta"]);
						}
					}
					$res6 = mysql_query("SELECT * FROM friends WHERE (member_id_send IN (".implode(",", $friendArray).") AND alive=1) AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY)") or die(mysql_error());
					if (mysql_num_rows($res6)) {
						while ($row6 = mysql_fetch_array($res6)) {
							$array[] = array("member_id" => $row6["member_id_send"], "date" => $row6["date"], "type" => 6, "data" => $row6["member_id_receive"]);
						}
					}
					//Amigos RECEIVE
					$res6 = mysql_query("SELECT member_id_receive, count(*) as cuenta FROM friends WHERE (member_id_receive IN (".implode(",", $friendArray).") AND alive=1) AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY) GROUP BY member_id_send") or die(mysql_error());
					if (mysql_num_rows($res6)) {
						while ($row6 = mysql_fetch_array($res6)) {
							if ($row6["cuenta"]) $array[] = array("member_id" => $row6["member_id_receive"], "date" => 0, "type" => 6, "data" => $row6["cuenta"]);
						}
					}
					$res6 = mysql_query("SELECT * FROM friends WHERE (member_id_receive IN (".implode(",", $friendArray).") AND alive=1) AND date > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY)") or die(mysql_error());
					if (mysql_num_rows($res6)) {
						while ($row6 = mysql_fetch_array($res6)) {
							$array[] = array("member_id" => $row6["member_id_receive"], "date" => $row6["date"], "type" => 6, "data" => $row6["member_id_send"]);
						}
					}
					//die(json_encode($array));
					if (empty($array)) echo "Tus amigos no tienen novedades";
					else {
						$array = orderArray($array, "member_id", SORT_DESC, 'type', SORT_DESC) or die('<br>Error: No se pueden listar las novedades<br>');
						foreach($array as $key) {
							if (!$useract || $useract != $key["member_id"]) {
								$u_row2 = fetchUser($key["member_id"]);
								if ($useract) echo '<br><hr width="80%"/>';
								echo '<b><a href=in.php?p=profile&id='.$u_row2["member_id"].'>'.$u_row2["firstname"].' '.$u_row2["lastname"].'</a></b><br>';
								$useract = $key["member_id"];
								$ty = 0;
							}
							if (!$ty) "<br>";
							if ($key["type"] == 1) {
								echo '<br><div id="spacerlist">Ha cambiado su spacer a: <b>'.$key["data"].'</b>'.'<br></div>';
							} else if ($key["type"] == 2) {
								$u_row2 = fetchUser($key["data"]["from"]);
								$image = getProfilePhotoByPhotoId($u_row2['prf_img']);

								echo ' <h4>Tiene un comentario nuevo de</h4> <div id="commentcontainer"><img src="'.$image.'" width="40px" align="left" style="padding:3px;" height="40px"><a href=in.php?p=profile&id='.$u_row2["member_id"].'>'.$u_row2["firstname"].' '.$u_row2["lastname"].'</a> en su spacer ('.$key["data"]["to_spacer"].'):<br/><hr>'.$key["data"]["text"]."<br/></div>";
							} else if ($key["type"] == 3) {
								if (!$key["date"]) echo '<b>Ha subido '.$key["data"].' imagenes: </b><br>';
								else {						
									echo " <span class='homeimage'><a href='in.php?p=photo&id=".$key["data"]["id"]."'><img title='".$key["data"]["name"]."' src='../tmp/small/".$key["data"]["url"]."'/></a>&nbsp;&nbsp;</span>";
								}
							} else if ($key["type"] == 4) {
								if (!$key["date"]) echo '<h4>Tiene '.$key["data"].' comentarios nuevos</h4>';
								else {
									$u_row2 = fetchUser($key["data"]["from"]);
									echo '<a href=in.php?p=profile&id='.$u_row2["member_id"].'>'.$u_row2["firstname"].' '.$u_row2["lastname"].'</a>:<br/>'.$key["data"]["text"].'<br/>';
								}
							} else if ($key["type"] == 5) {
								$u_row2 = fetchUser($key["data"]["from"]);
								if (!$key["date"]) echo '<h4>Ha sido etiquetado en '.$key["data"].' imagenes: </h4>';
								else {
									echo "<br><a href='in.php?p=photo&id=".$key["data"]["photo_id"]."'><img title='".$key["data"]["photo_name"]."' src='../tmp/small/".$key["data"]["photo_url"]."'/></a><br>Por ".$u_row2["firstname"]." ".$u_row2["lastname"]."<br>";
								}
							} else if ($key["type"] == 6) {
								if (!$key["date"]) echo '<h4>Tiene '.$key["data"].' amigos nuevos: </h4>';
								else {
									$u_row2 = fetchUser($key["data"]);
									echo '<a href=in.php?p=profile&id='.$u_row2["member_id"].'>'.$u_row2["firstname"].' '.$u_row2["lastname"].'</a>, ';
								}
							}
							$ty++;
						}
					}
				} 
			?>
		</div>
	</div>
	<div>
