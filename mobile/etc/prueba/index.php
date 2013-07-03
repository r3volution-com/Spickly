<!DOCTYPE html>
<html>
<head>
<script src="calendar.js"></script>
</head>
<?php
if ($_GET){
   $month = $_GET["new_month"];
   $year = $_GET["new_year"];
}else{
   $tiempo_actual = time();
   $month = date("n", $tiempo_actual);
   $year = date("Y", $tiempo_actual);
}
?>
<body onload="calendar(<?php echo $year.",".$month?>);">



</body>
</html>