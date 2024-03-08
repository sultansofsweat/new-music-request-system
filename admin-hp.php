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
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Visited system homepage settings page");
	
	$name=system_name();
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"name\"");
	
	$autorefresh=get_system_setting("autorefresh");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"autorefresh\"");
	
	$eroc=get_system_setting("eroc");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"eroc\"");
	
	$status=get_system_setting("status");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"status\"");
	
	$vcomments=get_system_setting("viewcomments");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"viewcomments\"");
	
	$pexpire=get_system_setting("postexpiry")/60/60;
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"postexpiry\"");
	
	$blanking=get_system_setting("blanking");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"blanking\"");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="generator" content="CoffeeCup HTML Editor (www.coffeecup.com)">
    <meta name="created" content="Wed, 17 Jun 2015 12:33:52 GMT">
    <meta name="description" content="Listening to a live stream? Got a song you have to hear? This is the place to request it!">
	<link rel="shortcut icon" href="backend/favicon.ico">
    <title><?php echo $name; ?>Music Request System-Administration: Homepage Settings</title>
    
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
			die("<p>You are not an administrator. Please <a href=\"login.php?ref=admin-sys\">sign in</a> or <a href=\"index.php\">cancel</a>.</p></body></html>");
		}
		if(!empty($_POST['s']))
		{
			if(isset($_POST['autorefresh']))
			{
				$autorefresh=preg_replace("/[^0-9]/","",$_POST['autorefresh']);
			}
			if(isset($_POST['eroc']))
			{
				if($_POST['eroc'] == "yes")
				{
					$eroc="yes";
				}
				else
				{
					$eroc="no";
				}
			}
			if(!empty($_POST['status']))
			{
				if($_POST['status'] == "yes")
				{
					$status="yes";
				}
				else
				{
					$status="no";
				}
			}
			if(!empty($_POST['vcomments']))
			{
				if($_POST['vcomments'] == "yes")
				{
					$vcomments="yes";
				}
				else
				{
					$vcomments="no";
				}
			}
			if(isset($_POST['pexpire']))
			{
				$pexpire=preg_replace("/[^0-9]/","",$_POST['pexpire']);
			}
			if(!empty($_POST['blanking']))
			{
				if($_POST['blanking'] == "yes")
				{
					$blanking="yes";
				}
				else
				{
					$blanking="no";
				}
			}
			
			$pexpire_sys=$pexpire*60*60;
			
			$error=false;
			
			$debug=save_system_setting("autorefresh",$autorefresh);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"autorefresh\" to \"$autorefresh\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"autorefresh\" to \"$autorefresh\"");
			}
			$debug=save_system_setting("eroc",$eroc);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"eroc\" to \"$eroc\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"eroc\" to \"$eroc\"");
			}
			$debug=save_system_setting("status",$status);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"status\" to \"$status\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"status\" to \"$status\"");
			}
			$debug=save_system_setting("viewcomments",$vcomments);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"viewcomments\" to \"$vcomments\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"viewcomments\" to \"$vcomments\"");
			}
			$debug=save_system_setting("postexpiry",$pexpire_sys);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"postexpiry\" to \"$pexpire_sys\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"postexpiry\" to \"$pexpire_sys\"");
			}
			$debug=save_system_setting("blanking",$blanking);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"blanking\" to \"$blanking\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"blanking\" to \"$blanking\"");
			}
			
			if($error === false)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Finished changing settings");
				trigger_error("Successfully changed settings.");
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"One or more settings failed to change");
				trigger_error("Failed to change one or more settings. You should hit the server with a bug bomb. Or, maybe even a real bomb.",E_USER_ERROR);
			}
		}
	?>
	<h1 style="text-align:center; text-decoration:underline;"><?php echo $name; ?>Music Request System-Administration: Homepage Settings</h1>
	<form action="admin-hp.php" method="post">
	<input type="hidden" name="s" value="y">
	Automatically refresh after: <input type="text" name="autorefresh" maxlength="4" size="4" value="<?php echo $autorefresh; ?>"> seconds (0 for never)<br>
	Always show existing requests: <input type="radio" name="eroc" value="yes"  <?php if ($eroc == "yes") { echo ("checked=\"checked\""); } ?>>Yes | <input type="radio" name="eroc" value="no"  <?php if ($eroc == "no") { echo ("checked=\"checked\""); } ?>>No<br>
	Display request status publicly: <input type="radio" name="status" value="yes"  <?php if ($status == "yes") { echo ("checked=\"checked\""); } ?>>Yes | <input type="radio" name="status" value="no"  <?php if ($status == "no") { echo ("checked=\"checked\""); } ?>>No<br>
	Display comments and responses publicly: <input type="radio" name="vcomments" value="yes"  <?php if ($vcomments == "yes") { echo ("checked=\"checked\""); } ?>>Yes | <input type="radio" name="vcomments" value="no"  <?php if ($vcomments == "no") { echo ("checked=\"checked\""); } ?>>No<br>
	Hide declined and played requests after: <input type="radio" name="pexpire" value="1" <?php if ($pexpire == "1") { echo ("checked=\"checked\""); } ?>>1 hour | <input type="radio" name="pexpire" value="3" <?php if ($pexpire == "3") { echo ("checked=\"checked\""); } ?>>3 hours | <input type="radio" name="pexpire" value="24" <?php if ($pexpire == "24") { echo ("checked=\"checked\""); } ?>>1 day<br>
	Distinguish open, queued and declined/played requests using: <input type="radio" name="blanking" value="yes" <?php if ($blanking == "yes") { echo ("checked=\"checked\""); } ?>>Opacity changes | <input type="radio" name="blanking" value="no" <?php if ($blanking == "no") { echo ("checked=\"checked\""); } ?>>Separators<br>
	<input type="submit" value="Change settings"><input type="reset">
	</form>
	<p><a href="admin-index.php">Abscond</a></p>
  </body>
</html>