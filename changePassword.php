<?php
include_once('lib/csrf.php');
include_once('lib/auth.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Change Password</title>
</head>
<body>
<h1>Change Password</h1>

<fieldset>
	<legend>Change Password</legend>
	<form id="loginForm" method="POST" action="auth-process.php?action=<?php echo ($action = 'chgPass'); ?>">
		<label for="email">Email:</label>
		<div>
		<input type="text" name="email" required="true" pattern="^[\w=+\-\/][\w=\'+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$" />
		</div>
		<label for="pw">Original Password:</label>
		<div>
		<input type="password" name="pw" required="true" pattern="^[\w@#$%\^\&\*\-]+$" />
		</div>
		<label for="pw">New Password:</label>
		<div>
		<input type="password" name="npw" required="true" pattern="^[\w@#$%\^\&\*\-]+$" />
		</div>
		<input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
		<input type="submit" value="Change Password" />
	</form>
</fieldset>
</body>
</html>
