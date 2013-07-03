<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
   <title>Calendario PHP</title>
   <link rel="STYLESHEET" type="text/css" href="estilo.css">
</head>

<body>
<div align="center">
<?php
require ("calendario.php");

if ($_GET){
   $month = $_GET["new_month"];
   $year = $_GET["new_year"];
}else{
   $tiempo_actual = time();
   $month = date("n", $tiempo_actual);
   $year = date("Y", $tiempo_actual);
}

calendar($year, $month);
?>
</div>
</body>
</html>