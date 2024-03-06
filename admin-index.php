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
	if(isset($_GET['slstatus']))
	{
		if($_GET['slstatus'] == 0)
		{
			echo ("Successfully changed song list.<br>\r\n");
		}
		elseif($_GET['slstatus'] == 1)
		{
			echo ("Failed to change song list. The list requires prompt microwaving.<br>\r\n");
		}
		else
		{
			echo ("Failed to change song list. Some wicked unidentifiable problem occurred and the whole system needs prompt microwaving.<br>\r\n");
		}
	}
	if(isset($_GET['blstatus']))
	{
		if($_GET['blstatus'] == 0)
		{
			echo ("Successfully changed base list.<br>\r\n");
		}
		elseif($_GET['blstatus'] == 1)
		{
			echo ("Failed to change base list. The list requires prompt microwaving.<br>\r\n");
		}
		else
		{
			echo ("Failed to change base list. Some wicked unidentifiable problem occurred and the whole system needs prompt microwaving.<br>\r\n");
		}
	}
	if(isset($_GET['pchange']))
	{
		if($_GET['pchange'] == 0)
		{
			echo ("Successfully changed password.<br>\r\n");
		}
		elseif($_GET['pchange'] == 1)
		{
			echo ("Failed to change password: user had ONE JOB and the old password given was incorrect.<br>\r\n");
		}
		elseif($_GET['pchange'] == 2)
		{
			echo ("Failed to change password: user had ONE JOB and the new password did not verify.<br>\r\n");
		}
		elseif($_GET['pchange'] == 3)
		{
			echo ("Failed to change password: the password file was dunked in a pool and couldn't be found or opened.<br>\r\n");
		}
		else
		{
			echo ("Failed to change password. Some wicked unidentifiable problem occurred and the whole system needs prompt microwaving.<br>\r\n");
		}
	}
	if(isset($_GET['rl']))
	{
		if($_GET['rl'] == 0)
		{
			echo ("Successfully changed rule list.<br>\r\n");
		}
		else
		{
			echo ("Failed to change rule list. Some wicked unidentifiable problem occurred and the whole system needs prompt microwaving.<br>\r\n");
		}
	}
	if(isset($_GET['autoset']))
	{
		if($_GET['autoset'] == "yes")
		{
			echo ("Successfully changed automation settings.<br>\r\n");
		}
		else
		{
			echo ("Failed to change automation settings. Throw a GPX clock radio at the server.<br>\r\n");
		}
	}
	if(isset($_GET['copyset']))
	{
		if($_GET['copyset'] == "yes")
		{
			echo ("Successfully changed copyright information.<br>\r\n");
		}
		else
		{
			echo ("Failed to change copyright information. It's time to hit the server with a bug bomb. Or, maybe even a real bomb.<br>\r\n");
		}
	}
	
	set_timezone();
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Visited administration main page");
	
	$name=system_name();
	write_log($_SERVER['REMOTE_ADDR'],date("g:i:s"),"Obtained setting \"name\"");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="generator" content="CoffeeCup HTML Editor (www.coffeecup.com)">
    <meta name="created" content="Wed, 17 Jun 2015 12:33:52 GMT">
    <meta name="description" content="Listening to a live stream? Got a song you have to hear? This is the place to request it!">
	<link rel="shortcut icon" href="backend/favicon.ico">
    <title><?php echo $name; ?>Music Request System-Administration</title>
    
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
			die("<p>You are not an administrator. Please <a href=\"login.php?ref=admin-index\">sign in</a> or <a href=\"index.php\">cancel</a>.</p></body></html>");
		}
	?>
	<h1 style="text-align:center; text-decoration:underline;"><?php echo $name; ?>Music Request System-Administration</h1>
	<p><b>NOTE: this new admin console is still UNDER CONSTRUCTION!</b><br>
	For anything not located here, please use the <a href="oldadmin.php">old administration page</a>.</p>
	<p><a href="index.php">Exit Admin Console</a></p>
	<p><a href="admin-dump.php">Show All System Settings</a><br>
	<!--<a href="admin-resprev.php">Reset <b>All</b> To Previous Values</a>--><span style="text-decoration:line-through;">Reset <b>All</b> To Previous Values</span><br>
	<!--<a href="admin-resdef.php">Reset <b>All</b> To System Defaults</a>--><span style="text-decoration:line-through;">Reset <b>All</b> To System Defaults</span></p>
	<p><a href="admin-id.php">System ID</a><br>
	<a href="admin-sys.php">System Settings</a><br>
	<a href="password.php">Change system password</a><?php if(password_verify("admin",get_system_password()) === true) { echo " <b>/!\ CONSIDER CHANGING YOUR PASSWORD!</b>"; } ?><br>
	<a href="security.php">Security Settings</a><br>
	<a href="copyright.php">Copyright Information</a><br>
	<a href="viewlog.php">System Log</a><br>
	<a href="viewatt.php">Login Attempts</a><br>
	<a href="viewerr.php">Error Log</a><br>
	<a href="viewdep.php">Deprecation Message Log</a><?php if(is_dep_log_blank() !== true) { echo " <b>/!\ NOT BLANK, CHECK AND REPORT MESSAGES!</b>"; } ?><br>
	<?php if(alt_ses_store() !== false) { echo("<a href=\"purgesess.php\">Clear Session Storage</a>"); } else { echo("<span style=\"text-decoration:line-through;\">Clear Session Storage</span> (not applicable with current settings)"); } ?></p>
	<p><!--<a href="admin-home.php">Homepage Options</a>--><span style="text-decoration:line-through;">Homepage Options</span></p>
	<p><!--<a href="admin-search.php">Song Search/Select Options</a>--><span style="text-decoration:line-through;">Song Search/Select Options</span></p>
	<p><!--<a href="admin-songs.php">Song Lists</a>--><span style="text-decoration:line-through;">Song Lists</span><br>
	<a href="listadd.php">Add Songs To Main List</a><br>
	<a href="listimport.php">Import Songs To Main List</a><br>
	<a href="listedit.php">Edit Songs On Main List</a><br>
	<a href="listadd2.php">Import External List</a><br>
	<a href="listedit2.php">Edit External List</a><br>
	<a href="listdel.php">Delete External List</a><br>
	<a href="listformat.php">Reformat ALL Lists</a></p>
	<p><!--<a href="admin-reqpost.php">Request Posting Options</a>--><span style="text-decoration:line-through;">Request Posting Options</span><br>
	<!--<a href="admin-reqres.php">Request Restrictions</a>--><span style="text-decoration:line-through;">Request Restrictions</span><br>
	<!--<a href="admin-reqpswd.php">Request Password</a>--><span style="text-decoration:line-through;">Request Password</span><br>
	<a href="ruledit.php">System Rules</a><br>
	<a href="autoopen.php">Automatic Open/Close Options</a><br>
	<a href="archive.php">Archive Requests</a><br>
	<a href="delall.php">Delete ALL Requests</a><br>
	<a href="microwave.php">The Microwave&trade;</a> (rebuild request database)<?php if(verify_request_db() !== true) { echo " <b>/!\ RECOMMENDED!</b>"; } ?></p>
	<p><!--<a href="admin-ban.php">Banhammer&trade; Options</a>--><span style="text-decoration:line-through;">Banhammer&trade; Options</span></p>
	<p><!--<a href="admin-api.php">System API Options</a>--><span style="text-decoration:line-through;">System API Options</span></p>
	<p><!--<a href="admin-upg.php">System Update Options</a>--><span style="text-decoration:line-through;">System Update Options</span><br>
	<a href="upgrade/index.php">System Updater</a></p>
	<p><a href="index.php">Exit Admin Console</a></p>
  </body>
</html>