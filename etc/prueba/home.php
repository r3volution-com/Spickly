<?	
session_start();
	if($_SESSION['login'] == TRUE) {
?>
		<html>
		<body>
		<h1>Hola <? echo $_SESSION['user'];?></h1>
				<a href="logout.php">Cerrar Sesion </a>
		</body>		
<?
	
	}else{
			header("location:index.php");


}
?>