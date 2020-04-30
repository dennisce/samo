<?php
ini_set('max_execution_time', '3000');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require 'nanodicom.php';

$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR;

//$dir = "smb://desktop-ng0lghu/Orthanc/03/83/";

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

foreach ($files as $file)
{
	$filename = $dir.$file;
//	$filename = "\\\\192.168.1.66\\desktop-ng0lghu\\Orthanc\\03\\83\\03831f27-9f56-4122-b6b6-3120572fdcb2.dcm";

	$t = explode(".dcm",$filename);
	if(count($t) > 1){
	
	// 16) Provide your own dumper formatting
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
/*
		$a = str_replace("TEXTENDEND","",$dicom->dump($formatting));
		$t = explode("BEGINTEXTBEGIN",$a);
		
		$paciente['nome'] = str_replace("]","",$t[26]);
		$paciente['nome'] = explode("[",$paciente['nome']);
		
		$paciente['id'] = str_replace("]","",$t[27]);
		$paciente['id'] = explode("[",$paciente['id']);
		
		$paciente['nascimento'] = str_replace("]","",$t[28]);
		$paciente['nascimento'] = explode("[",$paciente['nascimento']);
		
		$paciente['genero'] = str_replace("]","",$t[29]);
		$paciente['genero'] = explode("[",$paciente['genero']);

		echo "<pre>";
		print_r($dicom);
		echo "</pre>";		
	*/			
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}
	
	// 20) Gets the images from the dicom object if they exist. This example is for gd
	try
	{
		$dicom  = Nanodicom::factory($filename, 'pixeler');
		if ( ! file_exists($filename.'.0.jpg'))
		{
			
			$images = $dicom->get_images();
			// If using another library, for example, imagemagick, the following should be done:
			// $images = $dicom->set_driver('imagick')->get_images();

			if ($images !== FALSE)
			{
				foreach ($images as $index => $image)
				{
					// Defaults to jpg
					$dicom->write_image($image, $dir.$file.'.'.$index);
					// To write another format, pass the format in second parameter.
					// This will write a png image instead
					// $dicom->write_image($image, $dir.$file.'.'.$index, 'png');
				}
			}
			else
			{
				echo "There are no DICOM images or transfer syntax not supported yet.\n <br />";
			}
			$images = NULL;
		}
		else
		{
			echo "Image already exists\n <br />";
		}
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}
	
	// 1) Most basic example. Fast!
		/*try
		{
			echo "<b>".$filename."</b><br />";
			echo "1) Most basic example. Fast!\n <br />";
			$dicom = Nanodicom::factory($filename);
			// Only a small subset of the dictionary entries were loaded
			echo $dicom->parse()->profiler_diff('parse')."\n <br />"; 
			unset($dicom);
		}
		catch (Nanodicom_Exception $e)
		{
			echo 'File failed. '.$e->getMessage()."\n <br />";
		}*/
		
		// 20) Gets the images from the dicom object if they exist. This example is for gd
	/*try
	{
		echo "20) Gets the images from the dicom object if they exist. This example is for gd\n <br />";
		$dicom  = Nanodicom::factory($filename, 'pixeler');
		if ( ! file_exists($filename.'.0.jpg'))
		{
			
			$images = $dicom->get_images();
			// If using another library, for example, imagemagick, the following should be done:
			// $images = $dicom->set_driver('imagick')->get_images();

			if ($images !== FALSE)
			{
				foreach ($images as $index => $image)
				{
					// Defaults to jpg
					$dicom->write_image($image, $dir.$file.'.'.$index);
					// To write another format, pass the format in second parameter.
					// This will write a png image instead
					// $dicom->write_image($image, $dir.$file.'.'.$index, 'png');
				}
			}
			else
			{
				echo "There are no DICOM images or transfer syntax not supported yet.\n <br />";
			}
			$images = NULL;
		}
		else
		{
			echo "Image already exists\n <br />";
		}
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/
		
	

	// 2) Load only given tags. It will stop once all given tags are found. Fastest!
	/*try
	{
		echo "2) Load only given tags. It will stop once all given tags are found. Fastest!\n <br />";
		$dicom = Nanodicom::factory($filename, 'simple');
		$dicom->parse(array(array(0x0010, 0x0010)));
		// Only a small subset of the dictionary entries were loaded
		echo $dicom->profiler_diff('parse')."\n <br />"; 
		echo 'Patient name if exists: '.$dicom->value(0x0010, 0x0010)."\n <br />"; // Patient Name if exists
		// This will return nothing because dictionaries were not loaded
		echo 'Patient name should be empty here: '.$dicom->PatientName."\n <br />";
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/
	
	// 3) Load only given tags by name. Stops once all tags are found. Not so fast.
	/*try
	{
		echo "3) Load only given tags by name. Stops once all tags are found. Not so fast.\n <br />";
		$dicom = Nanodicom::factory($filename, 'simple');
		$dicom->parse(array('PatientName'));
		echo $dicom->profiler_diff('parse')."\n <br />";
		echo 'Patient name if exists: '.$dicom->value(0x0010, 0x0010)."\n <br />"; // Patient Name if exists
		// Or
		echo 'Patient name if exists: '.$dicom->PatientName."\n <br />"; // Patient Name if exists
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 4) Load only given tags. Dump it and print certain tags. Load 'dumper' directly.
	/*try
	{
		echo "4) Load only given tags. Dump it and print certain tags. Load 'dumper' directly.\n <br />";
		$dicom = Nanodicom::factory($filename, 'dumper');
		$dicom->parse(array(array(0x0010, 0x0010)));
		echo $dicom->dump();
		echo $dicom->profiler_diff('parse')."\n <br />";
		// Patient Name if exists
		echo 'Something should show if element exists.'.$dicom->value(0x0010, 0x0010)."\n <br />";
		// This will return the value because 'dumper' was used and loaded the dictionaries
		echo 'This should be empty, no dictionaries loaded.'.$dicom->PatientName."\n <br />";
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 5) Load simple and print certain value
	/*try
	{
		echo "5) Load simple and print certain value\n <br />";
		$dicom = Nanodicom::factory($filename);
		$dicom->parse();
		echo $dicom->profiler_diff('parse')."\n <br />";
		echo 'Patient Name: '.$dicom->value(0x0010, 0x0010)."\n <br />"; // Patient Name if exists
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 6) Load simple and extend it to 'dumper'
	/*try
	{
		echo "6) Load simple and extend it to 'dumper'\n <br />";
		$dicom = Nanodicom::factory($filename);
		
		echo "<pre>".$dicom->parse()->extend('dumper')->dump()."</pre> --- <br />";
		echo $dicom->profiler_diff('parse').' <br /> '.$dicom->profiler_diff('dump')."\n <br /><br />";
		
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}/*

	// 7) Load simple and extend it to 'dumper'. No need to parse, dump() does it. Parsing is done only once.
	/*try
	{
		echo "7) Load simple and extend it to 'dumper'. No need to parse, dump() does it. Parsing is done only once.\n <br />";
		$dicom = Nanodicom::factory($filename);
		echo $dicom->extend('dumper')->dump();
		echo $dicom->profiler_diff('parse').' '.$dicom->profiler_diff('dump')."\n <br />";
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 8) Load 'dumper' directly. Dump output is in html format.
	/*try
	{
		echo "8) Load 'dumper' directly. Dump output is in html format.\n <br />";
		$dicom = Nanodicom::factory($filename, 'dumper');
		echo $dicom->parse()->dump('html');
		echo $dicom->profiler_diff('parse').' '.$dicom->profiler_diff('dump')."\n <br />";
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 9) Load 'anonymizer' directly.
	/*try
	{
		echo "9) Load 'anonymizer' directly.\n <br />";
		$dicom = Nanodicom::factory($filename, 'anonymizer');
		file_put_contents($filename.'.ex9', $dicom->anonymize());
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 10) Extend 'anonymizer'. No need to call parse(), anonymize() will do it.
	/*try
	{
		echo "10) Extend 'anonymizer'. No need to call parse(), anonymize() will do it.\n <br />";
		$dicom = Nanodicom::factory($filename);
		file_put_contents($filename.'.ex10', $dicom->extend('anonymizer')->anonymize());
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 11) Double extension (and probably you can go on and on)
	/*try
	{
		echo "11) Double extension (and probably you can go on and on)\n <br />";
		$dicom = Nanodicom::factory($filename);
		echo $dicom->extend('dumper')->dump();
		file_put_contents($filename.'.ex11', $dicom->extend('anonymizer')->anonymize());
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 12) Save file as Explicit VR Little Endian
	/*try
	{
		echo "12) Save file as Explicit VR Little Endian\n <br />";
		$dicom = Nanodicom::factory($filename);
		echo $dicom->parse()->profiler_diff('parse')."\n <br />";
		// Setting values takes care of even length
		// If set to '1.2.840.10008.1.2.1.99' it will use deflate
		$dicom->value(0x0002, 0x0010, Nanodicom::EXPLICIT_VR_LITTLE_ENDIAN);
		echo $dicom->write_file($filename.'.ex12')->profiler_diff('write')."\n <br />";
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 13) Pass file contents instead of filename
	/*try
	{
		echo "13) Pass file contents instead of filename\n <br />";
		$contents = file_get_contents($filename);
		$dicom = Nanodicom::factory($contents, 'dumper', 'blob');
		echo $dicom->dump();
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 14) Check if file has preamble and DICM
	/*try
	{
		echo "14) Check if file has preamble and DICM\n <br />";
		$dicom = Nanodicom::factory($filename);
		echo 'Is DICOM? '.$dicom->is_dicom()."\n <br />";
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 15) Anonymize and save file as Explicit VR Little Endian
	/*try
	{
		echo "15) Anonymize and save file as Explicit VR Little Endian\n <br />";
		$dicom = Nanodicom::factory($filename);
		echo $dicom->parse()->profiler_diff('parse')."\n <br />";
		// Setting values takes care of even length
		// If set to '1.2.840.10008.1.2.1.99' it will use deflate
		$dicom->value(0x0002, 0x0010, Nanodicom::EXPLICIT_VR_LITTLE_ENDIAN);
		file_put_contents($filename.'.ex15', $dicom->extend('anonymizer')->anonymize());
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 16) Provide your own dumper formatting
	/*try
	{
		echo "16) Provide your own dumper formatting\n <br />";
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

		//echo "</pre>".var_dump($dicom->dump($formatting))."</pre> --- <br />";
		$a = str_replace("TEXTENDEND","",$dicom->dump($formatting));
		$t = explode("BEGINTEXTBEGIN",$a);
		
		$paciente['nome'] = str_replace("]","",$t[26]);
		$paciente['nome'] = explode("[",$paciente['nome']);
		
		$paciente['id'] = str_replace("]","",$t[27]);
		$paciente['id'] = explode("[",$paciente['id']);
		
		$paciente['nascimento'] = str_replace("]","",$t[28]);
		$paciente['nascimento'] = explode("[",$paciente['nascimento']);
		
		$paciente['genero'] = str_replace("]","",$t[29]);
		$paciente['genero'] = explode("[",$paciente['genero']);
		
		echo "<pre>";
		print_r($paciente);
		echo "</pre>";		
		
		
		
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 17) Anonymize on the fly and dump the contents, don't save to a file
	/*try
	{
		echo "17) Anonymize on the fly and dump the contents, don't save to a file\n <br />";
		$dicom  = Nanodicom::factory($filename, 'anonymizer');
		$dicom1 = Nanodicom::factory($dicom->anonymize(), 'dumper', 'blob');
		echo $dicom1->dump();
		unset($dicom);
		unset($dicom1);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 18) Pass your own list of elements to anonymizer
	/*try
	{
		echo "18) Pass your own list of elements to anonymizer\n <br />";
		// Own tag elements for anonymizing
		$tags = array(
			array(0x0008, 0x0020, '{date|Ymd}'),			// Study Date
			array(0x0008, 0x0021, '{date|Ymd}'),			// Series Date
			array(0x0008, 0x0090, 'physician{random}'),		// Referring Physician
			array(0x0010, 0x0010, 'patient{consecutive}'),  // Patient Name
			array(0x0010, 0x0020, 'id{consecutive}'), 		// Patient ID
			array(0x0010, 0x0030, '{date|Ymd}'), 			// Patient Date of Birth
		);
		$dicom  = Nanodicom::factory($filename, 'anonymizer');
		$dicom1 = Nanodicom::factory($dicom->anonymize($tags), 'dumper', 'blob');
		echo $dicom1->dump();
		unset($dicom);
		unset($dicom1);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 19) Pass your own list of mappings to anonymizer. Patient Name should be replace to
	// 'Mapped' if 'Anonymized' is found. Case sensitive
	/*try
	{
		echo "19) Pass your own list of mappings to anonymizer\n <br />";
		// Own tag elements for anonymizing
		$tags = array(
			array(0x0008, 0x0020, '{date|Ymd}'),			// Study Date
			array(0x0008, 0x0021, '{date|Ymd}'),			// Series Date
			array(0x0008, 0x0090, 'physician{random}'),		// Referring Physician
			array(0x0010, 0x0010, 'patient{consecutive}'),  // Patient Name
			array(0x0010, 0x0020, 'id{consecutive}'), 		// Patient ID
			array(0x0010, 0x0030, '{date|Ymd}'), 			// Patient Date of Birth
		);
		$replacements = array(
			array(0x0010, 0x0010, 'anonymized', 'Mapped'),
		);
		$dicom  = Nanodicom::factory($filename, 'anonymizer');
		$dicom1 = Nanodicom::factory($dicom->anonymize($tags, $replacements), 'dumper', 'blob');
		echo $dicom1->dump();
		file_put_contents($filename.'.ex19', $dicom1->write());
		unset($dicom);
		unset($dicom1);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/

	// 20) Gets the images from the dicom object if they exist. This example is for gd
	/*try
	{
		echo "20) Gets the images from the dicom object if they exist. This example is for gd\n <br />";
		$dicom  = Nanodicom::factory($filename, 'pixeler');
		if ( ! file_exists($filename.'.0.jpg'))
		{
			
			$images = $dicom->get_images();
			// If using another library, for example, imagemagick, the following should be done:
			// $images = $dicom->set_driver('imagick')->get_images();

			if ($images !== FALSE)
			{
				foreach ($images as $index => $image)
				{
					// Defaults to jpg
					$dicom->write_image($image, $dir.$file.'.'.$index);
					// To write another format, pass the format in second parameter.
					// This will write a png image instead
					// $dicom->write_image($image, $dir.$file.'.'.$index, 'png');
				}
			}
			else
			{
				echo "There are no DICOM images or transfer syntax not supported yet.\n <br />";
			}
			$images = NULL;
		}
		else
		{
			echo "Image already exists\n <br />";
		}
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/
	
	// 21) Prints summary report
	/*try
	{
		echo "21) Prints summary report\n <br />";
		$dicom  = Nanodicom::factory($filename);
		echo $dicom->summary();
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n <br />";
	}*/
	}
}
