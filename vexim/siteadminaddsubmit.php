<?php
  include_once dirname(__FILE__) . '/config/httpheaders.php';
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';

  # Strip off leading and trailing spaces
  $_POST['localpart'] = preg_replace("/^\s+/","",$_POST['realname']);
  $_POST['localpart'] = preg_replace("/\s+$/","",$_POST['realname']); 

  # Fix the boolean values
  if (isset($_POST['enabled'])) {
    $_POST['enabled'] = 1;
  } else {
    $_POST['enabled'] = 0;
  }
  check_user_exists(
    $dbh,$_POST['realname'],'hauptadmin','siteadmin.php'
  );
  $SQL = "SELECT * FROM users WHERE localpart = :localpart AND type = 'local'";
  $sthcheck = $dbh->prepare($SQL);
  $sthcheck->execute(array(':localpart' => $_POST['localpart']));
  if ($sthcheck->rowCount() != 0) {
	header ("Location: siteadmin.php?failadded={$_POST['localpart']}");
	die();
  }

  if (preg_match("/^\s*$/",$_POST['realname'])) {
    header('Location: siteadmin.php?blankname=yes');
    die;
  }

  if (preg_match("/['@%!\/\| ']/",$_POST['localpart'])
    || preg_match("/^\s*$/",$_POST['localpart'])) {
    header("Location: siteadmin.php?badname={$_POST['localpart']}");
    die;
  }

  if (validate_password($_POST['clear'], $_POST['vclear'])) {
    $query = "INSERT INTO users (localpart, username, domain_id, crypt, clear,
      smtp, pop, uid, gid, realname, type, admin, on_avscan, on_piped,
      on_spamassassin, sa_tag, sa_refuse, maxmsgsize, enabled, quota)
      VALUES (:localpart, :username, :domain_id, :crypt, :clear, :smtp, :pop,
      :uid, :gid, :realname, :type, :admin, :on_avscan, :on_piped, :on_spamassassin,
      :sa_tag, :sa_refuse, :maxmsgsize, :enabled, :quota)";
    $sth = $dbh->prepare($query);
    $success = $sth->execute(array(':localpart'=>$_POST['localpart'],
        ':localpart'=>$_POST['localpart'],
        ':username'=>'hauptadmin',
        ':domain_id'=>'1',
        ':crypt'=>crypt_password($_POST['clear'],$salt),
        ':clear'=>$_POST['clear'],
        ':smtp'=>'',
        ':pop'=>'',
        ':uid'=>'496',
        ':gid'=>'496',
        ':realname'=>$_POST['realname'],
        ':type'=>'local',
        ':admin'=>'2',
        ':on_avscan'=>'',
        ':on_piped'=>'',
        ':on_spamassassin'=>'',
        ':sa_tag'=>'',
        ':sa_refuse'=>'',
        ':maxmsgsize'=>'',
        ':enabled'=>$_POST['enabled'],
        ':quota'=>'',
        ));
    if ($success) {
      header ("Location: siteadmin.php?added={$_POST['localpart']}");
      die;
    } else {
      header ("Location: siteadmin.php?failadded={$_POST['localpart']}");
      die;
    }
  } else {
    header ("Location: siteadmin.php?badpass={$_POST['localpart']}");
    die;
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
