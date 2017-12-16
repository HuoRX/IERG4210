<?php
// Connect to MySQL
if (empty($_POST['email'])
  || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['email']))
  throw new Exception('Wrong Credentials');

    $username = "username";
    $password = "password";
    $host = "localhost";
    $dbname = "databasename";
    $db = new PDO('sqlite:/var/www/db/cart.db');
  	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Was the form submitted?
if (isset($_POST["ForgotPassword"])) {

		$email = $_POST["email"];

	// Check to see if a user exists with this e-mail
	$query = $db->prepare('SELECT * FROM accounts WHERE email = ?');
	try {
	  $query->execute(array($email));
	} catch (Exception $e) {
	  echo "No user with that e-mail address exists.";
	}


	if ($r=$query->fetch())
	{
		// Create a unique salt. This will never leave PHP unencrypted.
    $salt=$r['salt'];
		// Create the unique user password reset key
		$password = sha1($salt.$email);
		// Create a url which we will direct them to reset their password
		$pwrurl = "https://secure.s8.ierg4210.ie.cuhk.edu.hk/reset_password.php?q=".$password;

		// Mail them their key
		$mailbody = "Dear user,\n\nIf this e-mail does not apply to you please ignore it. It appears that you have requested a password reset at our website secure.s8.ierg4210.ie.cuhk.edu.hk\n\nTo reset your password, please click the link below. If you cannot click it, please paste it into your web browser's address bar.\n\n" . $pwrurl . "\n\nThanks,\nThe Administration";
		mail($r['email'], "secure.s8.ierg4210.ie.cuhk.edu.hk - Password Reset", $mailbody);
		echo "Your password recovery key has been sent to your e-mail address.";

	}
	else
		echo "No user with that e-mail address exists.";
}
?>
