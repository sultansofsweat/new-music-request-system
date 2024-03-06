<?php
	/* ORDER OF OPERATIONS
	-Require core
	-Open session
	-Open read-write connection to logging database
	-If not signed in, redirect to login page
	-Open read-only connection to system database
	-Get required settings
	-Close system database
	-Close logging database
	*/
	
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
	
	//Ancilliary page error handlers
	if(isset($_GET['status']))
	{
		switch($_GET['status'])
		{
			case 0:
			echo ("Successfully updated settings.<br>\r\n");
			break;
			
			default:
			echo ("Failed to update settings. Some wicked unidentifiable problem occurred and you should hit the server with a bug bomb. Or, maybe even a real bomb.<br>\r\n");
			break;
		}
	}
	
	set_timezone();
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Visited system ID settings page");
	
	$name=system_name();
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"name\"");
	
	$sysuid=get_system_setting("sysid");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"sysid\"");
	
	$idreq=get_system_setting("idreq");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"idreq\"");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="generator" content="CoffeeCup HTML Editor (www.coffeecup.com)">
    <meta name="created" content="Wed, 17 Jun 2015 12:33:52 GMT">
    <meta name="description" content="Listening to a live stream? Got a song you have to hear? This is the place to request it!">
	<link rel="shortcut icon" href="backend/favicon.ico">
    <title><?php echo $name; ?>Music Request System-Administration: System ID</title>
    
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
  </head>
  <body>
	<?php
		if(securitycheck() === false)
		{
			write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Not holding administrative privileges, exiting");
			die("<p>You are not an administrator. Please <a href=\"login.php?ref=admin-id\">sign in</a> or <a href=\"index.php\">cancel</a>.</p></body></html>");
		}
		if(!empty($_GET['regen']))
		{
			$bytes=bin2hex(random_bytes(32));
			$sysuid=hash("whirlpool",$bytes);
			write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Generated new system ID");
			trigger_error("Successfully generated new system UID.");
		}
		if(!empty($_POST['s']))
		{
			$newid="";
			if(!empty($_POST['sysid']))
			{
				$newid=$_POST['sysid'];
			}
			if(($idreq == 1 && $newid != "") || $idreq == 0)
			{
				$debug=save_system_setting("sysid",$newid);
				if($debug === true)
				{
					$sysuid=$newid;
					write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Set new system ID");
					trigger_error("Successfully set new system UID.");
				}
				else
				{
					write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to set new system ID");
					trigger_error("Failed to set new system UID, throw the MRS server out the second story window and try again.",E_USER_ERROR);
				}
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Cannot set system UID to blank as it is required");
				trigger_error("You cannot blank the UID as it is required by system security.",E_USER_ERROR);
			}
		}
	?>
	<h1 style="text-align:center; text-decoration:underline;"><?php echo $name; ?>Music Request System-Administration: System ID</h1>
	<p>This page allows you to set, modify, or delete, the system unique identifier, which is a string of letters and numbers used during the admin rights verification process to ensure you are logging in to the right MRS instance.</p>
	<p>It is strongly recommended you <a href="security.php">enable</a> this check and set the system UID to a sufficiently long value as a security measure.<br>
	Please note the system UID is <b>not</b> considered cryptographically secure (generated IDs theoretically are, but this <u>should not be relied on</u>), and is therefore <b>stored as-is</b> and can theoretically be retrieved by someone if they know where in the system it is stored!<br>
	Therefore, <b><u>do not enter a password here</u></b>!</p>
	<?php
		if($idreq == 1)
		{
			echo("<p><b>System UID is required by current security settings</b> and therefore cannot be blank.</p>");
		}
	?>
	<form action="admin-id.php" method="post">
	<input type="hidden" name="s" value="y">
	System ID: <input type="text" name="sysid" value="<?php echo $sysuid; ?>"<?php if($idreq == 1) { echo " required=\"required\""; } ?> size="100"><br>
	<input type="submit" value="Change system ID"><input type="reset">
	</form>
	<p><a href="admin-id.php?regen=y">Generate new ID</a> | <a href="admin-index.php">Abscond</a></p>
  </body>
</html>