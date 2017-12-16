<?php
include_once('lib/csrf.php');
include_once('lib/auth.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Forget Password</title>
</head>
<body>
<h1>Forget Password</h1>
<fieldset>
	<legend>Forget Password</legend>
  <form action="change.php" method="POST">
  <label for="email">Email:</label>
  <div>
  <input type="text" name="email" required="true" pattern="^[\w=+\-\/][\w=\'+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$" />
  </div>
  <input type="submit" name="ForgotPassword" value=" Request Reset " />
  </form>

</fieldset>
</body>
</html>
