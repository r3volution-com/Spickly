<?php 
	//if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>");
	$var = 0;
	$my_user_id = $user_row["member_id"];
	if (isset($_GET["id"])) {
		$user_row = fetchUser($_GET["id"]);
		if (!$user_row["member_id"]) die("<p>El perfil seleccionado no existe o ha sido eliminado.</p>");
		$row = fetchQuery("SELECT * FROM friends WHERE (member_id_send='".mysql_real_escape_string($user_row["member_id"])."' AND member_id_receive='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."') OR (member_id_receive='".mysql_real_escape_string($user_row["member_id"])."' AND member_id_send='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."')");
		$alive = $row["alive"];
	} 
	$nombre = $user_row["firstname"]." ".$user_row["lastname"]; 
	if ((isset($_GET["id"]) && $_GET["id"]) && $_GET["id"] != $_SESSION['SESS_MEMBER_ID'] && $alive == 1) $var = 2;
	else if (!isset($_GET["id"]) || $_GET["id"] == $_SESSION['SESS_MEMBER_ID']) $var = 1;
	else $var = 0;
	?>
	<div id="photo_container">
	<div class="tittle">
	<?php echo $nombre;
	if ($var == 0) { ?>
	<?php } ?>
	</div>
		<div id="perfil_photo">
			<?php 
			if ($user_row["perfil_img"] != 0) {
				$res = mysql_query("SELECT * FROM photos WHERE id=".$user_row["perfil_img"]) or die(mysql_error());   
				if (!mysql_num_rows($res)) $image="/resources/images/default.png";
				else { 
					$row = mysql_fetch_array($res);
					$image="/tmp/".$row["image"]; 
				}
			} else $image="/resources/images/default.png";
			echo '<img src="'.$image.'">'; 
			?>
			<center><?php if ($var == 2) { ?><a href="in.php?p=mp&t=2&id=<?php echo $_GET["id"]; ?>"><input type="button" class="boton" value="Enviar Mensaje"/><?php } ?>	
			</center>
			</a>
			<br>
		</div>
		<?php if ($var > 0) { ?> 
	</div>	
	<div id="container_buttons" data-type="horizontal" data-role="controlgroup">
	<a href="#" data-role="button" onclick="document.getElementById('spickline').style.display='block';document.getElementById('album').style.display='none';document.getElementById('story').style.display='none';document.getElementById('personal_info').style.display='none';">SpickLine</a>
	<a href="#" data-role="button" onclick="document.getElementById('spickline').style.display='none';document.getElementById('album').style.display='block';document.getElementById('story').style.display='none';document.getElementById('personal_info').style.display='none';">Álbum</a>
	<a href="#" data-role="button" onclick="document.getElementById('spickline').style.display='none';document.getElementById('album').style.display='none';document.getElementById('story').style.display='none';document.getElementById('personal_info').style.display='block';">Info</a>
	</div>
	<div id="container">
	<div id="personal_info" style="display: none;">
			<strong>Numero de Visitas:</strong> <? echo $user_row["num_visitas"]; ?><br />
			<?php if ($user_row["online"]) echo '<b style="color:#1C9101">Conectado'; else echo '<b style="color:red">Última visita '.$user_row["last_visit"]; ?></b> <br />
			<strong>Sexo:</strong> <?php if ($user_row["sexo"] == 0) echo "Sin sexo"; else if ($user_row["sexo"] == 1) echo "Hombre"; else echo "Mujer"; ?><br /> 
			<strong>Estado:</strong> <?php if ($user_row["estado"] == 0) echo "Desconocido"; else if ($user_row["estado"] == 1) echo "Soltero"; else if ($user_row["estado"] == 2) echo "Prometido"; else echo "Casado"; ?><br /> 
			<?php if ($user_row["fechanac"] != "0000-00-00") { ?><strong>Fecha Nacimiento:</strong> <? echo $user_row["fechanac"]; ?><br /> <? } ?>
			<?php if ($user_row["edad"]) { ?><strong>Edad:</strong> <? echo $user_row["edad"]; ?><br /> <? } ?>
			<?php if ($user_row["pais"]) { ?><strong>Pais:</strong> <? echo $user_row["pais"]; ?><br /><? } ?>
			<?php if ($user_row["ciudad"]) { ?><strong>Ciudad:</strong> <? echo $user_row["ciudad"]; ?><br /><? } ?>
		</div>
		<div id="personal_infoedit" style="display: none;">
			<form name="busqueda" action="/in_actions.php?a=eprofile" method="post">
				<strong>Sexo:</strong><input type="radio" name="sexo" value="1">Hombre <input type="radio" name="sexo" value="2">Mujer <br /> 
				<strong>Fecha de Nacimiento:</strong><br>
<select name="dia">
        <?php
        for ($i=1; $i<=31; $i++) {
            if ($i == date('j'))
                echo '<option value="'.$i.'" selected>'.$i.'</option>';
            else
                echo '<option value="'.$i.'">'.$i.'</option>';
        }
        ?>
</select>

<select name="mes">
        <?php
        for ($i=1; $i<=12; $i++) {
            if ($i == date('m'))
                echo '<option value="'.$i.'" selected>'.$i.'</option>';
            else
                echo '<option value="'.$i.'">'.$i.'</option>';
        }
        ?>
</select>
<select name="ano">
        <?php
        for($i=date('o'); $i>=1910; $i--){
            if ($i == date('o'))
                echo '<option value="'.$i.'" selected>'.$i.'</option>';
            else
                echo '<option value="'.$i.'">'.$i.'</option>';
        }
        ?>
</select>
				<br>
				<strong>Ciudad:</strong><input type="text" name="city" />
				<br>
				<strong>Estado:</strong> <select name="estado" id="estado" class="" tabindex="7">
					<option value="0">No te interesa</option>
					<option value="1">Soltero</option>
					<option value="2">Con novi@</option>
					<option value="3">Casado</option>
				</select><br>
				<input type="submit" name="enviar" text="enviar"/>
			</form>
		</div>
	</div>
<!-- ------------------------------------------------------------------------------------------------------------------------------------------------ -->	
		<div id="album" style="display: none;">
			<?php $res = mysql_query("SELECT * FROM photos WHERE member_id_send='".mysql_real_escape_string($user_row["member_id"])."' ORDER BY id DESC LIMIT 3") or die(mysql_error());      
			if (!mysql_num_rows($res)) echo "No tienes Fotos, subelas";
			else {
				while ($row = mysql_fetch_array($res)) echo '<a href="in.php?p=photo&f='.$row["id"].'"><img src="/tmp/small/'.$row["image_url"].'" width="200" height="200" style="margin:3px;"></a>'; 
			} ?>
			<a href="in.php?p=photo&id=<?php echo $user_row["member_id"]; ?>"><br/>Ver más fotos</a>
		</div>
<!-- ------------------------------------------------------------------------------------------------------------------------------------------------ -->
		<div id="story" style="display: none;">
			<h4>SpickStory</h4>
		</div>	
<!-- ------------------------------------------------------------------------------------------------------------------------------------------------ -->
		<div id="spickline" style="display: block;">
			<form id="spickform" style="margin: 0px; padding: 0px; display:<?php if ($var == 2) echo "block"; else echo "none"; ?>">
				<input type="hidden" id="spickid" value="<?php echo $user_row['member_id']; ?>"/>
				<input type="hidden" id="responseid"/>
			<div id="comment_area">
				<textarea name="spicktext" maxlength="130" onkeyup="document.getElementById('cuenta').value=130-this.value.length"></textarea>					
				<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('spicktext').value != '') sendComment()" data-mini="true"> 
			</div>  
			</form>
			<div id="comment_container">
			<?php
			$res = pagination("SELECT id, member_id_send, parent_comment, text, date FROM comment_users WHERE member_id_receive = ".mysql_real_escape_string($user_row['member_id'])." UNION SELECT id, member_id_send, type, text, date FROM spacers WHERE member_id_send = ".mysql_real_escape_string($user_row['member_id'])." ORDER BY date DESC", 10, 1) or die("17: ".mysql_error());				
			while ($row = mysql_fetch_array($res["query_result"])) {
			$fecha = date_create($row["date"]);
			$fecha = date_format($fecha, 'H:i:s d-m-Y');
				if ($row["member_id_send"] == $user_row["member_id"]){
					echo '<div id="spacerlist_'.$row["id"].'" class="spacerlist">'.$fecha.': '.$row['text'].'<br>';
					if ($row["member_id_send"] == $my_user_id) echo ' - <a href="#" onclick="deleteSpacer(\''.$row['id'].'\')">Eliminar</a>';
					?>
			<form style="margin: 0px">
			<div id="commentspacerarea">
				<textarea name="textarea" id="textarea-a" maxlength="130" onkeyup="document.getElementById('cuenta').value=130-this.value.length"></textarea>					
				<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('spickspacertext').value != '') sendSpacerComment('<?php echo $row['id']; ?>')" data-mini="true"> 
			</div>
			</form><?php
					$res2 = pagination("SELECT id, member_id_send, comment, date FROM comment_spacers WHERE to_spacer_id = ".mysql_real_escape_string($row['id'])." ORDER BY date DESC", 10, 1);
					echo '<div id="commentspacer_container" style="margin-left: 20px">';
					if ($res["n_items"]) {
						while ($drow = mysql_fetch_array($res2["query_result"])) {
							$fecha = date_create($drow["date"]);
							$fecha = date_format($fecha, 'H:i:s d-m-Y');
							$u_row = fetchUser($drow["member_id_send"]);
							echo "<div id='commentspacerlist_".$drow['id']."'>".$u_row["firstname"]." ".$u_row["lastname"].' a las '.$fecha.': '.$drow['comment'];
							if ($u_row["member_id"] == $my_user_id) echo ' - <a href="#" onclick="deleteSpacerComment(\''.$drow['id'].'\')">Eliminar</a>';
							echo "</div>";
						}
						echo $res["select_page"];
					}
					echo "</div></div>";
				} else {
					$u_row = fetchUser($row['member_id_send']);
					if ($row["member_id_send"] != $my_user_id) {
						$postextras = ' - <a href="#" onclick="formResponse(\''.$row['id'].'\', \''.$u_row["member_id"].'\')">Responder</a>';
					} else {
						$postextras = ' - <a href="#" onclick="deleteComment(\''.$row['id'].'\')">Eliminar</a>';
					}
					if ($row["parent_comment"]) { 
						$rest = mysql_query("SELECT * FROM comment_users WHERE id=".$row["parent_comment"]);
						$rowt = mysql_fetch_array($rest);
						$preextras = '<h6>RP: '.$rowt["comment"].'</h6>';
					} else $preextras = "";
					echo '<div id="commentlist_'.$row["id"].'"><a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"]." ".$u_row["lastname"].'</a> <br/>'.$preextras.$row['comment'].'<br/><h6 style="margin:0px;padding:0px;">'.$fecha.$postextras.'</h6><hr></div>';
				}
			}
			echo $res["select_page"]; ?>
			</div>
		</div>
<!-- ------------------------------------------------------------------------------------------------------------------------------------------------ -->
	<?php } else echo "Este Usuario no es tu amigo"; ?>