<?php

include_once 'includes.php';

if (isset($_GET['download']))
{
	$download_file_name = basename(LOG_FILE); // Clean from any injection using ../ syntax
	$download_file = LOG_FILE;
	
	if (file_exists($download_file))
	{
		header('Content-type: text/plain');
		header('Content-disposition: attachment; filename="'.$download_file_name.'"');
		readfile($download_file);
		//header('location: index.php');
		exit;
	}
	else
	{
		echo "Cannot find <i>$download_file_name</i>".HR;
	}
}
if (isset($_GET['clear']))
{
	file_put_contents(LOG_FILE, "");
	echo "Log cleared";
}

echo RETURN_LINK;
