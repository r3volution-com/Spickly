<div id="page">
<div id="drop_zone"><h1>Arrastra 1 o mas imagenes y sueltalas aqui</h1></div>
<form action="" method="post" enctype="multipart/form-data">
	<br>O si lo prefieres seleccionalas aqui: <input name="filesToUpload[]" id="filesToUpload" type="file" multiple accept="image/*" onchange="dgHandle(this.files)"/>
</form>
<span id="fuploaded"></span><div id="progress_bar"><div class="percent">0%</div></div>
<ul id="filelist" style="list-style: none;"></ul>
<script>
	dgCreateZone('drop_zone');
	dgSelectUri('in_actions.php?a=uphoto');
	dgCreateOutputList('filelist');
	dgSelectType('image');
	dgRedirectUploads("in.php?p=photo&id=");
	dgSelectMaxSize(5);
</script> 
</div>

