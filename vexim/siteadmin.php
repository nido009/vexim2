<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
?>
<html>
	<head>
		<title><?php echo _('Virtual Exim'); ?></title>
		<link rel="stylesheet" href="style.css" type="text/css">
	</head>
	<body>
<?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Menu">
		<a href="site.php"><?php echo _('Manage Domains'); ?></a><br>
		<a href="siteadminadd.php">Administrator hinzuf&uuml;gen</a>
		<br /><br />
		<a href="logout.php">Abmelden</a>
	</div>
	<div id="Content">
		<table>
			<tr>
				<th>&nbsp;</th>
				<th>Benutzername</th>
				<th>Aktiviert</th>
			</tr>
<?php
$SQL = "SELECT * FROM `users` WHERE `admin` = 2 AND type = 'local'";
$sth = $dbh->prepare($SQL);
$sth->execute();
while ($row = $sth->fetch()) {
	echo "<tr>";
	echo "<td><a href=\"siteadmindelete.php?user_id=".$row['user_id']."&localpart=".$row['localpart']."\"><img class=\"trash\" title=\"".$row['localpart']."\" src=\"images/trashcan.gif\" alt=\"trashcan\"></a></td>";
	echo "<td><a href=\"siteadminchange.php?user_id=".$row['user_id']."\">" . $row['localpart'] . "</a></td>";
	if ($row['enabled'] == 1) {
		echo "<td class=\"check\"><img class=\"check\" src=\"images/check.gif\" title=\"Domain Admin is an administrator\"></td>";
	}
	echo "</tr>";
}
?>
		</table>
	</div>
	</body>
</html>