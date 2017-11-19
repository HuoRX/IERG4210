<?php
session_start();
//login related functions here
//Handle the account related session and cookies here
function newDB(){
	// connect to the database
	$db = new PDO('sqlite:/var/www/db/cart.db');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $db;
}
function loginProcess($email, $password){
	$db=newDB();
		$q=$db->prepare('SELECT * FROM accounts WHERE email = ?');
		$q->execute(array($email));
		if($r=$q->fetch()){
			//expected format: $pw=sha1($salt.$plainPW);
			$saltPassword=sha1($r['salt'].$password);
			if($saltPassword == $r['saltedpassword']){
				$exp = time() + 3600 * 24 * 3; // 3days
				$token = array( 'em'=>$r['email'], 'exp'=>$exp,'k'=>sha1($exp . $r['salt'] . $r['saltedpassword']));

        if($r['flag']==1){
          // create the cookie, make it HTTP only
  				setcookie('authtoken', json_encode($token), $exp,'','',true,true);
  				// put it also in the session
  				$_SESSION['authtoken'] = $token;
          $_SESSION['username']=$email;
          return 2;
        }
				else {
          // create the cookie, make it HTTP only
  				setcookie('nortoken', json_encode($token), $exp,'','',true,true);
  				// put it also in the session
  				$_SESSION['nortoken'] = $token;
          $_SESSION['username']=$email;
          return 3;
        }

			}
		return false;
		}
	return false;
}
function auth(){
	if(!empty($_SESSION['authtoken']))
		return $_SESSION['authtoken']['em'];
	if(!empty($_COOKIE['authtoken'])){
		//stripslashes() Returns a string with backslashes stripped off. // (\' becomes ' and so on.)
		if($t = json_decode(stripslashes($_COOKIE['authtoken']),true)){
			if (time() > $t['exp'])
				return false; // to expire the user
			$db=newDB();
			$q=$db->prepare('SELECT * FROM account WHERE email = ?');
			$q->execute(array($t['em']));
			if($r=$q->fetch()){
				//expected format: $pw=hash_hmac('sha1', $exp.$PW, $salt);
				$realk=hash_hmac('sha1', $t['exp'].$r['saltedpassword'], $r['salt']);
				if($realk == $t['k']){
					$_SESSION['authtoken'] = $t;
					return $t['em'];
				}
			}
		}
	}
	return false;
}
?>
