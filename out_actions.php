<?php
	session_start();
	include('config.php'); 
	header ('Content-type: text/html; charset=utf-8');
	$link = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD );
	if(!$link) die('FALLO AL CONECTAR CON EL SERVIDOR MYSQL: ' . mysql_error());
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) die("NO SE ENCUENTRA LA BASE DE DATOS");
	mysql_query("set names utf8");
if (isset($_GET["a"]) && $_GET["a"]) {
	if (!function_exists("checkEmail")) {
		function checkEmail($email){
			$exp = "/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/"; 
			if(preg_match($exp,$email)){ 
				$my_temp_variable=explode("@",$email);
				$my_temp_variable= array_pop($my_temp_variable);
				if(checkdnsrr($my_temp_variable,"MX")) return true; 
				else return false; 
			} else return false; 
		} 
	}
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) $str = stripslashes($str);
		return mysql_real_escape_string($str);
	}
	switch ($_GET["a"]) {
		case "reg":
			if( isset($_POST['name']) ){
				//Start session
				session_start();
				//Include database connection details
				require_once('config.php');
				//Array to store validation errors
				$errmsg_arr = array();
				//Validation error flag
				$errflag = false;
				//Sanitize the POST values
				$fname = clean($_POST['name']);
				$lname = clean($_POST['surname']);
				$email = clean($_POST['email']);
				$password = clean($_POST['password']);
				$password2 = clean($_POST['password2']);
				$check = clean($_POST['check']);
				$sexo = clean($_POST['sexo']);
				$age = $_POST['ano'].'-'.$_POST['mes'].'-'.$_POST['dia'];
				$city = clean($_POST['city']);
				//Input Validations
				if(!$fname) {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png"> Falta el nombre</p>';
					$errflag = true;
				}
				if(!$lname) {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Falta los apellidos</p>';
					$errflag = true;
				}
				if(!$email) {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Falta el email</p>';
					$errflag = true;
				}
				if ($email && !checkEmail($email)) {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Email incorrecto</p>';
					$errflag = true;
				}
				if(!$password) {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Falta contraseña</p>';
					$errflag = true;
				}
				if($password != $password2) {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Las contraseñas no son &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;iguales</p>';
					$errflag = true;
				}
				if($password && (strlen($password)) > 25 || strlen($password) < 6) {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">La contraseña debe tener &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;entre 6 y 25 caracteres</p>';
					$errflag = true;
				}
				if($check = false) {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Debes aceptar las condiciones</p>';
				}	
					if($sexo == '') {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Falta el sexo</p>';
					$errflag = true;
				}
				if($age == '') {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Falta la edad</p>';
					$errflag = true;
				}
				if($city == '') {
					$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Falta la ciudad</p>';
					$errflag = true;
				}
				//Check for duplicate login ID
				if($email != '') {
					$qry = "SELECT * FROM members WHERE email='".$email."' ";
					$result = mysql_query($qry);
					if($result) {
						if(mysql_num_rows($result) > 0) {
							$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">El email esta en uso</p>';
							$errflag = true;
						}
						@mysql_free_result($result);
					} else die("Query failed");
				}
			}
			//If there are input validations, redirect back to the registration form
			if($errflag) {
				$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
				session_write_close();
				header("location: ./index.php");
				exit();
			}
				//Check whether the query was successful or not
			if($result) {
				//Create INSERT query
				$qry = "INSERT INTO members(firstname, lastname, email, passwd, birthdate, sex, city) VALUES('".mysql_real_escape_string($fname)."','".mysql_real_escape_string($lname)."','".mysql_real_escape_string($email)."','".mysql_real_escape_string(md5($password))."','".mysql_real_escape_string($age)."','".mysql_real_escape_string($sexo)."','".mysql_real_escape_string(htmlentities($city))."' )";
				$result = @mysql_query($qry);
				$para      = $_SESSION['remail'];
				$titulo = 'Bienvenido a Spickly.es';
				$mensaje = 'Saludos '.$fname.' '.$_lname.' y gracias por registrarte en spickly.es esperamos que disfrutes tu estancia.\n El correo con el que te has registrado es: '.$email.' \nGracias por confiar en nosotros. Spickly lo hacemos entre todos!\n\nEste mensaje ha sido generado automaticamente, por favor no contestes a este remitente.';
				$cabeceras = 'From: registro@spickly.es' . "\r\n" .
				'Reply-To: contacta@spickly.es' . "\r\n";
				mail($para, $titulo, $mensaje, $cabeceras);
				//Check whether the query was successful or not
				?>
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<link href="./resources/css/index_style.css" rel="stylesheet" type="text/css">
						<title>Se ha registrado con exito</title>
					</head>
					<body align="center">
						<p style="color:white;font-size:50px;" >Se ha completado con exito tu registro.</p>
						<br>
						<br>
						<p style="color:white;font-size:50px;">Disfruta de tu estancia en Spickly.</p><br><br>
						<p style="color:white;font-size:50px;">En 5 segundos seras redirigido.</p><br><br>
						<script>setTimeout("location.href='index.php'", 5000);</script>
					</body>
				</html>
			<?php
				exit();
			} else die("Query failed");
		break;
		// Script para iniciar sesion
		case "login":
			//Start session
			session_start();
			//Include database connection details
			require_once('config.php');
			//Array to store validation errors
			$errmsg_arr = array();
			//Validation error flag
			$errflag = false;
			if (isset($_GET["cs"]) && (isset($_SESSION['SESS_MEMBER_ID']))) {
				if ($_GET["cs"] == 0) mysql_query("UPDATE members SET online='0' WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'");
				else if ($_GET["cs"] == 1) mysql_query("UPDATE members SET online='1' WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'");
				die("ok");
			}
			//Sanitize the POST values
			$email = clean($_POST['email']);
			$password = clean($_POST['password']);
			//Input Validations
			if($email == '') {
				$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Debes introducir tu E-mail</p>';
				$errflag = true;
			}
			if($password == '') {
				$errmsg_arr[] = '<p style="color:white;"><img src="resources/images/error.png">Debes introducir tu contraseña</p>';
				$errflag = true;
			}
			//If there are input validations, redirect back to the login form
			if($errflag) {
				$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
				session_write_close();
				header("location: index.php");
				exit();
			}
			//Create query
			$qry="SELECT * FROM members WHERE email='$email' AND passwd='".md5($_POST['password'])."'";
			$result=mysql_query($qry);
			//Check whether the query was successful or not
			if($result) {
				if(mysql_num_rows($result) == 1) {
					//Login Successful
					session_regenerate_id();
					$member = mysql_fetch_array($result);
					mysql_query("UPDATE members SET online='1' WHERE member_id='".mysql_real_escape_string($member['member_id'])."'");
					$_SESSION['SESS_MEMBER_ID'] = $member['member_id'];
					session_write_close();
					if (!isset($_SESSION['lastpage'])) header("location: in.php");
					else die("<script>location.href='".$_SESSION['lastpage']."';</script>");
					exit();
				} else {
					header("location: index.php?e=1&email=".$_POST['email']);
					exit();
				}
			} else die("Query failed");
		break;
		case "lout":
			if (isset($_SESSION['SESS_MEMBER_ID'])) {
				//Unset the variables stored in session
				mysql_query("UPDATE members SET status='0' WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'") or die(mysql_error());
				unset($_SESSION['SESS_MEMBER_ID']);
				unset($_SESSION["user_row"]);
				if (isset($_SESSION['lastpage'])) unset($_SESSION['lastpage']);
				session_unset();
				header("location: index.php");
			} else {
				header("location: index.php");
				die();
			}
		break;
		case "rpass":
			$email=$_POST['email']; 
			$res=mysql_query("SELECT * FROM members WHERE email='$email'") or die(mysql_error());
			if (mysql_num_rows($res)) {
				$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
				$cad = "";
				for($i=0;$i<12;$i++) {
					$cad .= substr($str,rand(0,62),1);
				}
				$ipass=mysql_query("UPDATE members SET passwd='".md5($cad)."' WHERE email = '$email' ") or die(mysql_error());
				?>
				<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<link href="./resources/css/index_style.css" rel="stylesheet" type="text/css">
						<title>Verifica Tu E-Mail</title>
					</head>
					<body align="center">
					<br>
					<br>
						<p style="color:white;font-size:50px;" >Ahora comprueba tu Email.</p>
						<br>
						<br>
						<p style="color:white;font-size:50px;">En spickly no damos nada por perdido.</p><br><br>
						<p style="color:white;font-size:50px;">Seras redirigido en 5 segundos.</p><br><br>
						<script>setTimeout("location.href='index.php'", 5000);
						</script>
					</body>
				</html>
				<?
				//Se envia el E-mail 
				$headers = "MIME-Version: 1.0\r\n"; 
				$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
				mail($email, "Recuperación", "Su nueva contraseña es: $cad", $headers); 
				$_SESSION["lastpage"] = "http://spickly.es/in.php?p=option";
			} 
			if (!$i) echo "Ese E-mail no existe";
		break;
		case "status":
			if (isset($_GET["t"])){
				if ($_GET["status"]) {
					mysql_query("UPDATE members SET status=1 WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'") or die(mysql_error());
					die("ok");
				} else {
					mysql_query("UPDATE members SET status=0 WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'") or die(mysql_error());
					die("ok");
				}
			}
		break;
		case "checkemail":
			if (isset($_GET["e"]) && $_GET["e"]) {
				if (checkEmail($_GET["e"])) {
					$result = mysql_query("SELECT * FROM members WHERE email='".mysql_real_escape_string($_GET["e"])."'") or die(mysql_error());
					if(mysql_num_rows($result)) die("Email en uso");
					else die("OK");
				} else die ("Email invalido");
			} else die("Formulario no valido o vacio");
		break;
		default:
			die("OPCION INCORRECTA");
		break;;
	}
	die();
} else die("ERROR");
?>