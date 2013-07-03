<?php 
	session_start();
	include('config.php'); 
	header ('Content-type: text/html; charset=utf-8');
	//Check whether the session variable SESS_MEMBER_ID is present or not
	if(!isset($_SESSION['SESS_MEMBER_ID']) || !$_SESSION['SESS_MEMBER_ID']) {
		$_SESSION["lastpage"] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header("location: ./index.php");
		exit();
	}
	
	//Select database
	$link = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD );
	if(!$link) die('FALLO AL CONECTAR CON EL SERVIDOR MYSQL: ' . mysql_error());
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) die("NO SE ENCUENTRA LA BASE DE DATOS");
	if (isset($_SESSION["user_row"])) { //Si la session existe rescatamos la informacion
		global $user_row;
		global $friendArray;
		global $banArray;
		global $ismybirthday;
		$user_row = $_SESSION["user_row"];
		$friendArray = $user_row["friendlist"];
		$banArray = $user_row["friendlist"];
		$ismybirthday = $user_row["ismybirthday"];
	} else { //Si no existe volvemos a extraer la informacion
		$perfil = mysql_query("SELECT * FROM members WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'") or die(mysql_error());
		if(mysql_num_rows($perfil)) { // Comprobamos que exista el registro con la ID ingresada 
			global $user_row;
			global $friendArray;
			global $banArray;
			global $ismybirthday;
			$user_row = mysql_fetch_array($perfil); 
			//Creamos la sesion que contendra toda la informacion
			$_SESSION["user_row"] = $user_row;
			//Creamos un array con la lista de amigos
			$res = mysql_query("SELECT * FROM friends WHERE (member_id_send=".mysql_real_escape_string($user_row["member_id"])." OR member_id_receive=".mysql_real_escape_string($user_row["member_id"]).") AND alive=1") or die(mysql_error());
			if (mysql_num_rows($res)) {
				while ($row = mysql_fetch_array($res)) {
					if ($row["member_id_send"] != $user_row["member_id"]) $u_row = $row["member_id_send"];
					else if ($row["member_id_receive"] != $user_row["member_id"]) $u_row = $row["member_id_receive"]; 
					$friendArray[] = $u_row;
				}
				sort($friendArray);
			} else $friendArray = 0;
			$_SESSION["user_row"]["friendlist"] = $friendArray;
			//Creamos un array con la lista de usuarios bloqueados
			$res = mysql_query("SELECT * FROM friends WHERE (member_id_send=".mysql_real_escape_string($user_row["member_id"])." OR member_id_receive=".mysql_real_escape_string($user_row["member_id"]).") AND alive=2") or die(mysql_error());
			if (mysql_num_rows($res)) {
				while ($row = mysql_fetch_array($res)) {
					if ($row["member_id_send"] != $user_row["member_id"]) $u_row = $row["member_id_send"];
					else if ($row["member_id_receive"] != $user_row["member_id"]) $u_row = $row["member_id_receive"]; 
					$banArray[] = $u_row;
				}
				sort($banArray);
			} else $banArray = 0;
			$_SESSION["user_row"]["banlist"] = $banArray;
			//Comprobamos si es el cumpleaños del usuario
			$res = mysql_query("SELECT * FROM members WHERE ((MONTH(birthdate) = MONTH(NOW()) AND DAY(birthdate) = DAY(NOW())) AND member_id=".$user_row["member_id"].") AND DAY(last_visit) != DAY(NOW())") or die(mysql_error());
			$ismybirthday = mysql_num_rows($res);
			$_SESSION["user_row"]["ismybirthday"] = $ismybirthday;
		} else die ("El perfil seleccionado no existe o ha sido eliminado.");
	}
		mysql_query("set names utf8");
	include("functions.php");
	include("resources/lib/pagination.php");
?>