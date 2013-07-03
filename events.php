<?php include_once("common.php");
if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>");
if (isset($_GET["c"])) {
?>
<div id="page">
	<div id="inboxin">
		<div id="form">
			<form method="POST" action="in_actions.php?a=cevent">
				<table style="position: relative; width: 100%">
					<tr>
						<td colspan="2">
							<h2>Titulo Del Evento:</h2>
							<input name="titulo" type="text"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<h2>Descripcion Del evento:</h2>
							<textarea id="texto" name="texto"></textarea>
						</td>
					</tr>
					<tr>
						<td><h2>Dia del evento</h2></td>
						<td><h2>Hora del Evento</h2></td>
					</tr>
					<tr>
						<td><input type="date" name="fecha"></td>
						<td><input type="time" name="hora"></td>
					</tr>
					<tr>
						<td><h2>Lugar del Evento</h2></td>
						<td><h2>Telefono de contacto</h2></td>
					</tr>
					<tr>
						<td><input type="text" name="lugar"/></td>
						<td><input type="text" name="tlf"/></td>
					</tr>
					<tr>
						<td colspan="2">
								<h2>Imagen Del Evento:</h2>
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
								<br>
								<br>
								<input type="submit" id="cpage" name="cpage" class="boton" value="Crear Evento" style="position: relative; bottom:13px; left:45%;">
						</td>
					</tr>
				</table>
		</div>
	</div>
</div>
	<?php
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
else if (isset($_GET["mas"])) {
?>
<div id="page">
	<div id="pages_friend">
	<?php
		$pageid = $_GET["id"];
		$res0 = mysql_query("SELECT * FROM members_has_events WHERE alive=1 AND events_id='".$pageid."' ORDER BY alive") or die(mysql_error());  
		if (!mysql_num_rows($res0)) echo "No hay usuarios que  vallan al 100%";
		else {
			echo "<h1>Si que van</h1>";
			while ($row0 = mysql_fetch_array($res0)) {
				$res3 = mysql_query("SELECT * FROM members WHERE member_id=".$row0["member_id_receive"]." ");
				$row3 = mysql_fetch_array($res3);
				echo "<br><a href=in.php?p=profile&id=".$row0["member_id_receive"].">".$row3["firstname"]." ".$row3["lastname"]."</a></br>";
			} 
		}
		echo "<hr>";	
		$res1 = mysql_query("SELECT * FROM members_has_events WHERE alive=3 AND events_id='".$pageid."' ORDER BY alive") or die(mysql_error());  
		if (!mysql_num_rows($res1)) echo "No hay usuarios que esten dudando";
		else {
			echo "<h1>Quizas van</h1>";
			while ($row1 = mysql_fetch_array($res1)) {
				$res3 = mysql_query("SELECT * FROM members WHERE member_id=".$row1["member_id_receive"]." ");
				$row3 = mysql_fetch_array($res3);
				echo "<br><a href=in.php?p=profile&id=".$row1["member_id_receive"].">".$row3["firstname"]." ".$row3["lastname"]."</a></br>";
			} 
		}			
		echo "<hr>";
		$res2 = mysql_query("SELECT * FROM members_has_events WHERE alive=2 AND events_id='".$pageid."' ORDER BY alive") or die(mysql_error());  
		if (!mysql_num_rows($res2)) echo "No hay usuarios que no vayan";
		else {
			echo "<h1>No van</h1>";
			while ($row2 = mysql_fetch_array($res2)) {
				$res3 = mysql_query("SELECT * FROM members WHERE member_id=".$row2["member_id_receive"]." ");
				$row3 = mysql_fetch_array($res3);
				echo "<br><a href=in.php?p=profile&id=".$row2["member_id_receive"].">".$row3["firstname"]." ".$row3["lastname"]."</a></br>";
			} 
			echo "<hr>";
		}
		?>
	</div>
</div>
<?php
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
else if (!isset($_GET["id"]) || !$_GET["id"]) die("<script>location.href='in.php?p=profile';</script>");
else {
$res = mysql_query("SELECT * FROM events WHERE id=".mysql_real_escape_string($_GET["id"]));
if (mysql_num_rows($res)){
	$row = mysql_fetch_array($res);
	$res2 = mysql_query("SELECT * FROM members_has_events WHERE member_id_send=".mysql_real_escape_string($user_row["member_id"])." AND events_id=".mysql_real_escape_string($_GET["id"]));
	$row2 = mysql_num_rows($res2);
	$fecha = date_create($row["date"]);
	$fecha = date_format($fecha, 'H:i:s d-m-Y');
	$pageid = $_GET["id"];
	$res8 = mysql_query("SELECT * FROM members_has_events WHERE member_id_receive='".$_SESSION['SESS_MEMBER_ID']."' AND events_id='".$pageid."' ") or die(mysql_error());  
	$row8 = mysql_fetch_array($res8);
?>
<div id="page">
	<div id="top_eventos">
		<table>
			<tr>
				<td>
				<?php 
					$u_from = fetchUser($row["member_id_send"]);
					$imagep = getProfilePhotoByPhotoId($u_from["prf_img"]);
					$image = getPagePhotoByPhotoId($row["photos_id"]);
					echo '<img src="'.$image.'" width="200px" height="200px" >'; 
				?>		
				</td>
				<td>
					<h3><?php echo $row["topic"]."</h3>"; if (($row['member_id_send'] == $_SESSION['SESS_MEMBER_ID'])) { ?> <a href="javascript:void(0)" onclick="document.getElementById('textarea').innerHTML=document.getElementById('text_edit').innerHTML;" ><img src="resources/images/lapiz.png" /></a>   <a href="in_actions.php?a=dpage&d=<?php echo $row['id']; ?>">Borrar evento</a>  <?php } ?> </h3>
					<h5>Fecha:<small> <?php echo date ("d-m-Y", strtotime($row["date"])); ?></small></h5>
					<h5>Hora:<small> <?php echo date ("h:m", strtotime($row["date"])); ?></small></h5>
					<h5>Lugar:<small> <?php echo $row["place"]; ?></small></h5>
					<h5>Contacto:<small> <?php echo $row["contact"]; ?></small></h5>
					<h5>Autor:&nbsp;<?echo"<img src='".$imagep."' width='30px' height='30px'>";?><small> <a href="in.php?p=profile&id=<?php echo $row["member_id_send"]; ?>"><?php echo $u_from["firstname"]." ".$u_from["lastname"]; ?></a></small></h5>
				</td>
			</tr>	
		</table>	
	</div>
	<div id="grafica_evento_div">
		<div id="graf">
			<?php // Mejorar sistema de SELECT un bucle dentro de otro no me gusta XD
			$pageid = $_GET["id"];
			$res_polls = mysql_query("SELECT * FROM polls WHERE event_id='".$pageid."' ") or die(mysql_error());  
			$i=0; 
			while ($row_polls = mysql_fetch_array($res_polls)) {
				++$i; ?>
				<div id="<?php echo $row_polls["id"];?>" style="display: <?php if($i == 1) echo "block"; else echo "none"; ?>;"> 
					<div id="graphic_bar">
						<table border="1" class="graphic_bar">	
							<tr style="height:100px; text-align:center;"> 									
								<a href="#" onclick="document.getElementById('<?php echo $row_polls["id"]?>').style.display='none';document.getElementById('<?php echo $row_polls["id"] - 1?>').style.display='block';"> ← </a>
								<a href="#" onclick="document.getElementById('<?php echo $row_polls["id"]?>').style.display='none';document.getElementById('<?php echo $row_polls["id"] + 1?>').style.display='block';"> → </a><p>
<?php 							echo $row_polls["topic"]."<br>"; 
								$res_poll_opt = mysql_query("SELECT * FROM poll_options WHERE poll_id='".$row_polls["id"]."'") or die(mysql_error());
								if (!mysql_num_rows($res_poll_opt)) echo "Consulta sin respuestas";
								while ($row_poll_opt = mysql_fetch_array($res_poll_opt)) {
									$res_poll_opt_x = mysql_query("SELECT * FROM poll_votes WHERE option_id='".$row_poll_opt["id"]."' ") or die(mysql_error());  
									$res_poll_opt_n = mysql_query("SELECT * FROM poll_votes ") or die(mysql_error());  
									$cantvx = mysql_num_rows($res_poll_opt_x); //Cantidad de resultados de X
									$cantvn = mysql_num_rows($res_poll_opt_n); //Cantidad de resultados en total
									if($cantvx == 0) $result = 0;
									else $result = $cantvx * '100' / $cantvn;
									?>
								<td>
									<div id="graphic_bar_yes" style="height: <?php echo $result; ?>px;" class="ggraphic_bar">
										<?php if($row8['alive'] == 1){ ?><input type="submit" value="<?php echo $row_poll_opt["text"]; ?>" onclick="sendVote(<?php echo $row_poll_opt["id"]; ?>,<?php echo $row_polls["id"];?>,<?php echo $pageid; ?>)" > <?php } else echo $row_poll_opt["text"]; ?><br>
									</div>
								</td> 
									<?php
								}
								echo $result."%";															
?>
							</tr> 				 			
						</table>
					</div>
<?php 
				echo "</div>";
			}
?>
		</div>	
	</div>
	<div id="divsor"></div>
	<div id="overlay" class="overlay"></div>
	<div id="modal" class="modal">	
		<script>
			function dimePropiedades(){ 
				var indice = document.form.options.selectedIndex;
				var valor = document.form.options.options[indice].value;
				var preview = document.getElementById('opt');
				preview.innerHTML="";
				for (i=0;i<valor;i++) {
					var input = document.createElement("input");
					input.type="text";
					input.id="option";
					input.name="option[]";
					preview.appendChild(input);
				}
			}
		</script>	
		<input type="button" value="X" class="boton_redondo" onclick="document.getElementById('modal').style.display='none';document.getElementById('overlay').style.display='none';">
		<form id="form" name="form" method="POST" action="in_actions.php?a=event&id=<?php echo $_GET["id"];?>" >	
			<h1>Pregunta</h1>
			<input type="text" id="pregunta" name="pregunta" />
			<h1>Cantidad de opciones</h1>
			<select name="options" onchange="dimePropiedades()" >
			   <?php
				for ($i=1; $i<=5; $i++) {	
					echo '<option value="'.$i.'" >'.$i.'</option>';						
				}
				?>
			</select>				
			<div id="opt" ><!-- Div en el ke se muestran las opciones --></div>
			<input type="submit" class="boton" value="Enviar" /> 	
		</form>	
	</div>
	<div id="invia" class="modal">	
		<input type="button" value="X" class="boton_redondo" onclick="document.getElementById('invia').style.display='none';document.getElementById('overlay').style.display='none';">
		<?php $pageid = $_GET["id"]; ?>

			<h1>Amigos</h1>	
			<form method="GET" action="in_actions.php?a=event">	
				<input type="hidden" id="a" name="a" value="event"/>
				<input type="hidden" id="user" name="user" value=""/>
				<input type="hidden" id="idp" name="idp" value="<?php echo $_GET["id"];?>"/>
				<div id="inv_events_amigos" ondrop="drop(this, event)" ondragenter="return false" ondragover="return false" style="overflow: auto; padding: 10px;">
					<?php
						$pageid = $_GET["id"];
						$res9 = mysql_query("SELECT * FROM members_has_events WHERE member_id_receive='".$_SESSION['SESS_MEMBER_ID']."' AND alive=1 AND events_id='".$pageid."' ORDER BY alive") or die(mysql_error());  
						if (mysql_num_rows($res9)) {
							if (!$friendArray) echo "Aun no tienes amigos :(";
							else {
								$res1 = mysql_query("SELECT member_id_receive FROM members_has_events WHERE member_id_receive IN (".implode(",", $friendArray).") AND alive=1 AND events_id='".$pageid."' ORDER BY alive") or die(mysql_error());  
								$row1 = mysql_fetch_row($res1);
								foreach($friendArray as $user1) {
									if (!in_array($user1, $row1)) $u_row = fetchUser($user1); 
									$image = getProfilePhotoByPhotoId($u_row["prf_img"]); 
									echo '<img src="'.$image.'" title="'.$u_row["firstname"].'" draggable="true" ondragend="c_array_inv(this, event);" ondragstart="drag(this, event);" id="'.$u_row["member_id"].'" width="75" height="75" >';
								} 
							} 
						} else echo 'No estas vinculado ha este evento';
						?>
				</div>

			</form>

		<div id="inv_events_select" ondrop="drop(this, event);" ondragenter="return false" ondragover="return false" >
			<h1>Amigos invitados</h1>	
		</div>
		
		<input type="submit" id="inv" name="inv" class="boton" onclick="send_ids();" style="position: relative; top: 10px;">	
	
	<script>
			var invarray = new Array();
			
			function c_array_inv(persona, evento) {
				invarray.push(persona.id);
			}
			function drag(persona, evento) {
				evento.dataTransfer.setData('Text', persona.id);
			}
			function drop(contenedor, evento) {
				var id = evento.dataTransfer.getData('Text');
				contenedor.appendChild(document.getElementById(id));
				evento.preventDefault();
			}
			function sacar(){
				var list = $('#inv_events_select .persona').attr("id");
				$("b").append(document.createTextNode(list));
			}
			
			function send_ids(){
				var objeto = JSON.stringify(invarray);
				var res = sendAjaxPOST("in_actions.php?a=event", "ids="+objeto);
				if (res) alert (res);
			}
			
		</script>
	</div>
	<div id="sidebar" class="sideblock">
		<input type="button" value="Crear encuesta" class="boton" onclick="document.getElementById('modal').style.display='block';document.getElementById('overlay').style.display='block';">
		<input type="button" value="Invitar Amigos" class="boton" onclick="document.getElementById('invia').style.display='block';document.getElementById('overlay').style.display='block';">
		<h1>Seguidores</h1>
		<hr>
		<?php
		$res9 = mysql_query("SELECT * FROM members_has_events WHERE alive=1 AND events_id='".$pageid."' ORDER BY alive") or die(mysql_error());  
		if (!($numev = mysql_num_rows($res9))) echo "No hay usuarios siguiendo esta pagina";
		else {
			while ($row9 = mysql_fetch_array($res9)) {
				$res3 = mysql_query("SELECT * FROM members WHERE member_id=".$row9["member_id_receive"]." ");
				$row3 = mysql_fetch_array($res3);
				echo "<br><a href=in.php?p=profile&id=".$row9["member_id_receive"].">".$row3["firstname"]." ".$row3["lastname"]."</a></br>";
			} 	
		}
		if ($numev > 5) echo "<a href=in.php?p=eventos&id=".$pageid."&mas>Ver mas</a>";
		?>
	</div>
	<div id="container_eventos">
		<div id="check">
			<?php
			if ($row8['alive'] == NULL) echo "Tienen que invitarte para poder interactuar con el evento";
			else if ($row8['alive'] == 0) {			
				?>
				<input type="button" class="boton" value="Voy" onclick="sendvoyE(<?php echo $row8["events_id"]; ?>,<?php echo $row8["member_id_send"]; ?>)">  
				<input type="button" class="boton" value="Quizas" onclick="sendquizasE(<?php echo $row8["events_id"]; ?>,<?php echo $row8["member_id_ send"]; ?>)">  
				<input type="button" class="boton" value="No Voy" onclick="sendnvoyE(<?php echo $row8["events_id"]; ?>,<?php echo $row8["member_id_ send"]; ?>)">  	
				<?php
			} else if ($row8['alive'] == 1) echo "Estado: Vas al evento";
			else if ($row8['alive'] == 2) echo "Estado: No vas al evento";
			else if ($row8['alive'] == 3) echo "Estado: Quizas iras al evento";
			?>
		</div>
		<div id="textarea">
			<p><?php echo $row["text"]; ?></p>
		</div>
		<div id="text_edit" style="display: none;">
			<textarea id="text" rows="3" cols="60"><?php echo $row["text"]; ?></textarea>
			<input type="button" class="boton" value="Enviar" onclick="" style="position: relative; bottom: 15px;">  
		</div>
		<h1>Spickline</h1>
		<hr>
<?php 	if($row8['alive'] == 1){?>
			<form>
				<textarea id="spicktext_<?php echo $_GET["id"]?>" name="space" rows="2" cols="60"></textarea>
				<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('spicktext_<?php echo $_GET["id"]?>').value != '') comment(<?php echo $_GET["id"]?>, 2)">  
			</form>
		<?php
		} else echo "No estas invitado a esta pagina";
		?>
		<div id="comment_container">
			<div id="pgevento">
				<div id="new_comment_<?php echo $_GET["id"]?>"></div>
				<?php 					
				$pag = new Pagination("SELECT * FROM comment_events WHERE events_id=".mysql_real_escape_string($_GET["id"])." ORDER BY date DESC");
				$pag->pgSelectItemperPage(10);
				$pag->pgSelectType("evento");
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
<?php } else echo "Ups este evento no existe o ha sido borrado"; } ?>