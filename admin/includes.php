<?php

define(HR, "<br>\n<hr><br>\n");
define(NL, "<br>\n");
define(RETURN_LINK, NL."<a href='index.php'>&lt; Return to admin page</a>".NL.NL);

// ABSOLUTE
define(ROOT_DIR,	 dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
define(COMMENTS_DIR, ROOT_DIR . 'comments' . DIRECTORY_SEPARATOR);
define(SPAM_DIR, 	 ROOT_DIR . 'spam' . DIRECTORY_SEPARATOR);

define(LOG_FILE, 	 ROOT_DIR . 'admin' . DIRECTORY_SEPARATOR . 'mail.log');
define(SPAMFILTER,	 ROOT_DIR . 'php-spam-filter' . DIRECTORY_SEPARATOR . 'spamfilter.php');

// RELATIVE
//define('COMMENTS_DIR', 	'../comments');
//define('SPAM_DIR', 		'../spam');

