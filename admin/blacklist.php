<?php

include_once 'includes.php';

function delete_comment($delete_file_name)
{
	$delete_file_name = basename($delete_file_name); // Prevent injection (just in case)
	$delete_file = COMMENTS_DIR . $delete_file_name;
	
	if (file_exists($delete_file))
	{
		unlink($delete_file);
		echo "<b>Deleted</b> <i>$delete_file_name</i>".NL;
	}
	else
	{
		echo "Cannot find <i>$delete_file_name</i>.".NL."Did you already delete it?".NL;
	}
}

// MAIN CONTENTS

if (isset($_GET['update']))
{
	require_once SPAMFILTER;
	$filter = new SpamFilter();
	$version = $filter->update_blacklists();
	echo "Blacklists updated to $version().".NL;
	echo "<a href=\"blacklist.php?run=comments\">Re-run spam check using the new definitions</a> (not required, but recommended)".NL;
	
}
elseif (isset($_GET['run']))
{
	// For now, only allow checking comments
	if ($_GET['run'] == 'comments')
	{
		$yaml_files = glob(COMMENTS_DIR . '*.yaml');

		//$num_comments = count($yaml_files);
		$num_files_deleted = 0;
		$num_files_opened = 0;

		require_once SPAMFILTER;
		$filter = new SpamFilter();

		foreach ($yaml_files as $filename) 
		{
			$file_contents = file_get_contents($filename);
			$num_files_opened++;
	
			$SPAM = $filter->check_text($file_contents);
			if (!empty($SPAM))
			{
				delete_comment($filename);
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
