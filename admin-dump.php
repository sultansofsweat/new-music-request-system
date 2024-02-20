<?php
	if(file_exists("backend/errorhandler.php"))
	{
		require_once("backend/errorhandler.php");
	}
	else
	{
		trigger_error("Failed to invoke system error handler. Expect information leakage.",E_USER_WARNING);
	}
	require_once("backend/functions.php");

	switch(get_system_setting("errlvl"))
	{
		case 0:
		error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);
		break;
		case 2:
		error_reporting(E_ALL);
		break;
		case 1:
		default:
		error_reporting(E_ALL & ~E_NOTICE);
		break;
	}
	
	$altsesstore=alt_ses_store();
	if($altsesstore !== false)
	{
		session_save_path($altsesstore);
	}
	session_start();
	
	if(file_exists("backend/securitycheck.php"))
	{
		require_once("backend/securitycheck.php");
	}
	else
	{
		die("Failed to open file \"backend/securitycheck.php\" in read mode. It should now be microwaved.");
	}
	
	set_timezone();
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Visited administration main page");
	
	$name=system_name();
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"name\"");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="generator" content="CoffeeCup HTML Editor (www.coffeecup.com)">
    <meta name="dcterms.created" content="Sun, 14 Jan 2018 04:33:28 GMT">
    <meta name="description" content="Listening to a live stream? Got a song you have to hear? This is the place to request it!">
	<link rel="shortcut icon" href="backend/favicon.ico">
    <title><?php echo $name; ?>Music Request System-Settings Dump</title>
	<script type="text/javascript" src="backend/jquery.js"></script>
	<script type="text/javascript" src="backend/tablesorter.js"></script>
	<link rel="stylesheet" href="backend/tsstyle/style.css" type="text/css" media="print, projection, screen" />
	<script type="text/javascript">
	$(function() {
		$("#settings").tablesorter();
	});
	</script>
    
    <style type="text/css">
    <!--
    body {
      color:#000000;
	  background-color:#FFFFFF;
      background-image:url('backend/background.gif');
      background-repeat:repeat;
    }
    a  { color:#FFFFFF; background-color:#0000FF; }
    a:visited { color:#FFFFFF; background-color:#800080; }
    a:hover { color:#000000; background-color:#00FF00; }
    a:active { color:#000000; background-color:#FF0000; }
    -->
    </style>
    
    <!--[if IE]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
	<?php
		if(securitycheck() === false)
		{
			write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Not holding administrative privileges, exiting");
			die("<p>You are not an administrator. Please <a href=\"login.php?ref=admin-dump\">sign in</a> or <a href=\"index.php\">cancel</a>.</p></body></html>");
		}
		
		$settings_to_get=get_system_default("RETURN_ALL");
		write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Got list of all system settings");
		$current=array();
		$default=array();
		foreach($settings_to_get as $setting)
		{
			$current[$setting]=get_system_setting($setting);
			write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained current value for setting \"$setting\"");
			$default[$setting]=get_system_default($setting);
			write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained default value for setting \"$setting\"");
		}
	?>
	<h1 style="text-align:center; text-decoration:underline;"><?php echo $name; ?> Music Request System: List Of All Settings</h1>
	<table id="settings" class="tablesorter">
	<thead>
	<tr>
	<th>Name</th>
	<th>Current</th>
	<th>Default</th>
	</tr>
	</thead>
	<tbody>
	<?php
		foreach($settings_to_get as $setting)
		{
			echo("<tr><td>" . $setting . "</td><td>" . $current[$setting] . "</td><td>" . $default[$setting] . "</td></tr>");
		}
	?>
	</tbody>
	</table>
	<p><a href="admin-index.php">Abscond</a>
  </body>
</html>