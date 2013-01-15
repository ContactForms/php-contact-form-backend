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
require_once 'spamfilter.php';

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

$COMMENTER_NAME = get_post_field('name', "Anonymous");
$COMMENTER_EMAIL_ADDRESS = get_post_field('email', $EMAIL_ADDRESS);
$COMMENTER_WEBSITE = get_post_field('link');
$COMMENT_BODY = get_post_field('comment', "");
$COMMENT_DATE = date($DATE_FORMAT);

$POST_TITLE = get_post_field('post_title', "Unknown post");
$POST_ID = get_post_field('post_id', "");
unset($_POST['post_id']);


$SPAM = spam_check_text($COMMENT_BODY);
if (!empty($SPAM))
{
	include $COMMENT_CONTAINS_SPAM;
	die();
}


$subject = "Comment from $COMMENTER_NAME on '$POST_TITLE'";

$message = "$COMMENT_BODY\n\n";
$message .= "----------------------\n";
$message .= "$COMMENTER_NAME\n";
$message .= "$COMMENTER_WEBSITE\n";

$mail = new Mail($subject, $message);
$mail->set_from($EMAIL_ADDRESS, $COMMENTER_NAME);
$mail->set_reply_to($COMMENTER_EMAIL_ADDRESS, $COMMENTER_NAME);

$yaml_data = "post_id: $POST_ID\n";
$yaml_data .= "date: $COMMENT_DATE\n";
$yaml_data .= get_post_data_as_yaml();

$attachment_date = date('Y-m-d-H-i-s');
$attachment_name = Mail::filter_filename($POST_ID, '-') . "-comment-$attachment_date.yaml";

$mail->header_line_ending = $HEADER_LINE_ENDING;
$mail->set_attachment($yaml_data, $attachment_name);


if ($mail->send($EMAIL_ADDRESS))
{
	include $COMMENT_RECEIVED;
}
else
{
	echo "There was a problem sending the comment. Please contact the site's owner.";
}
