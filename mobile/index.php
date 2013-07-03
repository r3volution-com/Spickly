<?php
	session_start();
	//Check whether the session variable SESS_MEMBER_ID is present or not
	if(isset($_SESSION['SESS_MEMBER_ID']) && $_SESSION['SESS_MEMBER_ID']) {
		header("location: ./in.php");
		exit();
	}
	$array = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
	if ($array['geoplugin_latitude']) {
		$data = file_get_contents('http://free.worldweatheronline.com/feed/weather.ashx?q='.$array['geoplugin_latitude'].','.$array['geoplugin_longitude'].'&format=json&num_of_days=2&key=2191cb58f9163415121708');
		$data = json_decode($data, true);
		$actweather = $data['data']['current_condition'][0]['weatherDesc'][0]['value'];
		if ($actweather == "Sunny") {
			$wtype = 0;
		} else if ($actweather == "Clear") {
			$wtype = 1;
		} else if ($actweather == "Partly Cloudy") {
			$wtype = 2;
		} else if ($actweather == "Partly Cloudy" && (date("H") > 20 || date("H") < 8)) {
			$wtype = 3;
		} else if ($actweather == "Cloudy") {
			$wtype = 4;
		} else {
			$wtype = 5;
		}
	} else $wtype = -1;
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Spickly</title>
		<meta charset="UTF-8">
		<link rel="icon" type="image/png" href="resources/images/favicon.ico" />
		<link href="./resources/css/index_style.css" rel="stylesheet"/>
		<meta name="title" content="Spickly">
		<meta name="DC.Title" content="Spickly">
		<meta http-equiv="title" content="Spickly">
		<meta name="DC.Creator" content="www.spickly.es">
		<meta name="keywords" content="una,nueva,red,social,spickly.red,social,red social,amigos,chat,fotos,">
		<meta http-equiv="keywords" content="una,nueva,red,social,spickly.red,social,red social,amigos,chat,fotos">
		<meta name="description" content="Spickly, Una comunidad, Una web">
		<meta http-equiv="description" content="Spickly, Una comunidad, Una web">
		<meta http-equiv="DC.Description" content="Spickly, Una comunidad, Una web">
		<meta name="author" content="Spickly">
		<meta name="DC.Creator" content="Spickly">
		<meta name="vw96.objectype" content="Document">
		<meta name="resource-type" content="Document">
		<meta http-equiv="Content-Type" content="UTF-8">
		<meta name="distribution" content="all">
		<meta name="robots" content="all">
		<meta name="revisit" content="30 days">
		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
		<?php if (date("H") > 20 || date("H") < 8) { ?>
			<style type="text/css">
				body {
						background: -webkit-radial-gradient(#0088F5, #0f3ccc);
						background: radial-gradient(#0088F5, #0f3ccc);
						background: -moz-radial-gradient(45px 45px, cover, #0088FF 0%, #0F3CCC 100%);
				}
			</style>
		<?php } ?>
		<script>
				function sendAjax(s_url) {
					var resp = "";
					$.ajax({
						url : s_url,
						type : 'GET',
						async: false,
						success : function(res){
							resp = res;
						}				
					});
					return resp;
				}
			function checkEmail( email ) { 
				var re  = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/; 
				if (!re.test(email)) return "El email no es valido"; 
				else {
					var res = sendAjax("out_actions.php?a=checkemail&e="+email);
					if (res != "OK") return res;
				}
				return "Correcto"; 
			}
			function gup( name ){
				var regexS = "[\\?&]"+name+"=([^&#]*)";
				var regex = new RegExp ( regexS );
				var tmpURL = window.location.href;
				var results = regex.exec( tmpURL );
				if( results == null ) return "";
				else return results[1];
			}
			function goregister() {
				if (gup("r")) {
					document.getElementById('login').style.display='none'; 
					document.getElementById('register').style.display='block'; 
					document.getElementById('registrar').style.display='none'; 
					document.getElementById('loguear').style.display='block';
				}
			}
			$(function(){
				var drop = $('.drop').detach();
				function create(){
					var clone = drop
						.clone()
						.appendTo('.contain')
						.css('left', Math.random()*$(document).width()-20)
						.animate({
							'top': $(document).height()-20
						},
						Math.random(1000)+500,
						function(){
							$(this).fadeOut(200,function(){
								$(this).remove();
							}); 
						});
				}
				function makeRain(){
					for(var i=0; i<30; i++){
						setTimeout(create,Math.random()*700);
					}
				}
				setInterval(makeRain, 500);
			});
		</script>
		<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-39093100-1']);
			  _gaq.push(['_trackPageview']);

			  (function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();
		</script>
	</head>
<body onload="goregister()">

<!-- Start Alexa Certify Javascript -->
<script type="text/javascript">
_atrk_opts = { atrk_acct:"uz4jh1aMQV002R", domain:"spickly.es",dynamic: true};
(function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
</script>
<noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=uz4jh1aMQV002R" style="display:none" height="1" width="1" alt="" /></noscript>
<!-- End Alexa Certify Javascript -->

<?php if ($wtype >= 0) { ?>
<div id="thermometer">
	<div id="mercury" style="height:<?php echo $data['data']['current_condition'][0]['temp_C']+30; ?>%; top: <?php echo 70-$data['data']['current_condition'][0]['temp_C']; ?>%;"></div>
	<h3 style="position: relative; margin-left: -5px; z-index: 100;"><?php echo $data['data']['current_condition'][0]['temp_C']; ?><br/>ºC</h3>
</div>
<?php } ?>
<?php if ($wtype == 0 || $wtype == 2) echo '<div class="sun"></div>'; ?>
<?php if ($wtype == 1 || $wtype == 3) echo '<div class="moon"></div>'; ?>
<?php if ($wtype == 2 || $wtype == 3) { ?>
	<div id="sq">
		<div class="circ1"></div>
		<div class="circ2"></div>
		<div class="circ3"></div>
	</div>
	<div id="sq2">
		<div class="circ1"></div>
		<div class="circ2"></div>
		<div class="circ3"></div>
	</div>
	<div id="sq3">
		<div class="circ1"></div>
		<div class="circ2"></div>
		<div class="circ3"></div>
	</div>
<?php } ?>
<?php if ($wtype == 4) { ?><div class="contain"><div class="drop"></div></div><?php } ?>
	<div id="container">
		<div id="top">
		<ul>
			<li><input type="button" id="registrar" value="Registrate" onclick="document.getElementById('rpass').style.display='none';document.getElementById('rpass').style.display='none'; document.getElementById('login').style.display='none'; document.getElementById('register').style.display='block'; this.style.display='none'; document.getElementById('loguear').style.display='block';"/></li>
			<li><input type="button" id="loguear"  value="Accede" style="display: none;" onclick="document.getElementById('olvidado').style.display='block'; document.getElementById('rpass').style.display='none'; document.getElementById('login').style.display='block';document.getElementById('register').style.display='none'; this.style.display='none'; document.getElementById('registrar').style.display='block';"/></li>
		</div>
		<div id="logo">
			<img src="resources/images/logo_index.png" alt="logo" />

		</div>	
		<div id="login" style="display: block;">
			<form name="loginForm" method="post" action="out_actions.php?a=login">
			<table>
				<tr>
					<td><input name="email" placeholder="E-mail" size="20" type="text" class="first" value="<?php if($_GET['e'] == 1){echo $_GET['email'];}?>"/></td>
				</tr>
				<tr>
					<td><input name="password" placeholder="Contrase&ntilde;a" size="20" type="password" class="last"/></td>
				</tr>
				<tr>
					<td><input type="submit" name="Submit" value="Entrar"/></td>
				</tr>
				<tr>
				<td><h6><a href="#" id="olvidado" onclick="document.getElementById('login').style.display='none'; document.getElementById('rpass').style.display='block'; document.getElementById('loguear').style.display='block'; this.style.display='none';">¿Has olvidado tu contraseña?</a></h6></td>
				</tr>
				<?php
					if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
						echo '<tr><td><ul class="err">';
						foreach($_SESSION['ERRMSG_ARR'] as $msg) {
							echo '<li>',$msg,'</li>'; 
						}
						echo '</ul></td></tr>';
						unset($_SESSION['ERRMSG_ARR']);
					}
				?><?php if(isset($_GET["e"]) && $_GET["e"] == 1) echo '<p style="color:white; position:absolute; top:85%; left:25%;"><img src="resources/images/error.png">Error: Usuario o contrase&ntilde;a incorrectos</p>'; ?>	
				
			</table>
			</form>	
		</div>
		
		<div id="register">
			<form align="center" id="loginForm" name="loginForm" method="post" action="out_actions.php?a=reg">
			<table>
				<tr>
					<td colspan="2"><input style="background-color:rgb(210, 255, 251); color:blue;" name="name" type="text" class="first" placeholder="Nombre" /></td>
				</tr>
				<tr>
					<td colspan="2"><input style="background-color:rgb(210, 255, 251); color:blue;" name="surname" type="text" placeholder="Apellidos" /></td>
				</tr>
				<tr>
					<td colspan="2"><input style="background-color:rgb(210, 255, 251); color:blue;" name="email" id="email" type="text" placeholder="E-mail" onblur="alert(checkEmail(this.value));" /><span id="resemail"></span></td>
				</tr>
				<tr>
					<td colspan="2"><input style="background-color:rgb(210, 255, 251); color:blue;" name="password" id="password" type="password" placeholder="Contrase&ntilde;a" /></td>
				</tr>
				<tr>
					<td colspan="2"><input style="background-color:rgb(210, 255, 251); color:blue;" name="password2" type="password" placeholder="Repetir Contrase&ntilde;a" onblur="if (this.value != document.getElementById('password').value) alert('Las contraseñas no coinciden');" /></td>
				</tr>
				<tr>
					<td colspan="2"><input style="background-color:rgb(210, 255, 251); color:blue;" name="city" type="text" placeholder="Ciudad" class="last" /></td>
				</tr>
				<tr>
					<td width="60"><br><b>Sexo:</b> </td><td><br><input type="radio" name="sexo" value="1"/>Hombre <input type="radio" name="sexo" value="2"/>Mujer</td>
				</tr>
				<tr>
					<td><br><b>Naci el:</b> </td>
					<td><br>
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
							for($i=date('o')-13; $i>=1940; $i--){
								if ($i == date('o'))
									echo '<option value="'.$i.'" selected>'.$i.'</option>';
								else
									echo '<option value="'.$i.'">'.$i.'</option>';
							}
							?>
					</select>
					</td>
				</tr>
				<tr>
				    <td colspan="2"><br><input type="checkbox" value="check" name="check" id="check"/>Acepto las <a href="more.php?m=legal">condiciones de uso</a></td>
				</tr>
				<tr style="text-align: center;">
					<td colspan="2"><input type="submit" name="Submit" value="Registrarse" /></td>
				</tr>
			</table>			
			</form>
		</div>

	</div>
	
		<div id="rpass" style="display: none; #rpass #loguear{position:relative;left:30px;top:-15px;}">
			<form  method="post" action="out_actions.php?a=rpass"> 
				<table>
						<tr>
								<td><h3 style="color:white;">Recuperar contraseña</h3></td> 
						</tr>
						<tr>
								<td><input style="border-radius:7px;" name="email" type="text" class="textfield" placeholder="Correo electrónico" id="email" value="" /></td>
						</tr>
						<tr style="text-align: center;">
							<td><input type="submit" name="Submit" value="Solicitar" /> </td>
						</tr>
				</table>
			</form>  
		</div>
	<div id="mastright"></div>

	<script type="text/javascript">

	var _gaq = _gaq || [];
	var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';
	_gaq.push(['_require', 'inpage_linkid', pluginUrl]);
	_gaq.push(['_setAccount', 'UA-37781665-1']);
	_gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>