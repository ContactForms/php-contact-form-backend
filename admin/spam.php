<?php

include_once 'includes.php';

function delete_spam($delete_file_name)
{
	$delete_file_name = basename($delete_file_name); // Prevent injection (just in case)
	$delete_file = SPAM_DIR . $delete_file_name;
	
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

if (isset($_GET['clear']))
{
	// MAIN CONTENTS
	$yaml_files = glob(SPAM_DIR . '*.yaml');

	$num_files_total = count($yaml_files);
	$num_files_deleted = 0;

	foreach ($yaml_files as $filename) 
	{
		delete_spam($filename);
		$num_files_deleted++;
	}

	echo "Deleted $num_files_deleted of $num_files_total spam comments";
}

echo RETURN_LINK;
?>
