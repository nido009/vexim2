<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  $query = "UPDATE users SET localpart=:localpart, username=:username
    WHERE user_id=:user_id AND domain_id=:domain_id AND type='fail'";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':localpart'=>$_POST['localpart'],
    ':username'=>$_POST['localpart'].'@'.$_SESSION['local_domain'],
    ':user_id'=>$_POST['user_id'], ':domain_id'=>$_SESSION['local_domain_id']));
  if ($success) {
    header ("Location: mainadminfail.php?updated={$_POST['localpart']}");	
  } else {
    header ("Location: mainadminfail.php?failupdated={$_POST['localpart']}");
	die;
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
