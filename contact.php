<?php

// The Ultimate Contact Form - Version 3.7
// (C) L.W.C https://lior.weissbrod.com

// $theme = 'dark'; // Leave commented for using the user's system's theme
// $theme = 'light'; // Leave commented for using the user's system's theme
$siteName = "Your Site's Form";
$siteMail = "auto@form";
$sendresults = true; // Comment out to just save logs (if logs are enabled)
// $uselogs = '1'; // Uncomment to use logs based on $archive below, use commas for multiple logs like '1,2'
$autoreply = true; // Activate the global auto reply feature
$antispam = 10; // Activate spam protection by specifying a required reply (disable by emptying it or commenting out)

// Domain names that can use this script
// You can enable whole domains (e.g. bla.com)
// or just subdomains (e.g. www.bla.com, mysite.bla.com, etc.).
$domains = Array (
	'weissbrod.com'
);

// Thank you message. (Example: Thanks for submitting our web form)
$thankyoumessage = "Thank you for your time.
We will try to respond to your message soon!";

// Enter your e-mail address(es) (Example: user@domain.com, me@myhouse.com)

$emailaddresses = Array (
	'your@read.address'
);

// Log files
$archive = array(
	'../secret-folder/contact.log'
);

// Required fields
$required = array(
	'subject'
);

// Define the name of the message's input field/s (it could also be the name of an array):
$message_input = "message";

/* ----------------- DO NOT EDIT PAST THIS LINE -------------- */

function getOS() {
    if (getenv("HTTP_USER_AGENT"))
	$os = getenv("HTTP_USER_AGENT");
    else
	$os = "Unknown";
return $os;
}

function getIP() {
    $host = getenv("REMOTE_HOST");

    if (getenv("HTTP_CLIENT_IP") 
&& strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	$ip = getenv("HTTP_CLIENT_IP");
    elseif (getenv("HTTP_X_FORWARDED_FOR") 
&& strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	$ip = getenv("HTTP_X_FORWARDED_FOR");
    elseif (getenv("REMOTE_ADDR") 
&& strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	$ip = getenv("REMOTE_ADDR");
    else
	$ip = "Unknown";
$ip = $host . "[$ip]";
return $ip;
}

$redirect = isset($_REQUEST['redirect']) && $_REQUEST['redirect'] == 'false';
$output = $redirect ? array() : '';

if (!isset($_SERVER['HTTP_REFERER'])) {
	$referrer = "Unknown";
	$approved = true;
} else {
	$referrer = $_SERVER['HTTP_REFERER'];
	// Makes sure this is a
	// valid domain that can use the script.
	foreach ($domains as $value)
		if (strpos($referrer, $value) !== false) {
			$approved = true;
			break;
		}
}

if (!$redirect) {
	header('Content-Type: text/html; charset=utf-8');
	$output .= '<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Message result</title>
	<style>
	:root {
	  --text-for-light: black;
	  --bkg-for-light: white;
	  --link-for-light: blue;
	  --issues-for-light: red;
	  --text-for-dark: white;
	  --bkg-for-dark: black;
	  --link-for-dark: DeepSkyBlue;
	  --issues-for-dark: salmon;
	}
	body {color: var(--text-for-light); background-color: var(--bkg-for-light);}
	a {color: var(--link-for-light);}
	.error {color: var(--issues-for-light);}
	.stress {font-weight: bold;}
	.align {text-align: center;}
	' . (!isset($theme) ? '
	@media (prefers-color-scheme: dark) {
	' : '') . ((!isset($theme) || $theme == 'dark') ? '
	    body {color: var(--text-for-dark); background-color: var(--bkg-for-dark);}
	    a {color: var(--link-for-dark);}
	    .error {color: var(--issues-for-dark);}
	' : '') . (!isset($theme) ? '
	}
	' : '') . '
	@media only screen and (min-width: 768px) {
	  body {margin: 10% 15% 5% 20%;}
	}
	</style>
	</head>
	<body>
';
}

// Display error if the domain trying to access
// the script is not allowed to do so.
if (!isset($approved)) {
	if (!$redirect)
		$output .= '<span class="align stress error">
Ilegal Access! Your attempt was logged.</span>';
	else
		$output[] = 'Ilegal Access! Your attempt was logged.';
	if (!isset($error))
		$error = true;
	goexit();
}

if (isset($_REQUEST['email']))
	if (!empty($_REQUEST['email']))
		if (!fnmatch("??*@???*.???*", $_REQUEST['email'])) {
			$name = 'email';
			if (!$redirect)
				$output .= nl2br('<span class="stress">The Field <span class="error">' . $name . ' (' . $_REQUEST['email'] . ')</span> is Invalid.</span>');
			else
				$output[] = 'The Field <span class="error">' . $name . ' (' . $_REQUEST['email'] . ')</span> is Invalid.';
			if (!isset($error))
				$error = true;
		}

if (!isset($required))
	$required = array();

if (isset($_REQUEST['required'])) {
	$key = '';
	$required_temp = explode(",", $_REQUEST['required']);
	foreach ($required_temp as $value) {
		if (fnmatch("*\[*\]", $value))
			list($key, $value) = explode("[", substr($value, 0, -1));
		if (!in_array($value, $required)) {
			if (empty($key))
				array_push($required, $value);
		else
			$required[] = array($key => $value);
		}
	}
}

$error_temp = false;
foreach ($required as $value) {
	if ($value=="antispam")
		continue;
	$error_temp = true;
	if (is_array($value)) {
		foreach ($value as $key2 => $value2)
			if (isset($_REQUEST[$key2][$value2]))
				if (!empty($_REQUEST[$key2][$value2]))
					$error_temp = false;
					if ($error_temp)
						$name = $key2 . '[' . $value2 . ']';
	} else {
		if (isset($_REQUEST[$value]) && !empty($_REQUEST[$value]))
			$error_temp = false;
		if ($error_temp)
			$name = $value;
	}

	if ($error_temp) {
		if (isset($_REQUEST['errorpage'])) {
			header("Location: ". $_REQUEST['errorpage']);
			goexit();
		} else {
			if (!$redirect)
				$output .= nl2br('<span class="stress">Required Field <span class="error">' . $name . '</span> Left Blank.</span>');
			else
				$output[] = 'Required Field <span class="error">' . $name . '</span> Left Blank.';
			if (!isset($error))
				$error = true;
		}
	}
}

if (isset($antispam) && !empty($antispam) && (!isset($_REQUEST['antispam']) || $_REQUEST['antispam']<>$antispam)) {
	if (!$redirect)
		$output .= nl2br('<span class="error">Are you a spammer?</span> Please answer the spam test question correctly.');
	else
		$output[] = '<span class="error">Are you a spammer?</span> Please answer the spam test question correctly.';
	if (!isset($error))
		$error = true;
}

if (isset($error)) {
	if (!$redirect)
		$output .= '<p><span class="error">Please fix the above errors.</span></p>';
	else
		$output[] = '<span class="error">Please fix the above errors.</span>';
	finish();
}

if (isset($_REQUEST['subject']))
	if (!empty($_REQUEST['subject']))
		if ($_REQUEST['subject'] != ' ')
			$subject = $_REQUEST['subject'];
if (!isset($subject))
	$subject = '(no subject)';
$submitter = "<" . $_REQUEST['email'] . ">";
if (isset($_REQUEST['realname']))
		$submitter = '"' . $_REQUEST['realname'] . '" ' . $submitter;

$reply_to = '';

// Auto replying
if ((isset($autoreply) && $autoreply) || (isset($_REQUEST["autoreply"]) && $_REQUEST["autoreply"]))
	$autoreply_message = true;

if (isset($autoreply_message)) {
	$reply_to = "Reply-To: $submitter\r\n";
	$autoreply_message = "Thank you for contacting us.
This message has been automatically generated
in response to your e-mail regarding
\"" . $subject . "\"
from the page
". $referrer ."

Please do not reply.\n" . $thankyoumessage;

	$mail = mail($submitter, "Re: " . $subject, $autoreply_message,
"From: $siteName <$siteMail>\r\n");

	$autoreply_message = "sent";
	if (!$mail) $autoreply_message = "could not be " . $autoreply_message;
}

// Fixing potential problems before proceeding
if (isset($_REQUEST[$message_input]))
	$message = $_REQUEST[$message_input];
else
	$message = '';

if (!is_array($message)) $message = array($message_input => $message);

if (isset($_REQUEST['realname']))
	$message = array_merge(array('realname' => $_REQUEST['realname']), $message);

$message = array_merge(array('email' => $_REQUEST['email']), $message);

$message_temp = "";
$i = 0;
$linebreak = "\r\n\r\n";
foreach ($message as $key => $value) {
	$i += 1;
	if ($i == count($message)) $linebreak = "";
	if (!$value) $value = "None";
	$message_temp .= $key . ":" . ((strpos($value, PHP_EOL) !== false) ? "\r\n" : " ") . $value . $linebreak;
}
$message = $message_temp;

$message = "The following was submitted from\r\n"
. $referrer . " :\r\n\r\n" .
$message;

// Alerting about the auto replying status
if (isset($autoreply_message))
	$message .= "\r\n
Auto-Reply: " . $autoreply_message;

// Set the destination e-mail address
if (isset($_REQUEST['sendto']) && isset($sendresults) & $sendresults) {
	$sendto = explode(",", $_REQUEST['sendto']);

	foreach ($sendto as $value) {
		$receiver = $emailaddresses[intval($value)-1];
		$mail = mail($receiver, $subject, $message,
"From: $siteName <$siteMail>\r\n"
. $reply_to
."X-OS: " . getOS() . "\r\n"
."X-Originating-IP: " . getIP() . "\r\n");

		if (!$mail) {
			if (!$redirect)
				$output .= '<span class="error">Error Sending E-Mail. Please Try Again Later.</span>';
			else
				$output[] = '<span class="error">Error Sending E-Mail. Please Try Again Later.</span>';
			finish();
		}
	}
}

if (isset($_REQUEST['logs']) || isset($uselogs)) {
	if (isset($_REQUEST['logs']) && isset($uselogs))
		$logs = $_REQUEST['logs'] . ',' . $uselogs;
	elseif (isset($_REQUEST['logs']))
		$logs = $_REQUEST['logs'];
	else
		$logs = $uselogs;
	$logs = explode(",", $logs);
	$content = str_replace(array('T', '+'), array(' ', ' '), (new DateTime('now', new DateTimeZone('UTC')))->format('c')) . " Mail sent
Subject: " . $subject . "
X-OS: " . getOS() . "
X-Originating-IP: " . getIP() . "
Message: " . $message . PHP_EOL;
	foreach ($logs as $value) {
		$log = $archive[intval($value)-1];
		$file = fopen($log, "a");
		fwrite($file, $content);
		fclose($file);
	}
}

// If a success page is set, send the
// user to it via a header forward
if (isset($_REQUEST['successpage'])) {
	header("Location: " . $_REQUEST['successpage']);
	goexit();
} else {
// If no success page is specified,
// Output the thank you message
	if (!$redirect)
		$output .= $thankyoumessage;
	else
		$output[] = $thankyoumessage;
	finish(false);
}

function finish($failed = true) {
	global $output, $redirect;

	if ($failed) {
		$return_link_url = "javascript:history.back(1)";
		$return_link_title = "Go back";
	} else {
		if (isset($_REQUEST['return_link_url']))
			$return_link_url = $_REQUEST['return_link_url'];
		else
			$return_link_url = "javascript:window.close()";
		if (isset($_REQUEST['return_link_title']))
			$return_link_title = $_REQUEST['return_link_title'];
		else
			$return_link_title = "Close this window";
	}

	if (!$redirect)
		$output .= '
<p class="align">
<a href="' . $return_link_url . '">' . $return_link_title . '</a>
</p>
';
	elseif (!$failed)
		$output[] = '<a href="' . $return_link_url . '">' . $return_link_title . '</a>';
	goexit();
}

function goexit() {
	global $output, $redirect;
	exit($redirect ? json_encode(nl2br(implode(PHP_EOL, $output))) : ($output . '
</body>'));
}
?>
