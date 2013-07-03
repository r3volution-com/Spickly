<?php 
session_start();
$_SESSION['login'];
$user = $_POST['usuario'];
$pass = $_POST['contraseña'];


if ($user=='Pepe' || $pass=='pass' ) {
	$_SESSION['login'] = TRUE;
	$_SESSION['user'] = $user;

	header("location:home.php");
}else{
session_destroy();

echo "User o pass incorrecto";
}

?>
