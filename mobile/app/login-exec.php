<?php
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	//Start session
	session_start();
	
	//Include database connection details
	require_once('config.php');
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Connect to mysql server
	$link = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD );
	if(!$link) {
		die('FALLO AL CONECTAR CON EL SERVIDOR MYSQL: ' . mysql_error());
	}
	
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) {
		die("NO SE ENCUENTRA LA BASE DE DATOS");
	}

if (isset($_GET["l_out"]) && $_GET["l_out"] == 1 && (isset($_SESSION['SESS_MEMBER_ID']))) {
	//Unset the variables stored in session
	mysql_query("UPDATE members SET online='2' WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'");
	unset($_SESSION['SESS_MEMBER_ID']);
	if (isset($_SESSION['lastpage'])) unset($_SESSION['lastpage']);
	session_unset();
	header("location: in.php");
	die();
}
if (isset($_GET["cs"]) && (isset($_SESSION['SESS_MEMBER_ID']))) {
	if ($_GET["cs"] == 0) mysql_query("UPDATE members SET online='0' WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'");
	else if ($_GET["cs"] == 2) mysql_query("UPDATE members SET online='2' WHERE member_id='".mysql_real_escape_string($_SESSION['SESS_MEMBER_ID'])."'");
	else die("error");
	die("ok");
}
	//Sanitize the POST values
	$email = clean($_POST['email']);
	$password = clean($_POST['password']);
	
	//Input Validations
	if($email == '') {
		echo "<script>alert('Debes introducir tu E-mail')</script>";
		$errflag = true;
	}
	if($password == '') {
		echo "<script>alert('Debes introducir tu contraseña')</script>";
		$errflag = true;
	}
	
	//If there are input validations, redirect back to the login form
	if($errflag) {
		echo "<script>location.href='index.php'</script>";
		exit();
	}
	
	//Create query
	$qry="SELECT * FROM members WHERE email='$email' AND passwd='".md5($_POST['password'])."'";
	$result=mysql_query($qry);
	
	//Check whether the query was successful or not
	if($result) {
		if(mysql_num_rows($result) == 1) {
			//Login Successful
			$member = mysql_fetch_array($result);
			mysql_query("UPDATE members SET online='1' WHERE member_id='".mysql_real_escape_string($member['member_id'])."'");
			$_SESSION['SESS_MEMBER_ID'] = $member['member_id'];
			session_write_close();
			$resultado[]=array("logstatus"=>"1");
			echo json_encode($resultado);
			exit();
		} else {
			header("location: index.php?e=1");
			exit();
		}
	} else die("Query failed");
?>