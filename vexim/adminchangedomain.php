<?php
include_once dirname(__FILE__) . '/config/variables.php';
include_once dirname(__FILE__) . '/config/authpostmaster.php';
include_once dirname(__FILE__) . '/config/functions.php';
include_once dirname(__FILE__) . '/config/httpheaders.php';


if (isset($_REQUEST['domain']) && isset($_REQUEST['domainid'])) {
	# confirm that the postmaster is updating an alias they are permitted to change before going further  
	$query = "UPDATE users SET domain_id = :domain_id WHERE  user_id = :user_id";
	$sth = $dbh->prepare($query);
	$sth->execute(array(':domain_id' => $_REQUEST['domainid'], ':user_id' => $_SESSION['user_id']));
	echo $query;
	$_SESSION['domain_id'] = $_REQUEST['domainid'];
	$_SESSION['domain'] = $_REQUEST['domain'];

	header ("Location: admin.php");
}

?>