<?php
 include_once ("common.php");
if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>");
?>
<div id="page">
	<div id="sidebar" class="sideblock">
		<form name="busqueda" action="#" method="post">
			<h2>Buscador<h2>
				<input type="text" name="topic" />
				<input type="submit" class="boton" value="Enviar"  />
			<h2>Buscar entre:</h2>
				<p><input type="radio" name="filtro" value="all" checked="checked" /> Todo Spickly</p>
				<p><input type="radio" name="filtro" value="f" /> Mis paginas</p>
				<p><select name="type">
					<option value="">Todas</option>
					<option value="1" >Musica</option>
					<option value="2" >Arte</option>
					<option value="3" >Ocio</option>
					<option value="4" >Salud</option>
					<option value="5" >Tecnologia</option>
					<option value="6" >Internet</option>
					<option value="7" >Juegos</option>
					<option value="8" >Proyectos</option>

				</select></p>
		</form>
	</div>
	<div id="container" style="margin-top:6%;">
		<h2>Ultimas paginas</h2><hr>
		<div id="result">
		<?php
			
		if (isset($_POST["filtro"])){

			if ($_POST["type"] != NULL) $type = $_POST["type"];
			
			if ($_POST['filtro'] == 'all')	{
				$search = $_POST["topic"];
				$consulta = "SELECT * FROM pages WHERE topic LIKE '%".$search."%'";
				
				if ($type) $consulta .= " AND category = $type";			
				$res = mysql_query($consulta) or die (mysql_error());			
				while($row = mysql_fetch_array($res)){
					$images = getPagePhotoByPhotoId($row["photos_id"]);	
						echo "<img src=".$images." style='float:left; padding-right:5px;' width='90px' height='90px'>";
						echo '<a href="in.php?p=pages&id='.$row["id"].'">'.$row["topic"].'</a><br>';
						echo 'Hay X Personas<br>';
						echo '<button class="boton">Seguir Pagina</button><br>';
						echo '<br><hr>';
				}
			}else{
				$search = $_POST["topic"];
				$consulta = "SELECT * FROM pages WHERE topic LIKE '%".$search."%' AND id = ANY (SELECT pages_id FROM members_has_pages WHERE ( member_id_send = ".$user_row["member_id"]." OR member_id_receive = ".$user_row["member_id"].") AND alive = 1)";
				if ($type) $consulta .= " AND category = $type";	
				$images = getPagePhotoByPhotoId($row["photos_id"]);				
				$res = mysql_query($consulta) or die (mysql_error());		
				
				while($row = mysql_fetch_array($res)){
				$images = getPagePhotoByPhotoId($row["photos_id"]);	
						echo "<img src='".$images."' style='float:left; padding-right:5px;' width='90px' height='90px'>";
						echo '<a href="in.php?p=pages&id='.$row["id"].'">'.$row["topic"].'</a><br>';
						echo 'Hay X Personas<br>';
						echo '<button class="boton">Seguir Pagina</button><br>';
						echo "<br><hr>";
				}
			
			}
		}else{
	
				$res = mysql_query("SELECT * FROM pages LIMIT 10") or die(mysql_error()); 
			
				while($row = mysql_fetch_array($res)){
				    $imgp = mysql_query("SELECT * FROM photos WHERE id=(SELECT photos_id FROM pages WHERE id=".$row["id"].")");
					$imgrow = mysql_fetch_array($imgp);
					
				echo '<div style="text-align: left;">';	
				echo '<img style="float: left; padding-right:10px; " src="tmp/small/'.$imgrow["image_url"].'" width= 90px height= 90px>';
				echo '<a href="in.php?p=pages&id='.$row["id"].'">'.$row["topic"].'</a><br>';
				echo '<label >Hay X Personas en esta pagina<br></label>';
				echo '<button class="boton">Seguir Pagina</button><br>';
				echo "<br>";
				echo'</div><hr>';
				}
		}
		
		
		?>
		</div>
	</div>
</div>