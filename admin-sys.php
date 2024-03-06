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
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Visited system general settings page");
	
	$name=system_name();
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"name\"");
	
	$message=stripcslashes(get_system_setting("sysmessage"));
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"sysmessage\"");
	
	$timezone=get_system_setting("timezone");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"timezone\"");
	
	$logging=get_system_setting("logging");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"logging\"");
	
	$errlvl=get_system_setting("errlvl");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"errlvl\"");
	
	$logerr=get_system_setting("logerr");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"logerr\"");
	
	$logatt=get_system_setting("logatt");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"logatt\"");
	
	$uas=get_system_setting("altsesstore");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"altsesstore\"");
	
	$asl=get_system_setting("altsesstorepath");
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"altsesstorepath\"");
	
	$timelimit=30;
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"timelimit\"");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="generator" content="CoffeeCup HTML Editor (www.coffeecup.com)">
    <meta name="created" content="Wed, 17 Jun 2015 12:33:52 GMT">
    <meta name="description" content="Listening to a live stream? Got a song you have to hear? This is the place to request it!">
	<link rel="shortcut icon" href="backend/favicon.ico">
    <title><?php echo $name; ?>Music Request System-Administration: General Settings</title>
    
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
			if(isset($_POST['name']))
			{
				$name=htmlspecialchars($_POST['name']);
			}
			if(isset($_POST['message']))
			{
				$message=htmlspecialchars($_POST['message']);
			}
			if(!empty($_POST['zone']))
			{
				switch($_POST['zone'])
				{
					case "America/Toronto":
					case "America/Winnipeg":
					case "America/Denver":
					case "America/Phoenix":
					case "America/Vancouver":
					$timezone=$_POST['zone'];
					break;
					default:
					$timezone="America/Toronto";
					trigger_error("Timezone submitted not understood, reverting to default.",E_USER_WARNING);
					break;
				}
			}
			if(!empty($_POST['logging']))
			{
				if($_POST['logging'] == "yes")
				{
					$logging="yes";
				}
				else
				{
					$logging="no";
				}
			}
			if(isset($_POST['errlvl']))
			{
				$errlvl=preg_replace("/[^0-2]/","",$_POST['errlvl']);
				if($errlvl == "")
				{
					$errlvl=get_system_setting("errlvl");
				}
			}
			if(!empty($_POST['logerr']))
			{
				if($_POST['logerr'] == "yes")
				{
					$logerr="yes";
				}
				else
				{
					$logerr="no";
				}
			}
			if(!empty($_POST['logatt']))
			{
				if($_POST['logatt'] == "yes")
				{
					$logatt="yes";
				}
				else
				{
					$logatt="no";
				}
			}
			if(!empty($_POST['uas']))
			{
				if($_POST['uas'] == "yes")
				{
					$uas="yes";
				}
				else
				{
					$uas="no";
				}
			}
			if(isset($_POST['asl']))
			{
				$asl=preg_replace("/[^A-Za-z0-9]/","",$_POST['asl']);
			}
			if(isset($_POST['timelimit']))
			{
				$timelimit=30;
			}
			
			if($uas == "yes")
			{
				if(empty($asl))
				{
					trigger_error("Alternate session storage path empty, turning feature off.",E_USER_WARNING);
					$uas="no";
					$asl="";
				}
				elseif(!file_exists($asl) || !is_dir($asl))
				{
					trigger_error("Alternate session storage path does not exist, turning feature off.",E_USER_WARNING);
					$uas="no";
					$asl="";
				}
				elseif(!is_writable($asl))
				{
					trigger_error("Alternate session storage path cannot be written to, turning feature off.",E_USER_WARNING);
					$uas="no";
					$asl="";
				}
			}
			
			$error=false;
			$uaserr=false;
			
			$debug=save_system_setting("name",$name);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"name\" to \"$name\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"name\" to \"$name\"");
			}
			$debug=save_system_setting("sysmessage",$message);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"sysmessage\" to \"$message\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"sysmessage\" to \"$message\"");
			}
			$debug=save_system_setting("timezone",$timezone);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"timezone\" to \"$timezone\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"timezone\" to \"$timezone\"");
			}
			$debug=save_system_setting("logging",$logging);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"logging\" to \"$logging\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"logging\" to \"$logging\"");
			}
			$debug=save_system_setting("errlvl",$errlvl);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"errlvl\" to \"$errlvl\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"errlvl\" to \"$errlvl\"");
			}
			$debug=save_system_setting("logerr",$logerr);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"logerr\" to \"$logerr\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"logerr\" to \"$logerr\"");
			}
			$debug=save_system_setting("logatt",$logatt);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"logatt\" to \"$logatt\"");
				$error=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"logatt\" to \"$logatt\"");
			}
			$debug=save_system_setting("altsesstore",$uas);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"altsesstore\" to \"$uas\"");
				$error=true;
				$uaserr=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"altsesstore\" to \"$uas\"");
			}
			$debug=save_system_setting("altsesstorepath",$asl);
			if($debug !== true)
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Failed to change setting \"altsesstorepath\" to \"$asl\"");
				$error=true;
				$uaserr=true;
			}
			else
			{
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Changed setting \"altsesstorepath\" to \"$asl\"");
			}
			
			if($uaserr === true)
			{
				save_system_setting("altsesstore","no");
				save_system_setting("altsesstorepath","");
				write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Reverted settings \"altsesstore\" and \"altsesstorepath\" due to previous error");
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
	<h1 style="text-align:center; text-decoration:underline;"><?php echo $name; ?>Music Request System-Administration: General Settings</h1>
	<form action="admin-sys.php" method="post">
	<input type="hidden" name="s" value="y">
	System name: <input type="text" name="name" size="50" value="<?php echo $name; ?>"><br>
	System message:<br>
	<textarea name="message" rows="5" cols="50"><?php echo $message; ?></textarea><br>
	Timezone: <input type="radio" name="zone" value="America/Toronto" <?php if ($timezone == "America/Toronto") { echo ("checked=\"checked\""); } ?>>Eastern | <input type="radio" name="zone" value="America/Winnipeg" <?php if ($timezone == "America/Winnipeg") { echo ("checked=\"checked\""); } ?>>Central | <input type="radio" name="zone" value="America/Denver" <?php if ($timezone == "America/Denver") { echo ("checked=\"checked\""); } ?>>Mountain | <input type="radio" name="zone" value="America/Phoenix" <?php if ($timezone == "America/Phoenix") { echo ("checked=\"checked\""); } ?>>Mountain (no DST) | <input type="radio" name="zone" value="America/Vancouver" <?php if ($timezone == "America/Vancouver") { echo ("checked=\"checked\""); } ?>>Pacific</p>
	<p>General system logging: <input type="radio" name="logging" value="yes" <?php if ($logging == "yes") { echo ("checked=\"checked\""); } ?>>Yes | <input type="radio" name="logging" value="no"  <?php if ($logging == "no") { echo ("checked=\"checked\""); } ?>>No<br>
	PHP error reporting level: <input type="radio" name="errlvl" value="0"<?php if(isset($errlvl) && $errlvl == 0) { echo " checked=\"checked\""; } ?>>Only errors | <input type="radio" name="errlvl" value="1"<?php if(isset($errlvl) && $errlvl == 1) { echo " checked=\"checked\""; } ?>>System messages only | <input type="radio" name="errlvl" value="2"<?php if(isset($errlvl) && $errlvl == 2) { echo " checked=\"checked\""; } ?>>All messages<br>
	Write all errors to a log file: <input type="radio" name="logerr" value="yes"  <?php if ($logerr == "yes") { echo ("checked=\"checked\""); } ?>>Yes | <input type="radio" name="logerr" value="no"  <?php if ($logerr == "no") { echo ("checked=\"checked\""); } ?>>No<br>  
	Log system login attempts: <input type="radio" name="logatt" value="yes" <?php if (isset($logatt) && $logatt == "yes") { echo ("checked=\"checked\""); } ?>>Yes | <input type="radio" name="logatt" value="no" <?php if (isset($logatt) && $logatt == "no") { echo ("checked=\"checked\""); } ?>>No</p>  
	<p>Use alternate session storage?: <input type="radio" name="uas" value="yes"  <?php if ($uas == "yes") { echo ("checked=\"checked\""); } ?>>Yes | <input type="radio" name="uas" value="no"  <?php if ($uas == "no") { echo ("checked=\"checked\""); } ?>>No<br>
	Alternate session storage location: <input type="text" name="asl" value="<?php if(isset($asl)) { echo $asl; } ?>"> (<b>MUST be sub-directory of MRS root; anything but letters and numbers will be deleted from submission!</b>)</p>
	<p>Limit script execution time to: <input type="text" name="timelimit" maxlength="3" size="3" value="<?php echo $timelimit; ?>" <?php if(!function_exists("set_time_limit")) { echo("disabled=\"disabled\""); } ?>> seconds <?php if(!function_exists("set_time_limit")) { echo("(disabled for security reasons)"); } else { echo("(enter 0 for no limit, but note that doing so is potentially VERY dangerous)"); } ?><br>
	<input type="submit" value="Change settings"><input type="reset">
	</form>
	<p><a href="admin-index.php">Abscond</a></p>
  </body>
</html>