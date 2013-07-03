<?php include_once("common.php");
//SI ESTA C ES EL FORMULARIO PARA CREAR PAGINA
if (isset($_GET["c"])) {
?>
	<div id="page">
		<div id="inboxin">
				<div id="form">
					<form method="POST" action="in_actions.php?a=cpage">
						<table style="position: relative; width: 100%">
							<tr>
								<td>
									<h2>Titulo de la pagina:</h2>
									<input name="titulo" type="text"/>
								</td>
							</tr>
							<tr>
								<td>
									<h2>Descripcion de la pagina:</h2>
									<textarea id="texto" name="texto"></textarea>
								</td>
							</tr>
							<tr>
								<td>
									<h2>Categoria de la pagina</h2>
									<div align="center">
										<input type ="radio" name="categoty" value="1">Musica</option>
										<input type ="radio" name="categoty" value="2">Arte</option>
										<input type ="radio" name="categoty" value="3">Diversi&oacute;n</option>
										<input type ="radio" name="categoty" value="4">Salud</option>
										<input type ="radio" name="categoty" value="5">Tecnologia</option>
										<input type ="radio" name="categoty" value="6">Internet</option>
										<input type ="radio" name="categoty" value="7">Juegos</option>
										<input type ="radio" name="categoty" value="8">Proyectos</option>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<h2>Imagen de la pagina:</h2>
									<input type="hidden" id="image" name="image" value=""/>
									<div id="photoslider">
										<div id="slidercontent" align="center">
									<?php
										if (isset($_GET["id"])) $user = $_GET["id"];
										else $user = $user_row["member_id"];
										$res1 = mysql_query("SELECT * FROM photos WHERE member_id_send='".mysql_real_escape_string($user)."'") or die(mysql_error());  
										$res2 = mysql_query("SELECT * FROM photos WHERE member_id_send='".mysql_real_escape_string($user)."' ORDER BY id DESC LIMIT 0,3") or die(mysql_error());      
										if (!mysql_num_rows($res2)) echo "No tienes fotos, subelas";
										else {
											while ($row = mysql_fetch_array($res2)) echo '<a href="#" onclick="selectImage(\''.$row["id"].'\');"><img src="tmp/small/'.$row["image_url"].'" id="image_'.$row["id"].'" width="100" height="100" style="margin:5px;"></a>'; 
										} 
									?>
										</div>
										<div id="prevpage" style="display: none;"><a href="#" onclick="prevPage()">&lt;</a></div> 
										<?php if (mysql_num_rows($res1) > 3) { ?><div id="nextpage"><a href="#" onclick="nextPage()">&gt;</a></div><?php } ?>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<input type="submit" id="cpage" name="cpage" class="boton" value="Crear Pagina" style="position: relative; bottom:13px; left:45%;"/>
								</td>
							</tr>
				</div>
		</div>
	</div>
<?php
}
//SI ESTA MAS ES...
else if (isset($_GET["mas"])) {
?>
<div id="page">
	<div id="pages_friend">
	<?php
		$pageid = $_GET["id"];
		$res9 = mysql_query("SELECT * FROM members_has_pages WHERE alive=1 AND pages_id='".$pageid."' ORDER BY alive") or die(mysql_error());  
		if (!mysql_num_rows($res9)) echo "No hay usuarios siguiendo esta pagina";
		else {
			echo "<table align=center>"; 
			$columnes = 8; # Número de columnas (variable) 
			if (!($rows=mysql_num_rows($res9))) echo "<tr><td colspan=$columnes>No hay resultados en la BD.</td></tr> "; 
			else echo "<tr><td colspan=$columnes><h3>$rows  Resultados</h3> </td></tr>"; 
			for ($i=1; $row9 = mysql_fetch_array($res9); $i++) { 
				$res3 = mysql_query("SELECT * FROM members WHERE member_id=".$row9["member_id_receive"]." ");
				$row3 = mysql_fetch_array($res3);
				$image = getProfilePhotoByPhotoId($row3["prf_img"]);
				$resto = ($i % $columnes); # Número de celda del <tr> en que nos encontramos 
				if ($resto == 1) echo "<tr>"; # Si es la primera celda, abrimos <tr> 
				echo "<td align='center' style='padding:15px;'><img src='".$image."' width='100px' height='100px' /><br><a href=in.php?p=profile&id=".$row9["member_id_receive"].">".$row3["firstname"]."<br>".$row3["lastname"]."</a></br></td>";  
				if ($resto == 0) echo "</tr>"; # Si es la última celda, cerramos </tr> 
			} 
			if ($resto <> 0) { # Si el resultado no es múltiple de $columnes acabamos de rellenar los huecos 
				$ajust = $columnes - $resto; # Número de huecos necesarios 
				for ($j = 0; $j < $ajust; $j++) {echo "<td>&nbsp;</td>";} 
				echo "</tr>"; # Cerramos la última línea </tr> 
			} 
			echo "</table>"; 
		}
	?>
	</div>
</div>
	<?php
}
else if (!isset($opt) || !isset($_GET["id"])) die("<script>location.href='in.php?p=profile';</script>");
else {
$res = mysql_query("SELECT * FROM pages WHERE id=".mysql_real_escape_string($_GET["id"]));
if (mysql_num_rows($res)){
	$row = mysql_fetch_array($res);
	$res2 = mysql_query("SELECT * FROM members_has_pages WHERE member_id_send=".mysql_real_escape_string($user_row["member_id"])." AND pages_id=".mysql_real_escape_string($_GET["id"]));
	$row2 = mysql_num_rows($res2);
	$fecha = date_create($row["date"]);
	$fecha = date_format($fecha, 'H:i:s d-m-Y');
?>
<div id="page">
	<div id="sidebar" class="sideblock">
		<h3><?php echo $row["topic"]; if (($row['member_id_send'] == $_SESSION['SESS_MEMBER_ID'])) { ?> <a href="javascript:void(0)" onclick="document.getElementById('textarea').innerHTML=document.getElementById('text_edit').innerHTML;" ><img id="editimg" src="resources/images/lapiz.png" /></a>   <a href="in_actions.php?a=dpage&d=<?php echo $row['id']; ?>">Borrar pagina</a> <?php } ?> </h3>
		<?php 
			$image = getPagePhotoByPhotoId($row["photos_id"]);
			echo '<img src="'.$image.'" width="200px" height="200px">'; 
		?>	
		<h5>Creado: <?php echo $fecha; ?></h5>
		<h1>Seguidores</h1>
		<hr>
		<?php
		$pageid = $_GET["id"];
		$res9 = mysql_query("SELECT * FROM members_has_pages WHERE alive=1 AND pages_id='".$pageid."' ORDER BY alive") or die(mysql_error());  
		if (!($numu = mysql_num_rows($res9))) echo "No hay usuarios siguiendo esta pagina";
		else {
			for ($i=1;$row9 = mysql_fetch_array($res9);$i++) {
				$res3 = mysql_query("SELECT * FROM members WHERE member_id=".$row9["member_id_receive"]." ");
				$row3 = mysql_fetch_array($res3);
				echo "<a href=in.php?p=profile&id=".$row9["member_id_receive"].">".$row3["firstname"]." ".$row3["lastname"]."</a></br>";
				if ($i == 5) break;
			} 	
		}
		if ($numu > 5) echo "<a href=in.php?p=pages&id=".$pageid."&mas>Ver mas</a>";
		?>
		<div id="invita">
			<h1>Invita a tus amigos </h1>
			<hr>
			<form method="POST" action="in_actions.php?a=invpage&idg=<?php echo $_GET["id"];?>">	
				<input type="hidden" id="user" name="user" value=""/>
				<input type="hidden" id="idp" name="idp" value="<?php echo $_GET["id"];?>"/>
				<div style="overflow: auto; height: 234px;">
					<?php
						$pageid = $_GET["id"];
						$res9 = mysql_query("SELECT * FROM members_has_pages WHERE member_id_receive='".$_SESSION['SESS_MEMBER_ID']."' AND alive=1 AND pages_id='".$pageid."' ORDER BY alive") or die(mysql_error());  
						$row9 = mysql_fetch_array($res9);
						if ($row9["member_id_receive"] == $_SESSION['SESS_MEMBER_ID']){
							$res1 = mysql_query("SELECT * FROM friends WHERE (member_id_send='".$_SESSION['SESS_MEMBER_ID']."' OR member_id_receive='".$_SESSION['SESS_MEMBER_ID']."') AND alive=1  ORDER BY alive") or die(mysql_error());  
							if (!mysql_num_rows($res1)) echo "No tienes fotos, subelas";
							else {
								while ($row1 = mysql_fetch_array($res1)) {
									if ($row1["member_id_send"] != $_SESSION['SESS_MEMBER_ID']) $u_row = fetchUser($row1["member_id_send"]); 
									if ($row1["member_id_receive"] != $_SESSION['SESS_MEMBER_ID']) $u_row = fetchUser($row1["member_id_receive"]); ?>	
									<table>
										<tr>
											<td>
												<div id="info_friends">
													<?php 
													$image = getProfilePhotoByPhotoId($u_row["prf_img"]);
													echo '<a href="javascript:void(0);" onclick="selectUser(\''.$u_row["member_id"].'\');"><img src="'.$image.'" id="user_'.$u_row["member_id"].'" width="30" height="30" >'.$u_row["firstname"].' '.$u_row["lastname"].'</a>';
													?> 
												</div>
											<td>
										</tr>
									</table><?php 
								} 
							}
							$peter = true;
						} else echo 'No estas vinculado ha esta pagina';
					?>
				</div>
				<?php if (isset($peter)) echo '<input type="submit" id="inv" name="inv" class="boton"  style="position: relative; top: 10px;">'; ?>
			</form>
		</div>
	</div>
	<div id="container">
		<div id="check">
			<?php
			$pageid = $_GET["id"];
			$res8 = mysql_query("SELECT * FROM members_has_pages WHERE member_id_receive='".$_SESSION['SESS_MEMBER_ID']."' AND pages_id='".$pageid."' ") or die(mysql_error());  
			$row8 = mysql_fetch_array($res8);
			if ($row8['alive'] == NULL) { ?>
				<input type="button" class="boton" value="Seguir la pagina" onclick="sendseguirP(<?php echo $_GET['id']; ?>)" style="position: relative; bottom: 14px;">  
	<?php	} else if ($row8['alive'] == 0) { ?>		
				<input type="button" class="boton" value="Aceptar" onclick="sendaceptarP(<?php echo $row8["pages_id"]; ?>, <?php echo $row8["member_id_send"]; ?>)">  
				<input type="button" class="boton" value="Denegar" onclick="senddenegarP(<?php echo $row8["pages_id"]; ?>, <?php echo $row8["member_id_send"]; ?>)">  
	<?php 	} else if ($row8['alive'] == 1) { ?>
				<input type="button" class="boton" value="Dejar la pagina" onclick="senddejarP(<?php echo $row8["pages_id"]; ?>, <?php echo $row8["member_id_send"]; ?>)" style="position: relative;">  
	<?php	} else { ?>
				<input type="button" class="boton" value="Seguir la pagina - Denegada" onclick="sendseguirP(<?php echo $_GET['id']; ?>)" style="position: relative; bottom: 14px;">  
	<?php 	} ?>	
		</div>
		<h2><?php echo $row["topic"]; ?></h2>
		<div id="textarea">
			<p><?php echo $row["text"]; ?></p>
		</div>
		<div id="text_edit" style="display: none;">
			<hr>
			<h1>Editar Pagina</h1>
			<textarea id="text" rows="3" cols="60"><?php echo $row["text"]; ?></textarea>
			<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('text').value != '') sendText(<?php echo $_GET['id']; ?>);" style="position: relative; bottom: -3px;"> <hr> 
		</div>
		<h1>Spickline</h1>
		<hr>
		<form>
			<textarea id="spicktext_<?php echo $_GET["id"]?>" name="space" rows="2" cols="60"></textarea>
			<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('spicktext_<?php echo $_GET["id"]?>').value != '') comment(<?php echo $_GET["id"]?>, 1)">  
		</form>
		<div id="comment_container">
			<div id="pgpage">
				<div id="new_comment_<?php echo $_GET["id"]?>"></div>
				<?php 					
				$pag = new Pagination("SELECT * FROM comment_pages WHERE pages_id=".mysql_real_escape_string($_GET["id"])." ORDER BY date DESC");
				$pag->pgSelectItemperPage(10);
				$pag->pgSelectType("page");
				$res = $pag->pgDoPagination();
				while ($line = mysql_fetch_assoc($res)) {
					$u_row = fetchUser($line['member_id_send']);
					echo '<div id="commentlist_'.$line["id"].'"> <a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"]." ".$u_row["lastname"].'</a> <br/>'.$line['text'].'<br/><h6 style="margin:0px;padding:0px;">'.$line["date"].'</h6>';
					if ($line['member_id_send'] == $user_row["member_id"]) echo ' - <a href="javascript:void(0)" onclick="deleteEventComment(\''.$line['id'].'\')">Eliminar</a><hr width="80%">';
					echo '</div>';
				}	
				echo $pag->pgShowAjaxPagination(", ".$_GET["id"]);			
				?>
			</div>
		</div>
	</div>
</div>
<?php } else echo "Ups esta pagina ha sido borrada"; }?>