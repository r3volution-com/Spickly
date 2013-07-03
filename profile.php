<?php 
	if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>");
	$my_user_id = $user_row["member_id"];
	if (isset($_GET["id"]) && ($_GET["id"] != $user_row["member_id"])) {
		$user_row = fetchUser($_GET["id"]);
		if (!$user_row["member_id"]) die("<script>location.href='in.php';</script>");
		if ($friendArray && in_array($user_row["member_id"], $friendArray)) $alive = 1;
		else if ($friendArray && in_array($user_row["member_id"], $banArray)) $alive = 2;
		else $alive = 0;
	} else $alive = 3;
// Propiedades de el alive
// Alive = NULL; No se ha realizado ninguna peticion y por lo tanto no hay relación.
// Alive = 0; Hay una peticion pendiente
// Alive = 1; La peticion ha sido aceptada
// Alive = 2; Usuario bloqueado
?>
<div id="page">
	<div id="sidebar" class="sideblock">
		<?php echo '<h1 style="margin: 0px;">'.$user_row["firstname"]." ".$user_row["lastname"].'</h1>'; 
		if ($alive == NULL) { ?>
			<input type="button" class="boton" value="Añadir amigo" id="addamigo" onclick="sendanadirA(<?php echo $user_row['member_id']; ?>)" />  
		<?php } else if ($alive == 0 OR $alive == 2) echo "Petición pendiente, a la espera de ser aceptado."; ?>
		<div id="perfil_photo">
			<?php 
			$image = getProfilePhotoByPhotoId($user_row["prf_img"]);
			echo '<img src="'.$image.'" width="200px" height="200px">'; 
			?>
			<?php if ($alive == 1) { ?><input type="button" class="boton" value="Enviar Mensaje" onclick="window.location='in.php?p=mp&id=<?php echo $_GET["id"]; ?>' " /> <?php } ?>
		</div>
		<?php if ($alive == 1 OR $alive == 3) { ?> 
		<div id="friends">
			<h1>Amigos</h1> 
			<hr>
			<?php if (isset($_GET["id"])) $user = $_GET["id"];
				else $user = $user_row["member_id"];
				$res = mysql_query("SELECT * FROM friends WHERE (member_id_send=".$user." OR member_id_receive=".$user.") AND alive=1 ORDER BY date LIMIT 6") or die(mysql_error());
				if (!mysql_num_rows($res)) echo "No tienes amigos ;(";
				else {
				while ($row = mysql_fetch_array($res)) {
					if ($row["member_id_send"] != $user) $u_row = fetchUser($row["member_id_send"]);
					if ($row["member_id_receive"] != $user) $u_row = fetchUser($row["member_id_receive"]); ?>
					<div id="friendbox">
						<?php
						echo "<a href=in.php?p=profile&id=".$u_row["member_id"].">";
						$image = getProfilePhotoByPhotoId($u_row["prf_img"]);
						echo '<img src="'.$image.'" width="40px" height="40px" title="'.$u_row["firstname"].'">';
						"</a>"; ?>
					</div>
				<?php } ?>
				<br><br><a href="./in.php?p=friends<?php if ($alive == 1) echo "&fru=".$_GET["id"]; ?>">Ver más amigos</a>
			<?php } ?>
		</div>
		<div id="pages">
			<h1>Paginas - <input type="button" value="Crear página" class="boton" onclick="location.href='in.php?p=pages&c=1'"></h1>
			<hr>
			<?php 
			$res2 = mysql_query("SELECT * FROM members_has_pages WHERE member_id_receive='".mysql_real_escape_string($user)."' AND alive=1 LIMIT 5") or die(mysql_error());
			if ($numrow = mysql_num_rows($res2)) {
				while($row2 = mysql_fetch_array($res2)) {
					$res3 = mysql_query("SELECT * FROM pages WHERE id=".$row2["pages_id"]);
					$row3 = mysql_fetch_array($res3);
					echo "<a href='in.php?p=pages&id=".$row3["id"]."'>".$row3["topic"]."</a><br>";
					if ($numrow > 5) echo '<a href="#">Ver mas</a>';
				} 
			} else echo "Aun no eres miembro de ninguna pagina";
			?><br>
		<a href="in.php?p=search">Más paginas</a>
		</div>
		<div id="eventos">
			<h1>Eventos - <input type="button" value="Crear Eventos" class="boton" onclick="location.href='in.php?p=events&c=1'"></h1>
			<hr>
			<?php 
			$res2 = mysql_query("SELECT * FROM members_has_events WHERE member_id_receive='".mysql_real_escape_string($user)."' AND alive=1 LIMIT 5") or die(mysql_error());
			if ($numrow = mysql_num_rows($res2)) {
				while($row2 = mysql_fetch_array($res2)) {
					$res3 = mysql_query("SELECT * FROM events WHERE id=".$row2["events_id"]);
					$row3 = mysql_fetch_array($res3);
					echo "<a href='in.php?p=events&id=".$row2["events_id"]."'>".$row3["topic"]."</a> - <h6 class='noblock'>".$row3["date"]."</h6><br>";
					if ($numrow > 5) echo '<a href="#">Ver mas</a>';
				} 
			} else echo "Aun no eres miembro de ningun evento";
			?>
			
		</div>
		<div id="personal_info">
			<h1>Información personal<?php if ($alive == 3) { ?><a href="javascript:void(0)" onclick="document.getElementById('personal_info').innerHTML=document.getElementById('personal_infoedit').innerHTML;" > <img src="resources/images/lapiz.png" /></a><?php } ?></h1>
			<hr>
			<strong>Numero de Visitas:</strong> <? echo $user_row["num_visits"]; ?><br />
			<?php if ($user_row["online"]) echo '<b style="color:#1C9101">Conectado'; else echo '<b style="color:red">Última visita '.$user_row["last_visit"]; ?></b> <br />
			<strong>Sexo:</strong> <?php if ($user_row["sex"] == 0) echo "No especifica"; else if ($user_row["sex"] == 1) echo "Hombre"; else echo "Mujer"; ?><br /> 
			<strong>Estado:</strong> <?php if ($user_row["iam"] == 0) echo "Desconocido"; else if ($user_row["iam"] == 1) echo "Soltero"; else if ($user_row["iam"] == 2) echo "Prometido"; else echo "Casado"; ?><br /> 
			<?php if ($user_row["birthdate"] != "0000-00-00") { if ($user_row["birthdate"] == date("Y-m-d")) { ?><strong>Hoy es su cumple!</strong><br><?php } else { ?><strong>Fecha Nacimiento:</strong> <? echo $user_row["birthdate"]; ?><br /> <? } } ?>
			<?php if ($user_row["birthdate"]) { ?><strong>Edad:</strong> <? echo CalculaEdad($user_row["birthdate"]); ?><br /> <? } ?>
			<?php if ($user_row["city"]) { ?><strong>Ciudad:</strong> <? echo $user_row["city"]; ?><br /><? } ?>
		</div>
		<div id="personal_infoedit" style="display: none;">
			<h1>Información personal</h1>
			<hr>
			<form name="busqueda" action="in_actions.php?a=eprofile" method="post">
				<strong>Sexo:</strong><br><input type="radio" name="sexo" value="0" <?php if ($user_row["sex"] == 0) echo "checked"; ?>>Prefiero no decirlo<br><input type="radio" name="sexo" value="1" <?php if ($user_row["sex"] == 1) echo "checked"; ?>>Hombre<br><input type="radio" name="sexo" value="2" <?php if ($user_row["sex"] == 2) echo "checked"; ?>>Mujer <br /> 
				<strong>Fecha de Nacimiento:</strong><br>
				<select name="dia">
					<?php
					for ($i=1; $i<=31; $i++) {
						if ($i == date("j", strtotime($user_row["birthdate"]))) echo '<option value="'.$i.'" selected>'.$i.'</option>';
						else echo '<option value="'.$i.'">'.$i.'</option>';
					}
					?>
				</select>
				<select name="mes">
					<?php
					for ($i=1; $i<=12; $i++) {
						if ($i == date("n", strtotime($user_row["birthdate"]))) echo '<option value="'.$i.'" selected>'.$i.'</option>';
						else echo '<option value="'.$i.'">'.$i.'</option>';
					}
					?>
				</select>
				<select name="ano">
					<?php
					for($i=date('o')-13; $i>=1940; $i--){
						if ($i == date("Y", strtotime($user_row["birthdate"]))) echo '<option value="'.$i.'" selected>'.$i.'</option>';
						else echo '<option value="'.$i.'">'.$i.'</option>';
					}
					?>
				</select>
				<br>
				<strong>Ciudad:</strong><input type="text" name="city" value="<? echo $user_row["city"]; ?>"/>
				<br>
				<strong>Estado:</strong> <select name="iam" id="iam" class="" tabindex="7">
					<option value="0" <?php if ($user_row["iam"] == 0) echo "checked"; ?>>No te interesa</option>
					<option value="1" <?php if ($user_row["iam"] == 1) echo "checked"; ?>>Soltero</option>
					<option value="2" <?php if ($user_row["iam"] == 2) echo "checked"; ?>>Con novi@</option>
					<option value="3" <?php if ($user_row["iam"] == 3) echo "checked"; ?>>Casado</option>
				</select><br>
				<input type="submit" name="enviar" text="enviar"/>
			</form>
		</div>
		<?php if ($user_row["bio"] || $user_row["ilikeit"]) { ?>
		<div id="intereses">
			<h1> Intereses </h1>
			<hr>
			<?php if ($user_row["bio"]) { ?>
			<h3> Biografia: </h3>
			<?php echo $user_row["bio"]; ?>
			<br>
			<?php } 
			if ($user_row["ilikeit"]) { ?>
			<h3> Me gusta... </h3>
			<?php echo $user_row["ilikeit"]; ?>
			<br>
			<?php } ?>
			<br>
			<br>
		</div>
		<?php } ?>
	</div>
	<div id="spacer">
		<?php
		if (($user_row['member_id'] == $_SESSION['SESS_MEMBER_ID'])) {
			?>
			<h1>Spacer <small>¿En qué piensas?</small></h1>
			<hr>
			<form style="margin: 0px">
				<textarea id="textspacer" placeholder="¿En qué piensas?" name="space" rows="2" cols="60" maxlength="130" onkeyup="document.getElementById('cuenta').value=document.getElementById('textspacer').maxLength-this.value.length" ></textarea><input type="button" name="cuenta" size="3" id="cuenta" value="130" style="position: relative; bottom: 14px;" />
				<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('textspacer').value != '') sendSpacer()" style="position: relative; bottom: 14px;">  
			</form>
			<?
		} else {
			echo '<h1>Spacer <small>¿En qué piensa?</small></h1>';
		}
		?>
		<?php
		$space = mysql_query("SELECT id, text FROM spacers WHERE member_id_send='".mysql_real_escape_string($user_row['member_id'])."'ORDER BY date DESC LIMIT 1");
		$row = mysql_fetch_array($space);
	
		echo "<b>Ultimo spacer:</b> <span id='pgspacertext'>".$row["text"]."</span>";
		?>
	</div>
	<div id="container_etiquetas">
		<input type="button" value="Spickline" class="etiqueta" onclick="document.getElementById('spickline').style.display='block';document.getElementById('album').style.display='none';document.getElementById('notas').style.display='none';">
		<input type="button" value="Álbum" class="etiqueta" onclick="document.getElementById('spickline').style.display='none';document.getElementById('album').style.display='block';document.getElementById('notas').style.display='none';">
		<?php if ($alive == 3) { ?><input type="button" value="Notas Rapidas" class="etiqueta" onclick="document.getElementById('spickline').style.display='none';document.getElementById('album').style.display='none';document.getElementById('notas').style.display='block';"><?php } ?>
	</div>	
	<div id="spickline">
		<h1>Spickline <small>¿Qué te contarán?</small></h1>
		<hr>
		<form id="spickform" style="margin: 0px; padding: 0px; display:<?php if ($alive == 1) echo "block"; else echo "none"; ?>">
			<input type="hidden" id="spickid" value="<?php echo $user_row['member_id']; ?>"/>
			<input type="hidden" id="responseid"/>
			<textarea id="spicktext" name="space" rows="2" cols="60" maxlength="200" onkeyup="document.getElementById('cuenta_<?php echo $user_row['member_id']; ?>').value=200-this.value.length" ></textarea>
			<input type="button" class="cuenta" name="cuenta" size="3" id="cuenta_<?php echo $user_row['member_id']; ?>" value="200"  style="position: relative; bottom: 14px;" />
			<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('spicktext').value != '') sendComment()" style="position: relative; bottom: 15px;">  
		</form>
		<div id="pgspickline">
		<?php
		$pag3 = new Pagination("SELECT id, member_id_send, parent_comment, text, date FROM comment_users WHERE member_id_receive = ".mysql_real_escape_string($user_row['member_id'])." UNION SELECT id, member_id_send, 1, text, date FROM spacers WHERE member_id_send = ".mysql_real_escape_string($user_row['member_id'])." ORDER BY date DESC") or die(mysql_error());   
		$pag3->pgSelectItemperPage(10); 
		$pag3->pgSelectType("spickline");
		$res = $pag3->pgDoPagination();	
		while ($row = mysql_fetch_array($res)) {
			$fecha = date_create($row["date"]);
			$fecha = date_format($fecha, 'H:i:s d-m-Y');
			if ($row["member_id_send"] == $user_row["member_id"]){
				echo '<div id="spacerlist_'.$row["id"].'" class="spacerlist"><a name="spacer_'.$row["id"].'"></a>';
				if ($row["member_id_send"] == $my_user_id) echo '<div class="delspacer" onclick="cwindows(\'deleteSpacer('.$row['id'].')\')">X</div>';
				echo $row['text'].'<h5>'.$fecha.'</h5><br>';
				?>
				<form style="margin: 0px">
					<textarea id="spicktext_<?php echo $row["id"]; ?>" name="space" rows="2" cols="40" maxlength="130" onkeyup="document.getElementById('cuenta_<?php echo $row["id"]; ?>').value=130-this.value.length" ></textarea><input type="button" name="cuenta" class="cuenta" size="3" id="cuenta_<?php echo $row["id"]; ?>" value="130"  style="position: relative; bottom: 14px;"/>
					<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('spicktext_<?php echo $row["id"]; ?>').value != '') comment('<?php echo $row['id']; ?>', 3)" style="position: relative; bottom: 14px;">  
				</form>
				<?php
				$pag4 = new Pagination("SELECT id, member_id_send, text, date FROM comment_spacers WHERE spacers_id = ".mysql_real_escape_string($row['id'])." ORDER BY date DESC");
				$pag4->pgSelectItemperPage(5); 
				$pag4->pgSelectType("commentspacer_container");
				$res2 = $pag4->pgDoPagination();
					echo '<div id="pgcommentspacer_container_'.$row["id"].'" style="margin-left: 20px">';
					echo '<div id="new_comment_'.$row["id"].'"></div>';				
				if (mysql_num_rows($res2)) {

					while ($drow = mysql_fetch_array($res2)) {
						$fecha = date_create($drow["date"]);
						$fecha = date_format($fecha, 'H:i:s d-m-Y');
						$u_row = fetchUser($drow["member_id_send"]);
						$images = getProfilePhotoByPhotoId($u_row["prf_img"]);
						echo "<div class='commentspacer' id='commentspacerlist_".$drow['id']."'><div class='allcomment'>"; 
						if ($u_row["member_id"] == $my_user_id) echo '<div class="delspacer" onclick="cwindows(\'delcomment ('.$drow['id'].', 3)\');">X</div>'; 
						echo "<img src=".$images." width='40px' height='40px' class='prf_img_mini'> <a href='in.php?p=profile&id=".$u_row["member_id"]."'>".$u_row["firstname"]." ".$u_row["lastname"].'</a><br><h5>'.$fecha.'</h5>'.$drow['text'].'</div></div><br>';
					}
					echo $pag4->pgShowAjaxPagination (", ".$row["id"]);
				}
				echo "</div>";
				echo "</div>";
			} else {
				$u_row = fetchUser($row['member_id_send']);
				if ($row["member_id_send"] != $my_user_id) $postextras = ' - <a href="javascript:void(0)" onclick="formResponse(\''.$row['id'].'\', \''.$u_row["member_id"].'\')">Responder</a>';
				else $postextras = ' - <a href="javascript:void(0)" onclick="deleteComment(\''.$row['id'].'\')">Eliminar</a>';
				if ($row["parent_comment"]) { 
					$rest = mysql_query("SELECT * FROM comment_users WHERE id=".$row["parent_comment"]);
					$rowt = mysql_fetch_array($rest);
					$preextras = '<h6>RP: '.$rowt["text"].'</h6>';
				} else $preextras = "";
				$image = getProfilePhotoByPhotoId($u_row["prf_img"]);
				echo '<div id="commentlist_'.$row["id"].'"><a href="in.php?p=profile&id='.$u_row["member_id"].'"><img src="'.$image.'" width="40px" style="margin-right:5px; border-radius:5px;" height="40px" title="'.$u_row["firstname"].'">'.$u_row["firstname"]." ".$u_row["lastname"].'</a> <br/>'.$preextras.$row['text'].'<br/><h5 style="margin:0px;padding:0px;">'.$fecha.$postextras.'</h5><hr></div>';
			}
		}
		echo $pag3->pgShowAjaxPagination (", ".$user);
		?>
		</div>
	</div>
	<div id="album" style="display:none;">		
		<div id="upload_me">
			<h1>Subidas por mi</h1>
			<hr>
			<div id="pgalbum">
				<table>
					<tr align="center">
				<?php
				if (isset($_GET["id"])) $user = $_GET["id"];
				else $user = $user_row["member_id"];
				$pag = new Pagination("SELECT * FROM photos WHERE member_id_send='".mysql_real_escape_string($user)."' ORDER BY id DESC");
				$pag->pgSelectItemperPage(9);
				$pag->pgSelectType("album");
				$res = $pag->pgDoPagination();
				if (!mysql_num_rows($res)) echo "No tienes fotos, subelas";
				else {
					for ($i = 1; $row = mysql_fetch_array($res); $i++) {
						echo '<td><a href="in.php?p=photo&id='.$row["id"].'&m=1" title="'.$row["name"].'"><img class="photoalbum" src="tmp/small/'.$row["image_url"].'" ></a></td>'; 
						if ($i == 3 || $i == 6) echo '</tr><tr align="center">';
					}
				} 
				echo $pag->pgShowAjaxPagination (", ".$user);
				?>
					<tr>
				</table>
			</div>
		</div>
	</div>
	<?php if ($alive == 3) { ?><div id="notas" style="display:none;">
		<h1>Notas rapidas</h1>
		<hr>
		<form style="margin: 0px">
			<textarea id="textnota" placeholder="¿Qué tienes que recordar?" name="space" rows="2" cols="60" maxlength="130" onkeyup="document.getElementById('cuentan').value=document.getElementById('textnota').maxLength-this.value.length" ></textarea><input type="button" class="cuenta" size="3" id="cuentan" value="130" style="position: relative; bottom: 14px;" />
			<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('textnota').value != '') sendNota()" style="position: relative; bottom: 14px;">  
		</form>
		<?php
		$pag2 = new Pagination("SELECT * FROM notes WHERE member_id_send='".mysql_real_escape_string($user_row["member_id"])."' ORDER BY id DESC") or die(mysql_error());   
		$pag2->pgSelectItemperPage(10); 
		$pag2->pgSelectType("fastnotes");
		$res = $pag2->pgDoPagination();
		if (!mysql_num_rows($res)) echo "No tienes Notas rapidas";
		else {
		echo '<div id="comments_notes"><div id="pgfastnotes">';
		echo '<div id="new_nota"></div>';
			while ($row = mysql_fetch_array($res)) {
				$fecha = date_create($row["date"]);
				$fecha = date_format($fecha, 'H:i:s d-m-Y');
				echo "<span id='notatext_".$row["id"]."'>".$row["text"].'<br/>'.$fecha."<hr></span>"; 
			}
			echo $pag2->pgShowAjaxPagination();
			echo "</div></div>";
		} ?>
	</div>
	<?php }
} else echo "Este Usuario no es tu amigo"; ?>