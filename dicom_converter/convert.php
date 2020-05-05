<?php
ini_set('max_execution_time', '6000');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require 'nanodicom.php';

// Diretório onde as imagens DICOM estarão
$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'DICOM'.DIRECTORY_SEPARATOR;

$files = array();
if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) 
	{
        if ($file != "." && $file != ".." && is_file($dir.$file)) 
		{
			$files[] = $file;
		}
	}
    closedir($handle);
}
$i=0;

foreach ($files as $file)
{
	$filename = $dir.$file;

	$t = explode(".dcm",$filename);
	if(count($t) > 1){
	try
	{
	
		$formatting = array(
			'dataset_begin'	=> '',
			'dataset_end'	=> "\n",
			'item_begin'	=> 'BEGIN',
			'item_end'		=> 'END',
			'spacer'		=> ' ',
			'text_begin'	=> 'TEXTBEGIN',
			'text_end'		=> 'TEXTEND',
			'columns'		=> array(
				'off'		=> array('%04X', ' '),
				'g'			=> array('%04X', ':'),
				'e'			=> array('%04X', ' '),
				'name'		=> array('%-10.10s', ' '),
				'vr'		=> array('%2s', ' '),
				'len'		=> array('%-3d', ' '),
				'val'		=> array('[%s]', ''),
			),
		);
		$dicom = Nanodicom::factory($filename, 'dumper');

		$a = str_replace("TEXTENDEND","",$dicom->dump($formatting));
		$t = explode("BEGINTEXTBEGIN",$a);
		
		$paciente['nome'] = str_replace("]","",$t[26]);
		$paciente['nome'] = explode("[",$paciente['nome']);
		$paciente['nome'][1] = str_replace("\n","",$paciente['nome'][1]);
		
		$paciente['os'] = str_replace("]","",$t[27]);
		$paciente['os'] = explode("[",$paciente['os']);
		$paciente['os'][1] = str_replace("\n","",$paciente['os'][1]);
		
		$paciente['nascimento'] = str_replace("]","",$t[28]);
		$paciente['nascimento'] = explode("[",$paciente['nascimento']);
		$paciente['nascimento'][1] = str_replace("\n","",$paciente['nascimento'][1]);
		
		$paciente['genero'] = str_replace("]","",$t[29]);
		$paciente['genero'] = explode("[",$paciente['genero']);
		$paciente['genero'][1] = str_replace("\n","",$paciente['genero'][1]);

		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	} 
	
	try
	{
		$dicom  = Nanodicom::factory($filename, 'pixeler');
		if ( ! file_exists($filename.'.0.jpg')){
			
			$images = $dicom->get_images();
			
			if ($images !== FALSE)
			{
				foreach ($images as $index => $image)
				{
					$dicom->write_image($image, $dir.$paciente['os'][1].'_'.$i);
					$i++;
					unlink($dir.$file);
					
					echo "Imagem ".$dir.$paciente['os'][1].'_'.$i." convertida com sucesso <br />";
				}
			}
			else
			{
				echo "There are no DICOM images or transfer syntax not supported yet. <br />";
			}
			$images = NULL;
		}
		else
		{
			echo $dir.$paciente['os'][1].'_'.$i."Imagem já existe <br />";
		}
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()." <br />";
	}
	
	}
}
