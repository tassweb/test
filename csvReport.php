<?php

	require_once 'dbinfo.php';
	require_once 'dbfunctions.php';
	
	foreach($_GET as $key => $value) {
		$$key = $value;
	}
	foreach($_POST as $key => $value) {
		$$key = $value;
	}
	if (isset($report_id)) {
		$query = "select key_text, value_text from refreportdetailtb where report_id = " . $report_id;
		$reportDetailTb = mysql_query($query) or die("Error with Report details: " . mysql_error());
		$reportDetails = array();
		
		while ($reportDetailArr = mysql_fetch_assoc($reportDetailTb)) {
			$key = $reportDetailArr['key_text'];
			$value = $reportDetailArr['value_text'];
			$reportDetails[$key] = $value;
		}
	}
	echo "blah 1";
	
	if (isset($sql)) {
		//header("Content-Type: application/force-download\n"); 
		//header('Content-Disposition: attachment; filename="export.csv"');
		echo "blah 2";
		$fp = fopen('export.csv', 'w') or die("Cannot open file: export.csv");
		
		$resultTb = mysql_query($sql) or die ("Error running full query: " . mysql_error());
		while ($resultRow = mysql_fecth_assoc($resultTb)) {
			fputcsv($fp,$value);
		}
		fclose($fp);
		echo "blah blah blah";
		die();
		set_time_limit(0);
		$file_path='export.csv';
		output_file($file_path, $file_path, 'text/plain');
	}
	
	function output_file($file, $name, $mime_type='')
	{
		/*
		 This function takes a path to a file to output ($file),
		the filename that the browser will see ($name) and
		the MIME type of the file ($mime_type, optional).
	
		If you want to do something on download abort/finish,
		register_shutdown_function('function_name');
		*/
		if(!is_readable($file)) die('File not found or inaccessible!');
	
		$size = filesize($file);
		$name = rawurldecode($name);
	
		/* Figure out the MIME type (if not specified) */
		$known_mime_types=array(
	    "pdf" => "application/pdf",
	    "txt" => "text/plain",
	    "html" => "text/html",
	    "htm" => "text/html",
	    "exe" => "application/octet-stream",
	    "zip" => "application/zip",
	    "doc" => "application/msword",
	    "xls" => "application/vnd.ms-excel",
	    "ppt" => "application/vnd.ms-powerpoint",
	    "gif" => "image/gif",
	    "png" => "image/png",
	    "jpeg"=> "image/jpg",
	    "jpg" =>  "image/jpg",
	    "php" => "text/plain",
	    "csv" => "text/plain"
		);
	
		if($mime_type==''){
			$file_extension = strtolower(substr(strrchr($file,"."),1));
			if(array_key_exists($file_extension, $known_mime_types)){
				$mime_type=$known_mime_types[$file_extension];
			} else {
				$mime_type="application/force-download";
			};
		};
	
		@ob_end_clean(); //turn off output buffering to decrease cpu usage
	
		// required for IE, otherwise Content-Disposition may be ignored
		if(ini_get('zlib.output_compression'))
		ini_set('zlib.output_compression', 'Off');
	
		header('Content-Type: ' . $mime_type);
		header('Content-Disposition: attachment; filename="'.$name.'"');
		header("Content-Transfer-Encoding: binary");
		header('Accept-Ranges: bytes');
	
		/* The three lines below basically make the
		 download non-cacheable */
		header("Cache-control: private");
		header('Pragma: private');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
		// multipart-download and download resuming support
		if(isset($_SERVER['HTTP_RANGE']))
		{
			list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
			list($range) = explode(",",$range,2);
			list($range, $range_end) = explode("-", $range);
			$range=intval($range);
			if(!$range_end) {
				$range_end=$size-1;
			} else {
				$range_end=intval($range_end);
			}
	
			$new_length = $range_end-$range+1;
			header("HTTP/1.1 206 Partial Content");
			header("Content-Length: $new_length");
			header("Content-Range: bytes $range-$range_end/$size");
		} else {
			$new_length=$size;
			header("Content-Length: ".$size);
		}
	
		/* output the file itself */
		$chunksize = 1*(1024*1024); //you may want to change this
		$bytes_send = 0;
		if ($file = fopen($file, 'r'))
		{
			if(isset($_SERVER['HTTP_RANGE']))
			fseek($file, $range);
	
			while(!feof($file) &&
			(!connection_aborted()) &&
			($bytes_send<$new_length)
			)
			{
				$buffer = fread($file, $chunksize);
				print($buffer); //echo($buffer); // is also possible
				flush();
				$bytes_send += strlen($buffer);
			}
			fclose($file);
		} else die('Error - can not open file.');
	
		die();
	}
?>