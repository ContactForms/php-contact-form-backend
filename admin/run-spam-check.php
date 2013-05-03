<?php

include_once 'includes.php';

function delete_comment($delete_file_name)
{
	$delete_file_name = basename($delete_file_name); // Prevent injection (just in case)
	$delete_file = SPAM_DIR . $delete_file_name;
	
	if (file_exists($delete_file))
	{
		unlink($delete_file);
		echo "<b>Deleted</b> <i>$delete_file_name</i>".NL;
	}
	else
	{
		echo "Cannot find <i>$delete_file_name</i>.".NL."Did you already delete it?".BR;
	}
}

// MAIN CONTENTS

$yaml_files = glob(COMMENTS_DIR . '*.yaml');

//$num_comments = count($yaml_files);
$num_files_deleted = 0;
$num_files_opened = 0;

require_once '../spamfilter.php';

foreach ($yaml_files as $filename) 
{
	$file_contents = file_get_contents($filename);
	$num_files_opened++;
	
	$SPAM = spam_check_text($file_contents);
	if (!empty($SPAM))
	{
		delete_comment($filename);
		$num_files_deleted++;
	}
}

echo "Deleted $num_files_deleted of $num_files_opened";

echo RETURN_LINK;
?>
