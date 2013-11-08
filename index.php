<?php

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

if(isset($_REQUEST['delete'])){
	$file = $_REQUEST['delete'];
	$delete = TRUE;
	if($file == 'index.html' || $file == 'index.php' || $file == '..' || $file == '.'){
		$deletefail = TRUE;
	}else if(file_exists('uploads/'.$file)){
		unlink('uploads/'.$file);
		$deletefail = FALSE;
	}else{
		$deletefail = TRUE;
	}
}

if(!empty($_FILES)){
	$upload = TRUE;
	if(!isset($_FILES['files']['error'])){
		$error = TRUE;
		$msgs[] = $_FILES['files']['error'];
	}else{
		$names = $_FILES['files']['name'];
		$tmp_names = $_FILES['files']['tmp_name'];
		foreach($names as $key => $name){
			$name = str_replace(' ','',$name);
			if(file_exists('uploads/'.$name)){
				$error = TRUE;
				$msgs[] = $name . ' already exists.';
			}else{
				move_uploaded_file($tmp_names[$key], 'uploads/'.$name);
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>File Center</title>
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<style>
body{
	background-color: #448;
}
div#container{
	background-color: white;
	width: 400px;
	border: solid #666 1px;
	border-radius: 10px;
	margin: auto;
	padding: 10px;
}
span#heading{
	font-size: 20px;
}
input,label{
	cursor: pointer;
}
.msg{
	border: solid 1px;
	padding: 4px;
	width: 300px;
	margin: auto;
	text-align: center;
}
.success{
	background-color: #AFA;
}
.error{
	background-color: #FAA;
}
a.filelink{
	color: #555;
}
a.filelink:hover{
	color: #77A;
}
.files{
	padding: 3px 0px 3px 2px;
}
.remove{
	font-size: 12px;
	color: #999;
}
.remove:hover{
	color: red;
	cursor: pointer;
}
.prompt{
	display: none;
	cursor: pointer;
}
.prompt a{
	color: inherit;
	text-decoration: none;
}
.prompt a:hover{
	color: red;
}
.prompt .no:hover{
	color: green;
}
</style>
</head>
<body>
<div id="container">
<?php

if($upload && $error){
	echo '<div class="msg error">';
	foreach($msgs as $msg){
		echo '<div>'.$msg.'</div>';
	}
	echo '</div>';
}else if($upload && !$error){
	echo '<div class="msg success">File(s) uploaded.</div>';
}else if($delete && $deletefail){
	echo '<div class="msg error">Delete failed.</div>';
}else if($delete && !$deletefail){
	echo '<div class="msg success">Delete successful.</div>';
}

?>
<span id="heading">Apartment Files</span>
<div>Upload files:</div>
<form action="index.php" method="post" enctype="multipart/form-data">
<input type="file" name="files[]" id="files" multiple />
<br />
<input type="submit" name="submit" value="Upload File(s)" />
</form>
<div style="margin-top: 10px">Current files:</div>
<?php

$current_files = scandir('uploads/');
foreach($current_files as $file){
	$id = preg_replace('/[^A-z]/','',$file);
	if($file != '.' && $file != '..' && $file != 'index.html'){
		echo '<div class="files"><a class="filelink" href="uploads/' . $file . '">' . 
$file . 
'</a> (' . formatBytes(filesize('uploads/'.$file)) . ') <span id="'.$id.'" 
class="remove">[remove]</span><span 
id="'.$id.'" 
class="prompt" 
id="'.$file.'"><a 
href="index.php?delete='.$file.'">[yes]</a> <span id="'.$id.'" 
class="no">[no]</span></span></div>';
	}
}

?>
</div>
</body>
<script>
$('.remove').click(function(){
	var current_id = $(this).attr('id');
	console.log(current_id);
	$(this).hide();
	$('.prompt#' + current_id).show();
});
$('.no').click(function(){
	var current_id = $(this).attr('id');
	$('.prompt#'+current_id).hide();
	$('.remove#'+current_id).show();
});
</script>
</html>
