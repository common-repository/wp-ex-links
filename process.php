<?php
//Captcha
session_start();

if (!function_exists('add_action'))
{
	require_once("../../../wp-config.php");
}

echo '<div style="color: rgb(153, 102, 102);background-color: rgb(255, 246, 191);border-bottom: 2px solid rgb(255, 211, 36);border-top: 2px solid rgb(255, 211, 36);padding:20px 10px;">';

$captcha = $wpExLinks_pluginSeries->getPluginOptions(5);

if($captcha == "true"){
$key = $_SESSION["1k2j48djh"];
$number = md5($_POST["txtCaptcha"]);
	if($number!=$key)
	{
		echo "Invalid Captcha! <br />Please fill again ... ";
		unset($_SESSION['1k2j48djh']);
		die;
	}
}

$exname = $_POST['exname'];
$exmail = $_POST['exmail'];
$exwebtitle = $_POST['exwebtitle'];
$exweburl = $_POST['exweburl'];
$exrecurl = $_POST['exrecurl'];
$exwebdesc = $_POST['exwebdesc'];
$exIP = $_POST['exIP'];
$d = date('l dS \of F Y h:i:s A'); //Timestamp
$exmyurl = $wpExLinks_pluginSeries->getPluginOptions(1); //Get setting URL from plugin options

if(get_magic_quotes_gpc())
{
$exname = stripslashes($exname);
$exmail = stripslashes($exmail);
$exwebtitle = stripslashes($exwebtitle);
$exweburl = stripslashes($exweburl);
$exrecurl = stripslashes($exrecurl);
$exwebdesc = stripslashes($exwebdesc);
} 

$exname = htmlentities($exname);
$exmail = htmlentities($exmail);
$exwebtitle = htmlentities($exwebtitle);
$exweburl = htmlentities($exweburl);
$exrecurl = htmlentities($exrecurl);
$exwebdesc = htmlentities($exwebdesc);

//Check if empty fields
if ($exname == "") {
	die('Please enter your name');
}
if ($exmail == "") {
	die('Please enter your email address');
}
	//Check valid email
	if (!preg_match('/([\w\-]+\@[\w\-]+\.[\w\-]+)/',$exmail))
	{
	    die ('Please enter a valid e-mail address!');
	}
if ($exwebtitle == "") {
	die('Please enter your website title');
}
if ($exweburl == "") {
	die('Please enter your website URL ');
}
	//Check valid URL
	if (!(preg_match('/(http:\/\/+[\w\-]+\.[\w\-]+)/i',$exweburl)))
	{
	    die ('Please enter valid (http://) URL of your website!');
	} 
if ($exrecurl == "") {
	die('Please enter your Reciprocal Link URL');
}
	//Check valid URL
	if (!(preg_match('/(http:\/\/+[\w\-]+\.[\w\-]+)/i',$exrecurl)))
	{
	    die ('Please enter valid (http://) URL of your reciprocal link!');
	} 
if ($exwebdesc == "") {
	die('Please enter your website description');
}

//Check duplicate link
global $wpdb;
$pf = $wpdb->prefix."wpxlink";
$duplink = $wpdb->get_results("SELECT web_url, rec_url FROM $pf");	
	foreach($duplink as $link){
	
		//Kill duplicate web url
		$parsed_url_1 = parse_url($link->web_url);
		$parsed_url_2 = parse_url($exweburl);
		if ($parsed_url_2['host'] == $parsed_url_1['host']) {
			die ('Please don\'t submit the same website/domain more than once or we will be forced to delete all your links!');
		}
		
		//Kill duplicate reciprocal url
		$parsed_rec_1 = parse_url($link->rec_url);
		$parsed_rec_2 = parse_url($exrecurl);
		if ($parsed_rec_2['host'] == $parsed_rec_1['host']) {
			die ('Please don\'t submit multiple websites/domain with the same reciprocal link URL or we will be forced to delete all your links!');
		}
	
	}

//Compare URL and Reciprocal page URL 
$parsed_url = parse_url($exweburl);
$parsed_rec = parse_url($exrecurl);
if ($parsed_url['host'] != $parsed_rec['host'])
{
   die('The reciprocal link must be placed under the same (sub)domain as your link is!');
}

//Get HTML code of the reciprocal link URL 
$html = @file_get_contents($exrecurl) or die('Can\'t open remote URL!');
$html = strtolower($html);

//Check exsistence
$found = 0;
if (preg_match_all('/<a\s[^>]*href=([\"\']??)([^" >]*?)\\1([^>]*)>/siU', $html, $matches, PREG_SET_ORDER)) {
    foreach($matches as $match)
    {
         if ($match[2] == $exmyurl || $match[2] == $exmyurl.'/')
        {
			$found = 1;    
        } 
    }
}

//If URL not found
if (!$found == 1) {
	die ('
		Our URL (<a href="'.$exmyurl.'">'.$exmyurl.
        '</a>) wasn\'t found on your reciprocal link page (<a href="'.$exrecurl.'">'.
        $exrecurl.'</a>)!<br><br>Please make sure you place this exact URL on your
        links page before submitting your link!
	');
}


//If URL found - hurray!
echo '
<p>Thank you '.$exname.'.</p>
<p>We will follow up your link (<a href="'.$exweburl.'">'.$exweburl.'</a>) in 48 hour.<br />
Your link will added to our blogroll if we approved.<br />
Please don\'t remove reciprocal link or your submited site will rejected.</p>
<small>Submited on '.$d.'</small>
<p><a href="'.$exmyurl.'">Back to the main page</a></p>
';

//Mail webmaster
$message='
<html>
<head>
<title>New link submitted at '.$wpExLinks_pluginSeries->getPluginOptions(2).'</title>
</head>
<body>
<style type="text/css">
*{
margin:0;
padding:0;
}
body{
text-align:center;
font: 11px Verdana;
color:#434343;
}
.exTab{
font-size:11px;
}
</style>
<center><br /><br />
<table width="600px" class="exTab" frame="below">
<tr bgcolor="#464646">
	<td><center>
	<br /><br /><font color="#ffffff" face="Verdana" size=1><h2>Wordpress Exchange Links Plugin</h2><br />
	Simple and Easy Exchange Links in Wordpress!!</font><br /><br /><br />
	</center></td>
</tr>

<tr>
	<td><br />
	Howdy,<br /><br />
	Someone just added a new link in <a href="'.$exmyurl.'">'.$wpExLinks_pluginSeries->getPluginOptions(2).'</a>.<br /><br />
	<u>Details</u> :<br /><br />
	</td>
</tr>

<tr>
	<td>
	<table class="exTab">
		<tr>
		<td valign="top"><b>Name</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td>'.$exname.'<br /><br /></td>
		</tr>

		<tr>
		<td valign="top"><b>Email</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><a href="mailto:'.$exmail.'">'.$exmail.'</a><br /><br /></td>
		</tr>

		<tr>
		<td valign="top"><b>Website</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><a href="'.$exweburl.'">'.$exwebtitle.'</a><br /><br />
'.$exwebdesc.'<br /><br /></td>
		</tr>

		<tr>
		<td valign="top"><b>Rec URL</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><a href="'.$exrecurl.'">'.$exrecurl.'</a><br /><br /></td>
		</tr>

		<tr>
		<td valign="top"><b>Log</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td>Submitted on '.$d.'<br />IP : '.$exIP.'<br /><br /></td>
		</tr>
	</table>
	</td>
</tr>

<tr>
	<td>
<i>Manage your links now</i> : <br /><br />
<a href="'.$wpExLinks_pluginSeries->getPluginOptions(1).'/wp-admin/options-general.php?page=wpExLinks.php#managelinks" >&raquo; wpExLinks Options Page &raquo; Manage Links.</a><br /><br />
	</td>
</tr>
</table>

<center><font size=1><br />
	Powered by <a href="http://blog.smileylover.com/" target="_blank" title="Wordpress Exchange Links Plugin" rel="dofollow">wpExLinks</a><br /><br /><b>
	<a href="http://smileylover.com/" title="Friendster Smiley, MySpace Smiley, Forum Smiley">SmileyLover</a> |
	<a href="http://mp3.smileylover.com/" title="Friendster MP3, Free MP3 Player Codes">Friendster MP3</a> |
	<a href="http://rapidcheck.co.nr/" title="Universal AJAX Link Checker">AJAX Link Checker</a> |
	<a href="http://imageremoter.co.nr/" title="Imageshack.Us Batch Remote Uploader">Image Remoter</a></b>
	</font></center>

<br /><br /></center></body>
</html>
';

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Wp Exchange Link'."\r\n";
    $headers .= 'Reply-To: '.$exname.' <'.$exmail.'>'."\r\n";
    $subject = 'New link submitted at '.$wpExLinks_pluginSeries->getPluginOptions(2).''."\r\n";
    @mail($wpExLinks_pluginSeries->getPluginOptions(4),$subject,$message,$headers);

//Insert into DB, ESCAPE first!
$exname = $wpdb->escape($exname);
$exmail = $wpdb->escape($exmail);
$exwebtitle = $wpdb->escape($exwebtitle);
$exweburl = $wpdb->escape($exweburl);
$exrecurl = $wpdb->escape($exrecurl);
$exwebdesc = $wpdb->escape($exwebdesc);
$exIP = $wpdb->escape($exIP);

$wpdb->query("
	INSERT INTO $pf (ip,time, name, email, web_title, web_url, rec_url, web_desc, web_approve)".
	" VALUES ('$exIP',current_timestamp, '$exname', '$exmail', '$exwebtitle', '$exweburl', '$exrecurl', '$exwebdesc', 'Unapprove')
	") or die (mysql_error());	
	
	echo '</div>';
?>
