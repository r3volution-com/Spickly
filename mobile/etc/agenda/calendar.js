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
  
ctable();  
  
}





function ctable(){

var body = document.getElementsByTagName("body")[0];

var tbl     = document.createElement("table");
var tblBody = document.createElement("tbody");


  // creating all cells
  for (var j = 0; j < 7; j++) {
    // creates a table row
    var row = document.createElement("tr");
  
    for (var i = 0; i < 7; i++) {
      // Create a <td> element and a text node, make the text
      // node the contents of the <td>, and put the <td> at
      // the end of the table row
      var cell = document.createElement("td");
      var cellText = document.createTextNode(i);
      cell.appendChild(cellText);
      row.appendChild(cell);
    }
  
    // add the row to the end of the table body
    tblBody.appendChild(row);
  }
    // put the <tbody> in the <table>
  tbl.appendChild(tblBody);
  // appends <table> into <body>
  body.appendChild(tbl);
  // sets the border attribute of tbl to 2;
  tbl.setAttribute("border", "2");
  
}










