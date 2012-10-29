<?php

// mail.php -- Send emails (simplified) in utf8 format
// Copyright (C) 2012 Andreas Renberg <iq_andreas@hotmail.com>
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


class Mail
{

	public static function filter_name($input)
	{
		$rules = array( "\r" => '', "\n" => '', "\t" => '', '"'  => "'", '<'  => '[', '>'  => ']' );
		return trim(strtr($input, $rules));
	}
	
	public static function filter_email($input)
	{
		$rules = array( "\r" => '', "\n" => '', "\t" => '', '"'  => '', ','  => '', '<'  => '', '>'  => '' );
		return strtr($input, $rules);
	}
	
	// Taken from http://php.net/manual/en/function.preg-replace.php#80412
	public static function filter_filename($filename, $replace = "")
	{
		$reserved = preg_quote('\/:*?"<>|', '/'); //characters that are  illegal on any of the 3 major OS's
		//replaces all characters up through space and all past ~ along with the above reserved characters
		return preg_replace("/([\\x00-\\x20\\x7f-\\xff{$reserved}]+)/", $replace, $filename);
	}
	
	public static function create_full_address($email, $name = "")
	{
		$email = Mail::filter_email($email);
		$name =  Mail::filter_name($name);
	
		if (!$email) return "";
		
		if ($name)
		{
			return "$name <$email>";
		}
		else
		{
			return $email;
		}
	}


	public function __construct($subject, $message)
	{
		$this->subject = $subject;
		$this->message = $message;
	}
	
	
	// If the emails arrive in your client "garbled", you may need to change this
	// field to "\n" instead.
	public $header_line_ending = "\r\n";
	
	private $attachment_data;
	private $attachment_name;
	
	private $subject;
	private $message;
	private $from;
	private $reply_to;
	
	public function set_attachment($attachment_data, $attachment_name = 'attachment.txt')
	{
		$this->attachment_data = $attachment_data;
		$this->attachment_name = $attachment_name;
	}
	
	public function set_from($email, $name = "")
	{
		$this->from = Mail::create_full_address($email, $name);
	}
	
	public function set_reply_to($email, $name = "")
	{
		$this->reply_to = Mail::create_full_address($email, $name);
	}
	
	public function send($recipient_email, $recipient_name = "")
	{
		$subject = '=?UTF-8?B?'.base64_encode($this->subject).'?=';
		$from = $this->from;
		$reply_to = $this->reply_to;
		$recipient = Mail::create_full_address($recipient_email, $recipient_name);
		
		$uid = md5(uniqid(time()));
		$headers = array();
		
		if (!empty($from)) 		$headers []= "From: $from";
		if (!empty($reply_to))	$headers []= "Reply-To: $reply_to";
		
		$headers []= "X-Mailer: PHP/" . phpversion();
		$headers []= "MIME-Version: 1.0";
		$headers []= "Content-Type: multipart/mixed; boundary=\"$uid\"";
		$headers []= "";
		$headers []= "This is a multi-part message in MIME format.";
		
		// PLAIN-TEXT MESSAGE
		$headers []= "--$uid"; 
		$headers []= "Content-Type: text/plain; charset=utf-8";
		$headers []= "Content-Transfer-Encoding: 8bit";
		$headers []= "";
		$headers []= $this->message;
		$headers []= "";
		
		// ATTACHMENT
		if (!empty($this->attachment_data))
		{
			$attachment_data = chunk_split(base64_encode($this->attachment_data));
			$attachment_name = Mail::filter_filename($this->attachment_name);
			
			$headers []= "--$uid";
			$headers []= "Content-Type: application/octet-stream; name=\"$attachment_name\"";
			$headers []= "Content-Transfer-Encoding: base64";
			$headers []= "Content-Disposition: attachment; filename=\"$attachment_name\"";
			$headers []= "";
			$headers []= $attachment_data;
			$headers []= "";
		}
		
		$headers []= "--$uid--";
		
		$header = implode($this->header_line_ending, $headers) . $this->header_line_ending;
		
		return mail($recipient, $subject, "", $header);
	}
}

