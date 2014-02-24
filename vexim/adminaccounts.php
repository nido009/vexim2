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
		<a href="adminaccountsadd.php">Administrator hinzuf&uuml;gen</a>
		<a href="admin.php">Hauptmen&uuml;</a>
		<br /><br />
		<a href="logout.php">Abmelden</a>
	</div>
	<div id="Content">
		<table>
			<tr>
				<th>&nbsp;</th>
				<th>Benutzername</th>
				<th>Email-Adresse</th>
				<th>Rolle</th>
			</tr>
<?php
$SQL = "SELECT * FROM `users` WHERE (`admin` = 1 OR `admin` = 3) AND type = 'local'";
$sth = $dbh->prepare($SQL);
$sth->execute();
while ($row = $sth->fetch()) {
	echo "<tr>";
	echo "<td><a href=\"adminaccountsdelete.php?user_id=".$row['user_id']."&localpart=".$row['localpart']."\"><img class=\"trash\" title=\"".$row['localpart']."\" src=\"images/trashcan.gif\" alt=\"trashcan\"></a></td>";
	echo "<td><a href=\"adminaccountschange.php?user_id=".$row['user_id']."\">" . $row['localpart'] . "</a></td>";
	echo "<td><a href=\"adminaccountschange.php?user_id=".$row['user_id']."\">" . $row['username'] . "</a></td>";
	if ($row['admin'] == 1) {
		echo "<td>lokaler Administrator</td>";
	} else if ($row['admin'] == 3) {
		echo "<td>globaler Administrator</td>";
	}
	echo "</tr>";
}
?>
		</table>
	</div>
	</body>
</html>