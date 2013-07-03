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

function ndays_week($day,$month,$year){     
$numerodiasemana = date('w', mktime(0,0,0,$month,$day,$year));     
if ($numerodiasemana == 0) $numerodiasemana = 6;     else        $numerodiasemana--;     
return $numerodiasemana; } 


function ultimoDia($month,$year){
     $ultimo_dia=28;     
	 while (checkdate($month,$ultimo_dia + 1,$year)){
	 $ultimo_dia++;
     }
return $ultimo_dia; 
}

echo $numero_dia = ndays_week(1,$month,$year);
echo $ultimo_dia = ultimoDia($month,$year);
?>
<body onload="calendar(<?php echo $year.",".$month?>);">



</body>
</html>