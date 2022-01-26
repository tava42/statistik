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
				text-align: left;
				margin-left: auto;
				margin-right: auto;
				width: 70%;
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
				<input type="submit" name="search_utveckling" class="button" value="Sök utveckling">
				<br>
				<br>
				<input type="text" id="search" align="right" name="search_form" placeholder="Sök efter larm">
				<input type="submit" name="Search" class="button" value="Sök">

				<a href="fetch.php">Fetch</a>
			</div>

			</form>
		</nav>

		<?php
			function create_table($column) {
				echo "<table id='myTable'>";
				echo "<tr>";

				for ($x = 0; $x < count($column); $x++) {
					echo "<col span='1' style='width: 5%;'>";
				}

				foreach ($column as $key) {
					echo "<th>".$key."</th>";
				}
			}

			function get_data($sql) {
				$conn = new mysqli('localhost', 'root', '', 'steelform');

				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connection_error);
				}

				$result = $conn->query($sql);
				$conn->close();

				return $result;
			}

			function check_exclude() {
				if ($_POST['inc'] == '0') {
					$exclude_varning = "AND message_text NOT LIKE '%Varning%' ";
				} else {
					$exclude_varning = "";
				}
				return $exclude_varning;
			}

			function today($interval) {

				$exclude_varning = check_exclude();

				$sql = "SELECT
								(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
								COUNT(message_text) AS Antal,
								message_text AS Larm
								FROM alarm_tid
								WHERE DATE(message_start) $interval $exclude_varning
								GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
								ORDER BY COUNT(message_text) DESC;";

					$result = get_data($sql);

					if ($result->num_rows > 0) {
						$column = ["Minuter", "Antal", "Larm"];
						create_table($column);

						while($row = $result->fetch_assoc()) {
							echo "<tr><td>" . $row["Tid"] . "</td><td>" . $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
						}

					echo "</tr>";
					echo "</table><br><br>";

					} else {
						echo "<p align='center'>Inga resultat</p>";
				}
			}

			function utveckling() {
				$from = date('Y-m-d', strtotime($_POST['dateFrom']));
				$to = date('Y-m-d', strtotime($_POST['dateTo']));

				$sql = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 As NewTid,
					COUNT(message_text) As NewAntal,
					message_text As Larm,
					message_start As Datum
					FROM alarm_tid
					WHERE DATE(message_start) BETWEEN '2021-09-26' AND '2021-10-03' AND message_text NOT LIKE '%Varning%'
					group by message_text
					union
					SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 As NewTid,
					COUNT(message_text) As NewAntal,
					message_text As Larm,
					message_start As Datum
					FROM alarm_tid
					WHERE DATE(message_start) BETWEEN '".$from."' AND '".$to."' AND message_text NOT LIKE '%Varning%'
					group by message_text
					ORDER BY `Larm`  ASC;";

					$result = get_data($sql);

					if ($result->num_rows > 0) {
						$column = ["OldTid", "OldAntal", "NewTid", "NewAntal", "Diff Tid", "Diff Antal", "Larm"];
						create_table($column);

							$rows = [];
							while($row = mysqli_fetch_assoc($result))
								{
									$rows[] = $row;
								}

							$temp = 0;

							foreach($rows as $value) {

								if ($temp != 0) {

									if ($value['Larm'] == $Larm) {
										echo "<tr><td>" .$value['NewTid']. "</td>";
										echo "<td>" .$value['NewAntal']. "</td>";
										echo "<td>" .$OldTid. "</td>";
										echo "<td>" .$OldAntal. "</td>";

										if ($value['Datum'] > $OldDatum) {
											echo "<td>" .$value['NewTid'] - $OldTid. "</td>";
											echo "<td>" .$value['NewAntal'] - $OldAntal. "</td>";
										} else {
											echo "<td>" .$OldTid - $value['NewTid']. "</td>";
											echo "<td>" .$value['NewAntal'] - $OldAntal. "</td>";
										}

										echo "<td>" .$value['Larm']. "</td></tr>";
									}
								}
								$OldDatum = $value['Datum'];
								$OldAntal = $value['NewAntal'];
								$OldTid = $value['NewTid'];
								$Larm = $value['Larm'];
								$temp = 1;
							}

						echo "</tr>";
						echo "</table><br><br>";

					} else {
						echo "<p align='center'>Inga resultat</p>";
					}
			}

			function average() {

				if ($_POST['inc'] == '0') {
					$exclude_varning = "WHERE message_text NOT LIKE '%Varning%' ";
				} else {
					$exclude_varning = "";
				}

				$sql = "SELECT
								(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV COUNT(message_text) AS Avg,
								message_text AS Larm
								FROM alarm_tid $exclude_varning
								GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
								ORDER BY Avg DESC;";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					$column = ["Sekunder", "Larm"];
					create_table($column);

					while($row = $result->fetch_assoc()) {
						echo "<tr><td>" . $row["Avg"] . "</td><td>" . $row["Larm"] .  "</td></tr>";
					}
						echo "</tr>";
						echo "</table><br><br>";

					} else {
						echo "<p align='center'>Inga resultat</p>";
				}
			}

			function search_date($from, $to) {

				$exclude_varning = check_exclude();

				$sql = "SELECT
								(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
								COUNT(message_text) AS Antal,
								message_text AS Larm
								FROM alarm_tid WHERE DATE(message_start) BETWEEN '".$from."' AND '".$to."' $exclude_varning
								GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
								ORDER BY COUNT(message_text) DESC;";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					$column = ["Minuter", "Antal", "Larm"];
					create_table($column);

					while($row = $result->fetch_assoc()) {
							echo "<tr><td>" . $row["Tid"] . "</td><td>" . $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
					}
					echo "</tr>";
					echo "</table><br><br>";

				} else {
					echo "<p align='center'>Inga resultat</p>";
				}
			}

			function search_larm($search_larm) {
				$exclude_varning = check_exclude();

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

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					$column = ["År", "Vecka", "Tid", "Antal", "Genomsnitt", "Larm"];
					create_table($column);

					while($row = $result->fetch_assoc()) {
						echo "<tr><td>" . $row["Year"] . "</td><td>" . $row["Vecka"] . "</td><td>" . $row["Tid"] . "</td><td>" . $row["Avg"] . "</td><td>" . $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
					}

					echo "</tr>";
					echo "</table><br><br>";
				} else {
					echo "<p align='center'>Inga resultat</p>";
				}
			}

			if (isset($_POST['today'])) {
					echo "<p align='center'>Idag</p>";
					$interval = " = CURRENT_DATE() ";
					today($interval);

			} else if (isset($_POST['last_week'])) {
					echo "<p align='center'>Senaste 7 dagar</p>";
					$interval = " >= current_date - INTERVAL 700 DAY";
					today($interval);

			} else if (isset($_POST['last_month'])) {
					echo "<p align='center'>Senaste 30 dagar</p>";
					$interval = " >= current_date - INTERVAL 300 DAY";
					today($interval);

			} else if (isset($_POST['avg_time'])) {
					echo "<p align='center'>Genomsnittlig stopptid per larm</p>";
					average();

			} else if (isset($_POST['search_date'])) {
					$from = date('Y-m-d', strtotime($_POST['dateFrom']));
					$to = date('Y-m-d', strtotime($_POST['dateTo']));
					echo "<p align='center'>Från: $from  Till:  $to </p>";
					search_date($from, $to);

			} else if (isset($_POST['search_utveckling'])) {
					utveckling();

			} else if (isset($_POST['Search'])) {
					$search_larm = ltrim(filter_input(INPUT_POST, 'search_form'));
					echo "<p align='center'>" . $search_larm . "</p>";
					search_larm($search_larm);
			}

		?>

	</body>
</hmtl>
