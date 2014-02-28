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
  $domquery = "SELECT avscan,spamassassin,quotas,pipe FROM domains
    WHERE domain_id=:domain_id";
  $domsth = $dbh->prepare($domquery);
  $domsth->execute(array(':domain_id'=>$row['domain_id']));
  if ($domsth->rowCount()) {
    $domrow = $domsth->fetch();
  }
  $blockquery = "SELECT blockhdr,blockval,block_id FROM blocklists
    WHERE blocklists.user_id=:user_id";
  $blocksth = $dbh->prepare($blockquery);
  $blocksth->execute(array(':user_id'=>$_GET['user_id']));
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Administrator Verwaltung'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="scripts.js" type="text/javascript"></script>
  </head>
  <body onLoad="document.userchange.realname.focus()">
  <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="menu" style="width: 170px;">
      <a href="mainadminaccounts.php"><?php echo _('Administratoren verwalten'); ?></a><br>
      <a href="mainadmin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="forms" style="width: 650px;">
	<?php 
		# ensure this page can only be used to view/edit user accounts that already exist for the domain of the admin account
		if (!$sth->rowCount()) {			
			echo '<table align="center"><tr><td>';
			echo "Invalid userid '" . htmlentities($_GET['user_id']) . "' for domain '" . htmlentities($_SESSION['local_domain']). "'";			
			echo '</td></tr></table>';
		}else{
	?>
	
    <table align="center">
      <form name="userchange" method="post" action="mainadminaccountschangesubmit.php">
        <tr>
          <td><?php echo _('Name'); ?>:</td>
          <td>
            <input type="text" size="25" name="realname"
              value="<?php print $row['realname']; ?>" class="textfield">
          </td>
        </tr>
        <tr>
          <td><?php echo _('Email Address'); ?>:</td>
          <td><?php print $row['username']; ?></td>
        </tr>
        <input name="user_id" type="hidden"
          value="<?php print $_GET['user_id']; ?>" class="textfield">
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
               <td>
                 <?php printf (_('Mailbox quota (%s MB max)'),
                   $domrow['quotas']); ?>:</td>
                <td>
                  <input type="text" size="5" name="quota" class="textfield"
                    value="<?php echo ($domrow['quotas'] == 0 ? $row['quota'] : ($row['quota'] == 0 ? $domrow['quotas'] : min($domrow['quotas'], $row['quota']))); ?>">
                    <?php echo _('MB'); ?>
                </td>
              </tr>
          <?php
            //}
            if ((function_exists('imap_get_quotaroot'))
              && ($imap_to_check_quota == "yes")) {
              $mbox = imap_open(
                $imapquotaserver, $row['username'], $row['clear'], OP_HALFOPEN
              );
              $quota = imap_get_quotaroot($mbox, "INBOX");
              if (is_array($quota) && !empty($quota)) {
              printf ("<tr><td>"
                . _('Space used:')
                . "</td><td>"
                . _('%.2f MB')
                . "</td></tr>",
                $quota['STORAGE']['usage'] / 1024);
              }
              imap_close($mbox);
            }
          if ($domrow['pipe'] == "1") {
          ?>
          <tr>
            <td><?php echo _('Pipe to command or alternative Maildir'); ?>:</td>
            <td>
              <input type="textfield" size="25" name="smtp" class="textfield"
                value="<?php echo $row['smtp']; ?>">
            </td>
          </tr>
          <tr>
            <td colspan="2" style="padding-bottom:1em">
              <?php echo _('Optional'); ?>:
              <?php echo _('Pipe all mail to a command (e.g. procmail).'); ?>
              <br>
              <?php echo _('Check box below to enable'); ?>:
            </td>
          </tr>
          <tr>
            <td><?php _('Enable piped command or alternative Maildir?'); ?></td>
            <td>
              <input type="checkbox" name="on_piped"
              <?php
                if ($row['on_piped'] == "1") {
                  print " checked ";
                } ?>>
            </td>
          </tr>
        <?php
          }
        ?>
        <tr>
          <td>
            <?php echo _('Admin'); ?>:</td>
            <td>
			  <select name="admin">
				<option value="<?php echo $row['admin']; ?>"><?php ($row['admin'] == '1')? (print"lokaler Useradministrator"): (print "globaler Useradministrator"); ?></option>
				<option value="<?php echo $row['admin']; ?>">- - - - - - - - -</option>
				<option value="1">lokaler Useradministrator</option>
				<option value="3">globaler Useradministrator</option>
            </td>
          </tr>
        <?php
          if ($domrow['avscan'] == "1") {
        ?>
          <tr>
            <td><?php echo _('Anti-Virus'); ?>:</td>
            <td><input name="on_avscan" type="checkbox"
              <?php if ($row['on_avscan'] == "1") {
                print " checked";
              } ?>>
            </td>
          </tr>
        <?php
           }
           if ($domrow['spamassassin'] == "1") {
        ?>
            <tr>
              <td><?php echo _('Spamassassin'); ?>:</td>
              <td><input name="on_spamassassin" type="checkbox"
                <? if ($row['on_spamassassin'] == "1") {
                  print " checked";
                }?>>
              </td>
            </tr>
            <tr>
              <td><?php echo _('Spamassassin tag score'); ?>:</td>
              <td>
                <input type="text" size="5" name="sa_tag"
                  value="<?php echo $row['sa_tag']; ?>" class="textfield">
              </td>
            </tr>
            <tr>
              <td><?php echo _('Spamassassin refuse score'); ?>:</td>
              <td>
                <input type="text" size="5" name="sa_refuse"
                  value="<?php echo $row['sa_refuse']; ?>" class="textfield">
              </td>
            </tr>
          <?php
            }
          ?>
        <tr>
          <td><?php echo _('Maximum message size'); ?>:</td>
          <td>
            <input type="text" size="5" name="maxmsgsize"
              value="<?php echo $row['maxmsgsize']; ?>" class="textfield">Kb
          </td>
        </tr>
		<?php
		}
		?>
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
        <input name="localpart" type="hidden"
          value="<?php print $row['localpart']; ?>" class="textfield">
        <tr>
          <td colspan="2" class="button">
            <input name="submit" type="submit" value="Submit">
          </td>
        </tr>
      </form>
    </table>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
