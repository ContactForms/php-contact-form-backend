<?php

include_once 'includes.php';

function delete_comment($delete_file_name, $reason = "")
{
	$delete_file_name = basename($delete_file_name); // Prevent injection (just in case)
	$delete_file = COMMENTS_DIR . DIRECTORY_SEPARATOR . $delete_file_name;
	
	if (file_exists($delete_file))
	{
		unlink($delete_file);
		echo "<b>Deleted</b> <i>$delete_file_name</i> $reason".NL;
	}
	else
	{
		echo "Cannot find <i>$delete_file_name</i>.".NL."Did you already delete it?".NL;
	}
}

function get_comments()
{
	$files = array();
	$all_files = scandir(COMMENTS_DIR);
	foreach($all_files as $filename)
	{
		if($filename === '.' || $filename === '..') { continue; }
		if($filename === '.htaccess') { continue; }
		if(!is_file(COMMENTS_DIR . DIRECTORY_SEPARATOR . $filename)) { continue; }
		$files []= $filename;
	}
	return $files;
}

// MAIN CONTENTS

if (isset($_GET['update']))
{
	require_once SPAMFILTER;
	$filter = new SpamFilter();
	$version = $filter->update_blacklists();
	echo "Blacklists updated to $version.".NL;
	echo "<a href=\"blacklist.php?run=comments\">Re-run spam check using the new definitions</a> (not required, but recommended)".NL;
	
}
elseif (isset($_GET['run']))
{
	// For now, only allow checking comments
	if ($_GET['run'] == 'comments')
	{
		$comment_files = get_comments();
		//$num_comments = count($comment_files);
		$num_files_deleted = 0;
		$num_files_opened = 0;

		require_once SPAMFILTER;
		$filter = new SpamFilter();

		foreach ($comment_files as $filename) 
		{
			$file_contents = file_get_contents(COMMENTS_DIR . DIRECTORY_SEPARATOR . $filename);
			$num_files_opened++;
	
			$SPAM = $filter->check_text($file_contents);
			if (!empty($SPAM))
			{
				delete_comment($filename, "for spam keyword '$SPAM'");
				$num_files_deleted++;
			}
		}
	}	
	else
	{
		echo "Invalid option for 'run'.";
	}
	
	
	echo "Deleted $num_files_deleted of $num_files_opened";
}


echo RETURN_LINK;
?>
