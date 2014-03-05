<?php
include_once dirname(__FILE__) . '/config/variables.php';
include_once dirname(__FILE__) . '/config/authpostmaster.php';
include_once dirname(__FILE__) . '/config/functions.php';
include_once dirname(__FILE__) . '/config/httpheaders.php';

$SQL = "SELECT * FROM users WHERE user_id = :userid";
$sth = $dbh->prepare($SQL);
$sth->execute(array(':user_id' => $_REQUEST['user_id']));
if (!$sth->rowCount()) {
  header ("Location: mainadminaccounts.php?faildeleted={$_GET['localpart']}");
  die();  
}
if(!isset($_GET['confirm'])) { $_GET['confirm'] = null; }

if ($_GET['confirm'] == '1') {
	$SQL = "SELECT * FROM users WHERE user_id = :user_id";
	$sth = $dbh->prepare($SQL);
	$sth->execute(array(':user_id' => $_REQUEST['user_id']));
	if (!$sth->rowCount()) {
		header ("Location: mainadminaccounts.php?faildeleted={$_GET['localpart']}");
		die;                                                      
	} else {
		$SQL = "DELETE FROM users WHERE user_id = :user_id";
		$sth = $dbh->prepare($SQL);
		$success = $sth->execute(array(':user_id' => $_REQUEST['user_id']));
		if ($success) {
			header ("Location: mainadminaccounts.php?deleted={$_GET['localpart']}");
			die;                                                      
		} else {
			header ("Location: mainadminaccounts.php?faildeleted={$_GET['localpart']}");
			die;                                                      
		}
	}
} else if ($_GET['confirm'] == "cancel") {                 
    header ("Location: mainadminaccounts.php?faildeleted={$_GET['localpart']}");
    die;                                                      
} else {
	$query = "SELECT localpart FROM users WHERE user_id=:user_id";
	$sth = $dbh->prepare($query);
	$sth->execute(array(':user_id'=>$_GET['user_id']));
	if ($sth->rowCount()) { $row2 = $sth->fetch(); }
}

?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Confirm Delete'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="menu">
      <a href="mainadminaccountsadd.php">Administrator hinzuf&uuml;gen</a><br>
      <a href="mainadmin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Content">
      <form name="admindelete" method="get" action="mainadminaccountsdelete.php">
        <table align="center">
          <tr>
            <td colspan="2">
              <?php printf (_('Please confirm deleting user %s@%s'),
                $row2['localpart'],
                $_SESSION['local_domain']);
              ?>:
            </td>
          </tr>
          <tr>
            <td>
              <input name="confirm" type="radio" value="cancel" checked>
              <b><?php printf (_('Do Not Delete %s@%s'),
                $row2['localpart'],
                $_SESSION['local_domain']);
              ?></b>
            </td>
          </tr>
          <tr>
            <td>
              <input name="confirm" type="radio" value="1">
              <b><?php printf (_('Delete %s@%s'),
                $row2['localpart'],
                $_SESSION['local_domain']);
              ?></b>
            </td>
          </tr>
          <tr>
            <td>
              <input name='domain' type='hidden'
                value='<?php echo $_SESSION['local_domain']; ?>'>
              <input name='user_id' type='hidden'
                value='<?php echo $_GET['user_id']; ?>'>
              <input name='localpart' type='hidden'
                value='<?php echo $_GET['localpart']; ?>'>
              <input name='submit' type='submit'
                value='<?php echo _('Continue'); ?>'>
            </td>
          </tr>
        </table>
      </form>
    </div>
  </body>
</html>
