<?Php
function calcula_numero_dia_semana($dia,$mes,$ano){     
$numerodiasemana = date('w', mktime(0,0,0,$mes,$dia,$ano));     
if ($numerodiasemana == 0) $numerodiasemana = 6;     else        $numerodiasemana--;     
return $numerodiasemana; } 


function ultimoDia($mes,$ano){
     $ultimo_dia=28;     
	 while (checkdate($mes,$ultimo_dia + 1,$ano)){
	 $ultimo_dia++;
     }
return $ultimo_dia; 
}

function select_month($month){
	 switch ($month){
	 	case 1:
			$name_month="Enero";
			break;
	 	case 2:
			$name_month="Febrero";
			break;
	 	case 3:
			$name_month="Marzo";
			break;
	 	case 4:
			$name_month="Abril";
			break;
	 	case 5:
			$name_month="Mayo";
			break;
	 	case 6:
			$name_month="Junio";
			break;
	 	case 7:
			$name_month="Julio";
			break;
	 	case 8:
			$name_month="Agosto";
			break;
	 	case 9:
			$name_month="Septiembre";
			break;
	 	case 10:
			$name_month="Octubre";
			break;
	 	case 11:
			$name_month="Noviembre";
			break;
	 	case 12:
			$name_month="Diciembre";
			break;
	}
	return $name_month;
}


function calendar($year,$month){
//tomo el nombre del mes que hay que imprimir
$name_month = select_month($month);

//construyo la tabla general
echo '<table class="calendar" cellspacing="10" cellpadding="25" border="2">';
echo '<tr><td colspan="7" class="tit">';
//tabla para mostrar el mes el año y los controles para pasar al mes anterior y siguiente
echo '<table width="100%" cellspacing="2" cellpadding="2" border="0"><tr><td class="nextmonth">';
//calculo el mes y ano del mes anterior
$mes_anterior = $month - 1;
$ano_anterior = $year;
if ($mes_anterior==0){
   $ano_anterior--;
   $mes_anterior=12;
}
echo '<a href="index.php?new_month=' . $mes_anterior . '&new_year=' . $ano_anterior .'"><span> <-- </span></a></td>';
echo '<td class="titmesano">' . $name_month . " " . $year . '</td>';
echo '<td class="mesanterior">';
//calculo el mes y ano del mes siguiente
$mes_siguiente = $month + 1;
$ano_siguiente = $year;
if ($mes_siguiente==13){
   $ano_siguiente++;
   $mes_siguiente=1;
}
echo '<a href="index.php?new_month=' . $mes_siguiente . '&new_year=' . $ano_siguiente . '"><span> --> </span></a></td>';
//finalizo la tabla de cabecera
echo '</tr></table>';
echo '</td></tr>'; 
echo '   <tr>         <td  class="diasemana"><span>L</span></td>         <td  class="diasemana"><span>M</span></td>         <td  class="diasemana"><span>X</span></td>         <td  class="diasemana"><span>J</span></td>         <td  class="diasemana"><span>V</span></td>         <td  class="diasemana"><span>S</span></td>         <td  class="diasemana"><span>D</span></td>      </tr>'; 

$today = 1;
$numero_dia = calcula_numero_dia_semana(1,$month,$year);
$ultimo_dia = ultimoDia($month,$year);


echo "<tr>";

for($i=0;$i<7;$i++){
	if($i < $numero_dia){
		echo '<td class="diainvalido"><span></span></td>';
	}else {
	echo '<td class="diavalido"><span>' . $today . '</span></td>';      
	$today++;   
	}
}
 
 echo "</tr>";
		$numero_dia = 0;
	while($today <= $ultimo_dia){
	
		if($numero_dia == 0){
		echo "<tr>";}
		echo '<td class="diavalido"><span>' . $today . '</span></td>';
		$today++;
		$numero_dia++;
		if($numero_dia == 7){
		
		$numero_dia = 0;
		echo "</tr>";
		
		}
	}
	for ($i=$numero_dia;$i<7;$i++){   echo '<td class="diainvalido"><span></span></td>'; }
echo "</tr>"; echo "</table>";
 
}?>

















