<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authsite.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Domains'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.adminadd.realname.focus()">
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id='menu' style="width: 200px;">
      <a href="site.php"><?php echo _('Manage Domains'); ?></a><br>
      <a href="siteadmin.php"><?php echo _('Hauptadministratoren verwalten'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="forms" style="width: 700px;">
    <form name="adminadd" method="post" action="siteadminaddsubmit.php">
      <table align="center">
        <tr>
          <td><?php echo _('Name'); ?>:</td>
          <td colspan="2">
            <input type="textfield" size="25" name="realname" class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Password'); ?>:</td>
          <td colspan="2">
            <input type="password" size="25" id="clear" name="clear" class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Verify Password'); ?>:</td>
          <td colspan="2">
            <input type="password" size="25" id="vclear" name="vclear" class="textfield">
          </td>
        </tr>
        <tr>
          <td></td>
          <td colspan="2">
            <input type="button" value="<?php echo _('Generate password'); ?>" onclick="suggestPassword('suggest')">
            <input type="text" size="15" id="suggest" class="textfield">
            <input type="button" value="<?php echo _('Copy'); ?>" onclick="copyPassword('suggest', 'clear', 'vclear')">
          </td>
        </tr>
		<script>

var pwd = "";
function suggestPassword() {
	var str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!?=&$%#+*";
	for (var i = 0; i < 18; i++) {
		var rnum = Math.floor(Math.random() * str.length);
		pwd += str.substring(rnum,rnum+1);
	}
	
	$('#suggest').val(pwd);
}
function copyPassword() {
	$('#clear').val(pwd);
	$('#vclear').val(pwd);
}

		</script>
        <tr>
          <td><?php echo _('Enabled'); ?>:</td>
          <td colspan="2"><input name="enabled" type="checkbox" checked></td>
        </tr>
        <tr>
          <td colspan="3" class="button">
          <input name="submit" type="submit" value="<?php echo _('Submit'); ?>">
          </td>
        </tr>
      </table>
    </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
