<?php

include_once 'includes.php';

function list_file_contents($file_name)
{
	$file_path = COMMENTS_DIR . DIRECTORY_SEPARATOR . $file_name;
	$file_contents_html = nl2br(file_get_contents($file_path));

	$delete_link = "comments.php?delete=" . urlencode($file_name);
	$download_link = "comments.php?download=" . urlencode($file_name);
	
	echo "[";
		echo "<a href='$delete_link'>delete</a>, ";
		echo "<a href='$download_link'>save as</a>";
	echo "] ";
	echo "<b>$file_name</b>".NL;
	echo $file_contents_html.HR;
}

if (isset($_GET['delete']))
{
	$delete_file_name = basename($_GET['delete']); // Clean from any injection using ../ syntax
	$delete_file = COMMENTS_DIR . DIRECTORY_SEPARATOR . $delete_file_name;
	
	if (file_exists($delete_file))
	{
		unlink($delete_file);
		echo "<b>Deleted</b> <i>$delete_file_name</i>".HR;
		header('location: comments.php');
		exit;
	}
	else
	{
		echo "Cannot find <i>$delete_file_name</i>".NL."Did you already delete it?".HR;
	}
}
if (isset($_GET['download']))
{
	$download_file_name = basename($_GET['download']); // Clean from any injection using ../ syntax
	$download_file = COMMENTS_DIR . DIRECTORY_SEPARATOR . $download_file_name;
	
	if (file_exists($download_file))
	{
		header('Content-type: text/plain');
		header('Content-disposition: attachment; filename="'.$download_file_name.'"');
		readfile($download_file);
		//header('location: comments.php');
		exit;
	}
	else
	{
		echo "Cannot find <i>$download_file_name</i>".HR;
	}
}


// MAIN CONTENTS

echo RETURN_LINK;

$yaml_files = glob(COMMENTS_DIR . '*.yaml');
// Remove directory identifiers!
$yaml_files = array_map('basename', $yaml_files);

$num_comments = count($yaml_files);
$max_comments = 3;
$num_files_opened = 0;

$displayed_comments = ($max_comments > $num_comments) ? $num_comments : $max_comments;
echo "Showing $displayed_comments of $num_comments".HR;

//$directory_name = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'comments';
//$dir = new DirectoryIterator($directory_name);
//foreach ($dir as $fileinfo) {
//	if (($num_files_opened++ < $displayed_comments) && !$fileinfo->isDot() && ($fileinfo->getFilename() != 'index.php')) 
//	    list_file_contents($directory_name . DIRECTORY_SEPARATOR . $fileinfo->getFilename());

foreach ($yaml_files as $filename) {
	if ($num_files_opened++ < $displayed_comments) 
	{
	    list_file_contents($filename);
	}
}


?>
