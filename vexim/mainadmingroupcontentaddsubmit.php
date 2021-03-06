<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';

  # confirm that the user is updating a group they are permitted to change before going further  
  $query = "SELECT * FROM groups WHERE id=:group_id AND domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':group_id'=>$_POST['group_id'], ':domain_id'=>$_SESSION['local_domain_id']));
  if (!$sth->rowCount()) {
	  header ("Location: mainadmingroupchange.php?group_id={$_POST['group_id']}&group_failupdated={$_POST['localpart']}"); 
	  die();  
  }
  
  # validate user_id and group_id
  if (!isset($_POST['usertoadd']) or !isset($_POST['group_id'])) {
    header("Location: mainadmingroup.php?badname={$_POST['usertoadd']}");
    die;
  }
  $query = "INSERT INTO group_contents (group_id, member_id) VALUES (:group_id, :user_id)";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':group_id'=>$_POST['group_id'], ':user_id'=>$_POST['usertoadd']));
  if ($success) { 
    header ("Location: mainadmingroupchange.php?group_id={$_POST['group_id']}&group_updated={$_POST['localpart']}"); 
  } else { 
    header ("Location: mainadmingroupchange.php?group_id={$_POST['group_id']}&group_failupdated={$_POST['localpart']}"); 
	die;
  }
?>
