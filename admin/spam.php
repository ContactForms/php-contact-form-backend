<?php

include_once 'includes.php';

function delete_spam($delete_file_name)
{
	$delete_file_name = basename($delete_file_name); // Prevent injection (just in case)
	$delete_file = SPAM_DIR . DIRECTORY_SEPARATOR . $delete_file_name;
	
	if (file_exists($delete_file))
	{
		unlink($delete_file);
		//echo "<b>Deleted</b> <i>$delete_file_name</i>".NL;
	}
	else
	{
		echo "Cannot find <i>$delete_file_name</i>".BR;
	}
}

function get_spam_comments()
{
	$files = array();
	$all_files = scandir(SPAM_DIR);
	foreach($all_files as $filename)
	{
		if($filename === '.' || $filename === '..') { continue; }
		if($filename === '.htaccess') { continue; }
		if(!is_file(SPAM_DIR . DIRECTORY_SEPARATOR . $filename)) { continue; }
		$files []= $filename;
	}
	return $files;
}

if (isset($_GET['clear']))
{
	$spam_files = get_spam_comments();
	$num_files_total = count($spam_files);
	$num_files_deleted = 0;

	foreach ($spam_files as $filename) 
	{
		delete_spam($filename);
		$num_files_deleted++;
	}

	echo "Deleted $num_files_deleted of $num_files_total spam comments";
}

echo RETURN_LINK;
?>
