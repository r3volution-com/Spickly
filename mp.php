<?php if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>");
if (isset($_GET["t"]) && $_GET["t"]) {
	$idYo = $user_row["member_id"];		
?>
	<script>
		function mpLink(id){
			window.location="in.php?p=mp&mp="+id;
		}
	</script>
	<div id="page">
		<div id="sidebar_mp">
			<a href="in.php?p=mp&t=1">Bandeja de entrada</a>
			<?php
				if ($nuevosmp > 0)echo '<img id="entrada" src="resources/images/inbox-new.png" alt="nuevo" title="&iexcl;Nuevos Mensajes!"/>';
				else echo '<img id="entrada" src="resources/images/inbox-entrada.png" alt="entrada" title="Entrada"/>';
			?>
			<a id="second-a" href="in.php?p=mp&t=2">Bandeja de salida</a>
			<img id="salida" src="resources/images/inbox-salida.png" alt="salida" title="Salida"/>
			
		</div>
		<div id="mp_container">
			<a href="in.php?p=mp"><input type="button" class="boton" value="Nuevo mensaje" style="float:right; bottom: 40px;"></a>
			<?php if($_GET["t"] == 1) {
				$resIn = new Pagination("SELECT * FROM mps WHERE member_id_receive='$idYo' ORDER BY date DESC");
				$resIn->pgSelectItemperPage(5); 
				$resIn->pgSelectType("mpin");
				$resin = $resIn->pgDoPagination();	 
				?>
				<h1>Mensajes recibidos</h1>
				<hr>
				<div id="pgmpin">
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
						echo $resIn->pgShowAjaxPagination(); ?>
					</table>
				</div>
			<?php } else if ($_GET["t"] == 2) {
				$resOut = new Pagination("SELECT * FROM mps WHERE member_id_send='$idYo' ORDER BY date DESC");
				$resOut->pgSelectItemperPage(5); 
				$resOut->pgSelectType("mpout");
				$resout = $resOut->pgDoPagination();	
				?>
				<h1>Mensajes enviados</h1>
				<hr>
				<div id="pgmpout">
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
				</div>
			<?php } ?>
		</div> 
	</div>
<?php
} else if ((!isset($_GET["t"]) || !$_GET["t"]) && (isset($_GET["mp"]) && $_GET["mp"])) {
		$idmp=mysql_real_escape_string($_GET["mp"]);
		$row = fetchQuery("SELECT * FROM mps WHERE id=$idmp");
		if ($row["member_id_send"] == $user_row["member_id"]) {
			$u_row = fetchUser($row["member_id_receive"]);
			$type=0;
		} else {
			$u_row = fetchUser($row["member_id_send"]);
			$type=1;
		}
		if ($row["read"] == 0 && $user_row["member_id"] == $row["member_id_receive"]) mysql_query("UPDATE mps SET readed=1 WHERE id=$idmp") or die(mysql_error());
?>
<div id="page">
	<div id="sidebar_mp">
		<a href="in.php?p=mp&t=1">Bandeja de entrada</a>
		<?php
			if ($nuevosmp > 0) echo '<img id="entrada" src="resources/images/inbox-new.png" alt="nuevo" title="&iexcl;Nuevos Mensajes!"/>';
			else echo '<img id="entrada" src="resources/images/inbox-entrada.png" alt="entrada" title="Entrada"/>';
		?>
		<a id="second-a" href="in.php?p=mp&t=2">Bandeja de salida</a>
		<img id="salida" src="resources/images/inbox-salida.png" alt="salida" title="Salida"/>
		
	</div>
	<div id="inboxin">
		<?php
			//Cojo las imagenes de perfil Por ID
			$images = getProfilePhotoByPhotoId($u_row["prf_img"]);
		?>
		<table border="0" style="width:100%;" align="center" id="mensaje_recibido" cellspacing="0">
			<tr>
				<td style="margin:0 auto; width:100%;"><?php if ($type == 1) echo "De "; else echo "Para "; ?>
				<?php echo "<img style='border-radius:7px;' src=".$images." width='30' height='30'>";?> <?php echo"<a href='in.php?p=profile&id=".$u_row["member_id"]."'>".$u_row["firstname"]." ".$u_row["lastname"]."</a>"; ?><hr></td>
			</tr>
			<tr>
				<td style="margin:0 auto; width:100%;"><h2>Asunto:</h2>
				<?php echo"<div id='asunto'>".$row['topic']."</div>"; ?><hr></td>
			</tr>
			<tr>
				<td style="display:none;"> </td>
				<td style="margin:0 auto; width:100%; "><h2>Mensaje:</h2>
				<?php echo $row['message']; ?><hr></td>
			</tr>
		</table>
		<br>
		<a id="button" style="height:20px;" class="boton" href="in.php?p=mp&id=<?php echo $u_row["member_id"]; ?>"><?php if ($type) echo "Responder"; else echo "Reenviar"; ?></a>
	</div>
</div>
<?php 
} else if ((!isset($_GET["t"]) || !$_GET["t"]) && (!isset($_GET["mp"]) || !$_GET["mp"])) {
	if (isset($_GET["id"])) $u_row = fetchUser($_GET["id"]);
?>
<div id="page">
	<div id="inboxin">
		<form action="in_actions.php?a=cmp" method="POST"> 
			<h2>Para</h2>
			<input type="hidden" name="receiver" id="receiver" value="<?php if (isset($u_row["member_id"])) { ?><?php echo $u_row["member_id"]; ?><?php } ?>" />
			<input id="au_receiver" name="receivername" type="text" onkeypress="getSuggestToInput(document.getElementById('au_receiver').value, 'mpfriend', 'au_receiver', 'receiver')" autocomplete="off" <?php if (isset($_GET["id"])) echo 'disabled="disabled"'; ?> value="<?php if (isset($_GET["id"])) echo $u_row["firstname"]." ". $u_row["lastname"]; ?>" /><br />
			<ul id="mpfriend"></ul>
			<h2>Asunto</h2>
			<input align="center" id="topic_mp" name="topic" size="50" maxlength="60" width="100px" type="text" /><br />
			<h2>Mensaje</h2>
			<textarea name="message" class="inputbox" type="text"></textarea><br />
			<input type="submit" class="boton" value="Enviar">  
			
		</form>
	</div> 
</div>
<?php 
} else die("<script>location.href='in.php?p=profile';</script>");
?>