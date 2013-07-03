<?php
//FUNCIONES BASICAS DE SQL	
if (!function_exists("fetchQuery")) {
	function fetchQuery($query) {
		$res = mysql_query($query) or die(mysql_error().": ".$query); 
		$row = mysql_fetch_array($res);
		return $row;
	}
}
if (!function_exists("fetchUser")) {
	function fetchUser($foe = '') {
		global $MYSQL_PREFIX;
		if (!$foe) $foe = $_SESSION['SESS_MEMBER_ID'];
		$foe = mysql_real_escape_string($foe);
		$row = fetchQuery("SELECT * FROM members WHERE member_id='$foe'");
		return $row;
	}
}
//FUNCIONES DE COMPROBACION
if (!function_exists("checkMP")) {
	function checkMP() {
		global $MYSQL_PREFIX;
		$nuevosmp = 0;
		$row = fetchUser();
		$res = mysql_query("SELECT * FROM mps WHERE member_id_receive='".mysql_real_escape_string($row["member_id"])."'") or die(mysql_error());
		while ($row = mysql_fetch_array($res)) {
			if ($row["readed"] == 0) {
				$nuevosmp++;
			}
		}
		return $nuevosmp;
	}
}
//Funciones de Reemplazo y parseo de texto
if (!function_exists("parseText")) {
	function parseText($text) {
		$text = mysql_real_escape_string(nl2br(htmlentities(utf8_decode($text))));
		if(preg_match("/^http|www|spickly/", $text)) {
			$text = URLtoProfile($text);
			$text = URLtoYouTube($text);
			$text = URLtoLink($text);
		}
		$text = emoticonToImage($text);
		return $text;
	}
}
if (!function_exists("URLtoYouTube")) {
	function URLtoYouTube($text) {
		//se obtiene el identificador del video
		if (preg_match('%(?:http\://|http\://www\.|www\.)?(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $text, $match)) {
			$video_id = $match[1];
			//el codigo embed de youtube 
			$code = '<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"http://www.youtube.com/v/\1\"><param name=\"allowFullScreen\" value=\"true\"><param name=\"allowscriptaccess\" value=\"always\"><embed src=\"http://www.youtube.com/v/\1\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"425\" height=\"344\"></object>';
			$text = preg_replace('%(?:http\://|http\://www\.|www\.)?(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $code, $text);
		}
		return $text;
	}
}
if (!function_exists("URLtoProfile")) {
	function URLtoProfile($text) {
		$ret = ' ' . $text;
		if (preg_match('#(?:http\://|http\://www\.|www\.)?spickly\.es\/(?:pr\_(.*)|in\.php\?p\=profile\&id\=(.*))#i', $text, $match)) {
			if ($match[1]) $id = $match[1];
			else $id = $match[2];
			$row = fetchUser($id);
			$ret = preg_replace("#(?:http\://|http\://www\.|www\.)?spickly\.es/(?:pr\_(.*[0-9])|in\.php\?p\=profile\&id\=([0-9]))(?:/[^ \"\n\r\t<]*)?#i", '<a href="http://spickly.es/pr_'.$row["member_id"].'" target="_blank">@'.$row["firstname"].'</a> ', $ret);
			$ret = substr($ret, 1);
		}
		return($ret);
	}
}
if (!function_exists("URLtoLink")) {
	function URLtoLink($text){
		// pad it with a space so we can match things at the start of the 1st line. 
		$ret = ' ' . $text;
		// matches an "xxxx://yyyy" URL at the start of a line, or after a space. 
		// xxxx can only be alpha characters. 
		// yyyy is anything up to the first space, newline, comma, double quote or < 
		$ret = preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href=\"\2://\3\" target=\"_blank\">\2://\3</a>', $ret);
		// matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing 
		// Must contain at least 2 dots. xxxx contains either alphanum, or "-" 
		// zzzz is optional.. will contain everything up to the first space, newline,  
		// comma, double quote or <. 
		$ret = preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href=\"http://\2.\3\" target=\"_blank\">\2.\3</a>', $ret);
		// matches an email@domain type address at the start of a line, or after a space. 
		// Note: Only the followed chars are valid; alphanums, "-", "_" and or ".". 
		$ret = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
		// Remove our padding.. 
		$ret = substr($ret, 1);
		return($ret);
	}
}
if (!function_exists("emoticonToImage")) {
function emoticonToImage($valor){
	$caritas = array(" XD", " :)", "XD ", ":) ");
	$imagenes = array(" <img src=\"images/emoticonos/xd.gif\" width=\"20px\" height=\"20px\" />",
	" <img src=\"images/emoticonos/feliz.png\" width=\"20px\" height=\"20px\" />",
	"<img src=\"images/emoticonos/xd.gif\" width=\"20px\" height=\"20px\" /> ",
	"<img src=\"images/emoticonos/feliz.png\" width=\"20px\" height=\"20px\" /> "
	);
	return (str_replace($caritas, $imagenes, $valor));
}
}
if (!function_exists("image_resize")) {
	function image_resize($src, $dst, $width, $height, $crop=0){

  if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

  $type = strtolower(substr(strrchr($src,"."),1));
  if($type == 'jpeg') $type = 'jpg';
  switch($type){
	case 'bmp': $img = imagecreatefromwbmp($src); break;
	case 'jpg': $img = imagecreatefromjpeg($src); break;
	case 'png': $img = imagecreatefrompng($src); break;
	default : return "Unsupported picture type!";
  }

  // resize
  if($crop){
	if($w < $width or $h < $height) return "Picture is too small!";
	$ratio = max($width/$w, $height/$h);
	$h = $height / $ratio;
	$x = ($w - $width / $ratio) / 2;
	$w = $width / $ratio;
  }
  else{
	if($w < $width and $h < $height) return "Picture is too small!";
	$ratio = min($width/$w, $height/$h);
	$width = $w * $ratio;
	$height = $h * $ratio;
	$x = 0;
  }

  $new = imagecreatetruecolor($width, $height);

  // preserve transparency
  if($type == "png"){
	imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
	imagealphablending($new, false);
	imagesavealpha($new, true);
  }

  imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

  switch($type){
	case 'bmp': imagewbmp($new, $dst); break;
	case 'jpg': imagejpeg($new, $dst); break;
	case 'png': imagepng($new, $dst); break;
  }
  return true;
}
}
//Funciones Extra
if (!function_exists("pagination")) {
function pagination($query, $item_per_page = 20, $url_var = "pg") {
	//examino la p?na a mostrar y el inicio del registro a mostrar 
	if (isset($_GET[$url_var])) $pagina = $_GET[$url_var]; 
	if (!isset($pagina)) { 
		$inicio = 0; 
		$pagina=1; 
	} else $inicio = ($pagina - 1) * $item_per_page; 
	
	// La idea es pasar tambi?en los enlaces las variables hayan llegado por url.
	$enlace = $_SERVER['PHP_SELF'];
	$query_string = "?";
 
	//Si no se defini???ariables propagar, se propagar?odo el $_GET menos la variable pg (por compatibilidad con versiones anteriores)
	if (isset($_GET[$url_var])) unset($_GET[$url_var]); // Eliminamos esa variable del $_GET
	$propagar = array_keys($_GET);

	// Este foreach est?omado de la Clase Paginado de webstudio
	foreach($propagar as $var){
		if(isset($GLOBALS[$var])) $query_string.= $var."=".$GLOBALS[$var]."&"; // Si la variable es global al script
		elseif(isset($_REQUEST[$var])) $query_string.= $var."=".$_REQUEST[$var]."&"; // Si no es global (o register globals est?n OFF)
	}

	// A??mos el query string a la url.
	$enlace .= $query_string;
	
	//miro a ver el n??o total de campos que hay en la tabla con esa b??eda 
	$item_actual = ($pagina-1)*$item_per_page;
	$res = mysql_query($query); 
	$num_total_registros = mysql_num_rows($res); 
	$res = mysql_query($query." LIMIT ".$item_actual.", ".$item_per_page); 
	//calculo el total de p?nas 
	$total_paginas = ceil($num_total_registros / $item_per_page); 
	$s_paginas = "";
	//muestro los distintos ?ices de las p?nas, si es que hay varias p?nas 
	if ($total_paginas > 1){ 
		if ($pagina > 1) {
			$previus_page = $pagina-1;
			$s_paginas .= " <a href='".$enlace.$url_var."=1'>Primera</a> ";
			$s_paginas .= " <a href='".$enlace.$url_var."=".$previus_page."'>Anterior</a> ";
		}
		for ($i=1;$i<=$total_paginas;$i++){ 
			//si muestro el ?ice de la p?na actual, no coloco enlace 
			if ($pagina == $i) $s_paginas .= $pagina." ";
			//si el ?ice no corresponde con la p?na mostrada actualmente, coloco el enlace para ir a esa p?na 
			else $s_paginas .= "<a href='".$enlace.$url_var."=".$i."'>".$i."</a> ";
		}
		if ($pagina != $total_paginas) {
			$next_page = $pagina+1;
			$s_paginas .= " <a href='".$enlace.$url_var."=".$next_page."'>Siguiente</a> ";
			$s_paginas .= " <a href='".$enlace.$url_var."=".$total_paginas."'>Ultima</a> ";
		}
	}
	
	$resultado = array(
	"query_result" => $res,
	"n_items" => $num_total_registros,
	"n_pages" => $total_paginas,
	"items_per_page" => $item_per_page,
	"act_page" => $pagina,
	"select_page" => $s_paginas
	);
	
	return $resultado;
}
}
?>