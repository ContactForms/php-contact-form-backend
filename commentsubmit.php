<?php

// commentsubmit.php -- Receive comments and e-mail them to someone
// Copyright (C) 2011 Matt Palmer <mpalmer@hezmatt.org>
//
//  This program is free software; you can redistribute it and/or modify it
//  under the terms of the GNU General Public License version 3, as
//  published by the Free Software Foundation.
//
//  This program is distributed in the hope that it will be useful, but
//  WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
//  General Public License for more details.
//
//  You should have received a copy of the GNU General Public License along
//  with this program; if not, see <http://www.gnu.org/licences/>


require_once 'config.php';

require_once 'mail.php';
require_once 'php-spam-filter/spamfilter.php';

function get_post_field($key, $defaultValue = "")
{
	return (isset($_POST[$key]) && !empty($_POST[$key])) ? $_POST[$key] : $defaultValue;
}

function get_post_data_as_yaml()
{
	$yaml_data = "";
	
	foreach ($_POST as $key => $value) 
	{
		if (strstr($value, "\n") != "") 
		{
			// Value has newlines... need to indent them so the YAML
			// looks right
			$value = str_replace("\n", "\n  ", $value);
		}
		// It's easier just to single-quote everything than to try and work
		// out what might need quoting
		$value = "'" . str_replace("'", "''", $value) . "'";
		$yaml_data .= "$key: $value\n";
	}
	
	return $yaml_data;
}

function log_to($string, $filename = 'admin/mail.log')
{
	$log_date = date(DateTime::ATOM);
	try
	{
		$handle = fopen($filename, 'a');
		fwrite($handle, "[$log_date] $string\n");
		fclose($handle);
	}
	catch (Exception $e) 
	{
		// Unable to even log error! You are screwed!
		//cry();
	}
}

function save_as_file($filename, $data)
{
	try
	{
		$handle = fopen($filename, 'w');
		fwrite($handle, $data);
		fclose($handle);
	}
	catch (Exception $e) 
	{
		log_to("WARNING: Unable to create file '$filename'");
		return false;
	}
	
	return true;
}


// Automatically die if the user accessed this page directly, 
//  rather than by the submit button on the blog
if (!isset($_POST['submit']))
{
	log_to("Tried to access `commentsubmit.php` without any or with invalid POST data.");
	include $COMMENT_INVALID;
	die();
}
unset($_POST['submit']);

$COMMENTER_NAME = get_post_field('name', "Anonymous");
$COMMENTER_EMAIL_ADDRESS = get_post_field('email', $EMAIL_ADDRESS);
$COMMENTER_WEBSITE = get_post_field('link');
$COMMENT_BODY = get_post_field('comment', "");
unset($_POST['comment']);
$COMMENT_DATE = date($DATE_FORMAT);

$COMMENT_PRIVATE = (get_post_field('private') == "true");
unset($_POST['private']);
unset($_POST['published']); // Prevent any funny business by messing with POST values

$POST_TITLE = get_post_field('post_title', "Unknown post");
$POST_ID = get_post_field('post_id', "");
unset($_POST['post_id']);

$yaml_data  = "---\n";
$yaml_data .= "post_id: $POST_ID\n";
$yaml_data .= "date: $COMMENT_DATE\n";
$yaml_data .= get_post_data_as_yaml();
if ($COMMENT_PRIVATE) $yaml_data .= "published: false\n";
$yaml_data .= "---\n";

$yaml_data .= $COMMENT_BODY;

// Check for missing period before extension (or ignores it if no extension is given)
if (preg_match('/^[^.].*$/', $COMMENT_FILENAME_EXT)) { $COMMENT_FILENAME_EXT = ".$COMMENT_FILENAME_EXT"; }
$file_date = date('Y-m-d-H-i-s');
$file_name = Mail::filter_filename($POST_ID, '-') . "-comment-$file_date" . $COMMENT_FILENAME_EXT;


$title = $COMMENT_PRIVATE ? "Comment" : "Private message";
$title .= " from $COMMENTER_NAME on '$POST_TITLE'";
log_to($title);


$filter = new SpamFilter();
$SPAM = $SPAMCHECK_COMMENTS ? $filter->check_text($COMMENT_BODY) : false;
if ($SPAM)
{
	if ($SAVE_SPAM_COMMENTS)
	{
		// Save a backup of the file for double checking later for false positives
		$spam_filename = 'spam' . DIRECTORY_SEPARATOR . $file_name;
		if (save_as_file($spam_filename, $yaml_data))
			{ log_to("\tSuspected SPAM saved as '$spam_filename'"); }
	}
	
	include $COMMENT_CONTAINS_SPAM;
	die();
}
else
{
	if ($SAVE_COMMENTS)
	{
		$comment_filename = 'comments' . DIRECTORY_SEPARATOR . $file_name;
		if (save_as_file($comment_filename, $yaml_data))
			{ log_to("\tComment saved as '$comment_filename'"); }
	}
}


if ($SEND_COMMENTS)
{
	$subject = $title;

	$message = "$COMMENT_BODY\n\n";
	$message .= "----------------------\n";
	$message .= "$COMMENTER_NAME\n";
	$message .= "$COMMENTER_WEBSITE\n";

	$mail = new Mail($subject, $message);
	$mail->set_from($EMAIL_ADDRESS, $COMMENTER_NAME);
	$mail->set_reply_to($COMMENTER_EMAIL_ADDRESS, $COMMENTER_NAME);

	$mail->header_line_ending = $HEADER_LINE_ENDING;
	$mail->set_attachment($yaml_data, $file_name);


	if ($mail->send($EMAIL_ADDRESS))
	{
		log_to("\tSent as email to '$EMAIL_ADDRESS'");
	}
	else
	{
		log_to("\tERROR: Unable to send email to '$EMAIL_ADDRESS'. Diagnose ASAP!");
		echo "There was a problem sending the comment. Please contact the site's owner.";
		die();
	}
}

include $COMMENT_RECEIVED;

