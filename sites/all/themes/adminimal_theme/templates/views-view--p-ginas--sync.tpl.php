<script type="text/javascript">
var j = jQuery;

function toDo(url){
	j("#alvo").html(" ");
	j("#alvo").html("Carregando...");
	j.get(url, function(data, status){
    	j("#alvo").html(data + "<br /> Status: " + status);
  	});
}

</script>
<ul>
	<li><a class="button bto" href="#" id="bto" onClick="toDo('/admin/convert')"> Converter DICOM ! </a> <br /><br /></li>
	<li><a class="button bto" href="/admin/sync?op=update" id="bto"> Importar imagens ! </a></li>
</ul>
<div id="alvo"></div>

<?php 
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);


//Onde as imagens estão
$filepath = drupal_realpath('sites/default/files/DICOM');
if(isset($_GET['op']) && $_GET['op'] == "update"){


$files = array();
if ($handle = opendir($filepath)) {

    while (false !== ($file = readdir($handle))) 
	{
        if ($file != "." && $file != ".." && is_file($filepath.$file)) 
		{
			$files[] = $file;
		}
	}
    closedir($handle);
}
$lista = scandir($filepath,1);
$list = array_pop($lista);
$list = array_pop($lista);


foreach($lista as $f){

	$nodeTitle = explode("_",$f);
		
	$sql = "SELECT nid FROM node WHERE title = '".$nodeTitle[0]."' "; 
	
	$result = db_query($sql);
	$nid = $result->fetchField();
	
	if($nid){

		$n = node_load($nid);

		if($n){
			$file = (object) array(

				'uid' => 1,

				'uri' => $filepath."/".$f,

				'filemime' => file_get_mimetype($filepath."/".$f),

				'status' => 1,

			);
			$file = file_copy($file, 'public://');
			
			if(isset($n->field_imagens['und'])){
				array_push($n->field_imagens['und'],(array)$file);
			} else {
				$n->field_imagens['und'][0] = (array)$file;
			}
			

			$n = node_submit($n);
			node_save($n);
			echo "OS ".$nodeTitle[0]." atualizada com sucesso <br />";
			unlink($filepath."/".$f);
		}
	}
	
	
}

} else {?>


<?php } ?>