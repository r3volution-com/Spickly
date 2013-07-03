<?php if (!isset($opt)) die("<script>location.href='in.php?p=profile';</script>"); ?>
<script>
	function changecap(cap) {
		document.getElementsByTagName('capt1').style.display='none';
		document.getElementByTagName('capt2').style.display='none';
		document.getElementByTagName('capt3').style.display='none';
		document.getElementByTagName('capt4').style.display='none';
		document.getElementByTagName(cap).style.display='block';
	}
</script>
<ul id="nav" value="nav">
	<li><img src="resources/images/logo_navp.png"></li>
	<li id="lipartners"><a onclick="changecap('capt1');">Inicio</a></li>
	<li id="lipartners"><a onclick="changecap('capt2');">Información</a></li>
	<li id="lipartners"><a onclick="changecap('capt3');">Que hacemos</a></li>
	<li id="lipartners"><a onclick="changecap('capt4');">Contacta con nosotros</a></li>
	<li></li>
	<li></li>
	<li id="lipartners"><a href="http://www.spickly.es">Volver a Spickly.es</a></li>
</ul>
<div id="page">
<div id="container" class="cap1" name="capt1" align="center">
<h1 align="center">Bienvenido al sistema de Partners</h1>
<hr>
<h1 align="center">Hola y bienvenido al sistema de Partners de Spickly</h1>
Una gran forma de publicitarse y hacer que crezca tu negocio de una manera facil,sencilla y productiva.
<h1 align="center">¿A que esperas para publicitarte? Consulta los menus de arriba.
</div>

<div id="container" class="cap2" name="capt2" align="center">
<h1 align="center">Informacion sobre los Partners</h1>
<hr>
<h1 align="center">Hola y bienvenido al sistema de Partners de Spickly</h1>
Una gran forma de publicitarse y hacer que crezca tu negocio de una manera facil,sencilla y productiva.
<h1 align="center">¿A que esperas para publicitarte? Consulta los menus de arriba.
</div>

<div id="container" class="cap3" name="capt3" align="center">
<h1 align="center">Que hacemos</h1>
<hr>
<h1 align="center">Hola y bienvenido al sistema de Partners de Spickly</h1>
Una gran forma de publicitarse y hacer que crezca tu negocio de una manera facil,sencilla y productiva.
<h1 align="center">¿A que esperas para publicitarte? Consulta los menus de arriba.
</div>

<div id="container" class="cap4" name="capt4" align="center">
<h1 align="center">Contacta con nosotros</h1>
<hr>
<h1 align="center">Hola y bienvenido al sistema de Partners de Spickly</h1>
Una gran forma de publicitarse y hacer que crezca tu negocio de una manera facil,sencilla y productiva.
<h1 align="center">¿A que esperas para publicitarte? Consulta los menus de arriba.
</div>
</div>		