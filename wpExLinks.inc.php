<?php

if(!class_exists("wpExLinksPlugin")) {
	class wpExLinksPlugin {		
		
		var $adminOptionsName = "wpExLinksAdminOptions";
		function wpExLinksPlugin() {//constructor		
		}
		function init() {
			$this->getAdminOptions();
		}
		
		//Create table MySQL		
		function wpxlink_install() {		
			global $wpdb;
			$wpExLinks_version = "1.4";
			
			$table_name = $wpdb->prefix . "wpxlink";
			if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				$sql = "CREATE TABLE " . $table_name . " (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						ip VARCHAR(100) NOT NULL,
						time TIMESTAMP NOT NULL,
						name tinytext NOT NULL,
						email tinytext NOT NULL,
						web_title tinytext NOT NULL,
						web_url VARCHAR(55) NOT NULL,
						rec_url VARCHAR(55) NOT NULL,
						web_desc text NOT NULL,
						web_approve VARCHAR(15) NOT NULL,
						UNIQUE KEY id (id)
				);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			
			add_option("wpExLinks_version", $wpExLinks_version);
			}
		}
		
		//Returns an array of admin options
		function getAdminOptions() {
			$wpExLinksAdminOptions = array(
				'web_url' => ''.get_bloginfo('siteurl').'',
				'web_title' => ''.get_bloginfo('blogname').'', 
				'web_desc' => ''.get_bloginfo('description').'',
				'web_email' => ''.get_bloginfo('admin_email').'',
				'permalink_expage' => '',
				'enabled_captcha' => 'true',
				'web_limit_widget' => 0,
				'widget_exLinks_title' => 'Exchange Links',
				'widget_order_by' => 'name');
			$wpExLinksOptions = get_option($this->adminOptionsName);
			if (!empty($wpExLinksOptions)) {
				foreach ($wpExLinksOptions as $key => $option)
					$wpExLinksAdminOptions[$key] = $option;
			}				
			update_option($this->adminOptionsName, $wpExLinksAdminOptions);
			return $wpExLinksAdminOptions;
		}
		
		//Prints out the admin page
		function printAdminPage() {
			$wpExLinksOptions = $this->getAdminOptions();
								
			if (isset($_POST['update_wpExLinksPluginSettings'])) { 
				if (isset($_POST['web_url'])) {
					$wpExLinksOptions['web_url'] = $_POST['web_url'];
				}	
				if (isset($_POST['web_title'])) {
					$wpExLinksOptions['web_title'] = $_POST['web_title'];
				}	
				if (isset($_POST['web_desc'])) {
					$wpExLinksOptions['web_desc'] = $_POST['web_desc'];
				}
				if (isset($_POST['web_email'])) {
					$wpExLinksOptions['web_email'] = $_POST['web_email'];
				}
				if (isset($_POST['permalink_expage'])) {
					$wpExLinksOptions['permalink_expage'] = $_POST['permalink_expage'];
				}
				if (isset($_POST['enabled_captcha'])) {
					$wpExLinksOptions['enabled_captcha'] = $_POST['enabled_captcha'];
				}
				if (isset($_POST['web_limit_widget'])) {
					$wpExLinksOptions['web_limit_widget'] = $_POST['web_limit_widget'];
				}
				update_option($this->adminOptionsName, $wpExLinksOptions);
				
			?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "wpExLinksPlugin");?></strong></p></div>
			<?php
			} ?>
			
<div class="wrap">	
<?php
//Edit Links Page
if(isset($_GET['edit'])){
$id = $_GET['edit'];
	echo '<h2>Edit Link #'.$id.'</h2>';
	//Get links from DB into table
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$Links = $wpdb->get_row("
				SELECT * FROM $pf WHERE id='$id'
				", ARRAY_A);
	$name = stripslashes(htmlentities($Links['name']));
	$email = stripslashes(htmlentities($Links['email']));
	$webTitle = stripslashes(htmlentities($Links['web_title']));
	$webURL = stripslashes(htmlentities($Links['web_url']));
	$recURL = stripslashes(htmlentities($Links['rec_url']));
	$webDesc = stripslashes(htmlentities($Links['web_desc']));
	
echo '
<form name="post" action="'.get_bloginfo('siteurl').'/wp-admin/options-general.php?page=wpExLinks.php#managelinks" method="post" >
<div id="poststuff">
<div class="submitbox">
	<p class="submit">
	<input type="submit" name="save-edit" value="Save" tabindex="4" class="button button-highlighted" />
	Powered by <a href="http://blog.smileylover.com">WpExLinks</a>.
	</p>

</div>

<div id="post-body">
<input type="hidden" name="exID" id="exID" value="'.$id.'" />

<div id="namediv" class="stuffbox">
<h3><label for="name">Name</label></h3>
<div class="inside">
<input type="text" name="name" size="30" value="'.$name.'" tabindex="1" id="name" />
</div>
</div>

<div id="emaildiv" class="stuffbox">
<h3><label for="email">E-mail</label></h3>
<div class="inside">
<input type="text" name="email" size="30" value="'.$email.'" tabindex="2" id="email" />
</div>
</div>

<div id="namediv" class="stuffbox">
<h3><label for="web_title">Website Title</label></h3>
<div class="inside">
<input type="text" name="web_title" size="30" value="'.$webTitle.'" tabindex="3" id="web_title" />
</div>
</div>

<div id="uridiv" class="stuffbox">
<h3><label for="web_url">Website URL</label></h3>
<div class="inside">
<input type="text" id="web_url" name="web_url" size="30" value="'.$webURL.'" tabindex="4" />
</div>
</div>

<div id="uridiv" class="stuffbox">
<h3><label for="rec_url">Reciprocal URL</label></h3>
<div class="inside">
<input type="text" id="rec_url" name="rec_url" size="30" value="'.$recURL.'" tabindex="5" />
</div>
</div>

<div id="postdiv" class="stuffbox">
<h3><label for="content">Website Description</label></h3>
<div class="inside">
<textarea rows="5" cols="90" name="web_desc">'.$webDesc.'</textarea><br /><br />
<b>Don\'t use any HTML Tag!!</b>
</div>
</div>

</div>
</div>
</form>
';

//End Edit Page

} else {
//Check reciprocal URL
if(isset($_POST['checkrecurl'])){
$linkcheck = $_POST['linkcheck'];
$links = count($linkcheck);
echo '<br /><div class="updated"><p><ul>';
	for($i=0;$i<$links;$i++){
	//Get reciprocal URL
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$rec_url = $wpdb->get_var("
				SELECT rec_url FROM $pf WHERE id='$linkcheck[$i]'
				");
				
		$wpExLinksOptions = $this->getAdminOptions(); //Get data from database
		
		//Get HTML code of the reciprocal link URL 
		$html = @file_get_contents($rec_url);
		$html = strtolower($html);
		$site_url = strtolower($wpExLinksOptions['web_url']);
		
		//Check exsistence
		$found = 0;		
		if (preg_match_all('/<a\s[^>]*href=([\"\']??)([^" >]*?)\\1([^>]*)>/siU', $html, $matches, PREG_SET_ORDER)) {
		    foreach($matches as $match)
		    {
		        if ($match[2] == $wpExLinksOptions['web_url'] || $match[2] == $wpExLinksOptions['web_url'].'/' )
		        {
					$found = 1; 
		        } 
		    }
		}
		
		//If URL not found
		if (!$found == 1) {
		
			echo '<li><a href="'.$wpExLinksOptions['web_url'].'">'.$wpExLinksOptions['web_title'].'</a> <b>NOT</b> found in <a href="'.$rec_url.'">'.$rec_url.'</a> &nbsp;&nbsp;&nbsp;<img src="'.get_bloginfo('siteurl').'/wp-admin/images/no.png" border="0" width="15px"/></li>
			';
			
		} else {
		
			echo '<li><a href="'.$wpExLinksOptions['web_url'].'">'.$wpExLinksOptions['web_title'].'</a> found in <a href="'.$rec_url.'">'.$rec_url.'</a> &nbsp;&nbsp;&nbsp;<img src="'.get_bloginfo('siteurl').'/wp-admin/images/yes.png" border="0" width="15px"/></li>
			';			
		}
	}
echo '</ul><br/><br/>* <i>if reciprocal URL <b>not found</b>, you can delete it or email first to confirm webmaster of reciprocal URL </i> <b>(<a href="#managelinks"> Manage Links </a>)</b>.<br /><br /></p></div><br />';
}

//Insert new link command
if (isset($_POST['submitlink'])){
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	
//Define
$exname = $_POST['x_name'];
$exmail = $_POST['x_email'];
$exwebtitle = $_POST['web_title'];
$exweburl = $_POST['web_url'];
$exrecurl = $_POST['rec_url'];
$exwebdesc = $_POST['web_desc'];
$exIP = 'Admin IP';

//Safe string
$exname = $wpdb->escape(htmlentities($exname));
$exmail = $wpdb->escape(htmlentities($exmail));
$exwebtitle = $wpdb->escape(htmlentities($exwebtitle));
$exweburl = $wpdb->escape(htmlentities($exweburl));
$exrecurl = $wpdb->escape(htmlentities($exrecurl));
$exwebdesc = $wpdb->escape(htmlentities($exwebdesc));
$exIP = $wpdb->escape($exIP);
	
	$wpdb->query("
		INSERT INTO $pf (ip,time, name, email, web_title, web_url, rec_url, web_desc, web_approve)".
		" VALUES ('$exIP',current_timestamp, '$exname', '$exmail', '$exwebtitle', '$exweburl', '$exrecurl', '$exwebdesc',
		'Approve')
		");
	//Remove extra slash from escape
	$exname = stripslashes(stripslashes($exname));
	$exmail = stripslashes(stripslashes($exmail));
	$exwebtitle = stripslashes(stripslashes($exwebtitle));
	$exweburl = stripslashes(stripslashes($exweburl));
	$exrecurl = stripslashes(stripslashes($exrecurl));
	$exwebdesc = stripslashes(stripslashes($exwebdesc));
	
	echo '<br /><div class="updated"><p><strong>New link <a href="'.$exweburl.'">'.$exwebtitle.'</a> added ! (<a href="#managelinks">Manage Links</a>)</strong></p></div><br />';
}

//Delete command
if (isset($_POST['deleteit'])){
$linkcheck = $_POST['linkcheck'];
$links = count($linkcheck);
	for($i=0;$i<$links;$i++){
	//Delete links from DB
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$wpdb->query("
			DELETE FROM $pf WHERE id='$linkcheck[$i]'
			");
	}
	
	if($links == 0){
		echo '<br /><div class="updated"><p><strong>You\'re not delete anything!</strong></p></div><br />';
	} else {
		echo '<br /><div class="updated"><p><strong>'.$links.' Record Delete Successful</strong></p></div><br />';
	}
}

//Approve command
if(isset($_POST['approve'])){
$linkcheck = $_POST['linkcheck'];
$links = count($linkcheck);
	for($i=0;$i<$links;$i++){
	//Update approve status
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$wpdb->query("
			UPDATE $pf SET web_approve='Approve' WHERE id='$linkcheck[$i]'
			");
	}
	
	if($links == 0){
		echo '<br /><div class="updated"><p><strong>You\'re not approve anything!</strong></p></div><br />';
	} else {
		echo '<br /><div class="updated"><p><strong>'.$links.' Record Approve Successful</strong></p></div><br />';
	}	
}

//Unapprove command
if(isset($_POST['unapprove'])){
$linkcheck = $_POST['linkcheck'];
$links = count($linkcheck);
	for($i=0;$i<$links;$i++){
	//Update approve status
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$wpdb->query("
			UPDATE $pf SET web_approve='Unapprove' WHERE id='$linkcheck[$i]'
			") ;
	}
	
	if($links == 0){
		echo '<br /><div class="updated"><p><strong>You\'re not unapprove anything!</strong></p></div><br />';
	} else {
		echo '<br /><div class="updated"><p><strong>'.$links.' Record Unapprove Successful</strong></p></div><br />';
	}	
}

//Warning unapprove links
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$Links = $wpdb->get_var("
				SELECT COUNT(web_url) FROM $pf WHERE web_approve='Unapprove'
				");
	if (!$Links == 0){
		echo '<br /><div class="updated"><p><strong>You have '.$Links.' links to moderate ! (<a href="#managelinks">Manage Links</a>)</strong></p></div><br />';
	}
?>

<!-- General Setting  Admin Page-->
<h2>General Settings</h2><br />
<table id="WpExchangeLink">
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<tr>
<td>Website URL&nbsp;</td><td><input type="text" size="40" id="web_url" name="web_url" value="<?php _e(apply_filters('format_to_edit',$wpExLinksOptions['web_url']), 'wpExLinksPlugin') ?>"/></td>
</tr>
<tr>
<td>Website Title&nbsp;</td><td><input type="text" size="40" id="web_title" name="web_title" value="<?php _e(apply_filters('format_to_edit',$wpExLinksOptions['web_title']), 'wpExLinksPlugin') ?>"/></td>
</tr>
<tr>
<td>Website Description&nbsp;</td><td><input type="text" size="40" id="web_desc" name="web_desc" value="<?php _e(apply_filters('format_to_edit',$wpExLinksOptions['web_desc']), 'wpExLinksPlugin') ?>"/></td> 
</tr>
<tr>
<td>Email Address&nbsp;<br /><small>Use for sending notification email&nbsp;</small></td><td><input type="text" size="40" id="web_email" name="web_email" value="<?php _e(apply_filters('format_to_edit',$wpExLinksOptions['web_email']), 'wpExLinksPlugin') ?>"/></td> 
</tr>
<tr>
<td>Permalink of Exchange Links Page&nbsp;<br /><small>Use for link under widget "Submit your link!"&nbsp;</small></td><td><input type="text" size="40" id="permalink_expage" name="permalink_expage" value="<?php _e(apply_filters('format_to_edit',$wpExLinksOptions['permalink_expage']), 'wpExLinksPlugin') ?>"/></td> 
</tr>
<tr>
<td>Enabled Captcha&nbsp;<br /><small>Extra protection from Spammer"&nbsp;</small></td><td><input type="radio" id="captcha_false" name="enabled_captcha" value="false" <?php if($wpExLinksOptions['enabled_captcha'] == "false")  { _e('checked="checked"', "wpExLinksPlugin"); }?> /> No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="captcha_true" name="enabled_captcha" value="true" <?php if($wpExLinksOptions['enabled_captcha'] == "true")  { _e('checked="checked"', "wpExLinksPlugin"); }?> /> Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  - Please disable captcha if there's a compatibility issues</td> 
</tr>
</table>
<div class="submit">
<input type="submit" name="update_wpExLinksPluginSettings" value="<?php _e('Update Settings &raquo;', 'wpExLinksPlugin') ?>" /> <small>* Default retrieve from your Wordpress "<b>Settings - General</b>" !</small><br /><br />
</div><!-- /submit -->
</form>
<!-- End general setting admin page -->

	<br class="clear">

<!-- Manage Links Admin Page -->
<script type="text/javascript">
<!--
function checkAll(form)
{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].name == "linkcheck[]") {
				if(form.elements[i].checked == true)
					form.elements[i].checked = false;
				else
					form.elements[i].checked = true;
			}
		}
	}
}

function getNumChecked(form)
{
var num = 0;
for (i = 0, n = form.elements.length; i < n; i++) {
	if(form.elements[i].type == "checkbox") {
		if(form.elements[i].name == "linkcheck[]")
			if(form.elements[i].checked == true)
				num++;
	}
}
return num;
}
//-->
</script>
	
<h2 name="managelinks" id="managelinks">Manage Links (<a href="#addnew">Add New</a>)</h2>

<?php
//Update Entry After Edit
if(isset($_REQUEST['save-edit'])){

$id = $_POST['exID'];
$name = $_POST['name'];
$email = $_POST['email'];
$webTitle = $_POST['web_title'];
$webURL = $_POST['web_url'];
$recURL = $_POST['rec_url'];
$webDesc = $_POST['web_desc'];

	//Get links from DB into table
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$Links = $wpdb->query("
				UPDATE $pf SET name='$name', email='$email', web_title='$webTitle', web_url='$webURL', rec_url='$recURL', web_desc='$webDesc' WHERE id='$id'
				");

	echo '<br /><div class="updated"><p><strong>Link #'.$id.' Successfull Updated!</strong></p></div><br />';

}
//End Update Entry
?>

<form id="wpLinks" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">

	<br class="clear" />

<div class="tablenav">
	<div class="alignleft">
	<input type="submit" value="Delete" name="deleteit" class="button-secondary" onclick="var numchecked = getNumChecked(document.getElementById('wpLinks')); if(numchecked < 1) { alert(' No record selected \n Please use checkbox below'); return false } return confirm(' You are about to start delete for ' + numchecked + ' record \n \n \'Cancel\' to stop, \'OK\' to proceed.')"/>
	<input type="submit" value="Approve" name="approve" class="button-secondary" onclick="var numchecked = getNumChecked(document.getElementById('wpLinks')); if(numchecked < 1) { alert(' No record selected \n Please use checkbox below'); return false } return confirm(' You are about to Approve for ' + numchecked + ' record \n \n \'Cancel\' to stop, \'OK\' to proceed.')"/>
	<input type="submit" value="Unapprove" name="unapprove" class="button-secondary" onclick="var numchecked = getNumChecked(document.getElementById('wpLinks')); if(numchecked < 1) { alert(' No record selected \n Please use checkbox below'); return false } return confirm(' You are about to Unapprove for ' + numchecked + ' record \n \n \'Cancel\' to stop, \'OK\' to proceed.')"/>
	</div><!-- /alignleft -->
	
	<div class="alignright">
	<input type="submit" value="Check Reciprocal URL" name="checkrecurl" class="button-secondary" />
	</div><!-- /alignright -->
</div><!-- /tablenav -->

	<br class="clear" />

<table class="widefat">
<thead>
<tr>
	<th scope="col" class="check-column"><input type="checkbox" onclick="checkAll(document.getElementById('wpLinks'));" /></th>
	<th>ID</th><th style="width: 20%;">URL</th><th>Reciprocal URL</th><th style="text-align: center">Name / Email / IP</th><th style="text-align: center">Date</th><th style="text-align: center">Status</th>
</tr>
</thead>
<tbody>
	<?php
	//Get links from DB into table
	global $wpdb;
	$pf = $wpdb->prefix."wpxlink";
	$dbLinks = $wpdb->get_results("
				SELECT * FROM $pf ORDER BY id DESC
				");
				
		foreach ($dbLinks as $Links){
			echo '
<tr id="link-'.$Links->id.'" valign="middle"><th scope="row" class="check-column"><input type="checkbox" name="linkcheck[]" value="'.$Links->id.'" /></th><td>'.$Links->id.'</td><td><strong><a href="'.$Links->web_url.'">'.stripslashes(ucwords($Links->web_title)).'</a></strong><br /><i> '.stripslashes($Links->web_desc).'</i></td><td><a href="'.$Links->rec_url.'">'.$Links->rec_url.'</a></td><td>'.stripslashes($Links->name).'<br /><a href="mailto:'.$Links->email.'">'.$Links->email.'</a><br />'.$Links->ip.'</td><td style="text-align: center;">'.$Links->time.'</td><td style="text-align: center"><b>'.$Links->web_approve.'</b> | <a href="'.$_SERVER["REQUEST_URI"].'&edit='.$Links->id.'">Edit</a></td>
</tr>
			';
		}		
	?>
</tbody>
</form>
</table><br />
<!-- End manage links admin page-->

	<br class="clear" />
	
<!-- Add new link admin page -->
<h2 id="addnew" name="addnew">Add New Link</h2>
<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
<table class="form-table">
<tr class="form-field">
<th scope="row" valign="top"><label for="x_name">Name</label></th>
<td><input name="x_name" id="x_name" type="text" value="" size="40" /><br />
The name is used to identify the person who submitted link</td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="x_email">Email</label></th>
<td><input name="x_email" id="x_email" type="text" value="" size="40" /><br />
The email is used to identify the person who submited link</td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="web_title">Website Title</label></th>
<td><input name="web_title" id="web_title" type="text" value="" size="40" /><br />
The title is used to display URL name.</td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="web_url">Website URL</label></th>
<td><input name="web_url" id="web_url" type="text" value="http://" size="40" /><br />
Please add <i>http://</i> in your links !</td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="rec_url">URL Reciprocal</label></th>
<td><input name="rec_url" id="rec_url" type="text" value="http://" size="40" /><br />
URL where your link is placed.  Please add <i>http://</i> in your links !</td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="web_desc">Website Description</label></th>
<td><textarea name="web_desc" id="web_desc" rows="5" cols="50" style="width: 97%;"></textarea><br />
The description of submited website.</td>
</tr>
</table>
<p class="submit"><input type="submit" class="button" name="submitlink" value="Add Link" /></p>
</form>

	<br class="clear" /><br />
	
Powered by <a href="http://blog.smileylover.com/" target="_blank" title="Wordpress Exchange Links Plugin" rel="dofollow">wpExLinks</a>.
<!--  End add new link admin page -->

<?php } ?>

</div><!-- /wrap -->	
					
		<?php
		}//End function printAdminPage
		
		// CSS added to the header //
		function xcss() {
		echo '<link rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/wpExLinks/css/x_style.css" type="text/css" media="screen" />';		
		}
		
		function getPluginOptions($a){
			$wpExLinksOptions = $this->getAdminOptions(); //Get data from database
			
			switch ($a) {
				case 1:
					return $wpExLinksOptions['web_url'];
					break;
				case 2:
					return $wpExLinksOptions['web_title'];
					break;
				case 3:
					return $wpExLinksOptions['web_desc'];
					break;
				case 4:
					return $wpExLinksOptions['web_email'];
					break;
				case 5:
					return $wpExLinksOptions['enabled_captcha'];
					break;
			}
			
		}
		
		//Build Form Exchange Link Based On Pages
		function xreplacewords($content='' ) {
		$wpExLinksOptions = $this->getAdminOptions(); //Get data from database
		$ip = $_SERVER['REMOTE_ADDR']; // IP Poster

		/* Run the input check. */
		if(! preg_match('|<!--wpExLinks-->|', $content)) { // If there's no match...
		
			return $content; // ...Do nothing!		
			
		} else {// We have a match. Show the form.
		
		if($wpExLinksOptions['enabled_captcha'] == "true" ) {
		$captchaEx ='
<img id="imgCaptcha" src="'.get_bloginfo('wpurl').'/wp-content/plugins/wpExLinks/captcha.php" onclick="refreshimg(); return false;" alt="Click on me to change image - Wordpress Exchange Links Plugin"/>&nbsp;&nbsp;
<input id="txtCaptcha" type="text" name="txtCaptcha" value="" maxlength="10" size="10" autocomplete="off"/>
<br /><small><i>* Click on the AJAX CAPTCHA to refreshing image!</i></small>
<br /><br />
		';
		$txtCaptchaInput ='ajax.setVar("txtCaptcha", form.txtCaptcha.value);';
		$refImageCompl ='ajax.onCompletion = refreshimg;';
		}
			
		$form ='	
<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-includes/js/tw-sack.js"></script>
<script type="text/javascript" language="javascript">
var ajax = new sack();

function whenLoading(){
	var e = document.getElementById(\'replaceme\'); 
	e.innerHTML = "<img src=\"http://i37.tinypic.com/4gt6k0.jpg\" /><br /><div style=\"font-size:10px;color:green;\">Processing ...</div>";
}

function whenInteractive(){
	var e = document.getElementById(\'replaceme\'); 
	e.innerHTML = "<div style=\"font-size:10px;color:#800000;\">Return Data ...</div>";
}

// IMAGE REFRESHING
function refreshimg()
{
	//Get a reference to CAPTCHA image
	img = document.getElementById(\'imgCaptcha\'); 	
	img.src = \''.get_bloginfo('wpurl').'/wp-content/plugins/wpExLinks/captcha.php?\'+Math.random(); //Change the image
	document.getElementById(\'txtCaptcha\').value=\'\'; //Reset input Captcha  after succes return
}

function doit(){
	var form = document.getElementById(\'myform\');
	ajax.setVar("exname", form.exname.value); // recomended method of setting data to be parsed.
	ajax.setVar("exmail", form.exmail.value); 
	ajax.setVar("exwebtitle", form.exwebtitle.value); 
	ajax.setVar("exweburl", form.exweburl.value); 
	ajax.setVar("exrecurl", form.exrecurl.value); 
	ajax.setVar("exwebdesc", form.exwebdesc.value);
	'.$txtCaptchaInput.'
	ajax.requestFile = "'.get_bloginfo('wpurl') .'/wp-content/plugins/wpExLinks/process.php";
	ajax.method = "POST";
	ajax.element = \'replaceme\';
	ajax.onLoading = whenLoading;
	ajax.onInteractive = whenInteractive;
	'.$refImageCompl.'
	ajax.runAJAX();
}
</script>
<div id="ex-link">
<h3>Step 1: Add our link to your website</h3><br />
<p>Website URL : <b><a href="'.$wpExLinksOptions['web_url'].'">'.$wpExLinksOptions['web_url'].'</a></b></p>
<p>Website Title : <b>'.$wpExLinksOptions['web_title'].'</b></p>
<p>Website Description : <b> '.$wpExLinksOptions['web_desc'].'</b></p>
<br /><br />

<u>Use This Format</u><br />
<textarea style="width:428px;height:60px;border:3px double #ccc;padding:3px;margin:10px 0;color:#434343" onfocus="this.select()" readonly>
<a href="'.$wpExLinksOptions['web_url'].'" title="'.$wpExLinksOptions['web_desc'].'">'.$wpExLinksOptions['web_title'].'</a>
</textarea>

<br /><br />
<h3>Step 2: Submit your link</h3>
<form action="" name="myform" id="myform">
<p><input type="text" name="exname" id="exname" value="" size="40" maxlength="30"/>
<label for="exname">Your Name * <small>(max.30)</small></label></p>

<p><input type="text" name="exmail" id="exmail" value="" size="40" />
<label for="exmail">Your Email *</label></p>

<p><input type="text" name="exwebtitle" id="exwebtitle" value="" size="40" maxlength="50"/>
<label for="exwebtitle">Website Title * <small>(max.50)</small></label></p>

<p><input type="text" name="exweburl" id="exweburl" value="http://" size="40" />
<label for="exweburl">Website URL *</label></p>

<p><input type="text" name="exrecurl" id="exrecurl" value="http://" size="40" />
<label for="exrecurl">URL with Reciprocal Link *</label></p>

<p><textarea cols="40" rows="10" name="exwebdesc" id="exwebdesc" maxlength="200"></textarea>
<label for="exwebdesc">Website Description * <br /><small><b>HTML TAG not allowed!</b> (max.200)</small></label></p>

<input type="hidden" name="exIP" id="exIP" value="'.$ip.'" />

'.$captchaEx.'

<div class="xright">
<input class="submit" type="submit" name="submitXlink" id="submitXlink" value=" Submit Now! " style="width:100px;" onClick="doit(); return false;" onDblClick="doit(); return false;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input class="reset" type="reset" name="resetXlink" id="resetXlink" value=" Reset From! " style="width:100px;" />
</div>

</form>

<div style="clear:both; height:1px;">&nbsp;</div><br />

<!--Result-->
<div id="replaceme"></div>
<br />
<h5>* All fields are required</h5><br />
<small>Powered by <a href="http://blog.smileylover.com/" target="_blank" title="Wordpress Exchange Links Plugin" rel="dofollow">wpExLinks</a></small><br /><br />
</div><!-- //ex-link -->
<div style="clear:both; height:1px;">&nbsp;</div>
		';
		
				return str_replace('<!--wpExLinks-->', $form, $content);
		
			}
		}		
		
		//Prints out the admin page	
			
	}
	
} //End Class WpExLinks
?>