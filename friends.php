<?php include_once ("common.php");
	if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>");
?>	
<div id="page">
	<div id="sidebar" class="sideblock">
		<div id="personas" style="display: block;" >
			<form name="busqueda" action="" method="post">
				<input type="search" name="nombre" />
				<input type="submit" name="buscar" class="boton" value="Buscar" />
				<hr>
				<h2>Buscar entre:</h2>
				<p><input type="radio" name="filtro" value="all" checked="checked" onclick="document.getElementById('group').style.display='none';"/> Todo Spickly</p>
				<p><input type="radio" name="filtro" value="f" onclick="document.getElementById('group').style.display='block';"/> Mis amigos</p>
				<span id="group" style="display: none;">
					<h2>Grupo:</h2>
					<p><input type="radio" name="tipo" value="-1" checked="checked"/> Todos</p>
					<p><input type="radio" name="tipo" value="0"/> Sin Grupo</p>
					<p><input type="radio" name="tipo" value="1"/> Amigos</p>
					<p><input type="radio" name="tipo" value="2"/> Familiares</p>
					<p><input type="radio" name="tipo" value="3"/> Compa&ntilde;eros de trabajo</p>
				</span>
				<h2>Sexo</h2>
				<input type="radio" name="sexo" value="0" checked="checked">Ambos
				<input type="radio" name="sexo" value="1">Hombre
				<input type="radio" name="sexo" value="2">Mujer
				<h2>Edad</h2>
				<p>De &nbsp;
				<select  id="age1" name="age1">
					<option value="0">--</option>
					<?php for ($i = 14; $i < 100; $i++) echo '<option value="'.$i.'">'.$i.'</option>'; ?>
				</select>
				&nbsp;A&nbsp;
				<select id="age2" name="age2">
						<option value="0">--</option>
						<?php for ($i = 14; $i < 100; $i++) echo '<option value="'.$i.'">'.$i.'</option>'; ?>
				</select></p>
				<h2>Ciudad</h2>
				<input type="text" id="city_friendsearch" name="city"/>
		</div>
	</div>
	<div id="container" style="margin-top:6%;">
		<div id="search_results">
		<?php
		if (!isset($_GET["fru"])) $user = $user_row["member_id"];
		else $user = $_GET["fru"];
		if (isset($_POST['nombre']) && $_POST['nombre']) {
			echo "<h1>Gente</h1>";
			$nombre = mysql_real_escape_string($_POST['nombre']);
			$sexo = mysql_real_escape_string($_POST['sexo']);
			$edad1 = mysql_real_escape_string($_POST['age1']);
			$edad2 = mysql_real_escape_string($_POST['age2']);
			$city = mysql_real_escape_string($_POST['city']);
			$type = mysql_real_escape_string($_POST['tipo']);
			if ($_POST['filtro'] == 'all')	{
				$consulta = "SELECT * FROM members WHERE (firstname LIKE '%$nombre%' OR lastname LIKE '%$nombre%' OR CONCAT_WS(' ', firstname, lastname) LIKE '%$nombre%')";
			} else {
				$query = "SELECT * FROM friends WHERE ((member_id_send = ANY(SELECT member_id FROM members WHERE firstname LIKE '%$nombre%' OR lastname LIKE '%$nombre%' OR CONCAT_WS(' ', firstname, lastname) LIKE '%$nombre%') AND member_id_receive=".$user_row["member_id"].") OR (member_id_receive = ANY(SELECT member_id FROM members WHERE firstname LIKE '%$nombre%' OR lastname LIKE '%$nombre%' OR CONCAT_WS(' ', firstname, lastname) LIKE '%$nombre%') AND member_id_send=".$user_row["member_id"]."))";
				if ($type) $query .= " AND ftype=$type";
				$res = mysql_query($query) or die($query);
				if (mysql_num_rows($res)) {
					while ($row = mysql_fetch_array($res)) {
						if ($row["member_id_send"] != $user_row["member_id"]) $u = $row["member_id_send"];
						else if ($row["member_id_receive"] != $user_row["member_id"]) $u = $row["member_id_receive"]; 
						$array[] = $u;
					}
					$consulta = "SELECT * FROM members WHERE member_id IN (".implode(',',$array).")";
				} else {
					$sexo = $edad = $city = 0;
					$consulta = $query;
				}
			}
			if ($sexo) $consulta .= " AND sex = $sexo";
			if ($edad1 && $edad2) {
				$edad1 = date("m-d-Y", mktime(0, 0, 0, date("n"), date("d"), date("Y") - $edad1));
				$edad2 = date("m-d-Y", mktime(0, 0, 0, date("n"), date("d"), date("Y") - $edad2));
				$consulta .= " AND birthdate BETWEEN '$edad1' AND '$edad2'";
			}
			if ($city) $consulta .= " AND city LIKE '%$city%'";
			//die  ($consulta);
			$pag = new Pagination($consulta) or die(mysql_error());
			$pag->pgSelectItemperPage(10); 
			$pag->pgSelectType("people");
			$res = $pag->pgDoPagination();	
			$moin = $pag->pgGetMoreInfo();
			$contador = $moin["num_items"];
			if ($contador == 0) echo '<h3>Parece ser que no se ha encontrado a nadie llamado '.$nombre.'. Prueba otra vez.</h3>';
			else{
				echo '<h2>Se han encontrado '.$contador.' personas <br></h2>';
				while ($u_row = mysql_fetch_array($res)){
					if ($u_row["prf_img"] != 0) {
						$resa = mysql_query("SELECT * FROM photos WHERE id=".mysql_real_escape_string($u_row["prf_img"])) or die(mysql_error());   
						if (!mysql_num_rows($resa)) $image="resources/images/default.png";
						else { 
							$rowa = mysql_fetch_array($resa);
							$image="tmp/small/".$rowa["image_url"]; 
						}
					} else $image="resources/images/default.png";
										
					echo $var;
					echo '<div style="height: 120px">';
						echo "<a href=in.php?p=profile&id=".$u_row["member_id"]."><h2>".$u_row["firstname"]." ".$u_row["lastname"]."</h2></a>";
						echo '<img src="'.$image.'" width="80px" height="80px" style="float: left; padding-right:5px; border-radius:10px;" >';
						if ($u_row["birthdate"] != "0000-00-00") echo '<b>Edad: </b>'.CalculaEdad($u_row["birthdate"]).'<br>';
						if ($u_row["ciudad"]) echo '<b>Ciudad: </b> '.$u_row["city"].'<br>';
						echo "<b>Sexo: </b>"; if ($u_row["sex"] == 0) echo "No especificado"; else if ($u_row["sex"] == 1) echo "Hombre"; else echo "Mujer";
						if($friendArray && in_array($u_row["member_id"], $friendArray)) echo '<br><button class="boton" href="javascript:void(0);" onclick="cwindows(deleteFriend('.$u_row["member_id"].'););">Eliminar amigo</button>'; else echo '<br><input type="button" class="boton" value="Añadir amigo" id="addamigo" onclick="sendanadirA('.$u_row["member_id"].')">';

					echo "</div><br><br>";
				}
				
				echo  $pag->pgShowAjaxPagination();
			}
		} else {
			echo "<h1>Amigos</h1><div id='pgfriends'>";
			if ($friendArray) {
				$pag = new Pagination("SELECT * FROM members WHERE member_id IN (".implode(",", $friendArray).")") or die (mysql_error());
				$pag->pgSelectItemperPage(10); 
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
									echo '<img src="'.$image.'" style="border-radius:7px;" >';
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
								<a class="boton" href="javascript:void(0);" onclick="window.location='in.php?p=mp&t=2&id=<?php echo $u_row['member_id']; ?>' " />Enviar mensaje</a>
								<a class="boton" href="javascript:void(0);" onclick="cwindows('deleteFriend(<?php echo $u_row['member_id']; ?>)');">Eliminar amigo</a>
							</td>
						</tr>
						<?php } ?>
					</table>
					<hr>
<?php			}
				echo $pag->pgShowAjaxPagination();
			} else echo "No tienes amigos";
			echo "</div>";
		} ?>
		</div>
	</div>
</div>