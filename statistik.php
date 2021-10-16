<!DOCTYPE html>
<html>
	<head>
		<style>
		body {
		background-color: #ffffff;
		}
		table {
		box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.3), 0 6px 20px 0 rgba(0, 0, 0, 0.3);
		table-layout:fixed;
		border: 1px solid #1c87c9;
		border-radius: 5px;
		border-width: 1px;
		border-color: #6496C8;
		border-collapse: collapse;
		color: #000000;
		font-family: Times New Roman;
		font-size: 17px;
		margin-left: auto;
		margin-right: auto;

		}
		th {
		width: 5%;
		background-color: #6496C8;
		color: white;
		}

		tr:nth-child(even) {
		background-color: #F2F2F2
		}
		.button {
			border: none;
			padding: 7px 20px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
		}
		</style>
	</head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<body>
		<nav>


		<div align="center">
		<form method="post">
		<input type="submit" name="overview" class="button" value="Översikt">
		<input type="submit" name="today" class="button" value="Idag">
		<input type="submit" name="last_week" class="button" value="Senaste 7 dagar">
		<input type="submit" name="last_month" class="button" value="Senaste 30 dagar">
		<input type="submit" name="avg_time" class="button" value="Genomsnittlig stopptid">

		<input type="hidden" name="inc" value="0" />
		<input type="checkbox" name="inc" value="1">
		<label for="inc"> Inkludera varning</label>

		<br>
		<br>

		Från:
		<input type="date" name="dateFrom" value="<?php echo date('Y-m-d'); ?>">

		Till:
		<input type="date" name="dateTo" value="<?php echo date('Y-m-d'); ?>">

		<input type="submit" name="search_date" class="button" value="Sök">

		<input type="text" id="search" align="right" name="search_form" placeholder="Sök efter larm">

		<input type="submit" name="Search" class="button" value="Sök">
		</div>
		</form>
		
		</nav>

SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end)) / 60 AS Tid,
	COUNT(message_text) As Antal,
	message_text As Larm
    FROM alarm_tid WHERE message_text LIKE '%Stn 1%'
UNION
SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end)) / 60 AS Tid,
	COUNT(message_text) AS Antal,
	message_text AS Larm
    FROM alarm_tid WHERE message_text LIKE '%Stn 2%';

	<?php
	$sql_temp = "SELECT
			(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
			COUNT(message_text) AS Antal,
			message_text AS Larm
			FROM alarm_tid ";

	$sql_order = "GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
			ORDER BY Tid DESC;";


	$senaste = "WHERE DATE(message_start) >= current_date - INTERVAL ";
	$exclude_varning = "AND message_text NOT LIKE '%Varning%' AND message_text NOT LIKE '%Fett tryck under inställt Min värde%' ";






	$search = False;
	$avg_time = False;
	if (isset($_POST['today'])) {
	echo "<p align='center'>Idag</p>";


	if ($_POST['inc'] == '1') {
	$sql = $sql_temp . "WHERE DATE(message_start) = CURRENT_DATE() " . $sql_order;
	} else {
	$sql = $sql_temp . "WHERE DATE(message_start) = CURRENT_DATE() " . $exclude_varning . $sql_order;
	}
	Fetch_data($sql, $search, $avg_time);

	} else if (isset($_POST['last_week'])) {
	echo "<p align='center'>Senaste 7 dagar</p>";


	if ($_POST['inc'] == '1') {
	$sql = $sql_temp . $senaste . "7 DAY " . $sql_order;
	} else {
	$sql = $sql_temp . $senaste . "7 DAY " . $exclude_varning . $sql_order;
	}


	Fetch_data($sql, $search, $avg_time);




	} else if (isset($_POST['last_month'])) {
	echo "<p align='center'>Senaste 30 dagar</p>";
	if ($_POST['inc'] == '1') {
	$sql = $sql_temp . $senaste . "30 DAY " . $sql_order;
	} else {
	$sql = $sql_temp . $senaste . "30 DAY " . $exclude_varning . $sql_order;
	}




	Fetch_data($sql, $search, $avg_time);




	} else if (isset($_POST['avg_time'])) {
	echo "<p align='center'>Genomsnittlig stopptid per larm</p>";


		if ($_POST['inc'] == '1') {
			$sql = "SELECT
				(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV COUNT(message_text) AS Avg,
				message_text AS Larm
				FROM alarm_tid
				GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
				ORDER BY Avg DESC;";
		} else {
			$sql = "SELECT
				(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV COUNT(message_text) AS Avg,
				message_text AS Larm
				FROM alarm_tid WHERE message_text NOT LIKE '%Varning%'
				GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
				ORDER BY Avg DESC;";
	}




	$avg_time = True;
	Fetch_data($sql, $search, $avg_time);




	} else if (isset($_POST['search_date'])) {
	$from = date('Y-m-d', strtotime($_POST['dateFrom']));
	$to = date('Y-m-d', strtotime($_POST['dateTo']));


	if ($_POST['inc'] == '1') {
	$sql = $sql_temp . "WHERE DATE(message_start) BETWEEN '".$from."' AND '".$to."' 
	GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
	ORDER BY Tid DESC;";
	} else {
	$sql = $sql_temp . "WHERE message_text NOT LIKE '%Varning%' AND DATE(message_start) BETWEEN '".$from."' AND '".$to."' 
	GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
	ORDER BY Tid DESC;";
	}


	echo "<p align='center'>Från: $from  Till:  $to </p>";
	Fetch_data($sql, $search, $avg_time);




	} else if (isset($_POST['Search'])) {
	$search_larm = ltrim(filter_input(INPUT_POST, 'search_form'));
	echo "<p align='center'>" . $search_larm . "</p>";
	$sql = "SELECT
	YEAR(message_start) As Year,
	WEEK(message_start, 1) As Vecka,
	(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV 60 AS Tid,
	COUNT(message_text) As Antal,
	(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV COUNT(message_text) AS Avg,
	message_text AS Larm
	FROM alarm_tid
	WHERE message_text = '".$search_larm."'
	GROUP BY YEARWEEK(DATE(message_start), 1) DESC";


	$search = True;
	Fetch_data($sql, $search, $avg_time);
	}




	function Fetch_data($sql, $search, $avg_time) {
	$conn = new mysqli('localhost', 'root', '', 'steelform');




	if ($conn->connect_error) {
	die("Connection failed: " . $conn->connection_error);
	}




	$result = $conn->query($sql);
	$conn->close();




	if ($result->num_rows > 0) {


	if ($search == True) {
	echo "<table style='width: 70%;'>";
	echo "<tr>";

	echo "<col span='1' style='width: 5%;'>";
	echo "<col span='1' style='width: 5%;'>";
	echo "<col span='1' style='width: 5%;'>";
	echo "<col span='1' style='width: 5%;'>";
	echo "<col span='1' style='width: 5%;'>";
	echo "<col span='1' style='width: 5%;'>";
	echo "<th>År</th>";
	echo "<th>Vecka</th>";
	echo "<th>Minuter</th>";
	echo "<th>Genomsnitt(Sekunder)</th>";
	echo "<th>Antal</th>";


	} else if ($avg_time == True) {
	echo "<table style='width: 70%;'>";
	echo "<tr>";

	echo "<col span='1' style='width: 5%;'>";
	echo "<col span='1' style='width: 5%;'>";
	echo "<th>Sekunder</th>";


	} else {
	echo "<table style='width: 70%;'>";
	echo "<tr>";

	echo "<col span='1' style='width: 15%;'>";
	echo "<col span='1' style='width: 10%;'>";
	echo "<col span='1' style='width: 30%;'>";
	echo "<th align='center'>Minuter</th>";
	echo "<th align='center'>Antal</th>";
	}


	echo "<th align='left'>Larm</th>";




	if ($search == False && $avg_time == False) {
	while($row = $result->fetch_assoc()) {
	echo "<tr><td align='center'>" . $row["Tid"] . "</td><td align='center'>" . $row["Antal"] . "</td><td align='left'>" . $row["Larm"] . "</td></tr>";
	}
	} else if ($search == True) {
	while($row = $result->fetch_assoc()) {
	echo "<tr><td align='center'>" . $row["Year"] . "</td><td align='center'>" . $row["Vecka"] . "</td><td align='center'>" . $row["Tid"] . "</td><td align='center'>" . $row["Avg"] . "</td><td align='center'>" . $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
	}
	} else if ($avg_time == True) {
	while($row = $result->fetch_assoc()) {
	echo "<tr><td align='center'>" . $row["Avg"] . "</td><td>" . $row["Larm"] .  "</td></tr>";
	}
	}


	echo "</tr>";
	echo "</table><br><br>";




	} else {
	echo "<p align='center'>Inga resultat</p>";
	}
	}


	?>


	</body>
</hmtl>