<?php 
	if (!isset($opt)) header("location: in.php");
	if ((isset($_GET["id"]) && $_GET["id"]) ) {
	$res = mysql_query("SELECT * FROM photos WHERE id=".mysql_real_escape_string($_GET["id"])) or die(mysql_error()); 
	$row = mysql_fetch_array($res);
	if ($row["member_id_send"]) $u_row = fetchUser($row["member_id_send"]);
	else die("<script>location.href='in.php';</script>");
	$nimg = mysql_query("SELECT * FROM photos WHERE member_id_send='".mysql_real_escape_string($u_row["member_id"])."' AND id > '".mysql_real_escape_string($_GET["id"])."' ORDER BY id LIMIT 1") or die(mysql_error());
	$nimgs = mysql_fetch_array($nimg);
	$bimg = mysql_query("SELECT * FROM photos WHERE member_id_send='".mysql_real_escape_string($u_row["member_id"])."' AND id < '".mysql_real_escape_string($_GET["id"])."' ORDER BY id DESC LIMIT 1") or die(mysql_error());
	$bimgs = mysql_fetch_array($bimg);

	$resjson = mysql_query("SELECT * FROM photos") or die(mysql_error()); 
	if (mysql_num_rows($resjson)) {
		$rowjson = mysql_fetch_array($resjson);
		$obj = json_encode($rowjson["image_url"]);
	}
	
	$resu = mysql_query("SELECT * FROM friends WHERE (member_id_send=".$row["member_id_send"]." AND member_id_receive=".$user_row["member_id"].") OR (member_id_send=".$user_row["member_id"]." AND member_id_receive=".$row["member_id_send"].") AND alive = 1");
	if ($row["member_id_send"] != $user_row["member_id"] && !mysql_num_rows($resu)) die ("<script>location.href='in.php'</script>");
	?>
	
			<div id="photo_container" >
					<div id="photo" <?php if($_GET["m"] == 3){ ?>onMouseOver="graph('next,back')" onMouseOut="graph('next,back')"<?php } ?>>
								<div id="photobg" align="center">
									<img data-image-id="<?php echo $row["id"]; ?>" class="photoTag" id="foto" <?php if($_GET["m"] == 2){ echo "style='height: 70%; width: 70%;'";}elseif($_GET["m"] == 3){echo "style='height: 100%; width: 100%;'"; } else{}?>  oncontextmenu="return false;" ondragstart="return false;" src="tmp/<?php echo $row["image_url"]; ?> "/>
								</div>
								
								<div id="controls">
									<div id="controlsnext">
										<?php if ($nimgs["id"] > 1) { ?> <span class="next" id="next" onclick="window.location.href='in.php?p=photo&id=<?php echo $nimgs["id"]; ?>&m=<?php echo $_GET["m"]; ?>'" >Siguiente</span><?php } ?>
									</div>
									
									<div id="controlsback">
										<?php if ($bimgs["id"] > 1) { ?> <span class="back" id="back" onclick="window.location.href='in.php?p=photo&id=<?php echo $bimgs["id"]; ?>&m=<?php echo $_GET["m"]; ?>'">Atras</span><?php } ?>
									</div>
								</div>
								
								<div id="down">
									<?php if ($_GET["m"] == 3) { ?> <span href="javascript:void(0);" id="myBtn" value="Fade Out" onclick="fade(this);" class="down" ></span> <?php } ?>
								</div>
					</div>
				</div>

			<div id="spick" style="display: <?php echo ($_GET["m"] == 3) ? "none" : "block"; ?>">

				<div id="comment_photo">
								<h1>Spickline</h1>
								<hr align="left" width="90%">
								<form>
									<textarea id="spicktext_<?php echo $_GET["id"]?>" name="space" rows="2" cols="60" maxlength="200" onkeyup="document.getElementById('cuenta_<?php echo $user_row['member_id']; ?>').value=200-this.value.length" ></textarea>
									<input type="button" class="cuenta" name="cuenta" size="3" id="cuenta_<?php echo $user_row['member_id']; ?>" value="200"  style="position: relative; bottom: 14px;" />
									<input type="button" class="boton" value="Enviar" onclick="if (document.getElementById('spicktext_<?php echo $_GET["id"]?>').value != '') comment(<?php echo $_GET["id"]?>, 0)" style="position: relative; bottom: 15px;"> 
								</form>	
							<div id="pgspickphoto">
								<?php
								$pag = new Pagination("SELECT * FROM comment_photos WHERE photos_id='".mysql_real_escape_string($_GET["id"])."' ORDER BY id DESC");
								$pag->pgSelectItemperPage(10);
								$pag->pgSelectType("spickphoto");
								$res = $pag->pgDoPagination();
								while ($line = mysql_fetch_assoc($res)) {
									$u_row = fetchUser($line['member_id_send']);
									echo '<div id="commentlist_'.$line["id"].'"> <a href="in.php?p=profile&id='.$u_row["member_id"].'">'.$u_row["firstname"]." ".$u_row["lastname"].'</a> <br/>'.$line['text'].'<br/><h6 style="margin:0px;padding:0px;">'.$line["date"].'</h6>';
									if ($line['member_id_send'] == $user_row["member_id"]) echo ' - <a href="javascript:void(0)" onclick="deleteCommentPhoto(\''.$line['id'].'\')">Eliminar</a><hr width="80%">';
									echo '</div>';
								}	
								echo $pag->pgShowAjaxPagination(", ".$_GET["id"]);			
								?>
							</div>
							<div id="new_comment_<?php echo $_GET["id"] ?>">
							
							</div>
							
							<div id="comment_container">
						
						</div>
				</div>
				<?php
					if($nimgs["id"]) $delredir = $nimgs["id"];
					if($bimgs["id"]) $delredir = $bimgs["id"];
				?>
				<div id="sidebar_photos">
					<div id="buttons_images">
						<a href="javascript:void(0);" class="nav" onclick="document.getElementById('foto').style.height='';document.getElementById('foto').style.width='';newlink(<?php echo $nimgs["id"]; ?>,<?php echo $bimgs["id"]; ?>,1);" ><img src="resources/images/img_little.png" title="Tamaño original"></a></img>
						<a href="javascript:void(0);" class="nav" onclick="document.getElementById('foto').style.height='70%';document.getElementById('foto').style.width='70%';newlink(<?php echo $nimgs["id"]; ?>,<?php echo $bimgs["id"]; ?>,2);" ><img src="resources/images/img_media.png" title="Tamaño medio"></a></img> 
						<a href="javascript:void(0);" class="nav" onclick="document.getElementById('foto').style.height='100%';document.getElementById('foto').style.width='100%';newlink(<?php echo $nimgs["id"]; ?>,<?php echo $bimgs["id"]; ?>,3);" ><img src="resources/images/img_full.png" title="Pantalla completa"></a></img> 
						<a value="Me gusta" style="position:relative;" onclick="like(<?php echo $user_row["member_id"];?>,<?php echo $row["id"];?>,1)"> <img src="resources/images/like.png" style="cursor:pointer;" title="Me gusta"> </a>
						<?php if ($u_row["member_id"] == $_SESSION['SESS_MEMBER_ID']){ ?><a onclick="deletePhoto(<?php echo $_GET["id"]; ?>, <?php echo $delredir; ?>);" href="javascript:void(0);"><img src="resources/images/borrar.png" title="Borrar imagen"></a><?php } ?>
					</div>
					<div id="info">
							<h1>Informacion <a href="javascript:void(0)" onclick="document.getElementById('etiquetas').innerHTML=document.getElementById('etiquetasedit').innerHTML;" ><img src="resources/images/lapiz.png" /></a></h1>
							<hr align="left" width="90%">
							<b>Nombre:</b> <?php echo $row["name"]; ?><br>
							<?php if ($row["desc"]) { ?>Descripcion: <?php echo $row["desc"]; ?><br><?php } ?>
							<b>Fecha:</b> <?php echo $row["date"]; ?><br>
							<b>Subida por:</b> <?php echo $u_row["firstname"]." ".$u_row["lastname"]; 
							?>
							<input type="button" id="button" class="boton" onclick="this.value=sendAjax('in_actions.php?a=pphoto&setimg=<?php echo $_GET["id"]; ?>')" value="Establecer foto de perfil" />
					</div>
					<div id="etiquetas_sidebar">
							<h1>¿Quién sale?</h1>
							<hr align="left" width="90%" id="setimg" value="setimg">
							<?php if ($row["member_id_send"] == $user_row["member_id"]) { ?><a id="externalLink" href="javascript:void(0);" class="addTag">Añadir etiquetas</a><?php } ?>
							<div id="tagList"></div>
					</div>	
					<div id="likes_photo">				
							<h1>¿A quien le gusta?</h1>
							<hr align="left" width="90%">
							<?php $reslike = mysql_query("SELECT * FROM like_photos WHERE photos_id = '".parseText($_GET["id"])."' ") or die(mysql_error());
							if ($count = mysql_num_rows($reslike)) {
								echo "Hay ".mysql_num_rows($reslike)." me gusta: "; 
								?>
								<div id="likecheck">
									<?php
									for ($i = 0; $row = mysql_fetch_array($reslike); $i++) {
										$user = fetchUser($row["member_id_send"]);
										$images = getProfilePhotoByPhotoId($user["prf_img"]);
										$nombres = $user["firstname"]." ".$user["lastname"];
										echo "<table id='tablaimg'><tr><td id='imagens'><a href='in.php?p=profile&id=".$user["member_id"]."'><img title=".$nombres." src=".$images." width='40px' height='40px'></a></td></tr></table>";
										
										if ($count > 1 && $i) ", ";
									}
									?>
								</div>
								<?php
								} else echo "A nadie le gusta esto aun!.";
							?>
					</div>
				</div>
			</div>						
		<script type="text/javascript">
			$(document).ready(function(){
				var options = {  
					requestTagsUrl: 'in_actions.php?a=tag&ac=get',  
					deleteTagsUrl: 'in_actions.php?a=tag&ac=del',  
					addTagUrl: 'in_actions.php?a=tag&ac=put',
					placeList: 'tagList',
					parametersForNewTag: {  
						name: {  
							parameterKey: 'name',  
							isAutocomplete: true,  
							autocompleteUrl: '/in_actions.php?a=searchfriend&canme=1',  
							label: 'Name'                     
						}  
					},
					externalAddTagLinks: {
						bind: true,
						selector: ".addTag"
					}
				};
				$('.photoTag').photoTag(options);  
			});  

		function fade(btnElement) {
            if (btnElement.value === "Fade Out") {
				document.getElementById('spick').style.display='none'; $(window).scrollTo( 0, 900, {queue:true} );
				document.getElementById('myBtn').className = "down";
				document.getElementById('myBtn').value = "Fade In";
		  }
            else {
				document.getElementById('spick').style.display='block'; $(window).scrollTo( 700, 900, {queue:true} );
				document.getElementById('myBtn').value = "Fade Out";
				document.getElementById('myBtn').className = "downround";
		   }
        }
		
		function graph(){
			if(document.getElementById('next').style.display == 'block' || document.getElementById('back').style.display == 'block'){
				document.getElementById('next').style.display = 'none';
				document.getElementById('back').style.display = 'none';
			}else{
				document.getElementById('next').style.display = 'block';
				document.getElementById('back').style.display = 'block';
			}
		}
		function newlink(nid, bid, mode){
			if(nid > 1){document.getElementById('controlsnext').innerHTML = '<span class="next" id="next" onclick="window.location.href=\'in.php?p=photo&id='+nid+'&m='+mode+'\'">Siguiente</span>';}		
			if(bid > 1){document.getElementById('controlsback').innerHTML = '<span class="back" id="back" onclick="window.location.href=\'in.php?p=photo&id='+bid+'&m='+mode+'\'">Anterior</span>';	}		

			

		}
		
		var photoarray = new Array(10) 
		photoarray[0] = "Hola" 
		photoarray[1] = "Adios" 
		photoarray[2] = 127 

		var cont = 0;
		function contador(){
			var contador = document.getElementById("foto");
			contador.src = photoarray[cont];
			cont++;
		}
		function repla(){
				document.getElementById("comment_photo").innerHTML=obj.rowjson[2].image_url;
		}
		</script>
	<?php

	
	}else die("<script>location.href='in.php';</script>"); ?>