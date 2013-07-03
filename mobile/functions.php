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
if (!function_exists("getUsername")) {
	function getUsername($id) {
		$id = mysql_real_escape_string($id);
		$row = fetchQuery("SELECT * FROM members WHERE member_id='$id'");
		return $row["firstname"]." ".$row["lastname"];
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
if (!function_exists("replaceChars")) {
	function replaceChars($text) {  
		$text = str_replace("á","&aacute;",$text);  
		$text = str_replace("é","&eacute;",$text);  
		$text = str_replace("í","&iacute;",$text);  
		$text = str_replace("ó","&oacute;",$text);  
		$text = str_replace("ú","&uacute;",$text);  
		$text = str_replace("ñ","&ntilde;",$text);  
		$text = str_replace("<","&lt;",$text);  
		$text = str_replace(">","&gt;",$text);  
		return $text;  
	}
}
if (!function_exists("youtube_data")) {
	function youtube_data($id){
		$url = "http://gdata.youtube.com/feeds/api/videos/". $id;
		$doc = new DOMDocument;
		$doc->load($url);
		$ret = array("image" => 'http://img.youtube.com/vi/'.$id.'/default.jpg', "title" => $doc->getElementsByTagName("title")->item(0)->nodeValue);
		return $ret;
	}
}
if (!function_exists("URLtoYouTube")) {
	function URLtoYouTube($text) {
		//se obtiene el identificador del video
		if (preg_match('%(?:http\://|http\://www\.|www\.)?(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $text, $match)) {
			$video_id = $match[1];
			$data = youtube_data($video_id);
			//el codigo embed de youtube 
			$code = '<a href="#" onclick="setVideoToScreen(\''.$video_id.'\')" oncontextmenu="window.open(\'http://youtube.es/watch?v='.$video_id.'\', \'_blank\'); return false;"><img height="20" width="20" src="'.$data["image"].'"/> '.$data["title"].'</a>';
			$text = preg_replace('%(?:https\://|https\://www\.|http\://|http\://www\.|www\.)?(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $code, $text);
		}
		return $text;
	}
}
if (!function_exists("URLtoProfile")) {
	function URLtoProfile($text) {
		$ret = ' '.$text;
		if (preg_match('#(?:http\://|http\://www\.|www\.)?spickly\.es\/(?:pr\_([0-9]+)|in\.php\?p=profile&id=([0-9]+))#i', $text, $match)) {
			if ($match[1]) $id = $match[1];
			else $id = $match[2];
			$row = fetchUser($id);
			$ret = preg_replace("#(?:http\://|http\://www\.|www\.)?spickly\.es/(?:pr\_([0-9]+)|in\.php\?p=profile&id=([0-9]+))#i", '<a href="http://spickly.es/pr_'.$row["member_id"].'" target="_blank">@'.$row["firstname"].'</a> ', $ret);
			$ret = substr($ret, 1);
		}
		return($ret);
	}
}
if (!function_exists("emoticonToImage")) {
	function emoticonToImage($valor){
		$caritas = array(" xd"," xD", " XD", " :)", "XD ", ":) ");
		$imagenes = array(" <img src=\"resources/images/emoticonos/xd.gif\" width=\"20px\" height=\"20px\" />",
			"<img src=\"/resources/images/emoticonos/xd.gif\" width=\"20px\" height=\"20px\" />",
			"<img src=\"/resources/images/emoticonos/xd.gif\" width=\"20px\" height=\"20px\" />",
			" <img src=\"/resources/images/emoticonos/feliz.png\" width=\"20px\" height=\"20px\" />",
			"<img src=\"/resources/images/emoticonos/xd.gif\" width=\"20px\" height=\"20px\" /> ",
			"<img src=\"/resources/images/emoticonos/feliz.png\" width=\"20px\" height=\"20px\" /> "
		);
		return (str_replace($caritas, $imagenes, $valor));
	}
}
if (!function_exists("parseText")) {
	function parseText($text, $parselinks = 1, $tosql = 0) {
		$text = rtrim(utf8_decode($text));
		//$text = htmlentities($text, ENT_COMPAT, "UTF-8");
		$text = replaceChars($text);
		$text = nl2br(mysql_real_escape_string($text));
		$text = utf8_encode($text);
		if(preg_match("/^http|www|spickly/", $text) && $parselinks) {
			$text = URLtoProfile($text);
			$text = URLtoYouTube($text);
		}
		if ($parselinks) $text = emoticonToImage($text);
		if ($tosql) $text = addslashes($text);
		return $text;
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

if (!function_exists("orderArray")) {
function orderArray() { 
  $n_parametros = func_num_args(); // Obenemos el número de parámetros 
  if ($n_parametros<3 || $n_parametros%2!=1) { // Si tenemos el número de parametro mal... 
    return false; 
  } else { // Hasta aquí todo correcto...veamos si los parámetros tienen lo que debe ser... 
    $arg_list = func_get_args(); 
 
    if (!(is_array($arg_list[0]) && is_array(current($arg_list[0])))) { 
      return false; // Si el primero no es un array...MALO! 
    } 
    for ($i = 1; $i<$n_parametros; $i++) { // Miramos que el resto de parámetros tb estén bien... 
      if ($i%2!=0) {// Parámetro impar...tiene que ser un campo del array... 
        if (!array_key_exists($arg_list[$i], current($arg_list[0]))) { 
          return false; 
        } 
      } else { // Par, no falla...si no es SORT_ASC o SORT_DESC...a la calle! 
        if ($arg_list[$i]!=SORT_ASC && $arg_list[$i]!=SORT_DESC) { 
          return false; 
        } 
      } 
    } 
    $array_salida = $arg_list[0]; 
 
    // Una vez los parámetros se que están bien, procederé a ordenar... 
    $a_evaluar = "foreach (\$array_salida as \$fila){\n"; 
    for ($i=1; $i<$n_parametros; $i+=2) { // Ahora por cada columna... 
      $a_evaluar .= "  \$campo{$i}[] = \$fila['$arg_list[$i]'];\n"; 
    } 
    $a_evaluar .= "}\n"; 
    $a_evaluar .= "array_multisort(\n"; 
    for ($i=1; $i<$n_parametros; $i+=2) { // Ahora por cada elemento... 
      $a_evaluar .= "  \$campo{$i}, SORT_REGULAR, \$arg_list[".($i+1)."],\n"; 
    } 
    $a_evaluar .= "  \$array_salida);"; 
    // La verdad es que es más complicado de lo que creía en principio... :) 
 
    eval($a_evaluar); 
    return $array_salida; 
  } 
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
function CalculaEdad( $fecha ) {
    list($Y,$m,$d) = explode("-",$fecha);
    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
}
function getProfilePhotoByMemberId($uid) {
	$user_row = fetchUser($uid);
	if ($user_row["prf_img"] != 0) {
		$res = mysql_query("SELECT * FROM photos WHERE id=".$user_row["prf_img"]) or die(mysql_error());   
		if (!mysql_num_rows($res)) $image="resources/images/default.png";
		else { 
			$row = mysql_fetch_array($res);
			$image="tmp/small/".$row["image_url"]; 
		}
	} else $image="resources/images/default.png";
	return $image;
}
function getProfilePhotoByPhotoId($pid) {
	if ($pid) {
		$res = mysql_query("SELECT * FROM photos WHERE id=".$pid) or die(mysql_error());   
		if (!mysql_num_rows($res)) $image="resources/images/default.png";
		else { 
			$row = mysql_fetch_array($res);
			$image="tmp/small/".$row["image_url"]; 
		}
	} else $image="resources/images/default.png";
	return $image;
}
function getPagePhotoByPhotoId($pid) {
	if ($pid) {
		$res = mysql_query("SELECT * FROM photos WHERE id=".$pid) or die(mysql_error());   
		if (!mysql_num_rows($res)) $image="resources/images/page_def.png";
		else { 
			$row = mysql_fetch_array($res);
			$image="tmp/small/".$row["image_url"]; 
		}
	} else $image="resources/images/page_def.png";
	return $image;
}

?>