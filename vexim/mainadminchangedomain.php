<?php
include_once dirname(__FILE__) . '/config/variables.php';
include_once dirname(__FILE__) . '/config/authpostmaster.php';
include_once dirname(__FILE__) . '/config/functions.php';
include_once dirname(__FILE__) . '/config/httpheaders.php';

if (isset($_REQUEST['domain']) && isset($_REQUEST['domainid'])) {
	$_SESSION['local_domain_id'] = $_REQUEST['domainid'];
	$_SESSION['local_domain'] = $_REQUEST['domain'];

	header ("Location: mainadmin.php");
}

?>