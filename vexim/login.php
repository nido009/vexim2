<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

	# first check if we have sufficient post variables to achieve a successful login... if not the login fails immediately
	if (!isset($_POST['crypt']) || $_POST['crypt']==''
		|| !isset($_POST['localpart']) || $_POST['localpart']==''
		|| !isset($_POST['domain'])
	){
    header ('Location: index.php?login=failed');
    die;
  }

	# construct the correct sql statement based on who the user is
  if ($_POST['localpart'] == 'siteadmin') {
		$query = "SELECT crypt,localpart,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
      WHERE localpart='siteadmin'
      AND domain='admin'
      AND username='siteadmin'
      AND users.domain_id = domains.domain_id";
  } else if ($AllowUserLogin) {
		$query = "SELECT crypt,localpart,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
      WHERE (localpart=:localpart AND clear=:clear
      AND users.domain_id = domains.domain_id
      AND domains.domain=:domain) OR ((users.admin = 2 OR users.admin = 3) AND users.domain_id = domains.domain_id AND clear=:clear AND localpart=:localpart);";
  } else {
		$query = "SELECT crypt,localpart,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
      WHERE localpart=:localpart
      AND users.domain_id = domains.domain_id
      AND domains.domain=:domain
      AND (admin=1 OR admin = 2 OR admin = 3);";
  }
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':localpart'=>$_POST['localpart'], ':domain'=>$_POST['domain'], ':clear'=>$_POST['crypt']));
  if(!$success) {
    print_r($sth->errorInfo());
    die();
  }
  if ($sth->rowCount()!=1) {
    header ('Location: index.php?login=failed');
    die();
  }

  $row = $sth->fetch();
	
  $cryptedpass = crypt_password($_POST['crypt'], $row['crypt']);

//  Some debugging prints. They help when you don't know why auth is failing.
  /*
  print $query. "<br>\n";;
  print $row['localpart']. "<br>\n";
  print $_POST['localpart'] . "<br>\n";
  print $_POST['domain'] . "<br>\n";
  print "Posted crypt: " .$_POST['crypt'] . "<br>\n";
  print $row['crypt'] . "<br>\n";
  print $cryptscheme . "<br>\n";
  print $cryptedpass . "<br>\n";
  */

	# if they have the wrong password bail out
	if ($cryptedpass != $row['crypt']) {
		header ('Location: index.php?login=failed');
		die();
	}

	# populate session variables from what was retrieved from the database (NOT what they posted)
    $_SESSION['localpart'] = $row['localpart'];
	$_SESSION['crypt'] = $row['crypt'];
    $_SESSION['user_id'] = $row['user_id'];
	$_SESSION['admin'] = $row['admin'];
	if (!empty($_REQUEST['domain']) > 0) {
		$sth = $dbh->prepare("SELECT domain_id, domain FROM domains WHERE domain = :domain");
		$sth->execute(array(':domain' => $_REQUEST['domain']));
		$row2 = $sth->fetch();
		$_SESSION['domain'] = $row2['domain'];
		$_SESSION['domain_id'] = $row2['domain_id'];
		$sth = $dbh->prepare("UPDATE users SET domain_id = :domain_id WHERE user_id = :user_id");
		$sth->execute(array(':domain_id' => $row2['domain_id'], ':user_id' => $_SESSION['user_id']));
	} else {
		$_SESSION['domain'] = $row['domain'];
		$_SESSION['domain_id'] = $row['domain_id'];
		$sth = $dbh->prepare("UPDATE users SET domain_id = :domain_id WHERE user_id = :user_id");
		$sth->execute(array(':domain_id' => $row['domain_id'], ':user_id' => $_SESSION['user_id']));
	}

	# redirect the user to the correct starting page
	if (($row['admin'] == '1') && ($row['type'] == 'site')) {
		header ('Location: site.php');
		die();
	} 
	if ($row['admin'] == '1' || $row['admin'] == 3) {
		header ('Location: admin.php');
		die();
    }
	if ($row['admin'] == 2) {
		header ('Location: mainadmin.php');
		die();
    }
	if (($row['domainenabled'] == '0')) {
		header ('Location: index.php?domaindisabled');
		die();
}
	
	# must be a user, send them to edit their own details
	header ('Location: userchange.php');
	
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
