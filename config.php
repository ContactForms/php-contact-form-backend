<?php

// Where the comment e-mails should be sent to.  This will also be used as
// the From: address.  Whilst you could, in theory, change this to take the
// address out of the form, it's *incredibly* highly recommended you don't,
// because that turns you into an open relay, and that's not cool.
$EMAIL_ADDRESS = "blogger@example.com";

// The contents of the following file (relative to this PHP file) will be
// displayed after the comment is received.  Customise it to your heart's
// content.
$COMMENT_RECEIVED = "html/comment_received.html";

// The contents of the following file (relative to this PHP file) will be
// displayed if the comment contains spam.  Customise it to your heart's
// content.
$COMMENT_CONTAINS_SPAM = "html/comment_contains_spam.html";

// The contents of the following file (relative to this PHP file) will be
// displayed if there was no comment, or if the comment contains severely
// incorrect data. Customise it to your heart's content.
$COMMENT_INVALID = "html/redirect_to_blog.html";


// Format of the date you want to use in your comments.  See
// http://php.net/manual/en/function.date.php for the insane details of this
// format.
$DATE_FORMAT = "Y-m-d H:i";

// If the emails arrive in your client "garbled", you may need to change this
// line to "\n" instead.
$HEADER_LINE_ENDING = "\r\n";


