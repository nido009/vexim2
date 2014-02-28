<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  $query = "SELECT * FROM users WHERE user_id=:user_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':user_id'=>$_GET['user_id']));
  if ($sth->rowCount()) { $row = $sth->fetch(); }
  
  $username = $row['username'];
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Administrator Verwaltung'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="scripts.js" type="text/javascript"></script>
  </head>
  <body onLoad="document.userchange.realname.focus()">
  <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="menu" style="width: 200px;">
      <a href="site.php"><?php echo _('Domains verwalten'); ?></a><br>
      <a href="siteadmin.php"><?php echo _('Hauptadministratoren verwalten'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="forms" style="width: 650px;">
	<?php 
		# ensure this page can only be used to view/edit user accounts that already exist for the domain of the admin account
		if (!$sth->rowCount()) {			
			echo '<table align="center"><tr><td>';
			echo "Invalid userid '" . htmlentities($_GET['user_id']) . "' for domain '" . htmlentities($_SESSION['domain']). "'";			
			echo '</td></tr></table>';
		}else{
	?>
	
    <table align="center">
      <form name="userchange" method="post" action="siteadminchangesubmit.php">
        <tr>
          <td><?php echo _('Name'); ?>:</td>
          <td>
            <input type="text" size="25" name="localpart"
              value="<?php print $row['localpart']; ?>" class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Password'); ?>:</td>
          <td>
            <input type="password" size="25" id="clear" name="clear" class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Verify Password'); ?>:</td>
          <td>
            <input type="password" size="25" id="vclear" name="vclear" class="textfield">
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <input type="button" value="<?php echo _('Generate password'); ?>" onclick="suggestPassword('suggest')">
            <input type="text" size="15" id="suggest" class="textfield">
            <input type="button" value="<?php echo _('Copy'); ?>" onclick="copyPassword('suggest', 'clear', 'vclear')">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Enabled'); ?>:</td>
          <td><input name="enabled" type="checkbox" <?php
            if ($row['enabled'] == 1) {
              print "checked";
            } ?> class="textfield">
          </td>
        </tr>
        <input name="user_id" type="hidden"
          value="<?php print $_GET['user_id']; ?>" class="textfield">
        <tr>
          <td colspan="2" class="button">
            <input name="submit" type="submit" value="Submit">
          </td>
        </tr>
      </form>
    </table>
	<?php
	}
	?>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
