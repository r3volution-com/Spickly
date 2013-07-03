<?php 
	session_start();
	include('config.php'); 
	include("../functions.php");
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	$db = mysql_select_db(DB_DATABASE);
	if (isset($_POST["topic"]) && ($_POST["topic"] && $_POST["text"] && $_POST["author"]) && ($_POST["pass"] == "")) {
		mysql_query ("INSERT INTO articles (title, text, author) VALUES ('".$_POST["topic"]."','".$_POST["text"]."','".$_POST["author"]."')");
		die();
	}
	?>
	<html> 
	<head>
		<title>Blog | Spickly</title>
		<link href="style.css" type="text/css" rel="stylesheet">
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
 			<img alt="Logo" height="42px" width="150px" src="../resources/images/logo_nav.png" /> 
				<a class="link" id="link1" href="../download/index.php">Ir a Descargas</a>
				<a class="link" id="link2" href="../index.php">Volver a Spickly</a>
 		</header>		
	<div id="container">	
		<div id="page">
			<section>
	<?php if (isset($_GET["create"])) {?>
		<form action="" method="POST" align="center">
			Topic:<br> <input type="text" name="topic"/><br>
			Autor:<br> <input type="text" name="author"/><br>
			Text:<br> <textarea name="text"></textarea><br>
			PassWord: <br><input type="password" name="pass"/><br>(La que os di por skype)<br> 
			<input type="submit"/>
		</form>
	<?php } else {
		$res = mysql_query("SELECT * FROM articles ORDER BY id DESC LIMIT 5");
		if (mysql_num_rows($res)) {
			while ($row = mysql_fetch_array($res)) {
				echo '<article>
					<div id="headart"><h2>'.$row["title"].'</h2></div><br>
					<p id="bodyart">'.$row["text"].'</p>
					<footer><h6 align="left">Escrito por '.$row["author"].' a '.$row["date"].'</h6></footer>
				</article><br>';
			}
		} else echo "No hay articulos"; 
	} ?>
			</section>
			<br>
			<footer>
				&copy; Spickly.es - 2013  
			</footer>
		</div>
	</div>
	</body>
</html>