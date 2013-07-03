/*Variables Internas*/
var dropbox;
var uploadlist;
var dguri = "upload.php";
var dgtype = "file";
var dgmaxsize = 20;
var dgprogress;
var dgzone;
var dgisuploading = 0;
var dgredirect;
/*Funciones internas*/
function dragenter(e) {
	e.stopPropagation();
	e.preventDefault();
}
function dragover(e) {
	e.stopPropagation();
	e.preventDefault();
} 
function drop(e) {
	e.stopPropagation();
	e.preventDefault();
	var dt = e.dataTransfer;
	dgfiles = dt.files;
	dgHandle(dgfiles);
	document.getElementById(dgzone).style.backgroundColor="green";
}
function isDefined( variable) { return (typeof(window[variable]) != "undefined");}
/*Funciones externas*/
function dgCreateZone(zone) {
	dgzone = zone;
	dropbox = document.getElementById(zone);
	dropbox.addEventListener("dragenter", dragenter, false);
	dropbox.addEventListener("dragover", dragover, false);
	dropbox.addEventListener("drop", drop, false);
}
function dgCreateOutputList(retlist) {
	uploadlist = retlist;
}
function dgSelectType(type) {
	dgtype = type;
}
function dgSelectMaxSize(size) {
	dgmaxsize = size;
}
function dgRedirectUploads(link) {
	dgredirect = link;
}
function dgSelectUri(link) {
	dguri = link;
}
function dgHandle(files) {
	if (!dgisuploading) {
	var preview = document.getElementById(uploadlist);
	var formdata = new FormData(); 
	for (var i = 0; i <files.length; i++) {
		var file = files[i];
		if (file.size < dgmaxsize*1024*1024){
			var li = document.createElement("li");
			if (dgtype=="image"){
				var imageType = /image.*/;
				if (!file.type.match(imageType)) {
					alert("El archivo "+file.name+" que ha introducido no es una imagen valida");
					continue;
				}
			}else{
				var name = document.createElement("span");
			}
			if (dgtype=="image"){
				if (dgredirect) {
					var link = document.createElement("a");
					link.href=dgredirect;
					link.id="img_"+i;
				}
				var img = document.createElement("img");
				img.classList.add("obj");
				img.file = file;
				img.width = 50;
				img.height = 50;
				img.title = file.name;
				if (dgredirect){
					link.appendChild(img);
					li.appendChild(link);
				} else li.appendChild(img);
			} else {
				name.innerHTML=file.name;
				li.appendChild(name);
			}
			preview.appendChild(li);
			var reader = new FileReader();
				if (dgtype=="image") reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
				reader.readAsDataURL(file);
				formdata.append('user_file[]', file);
		} else alert("El archivo "+file.name+" que ha introducido supera el tamaño maximo permitido (5MB)");
	}
	dgprogress = document.querySelector('.percent');
	dgprogress.style.width = '0%';
	dgprogress.textContent = '0%';
	var xhr = new XMLHttpRequest();
	xhr.open("POST", dguri, true);
	xhr.addEventListener('progress', function(e) {
		document.getElementById('progress_bar').style.display = "block";
		document.getElementById('progress_bar').className = 'loading';
		var done = e.position || e.loaded, total = e.totalSize || e.total;
		var percentLoaded = Math.round((done / total) * 100);
		// Increase the progress bar length.
		if (percentLoaded < 100) {
			dgprogress.style.width = percentLoaded + '%';
			dgprogress.textContent = percentLoaded + '%';
		}
	}, false);
	if ( xhr.upload ) {
		xhr.upload.onprogress = function(e) {
			dgisuploading = 1;
			var done = e.position || e.loaded, total = e.totalSize || e.total;
			var percentLoaded = Math.round((done / total) * 100);
			// Increase the progress bar length.
			if (percentLoaded < 100) {
				dgprogress.style.width = percentLoaded + '%';
				dgprogress.textContent = percentLoaded + '%';
			}
		};
	}
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && xhr.status == 200) {
			// Handle response.
			dgprogress.style.width = '100%'; 
			dgprogress.textContent = '100%'; 
			setTimeout("document.getElementById('progress_bar').className='';", 2000);
			document.getElementById('filelist').style.display = "block";
			obj = JSON.parse(xhr.responseText);
			for (var i = 0; i < obj.length; i++) {
				if (obj[i].status.toLowerCase().indexOf('ok')) alert(obj[i].status);
				else {
					document.getElementById("img_"+obj[i].id).href += obj[i].pid;
					dgisuploading = 0;
				}
			}
		}
	};
	// Initiate a multipart/form-data upload
	xhr.send(formdata);
	} else alert("Ya estas subiendo contenido");
}
