<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  # confirm that the postmaster is updating an alias they are permitted to change before going further  
  $query = "SELECT localpart,realname,smtp,on_spamassassin,
    admin,enabled FROM users 
	WHERE user_id=:user_id AND domain_id=:domain_id AND type='alias'";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':user_id'=>$_POST['user_id'], ':domain_id'=>$_SESSION['local_domain_id']));
  if (!$sth->rowCount()) {
	  header ("Location: mainadminalias.php?failupdated={$_POST['localpart']}");
	  die();  
  }
  
  # Fix the boolean values
  $_POST['admin'] = 0;
  if (isset($_POST['enabled'])) {
    $_POST['enabled'] = 1;
  } else {
    $_POST['enabled'] = 0;
  }
  $query = "SELECT avscan,spamassassin from domains
    WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['local_domain_id']));
  $row = $sth->fetch();
  if ((isset($_POST['on_avscan'])) && ($row['avscan'] == 1)) {
    $_POST['on_avscan'] = 1;
  } else {
    $_POST['on_avscan'] = 0;
  }
  if ((isset($_POST['on_spamassassin'])) && ($row['spamassassin'] == 1)) {
    $_POST['on_spamassassin'] = 1;
  } else {
    $_POST['on_spamassassin'] = 0;
  }

  # Update the password, if the password was given
  if(isset($_POST['password']) && $_POST['password']!='' ){
	if (validate_password($_POST['password'], $_POST['vpassword'])) {
		$cryptedpassword = crypt_password($_POST['password']);
		$query = "UPDATE users SET crypt=:crypt, clear=:clear
          WHERE user_id=:user_id
		  AND domain_id=:domain_id AND type='alias'";
          $sth = $dbh->prepare($query);
          $success = $sth->execute(array(':crypt'=>$cryptedpassword, ':clear'=>$_POST['crypt'], ':user_id'=>$_POST['user_id'], ':domain_id'=>$_SESSION['local_domain_id']));
		if ($success) {
			if ($_POST['localpart'] == $_SESSION['localpart']) {
				$_SESSION['crypt'] = $cryptedpassword;
			}
		} else {
		  header ('Location: mainadminalias.php?failedupdated=' . $_POST['localpart']);
		  die();
		}
	} else {
		header ('Location: mainadminalias.php?badaliaspass');
		die();
	}
  }

  # update the actual alias in the users table
  $aliasto = preg_replace("/[', ']+/", ",", $_POST['target']);
  $query = "UPDATE users SET localpart=:localpart,
    username=:username, smtp=:smtp, pop=:pop,
    realname=:realname, admin=:admin, on_avscan=:on_avscan,
    on_spamassassin=:on_spamassassin, enabled=:enabled
    WHERE user_id=:user_id
	AND domain_id=:domain_id AND type='alias'";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(
    ':localpart'=>$_POST['localpart'],
    ':username'=>$_POST['localpart'].'@'.$_SESSION['local_domain'],
    ':smtp'=>$aliasto,
    ':pop'=>$aliasto,
    ':realname'=>$_POST['realname'],
    ':admin'=>$_POST['admin'],
    ':on_avscan'=>$_POST['on_avscan'],
    ':on_spamassassin'=>$_POST['on_spamassassin'],
    ':enabled'=>$_POST['enabled'],
    ':user_id'=>$_POST['user_id'],
    ':domain_id'=>$_SESSION['local_domain_id']
    ));
  if ($success) {
    header ("Location: mainadminalias.php?updated={$_POST['localpart']}");
  } else {
    header ("Location: mainadminalias.php?failupdated={$_POST['localpart']}");
  }
?>