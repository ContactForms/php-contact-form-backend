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


// The extension of the comment's filename determines what type of converter
// will be used. The list of extensions can be found in `configuration.rb`
// and is dependent on the converters installed in Jekyll. The default 
// extensions (at least as of Jekyll 1.2.1) are:
//    Markdown => markdown, mkd, mkdn, md
//    Textile  => textile
// If you want the comment to just be treated as HTML, use 
//    '.html', '.txt', or just an emtpy string ''.
$COMMENT_FILENAME_EXT = '.md';

// Format of the date you want to use in your comments.  See
// http://php.net/manual/en/function.date.php for the insane details of this
// format.
$DATE_FORMAT = "Y-m-d H:i";

// If the emails arrive in your client "garbled", you may need to change this
// line to "\n" instead.
$HEADER_LINE_ENDING = "\r\n";


// Make sure comments pass the proper "spam check" rules before sending them.
// Users who write spam comments will be prompted with an error message.
$SPAMCHECK_COMMENTS = true;

// Email the comments to the recipient specified above (can be disabled, for
// instance, if your inbox is getting flooded with spam comments, and you
// would much rather just use the admin interface.
$EMAIL_COMMENTS = true;

// Store the comments on the server in the `comments` folder.
$SAVE_COMMENTS = true;

// Store the spam comments on the server. These will go into a separate 
// directory named `spam`.
$SAVE_SPAM_COMMENTS = false;


