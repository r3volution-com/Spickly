function select_month(month){
	 switch (month){
	 	case 1:
			var name_month="Enero";
			break;
	 	case 2:
			var name_month="Febrero";
			break;
	 	case 3:
			var name_month="Marzo";
			break;
	 	case 4:
			var name_month="Abril";
			break;
	 	case 5:
			var name_month="Mayo";
			break;
	 	case 6:
			var name_month="Junio";
			break;
	 	case 7:
			var name_month="Julio";
			break;
	 	case 8:
			var name_month="Agosto";
			break;
	 	case 9:
			var name_month="Septiembre";
			break;
	 	case 10:
			var name_month="Octubre";
			break;
	 	case 11:
			var name_month="Noviembre";
			break;
	 	case 12:
			var name_month="Diciembre";
			break;
	}
	return name_month;
}


function calendar(year, month){
var name_month = select_month(month);

document.write(name_month);




}