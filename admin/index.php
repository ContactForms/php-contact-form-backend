<html><body>
<?php
	require_once 'includes.php';
	require_once SPAMFILTER;
	$filter = new SpamFilter();
	if ($filter->blacklist_update_available())
	{
		echo 'A new version of the spam blacklists is available ';
		echo '[<a href="blacklist.php?update">update</a>]';
	}
	else
	{
		echo 'Spam blacklist is up to date (using version '.$filter->version().')';
	}
?>
<ul>
<li><strong>Comments</strong> [
	<a href="comments.php">list</a>,
	<a href="blacklist.php?run=comments">re-run spam check</a>
]</li>
<li><strong>Spam comments</strong> [
	<a href="spam.php?clear">delete all</a>
]</li>
<li><strong>Log</strong> [
	<a href="log.php?download">download</a>,
	<a href="log.php?clear">clear</a>
]</li>
</ul> 
</body></html>
