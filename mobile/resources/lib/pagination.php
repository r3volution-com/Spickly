<?php
class Pagination {
	private $num_total_registros;
	private $total_paginas;
	private $item_per_page;
	private $pagina_act;
	private $query; 
	private $typevar; 
	
	function __construct($query) {
		$res = mysql_query($query) or die(mysql_error()); 
		$this->num_total_registros = mysql_num_rows($res); 
		$this->query = $query;
		$this->pagina_act = 1;
		$this->item_per_page = 20;
		$this->typevar = "pg";
	}
	public function pgSelectItemperPage($ipp) {
		if ($ipp) $this->item_per_page = $ipp; 
	}
	public function pgSelectActualPage($ap) {
		if ($ap) $this->pagina_act = $ap; 
	}
	public function pgSelectType($tp) {
		if ($tp) $this->typevar = $tp; 
	}
	public function pgGetMoreInfo() {
		return array("num_pages"=> $this->total_paginas, "num_items"=>$this->num_total_registros, "item_per_page"=>$this->item_per_page, "act_page" =>$this->pagina_act);
	}

	public function pgDoPagination() {
		/*if (!isset($pagina_act)) { 
			$inicio = 0; 
			$pagina_act=1; 
		} else $inicio = ($pagina_act - 1) * $item_per_page; */
		
		//miro a ver el n??o total de campos que hay en la tabla con esa b??eda 
		$item_actual = ($this->pagina_act-1)*$this->item_per_page;
		
		//calculo el total de p?nas 
		$this->total_paginas = ceil($this->num_total_registros / $this->item_per_page); 
		
		$query = $this->query." LIMIT ".$item_actual.", ".$this->item_per_page;
		$res = mysql_query($query) or die(mysql_error()); 
		
		return $res;
	}

	public function pgShowAjaxPagination ($extra = "") {
		$s_paginas = "";
		if ($this->total_paginas > 1){ 
			if ($this->pagina_act > 1) {
				$previus_page = $this->pagina_act-1;
				$s_paginas .= " <a href='javascript:pagination(\"".$this->typevar."\", 1 $extra);'><span class='boton'>&lt;&lt;</span></a> ";
				$s_paginas .= " <a href='javascript:pagination(\"".$this->typevar."\", $previus_page $extra);'><span class='boton'>&lt;</span></a> ";
			}
			if ($this->pagina_act != $this->total_paginas) {
				$next_page = $this->pagina_act+1;
				$s_paginas .= " <a href='javascript:pagination(\"".$this->typevar."\", $next_page $extra);'><span class='boton'>&gt;</span></a> ";
				$s_paginas .= " <a href='javascript:pagination(\"".$this->typevar."\", $this->total_paginas $extra);'><span class='boton'>&gt;&gt;</span></a> ";
			}
		}
		return $s_paginas;
	}
	public function pgShowPagination () {
		// La idea es pasar tambi?en los enlaces las variables hayan llegado por url.
		$enlace = $_SERVER['PHP_SELF'];
		//Si no se defini???ariables propagar, se propagar?odo el $_GET menos la variable pg (por compatibilidad con versiones anteriores)
		if (isset($_GET[$this->typevar])) unset($_GET[$this->typevar]); // Eliminamos esa variable del $_GET
		$propagar = array_keys($_GET);
		// Este foreach est?omado de la Clase Paginado de webstudio
		$query_string = "?";
		foreach($propagar as $var){
			if(isset($GLOBALS[$var])) $query_string.= $var."=".$GLOBALS[$var]."&"; // Si la variable es global al script
			elseif(isset($_REQUEST[$var])) $query_string.= $var."=".$_REQUEST[$var]."&"; // Si no es global (o register globals est?n OFF)
		}
		// A??mos el query string a la url.
		$enlace .= $query_string;
		$s_paginas = "";
		//muestro los distintos ?ices de las p?nas, si es que hay varias p?nas 
		if ($this->total_paginas > 1){ 
			if ($this->pagina_act > 1) {
				$previus_page = $this->pagina_act-1;
				$s_paginas .= " <a href='".$enlace.$this->typevar."=1'>Primera</a> ";
				$s_paginas .= " <a href='".$enlace.$this->typevar."=".$previus_page."'>Anterior</a> ";
			}
			for ($i=1;$i<=$this->total_paginas;$i++){ 
				//si muestro el ?ice de la p?na actual, no coloco enlace 
				if ($pagina == $i) $s_paginas .= $this->pagina_act." ";
				//si el ?ice no corresponde con la p?na mostrada actualmente, coloco el enlace para ir a esa p?na 
				else $s_paginas .= "<a href='".$enlace.$this->typevar."=".$i."'>".$i."</a> ";
			}
			if ($this->pagina_act != $this->total_paginas) {
				$next_page = $this->pagina_act+1;
				$s_paginas .= " <a href='".$enlace.$this->typevar."=".$next_page."'>Siguiente</a> ";
				$s_paginas .= " <a href='".$enlace.$this->typevar."=".$this->total_paginas."'>Ultima</a> ";
			}
		}
		return $s_paginas;
	}
}
?>