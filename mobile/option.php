<?php if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>"); ?>
<div id="page">
	<div id="sidebar" class="sideblock">	
		<ul>
			<li>Version 0.5</li>
			<hr>
			<li><a href="./mobile/index.php"> Versi&oacute;n Movil.</a></li>
			<li><a href="in.php?p=partners"> Sistema de patrocinio.</a></li>
			<li><a href="./download/index.php"> Centro de descargas.</a></li>
			<hr>
			<li><a href="./more.php?m=legal"> Condiciones de uso.</a></li>
			<li><a href="./more.php?m=abuso"> Contra el abuso.</a></li>	
			<li><a href="./more.php?m=contacta"> Contacta con nosotros.</a></li>	
				
		</ul>
	</div>
	<div id="container">
		<form autocomplete="off" method="POST" action="in_actions.php?a=edata">
			<h3>Ajustes Personales</h3>
			<table>
				<tr>
					<td>Cambiar Nombre: </td><td><input type="text" name="nombre" id="nombre" value="<?php echo $user_row["firstname"]; ?>" placeholder="Nombre..."/></td>	
				</tr>
				<tr>
					<td>Cambiar Apellidos: </td><td><input type="text" name="apellidos" id="apellidos" value="<?php echo $user_row["lastname"]; ?>" placeholder="Apellidos..."/></td>
				</tr>
				<tr>
					<td>Cambiar E-Mail: </td><td><input type="text" name="email" id="email" value="<?php echo $user_row["email"]; ?>" placeholder="E-Mail..." onblur="document.getElementById('inforet').innerHTML=checkEmail(this.value); else document.getElementById('inforet').innerHTML=''"/></td><td><span id="inforet"></span></td>
				</tr>
				<tr>
					<td>Cambiar Contrase&ntilde;a: </td><td><input type="password" name="pass" id="pass" placeholder="Contrase&ntilde;a..." onblur="if (this.value != document.getElementById('pass2').value) document.getElementById('inforet').innerHTML='Las contrase&ntilde;as no coinciden'; else document.getElementById('inforet').innerHTML=''"/></td>
					<td>Repite la Contrase&ntilde;a: </td><td><input type="password" name="pass2" id="pass2" placeholder="Contrase&ntilde;a..." onblur="if (this.value != document.getElementById('pass').value) document.getElementById('inforet').innerHTML='Las contrase&ntilde;as no coinciden'; else document.getElementById('inforet').innerHTML=''"/></td>
				</tr>
				<tr><td><input type="submit" id="enviar" name="enviar" class="boton" value="Enviar"></td></tr>
			</table>
		</form>	
		<hr>
		<h3> Mis intereses </H3>	
		<form method="POST" action="in_actions.php?a=emoreinfo">
			<table>
				<tr>
					<td>Me gusta... </td><td><textarea cols="100" rows="5" id="cuadro2" name="ilikeit" id="ilikeit" placeholder="La pizza, la musica pop, ..."><?php echo $user_row["ilikeit"]; ?></textarea></td>	
				</tr>
				<tr>
					<td>Biografia: </td><td><textarea cols="100" rows="5" id="cuadro1" name="bio" id="bio" placeholder="Cuentanos tu vida!"><?php echo $user_row["bio"]; ?></textarea></td>
				</tr>
			<tr><td><input type="submit" id="enviar2" name="enviar2" class="boton" value="Enviar"></td></tr>
			</table>
		</form>	
	</div>
</div>
<script>
	function checkEmail(email) { 
		var re  = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/; 
		if (!re.test(email)) return "El email no es valido"; 
		else {
			var res = sendAjax("out_actions.php?a=checkemail&e="+email);
			if (res != "OK") return res;
		}
		return "Correcto"; 
	}
</script>