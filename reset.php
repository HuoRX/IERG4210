<?php
include_once('lib/auth.php');
// Connect to MySQL
    $db = new PDO('sqlite:/var/www/db/cart.db');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
// Was the form submitted?
if (isset($_POST["ResetPasswordForm"]))
{
	// Gather the post data
	$email = $_POST["email"];
	$password = $_POST["password"];
	$confirmpassword = $_POST["confirmpassword"];
	$hash = $_POST["q"];

  $query = $db->prepare('SELECT * FROM accounts WHERE email = ?');
	try {
	  $query->execute(array($email));
	} catch (Exception $e) {
	  echo "No user with that e-mail address exists.";
	}
	// Use the same salt from the forgot_password.php file
  if ($r=$query->fetch())
	{
		// Create a unique salt. This will never leave PHP unencrypted.
    $salt=$r['salt'];
		// Create the unique user password reset key
		$resetkey = sha1($salt.$email);
		// Create a url which we will direct them to reset their password
    // Does the new reset key match the old one?
    if ($resetkey == $hash)
    {
      if ($password == $confirmpassword)
      {
        //has and secure the password
        //$db=newDB();
    		$q=$db->prepare('SELECT * FROM accounts WHERE email = ?');
    		$q->execute(array($email));
    		if($r=$q->fetch()){
    			//expected format: $pw=sha1($salt.$plainPW);
    			$salt=mt_rand();
    			$saltPassword=sha1($salt.$password);
    			$q=$db->prepare('UPDATE accounts SET salt = ?, saltedpassword = ? WHERE email = ?');
    			$q->execute(array($salt,$saltPassword,$email));
    		}
        echo "Your password has been successfully reset.";
        header("Location:login.php", true, 302);
      }
      else
        echo "Your password's do not match.";
    }
    else
      echo "Your password reset key is invalid.";
	}
	else
		echo "No user with that e-mail address exists.";

}

?>
