<?php include_once ("common.php");
	if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>");
	function calcular_edad($fecha){
		$dias = explode("-", $fecha, 3);
		$dias = mktime(0,0,0,$dias[2],$dias[1],$dias[0]);
		$edad = (int)((time()-$dias)/31556926 );
		return $edad;
	}
?>	
<div id="page">
	<div id="sidebar">
		<div align="center" id="busqueda" data-type="horizontal" data-role="controlgroup">
		
			<a data-role="button" href="#" onclick="document.getElementById('personas').style.display='block';document.getElementById('paginas').style.display='none';" >Personas</a>
			<a data-role="button" href="#" onclick="document.getElementById('personas').style.display='none';document.getElementById('paginas').style.display='block';" >Paginas</a>
		</div><br/><br/>
		<div id="personas" style="display: block;" >
			<form name="busqueda" action="" method="post">
				<h2>Gente</h2>
				<input type="search" name="nombre" />
				<input type="submit" name="buscar" class="boton" value="Buscar" />


			</form>
		</div>
		<div id="paginas"  style="display: none;">
			<form name="busqueda" action="" method="post">
		</div>
	</div>
	<div id="container" style="margin-top:6%;">
		<div id="search_results">
<?php
	if (!isset($_GET["fru"])) $user = $user_row["member_id"];
	else $user = $_GET["fru"];
	$topic = $_POST['topic'];
	if (isset($_POST['topic']) && $_POST['topic']){
		echo "<h1>Paginas</h1>";
		$tipo = mysql_real_escape_string($_POST['tipo']);
		if ($_POST['tipo'] == 'all')	{
				$consulta = "SELECT * FROM pages WHERE topic LIKE '%$topic%'";
		} else {
				$consulta = "SELECT * FROM pages WHERE topic LIKE '%$topic%' AND category = '$tipo' ";
		}

		$res = mysql_query($consulta) or die($consulta);
		if (mysql_num_rows($res)) {
			while ($row = mysql_fetch_array($res)) {	
				$res2 = mysql_query("SELECT count(*) as gente FROM members_has_pages WHERE to_page_id = ".$row["id"]);
				$row2 = mysql_fetch_array($res2);
				if ($row["image"]) {
					$resI = mysql_query("SELECT * FROM images WHERE id=".mysql_real_escape_string($row["image"])) or die(mysql_error());   
					if (!mysql_num_rows($resI)) $image="http://www.spickly.es/resources/images/default.png";
					else { 
						$rowI = mysql_fetch_array($resI);
						$image="tmp/small/".$rowI["image"]; 
					}
				} else $image="http://www.spickly.es/resources/images/default.png";
				echo '<table border="1" cellspacing="0" cellpadding="0">';
				echo '<tr><td rowspan="2"><img src="'.$image.'" style="height: 100px; width: 100px;"/></td><td><a href="in.php?p=pages&id='.$row['id'].'">'. $row["topic"].'</a></td></tr><tr><td><h5 style="color:#4277d9;">Hay '.$row2["gente"].' miembros</h5></td></tr>';
				echo '<hr>';
				echo '</table>';
			}	
		} else {
			echo "<h2>No hay resultados</h2>";
		}
		
	} else if (isset($_POST['nombre']) && $_POST['nombre']){
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
		if ($sexo) $consulta .= " AND sexo = $sexo";
		if ($edad1 && $edad2) $consulta .= " AND edad BETWEEN $edad1 AND $edad2";
		if ($city) $consulta .= " AND ciudad LIKE '%$city%'";
		$res = mysql_query($consulta) or die(mysql_error());
		
		$contador = mysql_num_rows($res);
		if ($contador == 0) echo '<h3>Parece ser que no se ha encontrado a nadie llamado '.$nombre.'. Prueba otra vez.</h3>';
		else{
			echo '<h2>Se han encontrado '.$contador.' personas <br></h2>';
			while ($u_row = mysql_fetch_array($res)){
				if ($u_row["perfil_img"] != 0) {
					$resa = mysql_query("SELECT * FROM images WHERE id=".mysql_real_escape_string($u_row["perfil_img"])) or die(mysql_error());   
					if (!mysql_num_rows($resa)) $image="http://www.spickly.es/resources/images/default.png";
					else { 
						$rowa = mysql_fetch_array($resa);
						$image="tmp/small/".$rowa["image"]; 
					}
				} else $image="http://www.spickly.es/resources/images/default.png";
				echo '<div style="height: 120px">';
				echo "<a href=in.php?p=profile&id=".$u_row["member_id"]."><h2>".$u_row["firstname"]." ".$u_row["lastname"]."</h2></a>";
				echo '<img src="'.$image.'" width="80px" height="80px" style="float: left;" >';
				if ($u_row["ciudad"]) echo '<b>Ciudad:</b> '.$u_row["ciudad"].'<br>';
				echo "<b>Sexo:</b>"; if ($u_row["sexo"] == 0) echo "No especificado"; else if ($u_row["sexo"] == 1) echo "Hombre"; else echo "Mujer";
				echo "</div>";
			}
		}
	} else {
		echo "<h1>Amigos</h1>";
		$res = mysql_query("SELECT * FROM friends WHERE (member_id_send='".$user."' OR member_id_receive='".$user."') AND alive=1 ORDER BY alive") or die(mysql_error());
		if (!mysql_num_rows($res)) echo "No tienes amigos ;(";
		while ($row = mysql_fetch_array($res)) { 
			if ($row["member_id_send"] != $user) $u_row = fetchUser($row["member_id_send"]); 
			if ($row["member_id_receive"] != $user) $u_row = fetchUser($row["member_id_receive"]); ?>	
			<table id="fr_<?php echo $u_row["member_id"]; ?>">
				<tr>
					<td id="bordert" align="left">
						<div id="info_friends"  >
						<?php  
						if ($u_row["perfil_img"] != 0) {
							$resa = mysql_query("SELECT * FROM images WHERE id=".mysql_real_escape_string($u_row["perfil_img"])) or die(mysql_error());   
							if (!mysql_num_rows($resa)) $image="http://www.spickly.es/resources/images/default.png";
							else { 
								$rowa = mysql_fetch_array($resa);
								$image="tmp/small/".$rowa["image"]; 
							}
						} else $image="http://www.spickly.es/resources/images/default.png";
						?>
						</td>
						<div id="info_container" >
						<?php
						echo "<a href=in.php?p=profile&id=".$u_row["member_id"]."><h2>".$u_row["firstname"]." ".$u_row["lastname"]."</h2></a>";
						echo '<img src="'.$image.'" >';
						if ($u_row["fechanac"] != "0000-00-00") echo '<b>Edad:</b>'.calcular_edad($u_row["fechanac"]).'<br>';
						if ($u_row["ciudad"]) echo '<b>Ciudad:</b> '.$u_row["ciudad"].'<br>';
						echo "<b>Sexo:</b>"; if ($u_row["sexo"] == 0) echo "No especificado"; else if ($u_row["sexo"] == 1) echo "Hombre"; else echo "Mujer";
						?> 
						<?php if (!isset($_GET["u"])) { ?>
				<tr>
					<td>
						<a href="javascript:void(0);" onclick="deleteFriend(<?php echo $u_row['member_id']; ?>);">Eliminar amigo</a>
					</td>
				</tr>
				<?php } ?>
					</div>
				</tr>
				</div>
				
			</table>
			<hr>

<?php	}
	} ?>
</div></div>
</div>
