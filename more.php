<?php
	if (isset($_GET["m"]) && $_GET["m"]) {
		switch ($_GET["m"]) {
			case "abuso":
				$opt = 1;
				break;
			case "contacta":
				$opt = 2;
				break;
			case "legal":
				$opt = 3;
				break;
			default: 
				die();
				break;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
<head>
<title>Spickly</title>
<link href="./resources/css/team_style.css" rel="stylesheet" type="text/css">
</head>
<body>
<!-- Start Alexa Certify Javascript -->
<script type="text/javascript">
_atrk_opts = { atrk_acct:"uz4jh1aMQV002R", domain:"spickly.es",dynamic: true};
(function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
</script>
<noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=uz4jh1aMQV002R" style="display:none" height="1" width="1" alt="" /></noscript>
<!-- End Alexa Certify Javascript -->
	<header>
 		<img alt="Logo" height="42px" width="150px" src="./resources/images/logo_nav.png" />
			<a class="link"  href="../download/index.php">Ir a Descargas</a>
			<a class="link"  href="../index.php">Volver a Spickly</a>
 	</header>
 	<div id="container">
<?php 
	switch($opt) {
		case 1:
			?>
		<div class="top">
 			<h1>La Seguridad En Spickly</h1>
 			<h2>Para Su Seguridad Y La De Todos, Lea Atentamente</h2>
 		</div>
 		<div class="condiciones">
 			<h1>Seguridad</h1>
			<p>La seguridad es algo muy importante en cualquier pagina web en la que expongas tu informaci&oacute;n personal y fotos.</p>
			<p>Por eso en Spickly no exponemos tu perfil ni tus fotos a gente que no sea tu amig@ en Spickly.</p>
			<hr>
			<br>
			<h1>Abuso</h1>
			<p>Hay a veces que ocurren accidentes en la red social,asi como insultos,amenazas y demas cosas Ofensivas</p>
			<p>Por eso en Spickly ofrecemos un correo por si eso pasa en el cual podras mencionar el nombre de la persona</p>    <p> Asi como las amenazas que hace y nosotros nos ocuparemos de que eso no suceda mas</p>
			<p>El correo es Support@Spickly.es podras ponerte en Contacto con nosotros las 24 horas del dia y nuestra respuesta se te mandara a tu E-mail en un periodo de entre 1-3 dias</p>
			<p>No dejes que te amarguen la estancia en Spickly, con nosotros no sucedera
			<br>
			<p>Support@Spickly.es</p>	
			<br>
			<br>
			<p>Pulsa en El enlace que mas te convenga</p>
			<a href="http://www.spickly.es">Volver A Spickly</A>
			<br>
			<a href="./legal.php">Condiciones de Uso De Spickly</a>
		</div>	
			<?php
			break;
		case 2:
			?>
		<div class="top">
 			<h1>Contacta con nosotros</h1>
 			<h2>&iquest;Tienes alguna duda? &iquest;Quieres recomendar algo?</h2>
 		</div>
 		<div class="contact">
			<p>Si tienes alguna duda,te falta algo en Spickly,o quieres hablar de algo.</p>
			<p>Ponemos a disposicion de los usuarios un correo electronico para que formuleis lo que necesiteis.</p>
			<hr>
 	        <p>Contacta@spickly.es</p>
			<p>No dudeis en formular vuestras preguntas o incomodidades</p>
			<p>Pulsa en El enlace que mas te convenga</p>
			<a href="http://www.spickly.es">Volver A Spickly</A>
			<br>
		</div>	
			<?php
			break;
		case 3:
			?>
			<div class="top">
 			<h1>Condiciones de uso de Spickly</h1>
 			<h2>Por favor leelas atentamente ya que contiene informaci&oacute;n sobre tus derechos y obligaciones como usuario en la plataforma Spickly.</h2>
 		</div>
 		<div class="condiciones">
 			<h1>Condiciones de uso de Spickly</h1>
 			<p>Spickly es una plataforma social privada ( denominado a partir de ahora Sitio web) que facilita al usuario un espacio personal (denominado a partir de ahora Perfil) a trav&eacute;s del cual puede intercambiar informaci&oacute;n y comunicarse con sus contactos. Spickly permite al usuario obtener informaci&oacute;n de lo que ocurre a su alrededor. Tambi&eacute;n permite estar comunicado con sus contactos y/o hacer nuevos.</p>				<p>Estas Condiciones de usos regulan la utilizaci&oacute;n y el acceso a Spickly bajo los nombres de dominios www.spickly.es y www.spickly.com . Una vez entrado en el Sitio web o cualquier producto derivado de la plataforma social privada Spickly, manifiestas que has le&iacute;do y aceptas las condiciones de este sitio web.</p>			<p>Spickly se reserva el derecho de revisar las presentes Condiciones de uso en cualquier momento por cual tipo de raz&oacute;n legal, debidos a motivos estrat&eacute;gicos y cambios en la prestaci&oacute;n del sitio web. Si esto ocurriese el cambio de las condiciones lo publicar&iacute;amos en el Sitio web, por el cual si sigues usando el Sitio web, daremos a entender que aceptas las nuevas modificaciones. Si no estuvieras de acuerdo con las modificaciones, podr&aacute;s darte de baja del Sitio web a trav&eacute;s del procedimiento hubicado en Ajustes.</p>			<p>La recogida y tratamiento de tus datos personales as&iacute; como tu derecho sobre ellos, se regir&aacute;n por estas Condiciones de usos, la Pol&iacute;tica de privacidad y la Protecci&oacute;n de datos personales.</p> 		</div>
 		<hr>
 		<br>
 		<div class="condiciones">
 			<h1>Acceso al servicio web</h1>
 			<p>El acceso al Sitio web queda PROHIBIDO a los menores de 14 a&ntilde;os, por el cu&aacute;l, al aceptar estas Condiciones de uso, nos garantizas que eres mayor de 14 a&ntilde;os.</p>			<p>El equipo de Spickly podr&aacute; ponerse en contacto contigo para demostrar tu edad real aport&aacute;ndonos una copia de tu Documento Nacional de Identidad o alg&uacute;n documento semejante.</p>			<p>Los datos del documento aportado al equipo de Spickly ser&aacute;n utilizados exclusivamente para fines de identificaci&oacute;n, en ning&uacute;n caso, para otro fin.</p>			<p>Al ser menor de edad le aconsejamos que consulte con tus padres o tutores legales a la hora de transmitir informaci&oacute;n con otros miembros en el sitio web.</p>			<p>Spickly podr&aacute; permitir el registro de miembros menores de 14 a&ntilde;os a la plataforma social privada. Para ello se deber&aacute; aportar la confirmaci&oacute;n de la autorizaci&oacute;n parentar firmada por madre, padre o tutor legal.</p>				<p>Adem&aacute;s, para poder ser usuario del Sitio web, es necesario que previamente hallas recibido una invitaci&oacute;n de un contacto que ya sea usuario de Spickly o a trav&eacute;s de los servicios que ponemos a disposici&oacute;n.</p>
 		</div>
 		<hr>
 		<br>
 		<div class="condiciones">
 			<h1>Responsabilidades</h1>
 			<p>Est&aacute;s obligado a hacer un uso razonable del Sitio web y de sus contenidos.</p>			<p>En relaci&oacute;n al Sitio web, nosotros solo actuamos como intermediario poniendo a disposici&oacute;n la plataforma social, asumiendo &uacute;nicamente y exclusivamente las responsabilidades derivadas de la diligencia que pudiera ser exigible por la ley. No asumiremos ninguna responsabilidad, directa o indirecta debido al mal uso del Sitio web y de los contenidos alojados en este.</p>				<p>Haremos todo lo posible para vigilar la legalidad de los contenidos publicados en el Sitio web, im&aacute;genes, informaci&oacute;n, comentarios. Sin embargo no  es posible el control absoluto de estos, por lo que t&uacute; ser&aacute;s el &uacute;nico responsable del contenido que alojes, transmitas y exhibas a trav&eacute;s del la plataforma.	Adem&aacute;s, ser&aacute;s el &uacute;nico responsable del mantenimiento de tu Perfil y de los contenidos de cualquier tipo de este.</p>			<p>No nos identificamos con las opiniones que los usuarios puedan hacer en el Sitio web, de cuyas consecuencias se hace responsable el emisor de las mismas.</p>				<p>Podemos limitar la publicaci&oacute;n de todo tipo de contenidos en el Sitio web, instalando filtros en el mismo. Esto no significa que controlemos los contenidos si no evitar situaciones de comentarios y contenidos racistas, sexistas, xen&oacute;fobos, pornogr&aacute;ficos, discriminatorios, violentos o que de alguna forma contrar&iacute;en la moral, el orden p&uacute;blico, o resulten il&iacute;citos o ilegales.</p>			<p>No nos responsabilizaremos en ning&uacute;n caso de los problemas al acceder al Sitio web ajenos a la plataforma, los da&ntilde;os o perjuicios que puedan causar la interrupci&oacute;n del servicio, la existencia de malware u otros elemntos que puedan producir un funcionamiento err&oacute;neo en tu sistema inform&aacute;tico y/o en tu terminal movil, las p&eacute;rdidas y/o da&ntilde;os que puedan causer terceros por el arcceso a tu cuenta sin autorizaci&oacute;n. Por lo que recordamos que no publiques tu contrase&ntilde;a del Sitio web y por el cual eres responsables de los da&ntilde;os y/o perjuicios ocasionados por terceros por un uso no autorizado.</p>
 		</div>
		<hr>
		<br>
		<div class="condiciones">
		<h1>Seguridad</h1>
		<p>Los servicios ofrecidos en la plataforma social privada Spickly son exclusivamente para uso personal, quedando prohibido utilizarlo para una finalidad econ&oacute;mica o comercial.</p>		<p>Exceptos las herramientas publicitarias, de comunicaci&oacute;n y de patrocinio que Spickly pone a su disposici&oacute;n, las personas jur&iacute;dicas tienen prohibido poseer un Perfil en el Sitio web.</p>		<p>Para acceder al Sitio web, es debidamente necesario que nos aportes una serie de datos personales y, por el cual, aceptar nuestra Pol&iacute;tica de privacidad y Protecci&oacute;n de datos personales. Por lo cual, queda prohibido el uso de datos falsos, identific&aacute;ndote siempre con tu nombre y datos reales. Si detect&aacute;ramos datos falsos, podr&iacute;amos cancerlar los perfiles, de acuerdo con lo dicho en estas Condiciones de uso.</p>		<p>Adem&aacute;s, el acceso al Sitio web implica tu compromiso y obligaci&oacute;n de hacer un uso correcto de los mismos. Tu eres el responsable del uso del Perfil  que este Sitio web pone a tu disposici&oacute;n.</p>
		
		</div>
		<hr>
		<br>	
 		<div class="condiciones">
 		<h1>Usos no permitidos</h1>
 		<p>Queda prohibido el acceso a la plataforma con fines ilegales o no autorizados tales como: vulneraci&oacute;n del honor, imagen e intimidad, injurias, virus, phishing o spam, fines publicitatrios, racimso, pornograf&iacute;a, etc.</p>			<p>Spickly podr&iacute;a suspender o cancelar tu Perfil a sin previo aviso. Spickly podr&aacute; poner en conocimiento y colaborar con las autoridades policiales y judiciales si detectase cualquier infracci&oacute;n de la legislaci&oacute;n vigente o si tuviera sospecha de delito o falta penal.</p>
 		</div>
 		<hr>
 		<br>
 		<div class="condiciones">
 		<h1>Suplantaci&oacute;n de la identidad</h1>
 		<p>En el momento en el que halla indicios de que hayas suplantado la identidad de un tercero , procederemos a comprobar tu identidad, y si comprobamos que los indicios son reales, borraremos tu Perfil. Adem&aacute;s si en alg&uacute;n momento no podemos comprobar tu identidad, procederemos a borrar tu Perfil tambi&eacute;n.</p>		
		<p>Si sabes de alg&uacute;n Perfil que es falso o tienes indicios de que puede serlo, por favor, no dudes en ponerte en contacto con nosotros a trav&eacute;s de los correos de contactos o denunciar el mismo a trav&eacute;s del bot&oacute;n Denunciar usuario que ponemos a su disposici&oacute;n.</p>		<p>Podremos poner en conocimiento y colaborar oportunamente con las autoridades policiales y judiciales si se detectase una suplantaci&oacute;n de identidad que pudiera implicar un delito, en particular, del tipificado en el art&iacute;culo 401 del C&oacute;digo Penal vigente en Espa&ntilde;a.</p>
 		</div>
 		<hr>
 		<br>
 		<div class="condiciones">
 		<h1>Contenidos de los perfiles</h1>
 		<p>Al subir contenido a tu Perfil nos garantizas que eres el propietario del contenido o que has obtenido consentimiento de terceros para su publicaci&oacute;n, no vulnera las leyes aplicables.</p>		<p>El usuario se compromete a no incluir informaci&oacute;n o contenido con finalidad comercial ni aquellos que pudiesen ser contradictorios a los derechos del Sitio web y/o de terceros a la ley presente en estas Condiciones de uso.</p>
 		</div>
 		<hr>
 		<br>
 		<div class="condiciones">
 		<h1>Uso del chat</h1>
 		<p>El chat es una funcionalidad de mensajer&iacute;a instant&aacute;nea que permite al usuario comunicarse con sus contactos.</p>		<p>El usuario se compromete a no incluir en los mensajes que env&iacute;e a trav&eacute;s de las de la plataforma social privada Spickly informaci&oacute;n o contenidos con finalidad comercial ni aquellos que pudiesen ser de alguna manera contrarios a los derechos de esta plataforma y/o de terceros, a la ley, y/o a las Condiciones de uso.</p>		<p>Nosotros solos somos un intermediario, teniendo en cuenta que no podemos revisar ni controlar los contenidos de los mensajes instant&aacute;neos. Por lo cual, el usuario ser&aacute; el responsable de estos.</p>		<p>El usuario se compromete a no incluir en los mensajes que env&iacute;e a trav&eacute;s de los servicios de mensajer&iacute;a que ponemos a su disposici&oacute;n informaci&oacute;n o contenidos con una finalidad comercial ni aquellos que sean contrarios a la ley, a los derechos de este Sitio web ni a las Condiciones de uso.</p>
 		</div>
 		<hr>
 		<br>
 		<div class="condiciones">
 		<h1>Notificaci&oacute;n de infracciones</h1>
 		<p>Si detectas que alguien esta haciendo un uso inadecuado de la plataforma, comun&iacute;quenoslo a trav&eacute;s de los correo de contacto que ponemos a tu disposici&oacute;n.</p>
 		</div>
 		<hr>
 		<br>
 		<div class="condiciones">
 		<h1>Modificaciones de las condiciones de uso presentes</h1>
 		<p>Podemos sustituir por motivos t&eacute;cnicos o por cambios en la prestaci&oacute;n del Sitio web o en las normativas las Condiciones de uso, Pol&iacute;tica de privacidad y Protecci&oacute;n de datos personales debido a decisiones estrat&eacute;gicas o a c&oacute;digos tipo aplicables.</p>		<p>Cuando modifiquemos las Condiciones de uso, Pol&iacute;tica de privacidad y Protecci&oacute;n de datos personales se les notificar&aacute; a trav&eacute;s del Sitio web. Si se continua haciendo uso de la plataforma, daremos por hecho que aceptas las modificaciones introducidas. En cambio si no lo estas, podr&aacute;s darte de baja de la plataforma.</p>
 		</div>
 		<hr>
 		<br>
 		<div class="condiciones">
 		<h1>Informaci&oacute;n personal</h1>
 		<p>No nos hacemos responsables de la informaci&oacute;n que compartas con otros usuarios en la plataforma. No obstantes, contacta con nosotros en caso de encontrar contenido inapropiado o il&iacute;cito.</p>
 		</div>
 		<br>
		
			<?php
			break;
		default:
			break;
	}
?>
	</div>
</body>
</html>
