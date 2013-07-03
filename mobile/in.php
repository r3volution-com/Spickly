<?php
	include_once("common.php");
	$nuevosmp = checkMP();
	if (isset($_GET["p"]) && $_GET["p"]) {
		switch ($_GET["p"]) {
			case "index":
				$opt = 1;
				break;
			case "profile":
				$opt = 2;
				break;
			case "friends":
				$opt = 3;
				break;
			case "mp":
				$opt = 4;
				break;
			case "photo":
				$opt = 5;
				break;
			case "search":
				$opt = 6;
				break;
			case "option":
				$opt = 7;
				break;
			case "pages":
				$opt = 8;
				break;
			case "events":
				$opt = 9;
				break;
			case "calendar":
				$opt = 10;
				break;
			case "partners":
				$opt = 11;
				break;	

			default: 
				header('location: in.php?p=index');
				die();
				break;
		}
		if ($opt == 2 && isset($_GET["id"]) && $_GET["id"] != $_SESSION['SESS_MEMBER_ID'] && !isset($_COOKIE["lastprofilevisit".$_GET["id"]])) {
			mysql_query("UPDATE members SET num_visits=num_visits+1 WHERE member_id='".mysql_real_escape_string($_GET["id"])."' ") or die(mysql_error());
			setcookie("lastprofilevisit".$_GET["id"], "1", time()+3600);
		} else if ($opt == 1 && !isset($_COOKIE["lastprofilevisit_me"])) {
			mysql_query("UPDATE members SET last_visit=CURRENT_TIMESTAMP() WHERE member_id=".mysql_real_escape_string($user_row["member_id"])) or die(mysql_error());
			setcookie("lastprofilevisit_me", "1", time()+600);
		}
	} else {
		header('location: in.php?p=index');
		die();
	}
?>
<html>
	<head>
		<title>Spickly - <?php 
			switch($opt) {
				case 1:
						echo 'Inicio';
					break;
				case 2:
						echo 'Perfil'; 
					break;
				case 3:
						echo 'Gente';
					break;
				case 4:
						echo 'Mensajes'; 
					break;
				case 5:
						echo 'Albumes'; 
					break;
				case 6:
						echo 'Search'; 
					break;
				case 7:
						echo 'Ajustes'; 
					break;
				case 8:
						echo 'Paginas'; 
					break;
				case 9:
						echo 'Eventos'; 
					break;
				case 10:
						echo 'Calendario'; 
					break;
				case 11:
						echo 'Partners'; 
					break;	
				default:
					break;
			}
			//echo memory_get_usage();
		?></title>
		<link rel="icon" type="image/png" href="resources/images/favicon.ico" />
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
		<script>			
			var my_info = new Array('id','name','lname','prf_img');
			my_info [my_info[0]] = ['<?php echo $user_row["member_id"]; ?>'];
			my_info [my_info[1]] = ['<?php echo $user_row["firstname"]; ?>'];
			my_info [my_info[2]] = ['<?php echo $user_row["lastname"]; ?>'];
			my_info [my_info[3]] = ['<?php echo getProfilePhotoByPhotoId($user_row["prf_img"]); ?>'];
		</script>
		<link href="resources/css/main.css" rel="stylesheet" type="text/css">
		<?php 
			switch($opt) {
				case 1:
						echo '<link href="resources/css/home.css" rel="stylesheet" type="text/css">';
					break;
				case 2:
						echo '<link href="resources/css/perfil.css" rel="stylesheet" type="text/css">'; 
					break;
				case 3:
						echo '<link href="resources/css/friends.css" rel="stylesheet" type="text/css">';
					break;
				case 4:
						echo '<link href="resources/css/mp.css" rel="stylesheet" type="text/css">'; 
					break;
				case 5:
						echo '<link href="resources/css/photo.css" rel="stylesheet" type="text/css">
						<link rel="stylesheet" href="resources/css/qunit.css" type="text/css" media="screen" />
						<link rel="stylesheet" href="resources/css/styles.css" type="text/css" media="screen" />
						<link rel="stylesheet" href="resources/css/jquery-ui-1.8.17.custom.css" type="text/css" media="screen" />'; 
					break;
				case 6:
						echo '<link href="resources/css/upload.css" rel="stylesheet" type="text/css">'; 
					break;
				case 7:
						echo '<link href="resources/css/option.css" rel="stylesheet" type="text/css">'; 
					break;
				case 8:
						echo '<link href="resources/css/pages.css" rel="stylesheet" type="text/css">'; 
					break;
				case 9:
						echo '<link href="resources/css/eventos.css" rel="stylesheet" type="text/css">'; 
					break;
				case 10:
						echo '<link href="resources/css/calendar.css" rel="stylesheet" type="text/css">'; 
					break;	
				case 11:
						echo '<link href="resources/css/partners.css" rel="stylesheet" type="text/css">'; 
					break;	
				default:
					break;
			} ?>
		<script src="http://code.jquery.com/jquery-1.9.1.min.js" charset="UTF-8"></script>
		<script src="resources/js/dgUpload.js" type="text/javascript"></script>
		<script src="resources/js/tools.js" type="text/javascript" charset="UTF-8"></script>
		<script src="resources/js/jquery.scrollTo-min.js" type="text/javascript"></script>
			<?php
			//echo memory_get_usage();
			if ($opt == 5) echo '
						<script src="resources/js/jquery.phototag.js" type="text/javascript"></script>
						<script src="resources/js/qunit.js" type="text/javascript"></script>
						<script src="resources/js/jquery-ui-1.8.17.custom.min.js" type="text/javascript"></script>'; ?>
</head>
<body onLoad="changeStatus(1);" onUnload="changeStatus(0);" onBeforeUnload="changeStatus(0);" onscroll="if (document.body.scrollTop) document.getElementById('up').style.display='block'; else document.getElementById('up').style.display='none';">
	<ul id="nav" value="nav" >
		<li><a href="./in.php?p=index"><img alt="Logo" src="./resources/images/logo_nav.png"/> </a></li>
		<li><a href="./in.php?p=index">Inicio</a></li>
		<li><a href="./in.php?p=profile">Perfil</a></li>
		<li><a href="./in.php?p=friends">Gente</a></li>
		<li><a href="./in.php?p=mp&t=1">Mensajes<?php if ($nuevosmp > 0) echo "(".$nuevosmp.")"; ?></a></li>
		<li><a href="javascript:void(0);">...</a>
			<ul>
				<li><a href="./in.php?p=option">Ajustes</a></li>
				<li><a href="./out_actions.php?a=lout">Desconectarse</a></li>	
			</ul>	
		</li>
		<form action="in.php?p=friends" method="post" id="search">
			<li><input type="search" name="nombre" id="searchinput" onkeyup="if (document.getElementById('searchinput').value) document.getElementById('response').style.display='block'; else document.getElementById('response').style.display='none'; getSuggest(document.getElementById('searchinput').value, 'response')" autocomplete="off"/><input type="image" id="lupa" name="buscar" style="margin-left: -25px; margin-top: 3px;" src="resources/images/lupa.png"></li>
		</form>
		<li onclick="muestraoculta('upload')" style="cursor:pointer; position:absolute; left:86.5%; bottom:30%; color:white;">Subir Fotos</li>
		<div id="upload" style="background: white; border-color: white;">	
			<div id="drop_zone" class="circulo" ><h1>Arrastra la imagen</h1></div>
			<div>
				<form action="" method="post" enctype="multipart/form-data" style="background: white; border-radius: 10px">
					<br>O si lo prefieres seleccionalas aqui:
					<div class="custom-input-file"><input type="file" id="filesToUpload" name="filesToUpload[]" size="1" class="input-file" multiple accept="image/*" onchange="dgHandle(this.files)" />
						Subir archivo
					</div>
				</form>
			</div>
			<div id="progress_bar" style="display:none;"><div class="percent">0%</div></div>
			<script>
				dgCreateZone('drop_zone');
				dgSelectUri('in_actions.php?a=uphoto');
				dgCreateOutputList('filelist');
				dgSelectType('image');
				dgRedirectUploads("in.php?p=photo&id=");
				dgSelectMaxSize(5);
			</script> 
		</div>
	</ul> 
	<ul id="response"></ul>
	<ul id="filelist">
	<div id="cerrar" onclick="document.getElementById('filelist').style.display='none';" style="cursor:pointer;float: right;position: relative;top: -34px;"> <h3>X</h3> </div>
	</ul>
<?php 
	switch($opt) {
		case 1:
			include("home.php");
			break;
		case 2:
			include("profile.php");
			break;
		case 3:
			include("friends.php");
			break;
		case 4:
			include("mp.php");
			break;
		case 5:
			include("photo.php");
			break;
		case 6:
			include("search.php");
			break;
		case 7:
			include("option.php");
			break;
		case 8:
			include("pages.php");
			break;
		case 9:
			include("events.php");
			break;
		case 10:
			include("calendar.php");
			break;
		case 11:
			include("partners.php");
			break;	
		default:
			break;
	}
	//echo memory_get_usage();
?>
<div id="up" style="display: none;"><a href="javascript:void(0);" alt="nombre" onclick="$(window).scrollTo( 0, 800, {queue:true} ); return false;"><img src="resources/images/up.png"/></a></div>
<div id="videobg"><a href="javascript:void(0);" onclick="document.getElementById('videobg').style.display='none';"><h1 align="right" style="color: white"><br>X</h1></a><div id="videocontainer"></div></div>
<?php 
if ($ismybirthday) { ?>
<script>setVideoToScreen('_l1H6-TAEmM');</script>
<?php } ?>
</body>
</html>
