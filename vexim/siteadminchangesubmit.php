<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  if (isset($_POST['enabled'])) {
    $_POST['enabled'] = 1;
  } else {
    $_POST['enabled'] = 0;
  }
  
  // check if username available
  $SQL = "SELECT * FROM users WHERE localpart = '".$_POST['localpart']."'";
  $sth = $dbh->prepare($SQL);
  $sth->execute();
  if ($sth->rowCount() != 0) {
	$row = $sth->fetch();
	if ($row['user_id'] != $_POST['user_id']) {
		header ("Location: siteadmin.php?failupdated={$_POST['localpart']}");
		die();
	}
  }

  # Update the password, if the password was given
  if (validate_password($_POST['clear'], $_POST['vclear'])) {
	$cryptedpassword = crypt_password($_POST['clear']);
	$query = "UPDATE users
	  SET crypt=:crypt, clear=:clear
	  WHERE localpart=:localpart";
	$sth = $dbh->prepare($query);
	$success = $sth->execute(array(':crypt'=>$cryptedpassword, ':clear'=>$_POST['clear'],
		':localpart'=>$_POST['localpart']));
	if (!$success) {
	  header ("Location: siteadmin.php?failupdated={$_POST['localpart']}");
	  die;
	}
  } else if ($_POST['clear'] != $_POST['vclear']) {
	  header ("Location: siteadmin.php?badpass={$_POST['localpart']}");
	  die;
  }
  
  $query = "UPDATE users SET localpart=:localpart, realname=:realname, enabled=:enabled WHERE user_id=:user_id";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':realname'=>$_POST['localpart'],':enabled'=>$_POST['enabled'],':user_id'=>$_POST['user_id'],':localpart'=>$_POST['localpart']));
  if ($success) {
	header ("Location: siteadmin.php?updated={$_POST['localpart']}");
  } else {
	header ("Location: siteadmin.php?failupdated={$_POST['localpart']}");
  }

?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
