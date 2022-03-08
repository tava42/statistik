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
				width: 80%;
			}
			.Datum { width: 12%; }
			.År { width: 6%; }
			.Vecka { width: 6%; }
			.Minuter { width: 6%; }
			.Sekunder { width: 6%; }
			.Tid { width: 6%; }
			.Antal { width: 6%; }
			.Larm { width: 40%; }
			.date_start { width: 12%; }
			.date_end { width: 12%; }
			.kassV { width: 4%; }
			.kassH { width: 4%; }


			td:hover {
				overflow: visible;
			}
			th {

				background-color: #6496C8;
				color: white;

			}
			td {
				white-space: nowrap;
 				overflow: hidden;
  				text-overflow: ellipsis;
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

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
				<input type="submit" name="TAKOEE" class="button" value="TAKOEE">
				<input type='submit' name='produktion' class='button' value='Produktion'>
				<input type="submit" name="overview" class="button" value="Översikt">
				<input type="submit" name="utveckling" class="button" value="Utveckling">
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
				<input type="submit" name="search_larm" class="button" value="Sök">
				<input type="submit" name="search_all" class="button" value="Sök allt">
			</div>

			</form>
		</nav>

		<script type="text/javascript">

			function sortTable() {
				var table, rows, switching, i, x, y, shouldSwitch;
				table = document.getElementById("myTable");
				switching = true;

				while (switching) {
					switching = false;
					rows = table.rows;

					for (i = 1; i < (rows.length - 1); i++) {
						shouldSwitch = false;
						x = rows[i].getElementsByTagName("TD")[4];
						y = rows[i + 1].getElementsByTagName("TD")[4];

						if (Number(x.innerHTML) < Number(y.innerHTML)) {
							shouldSwitch = true;
							break;
						}
					}

					if (shouldSwitch) {
						rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
						switching = true;
					}
				}
			}

			function buildChart() {

				var label = [];
				var tid = [];
				var antal = [];
				for ( var i = 1; i < myTable.rows.length; i++ ) {
				    label.push(myTable.rows[i].cells[1].innerHTML);
				    tid.push(myTable.rows[i].cells[2].innerHTML);
				    antal.push(myTable.rows[i].cells[3].innerHTML);
				}

				var ctx = document.getElementById("line-chart").getContext("2d");
				ctx.canvas.width = 40;
				ctx.canvas.height = 10;

				var myChart = new Chart(ctx, {
					type: 'line',
					data: {
					    labels: label.reverse(),
					    datasets: [{
							  label: "Tid",
							  data: tid.reverse(),
							  fill: false,
							  backgroundColor: "red",
							  borderColor: "red",
							  borderCapStyle: 'butt'
						  },
						  {
							  label: "Antal",
							  data: antal.reverse(),
							  fill: false,
							  backgroundColor: "blue",
							  borderColor: "blue",
							  borderCapStyle: 'butt'
					   }
				   ]
				   }
				});
			}


		</script>

		<?php

			function create_table($headers) {
				//echo "<table id='myTable'>";
				echo "<tr>";

				foreach ($headers as $key) {
					echo "<th class=".$key.">".$key."</th>";
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
					$exclude_varning = " AND message_text NOT LIKE '%Varning%'";
				} else {
					$exclude_varning = "";
				}
				return $exclude_varning;
			}

			function recent($interval) {

				$exclude_varning = check_exclude();

				$sql = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm
					FROM alarm_tid
					WHERE DATE(message_start) $interval $exclude_varning
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
					ORDER BY (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 DESC;";

				$result = get_data($sql);


				if ($result->num_rows > 0) {
					$headers = array("Minuter", "Antal", "Larm");
					echo "<table style='width:40%;' id='myTable'>";
					create_table($headers);

					$sum_antal = 0;
					$data = array();

					while($row = $result->fetch_assoc()) {
						echo "<tr><td>" . $row["Tid"] . "</td><td>" . $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
						$sum_antal = $sum_antal + $row['Antal'];
						array_push($data, $row["Tid"]);
					}

					echo "</tr>";
					echo "</table><br><br>";
					echo $sum_antal;

					} else {
					echo "<p align='center'>Inga resultat</p>";
					}

			}

			function utveckling($search_date) {
				$from = date('Y-m-d', strtotime($_POST['dateFrom']));
				$to = date('Y-m-d', strtotime($_POST['dateTo']));
				$exclude_varning = check_exclude();
				//	echo "Utveckling mellan " . date('Y-m-d', strtotime($_POST['dateFrom'] . '+30 days'));

				$start = new DateTime($_POST['dateFrom'] . '-1 days');
				$end = new DateTime($_POST['dateTo']);
				$diff = $start->diff($end);

			 	if ($search_date) {
				 	$first = '"' . $from . '" AND "' . $to . '"';
				 	$second = '"'. date('Y-m-d', strtotime($_POST['dateFrom'] . '-' . $diff->format('%d') . ' days')) .
					 '" AND "' . date('Y-m-d', strtotime($_POST['dateFrom'] . '-1 days')) . '"';
					echo $first . '<br>';
					echo $second;
				} else {
					echo "<p align='center'>Denna månaden jämfört med förra månaden</p>";
					$first = 'CURRENT_DATE - INTERVAL 30 DAY AND current_date ';
					$second = 'current_date - INTERVAL 61 DAY AND current_date - INTERVAL 31 DAY ';
				}

				$sql = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV 60 As NewTid,
					COUNT(message_text) As NewAntal,
					message_text As Larm,
					message_start As Datum
					FROM alarm_tid
					WHERE DATE(message_start) BETWEEN $first
					$exclude_varning AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					group by message_text
					union
					SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV 60 As NewTid,
					COUNT(message_text) As NewAntal,
					message_text As Larm,
					message_start As Datum
					FROM alarm_tid
					WHERE DATE(message_start) BETWEEN $second
					$exclude_varning AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					group by message_text
					ORDER BY `Larm`  ASC;";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					// echo "<p><button onclick='sortTable(1);'>Desc</button></p>";
					// echo "<p><button onclick='sortDesc(5);'>Desc</button></p>";
					// echo "<p><button onclick='sortAsc();'>Asc</button></p>";
					$headers = array("NewTid", "NewAntal", "OldTid", "OldAntal", "Diff Tid", "Diff Antal", "Larm");
					echo "<table style='width:70%;' id='myTable'>";
					create_table($headers);

					$rows = array();
					while($row = mysqli_fetch_assoc($result)) {
						$rows[] = $row;
					}

					$temp = 0;

					foreach($rows as $value) {
						if ($temp != 0) {
							if ($value['Larm'] == $Larm) {
								if ($value['Datum'] > $OldDatum) {
									echo "<tr><td>" .$value['NewTid']. "</td>";
									echo "<td>" .$value['NewAntal']. "</td>";

									echo "<td>" .$OldTid. "</td>";
									echo "<td>" .$OldAntal. "</td>";

									$NewTid = $value['NewTid'] - $OldTid;
									$NewAntal = $value['NewAntal'] - $OldAntal;

									echo "<td>" .$NewTid. "</td>";
									echo "<td>" .$NewAntal. "</td>";
								} else {
									echo "<tr><td>" .$OldTid. "</td>";
									echo "<td>" .$OldAntal. "</td>";

									echo "<td>" .$value['NewTid']. "</td>";
									echo "<td>" .$value['NewAntal']. "</td>";

									$NewTid = $OldTid - $value['NewTid'];
									$NewAntal = $OldAntal - $value['NewAntal'];

									echo "<td>" .$NewTid. "</td>";
									echo "<td>" .$NewAntal. "</td>";
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
					echo '<script type="text/javascript">sortTable();</script>';
				} else {
					echo "<p align='center'>Inga resultat</p>";
				}
			}

			function average() {
				if ($_POST['inc'] == '0') {
					$exclude_varning = "WHERE message_text NOT LIKE '%Varning%' AND ";
				} else {
					$exclude_varning = "WHERE";
				}

				$sql = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV COUNT(message_text) AS Avg,
					message_text AS Larm
					FROM alarm_tid $exclude_varning
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
					ORDER BY Avg DESC;";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					$headers = array("Sekunder", "Larm");
					echo "<table style='width:40%;' id='myTable'>";
					create_table($headers);

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
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm
					FROM alarm_tid WHERE DATE(message_start) BETWEEN '".$from."' AND '".$to."' $exclude_varning
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
					ORDER BY (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 DESC";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					$headers = array("Minuter", "Antal", "Larm");
					echo "<table style='width:40%;' id='myTable'>";
					create_table($headers);

					$sum = 0;
					while($row = $result->fetch_assoc()) {
						echo "<tr><td>" . $row["Tid"] . "</td><td>" . $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
						$sum = $sum + $row["Tid"];
					}
					echo "</tr>";
					echo "</table><br><br>";
					echo "sum tid: " . $sum / 60 . " timmar";

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
					message_text AS Larm
					FROM alarm_tid
					WHERE message_text = '".$search_larm."'
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY YEARWEEK(DATE(message_start), 1) DESC";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					$headers = array("År", "Vecka", "Minuter", "Antal", "Larm");
					echo "<table style='width:50%;' id='myTable'>";
					create_table($headers);
					echo "<canvas id='line-chart' width='200' height='400'></canvas>";

					while($row = $result->fetch_assoc()) {
						echo "<tr><td>" . $row["Year"] . "</td><td>" . $row["Vecka"] . "</td><td>" . $row["Tid"] . "</td><td>" .
						 $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
						 $average[] = $row['Tid'];
					}
					echo "</tr>";
					echo "</table><br><br>";
					$average = array_sum($average)/count($average);
					echo $average;
					echo '<script type="text/javascript">buildChart();</script>';
				} else {
					echo "<p align='center'>Inga resultat</p>";
				}
			}

			function search_all($search_larm) {

				$sql = "SELECT
					((TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					message_text AS Larm,
					message_start as Start
					FROM alarm_tid
					WHERE message_text LIKE '%".$search_larm."%'
					ORDER BY message_start DESC;";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					$headers = array('Datum', 'Tid', 'Larm');
					echo "<table style='width:50%;' id='myTable'>";
					create_table($headers);

					$rows = array();

					while($row = mysqli_fetch_assoc($result)) {
						$rows[] = $row;
					}

					$change = 0;
					#$reverse=array_reverse($rows);
					$temp = 0;

					foreach($rows as $value) {
						if ($temp != 0) {
							echo "<tr><td>" .$Start. "</td>";
							echo "<td>" .$Tid. "</td>";
							echo "<td>" .$Larm. "</td></tr>";
						}

						$Tid = $value['Tid'];
						$Start = $value['Start'];
						$Larm = $value['Larm'];
						$temp = 1;
					}

					echo "</tr>";
					echo "</table><br><br>";

				} else {
					echo "<p align='center'>Inga resultat</p>";
				}
			}

			function overview() {
				$exclude_varning = check_exclude();
				$sql = "";
				for ($x = 2; $x <= 10; $x++) {
					$sql .= "SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end)) DIV 60 AS Tid,
					COUNT(message_text) As Antal,
					message_text As Larm
		    			FROM alarm_tid WHERE (message_text LIKE '%Stn ".$x."%' OR message_text LIKE '%Station ".$x."%')
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end))) / 60 < 60
					$exclude_varning UNION ";
				}

				$sql .= "SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end)) DIV 60 AS Tid,
				COUNT(message_text) As Antal,
				message_text As Larm
				FROM alarm_tid
				WHERE message_text NOT LIKE '%Stn%' AND message_text NOT LIKE '%Station %'
				AND (SELECT SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end))) / 60 < 60
				$exclude_varning";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					$headers = array('Tid', 'Antal');
					echo "<table style='width:40%;' id='myTable'>";
					create_table($headers);
					echo "<th style='width:10%;'>Larm</th>";

					$x = 2;
					while($row = $result->fetch_assoc()) {
						if ($x == 11) {$stn = "Övriga";} else {$stn = "Station " . $x;}
						// echo "<tr><td>" . $row["Tid"] . "</td><td>" . $row["Antal"] . "</td><td>" . $stn . "</td></tr>";
						echo "<tr><td>" . $row["Tid"] . "</td><td>" . $row["Antal"] . "</td><td>" . $stn . "</td></tr>";
						$x++;
					}
				}
			}

			function produktion() {
				$sql = "SELECT * FROM produktion ORDER BY `produktion`.`datum` DESC";
				$result = get_data($sql);

				if ($result->num_rows > 0) {

					$headers = array();
						for ($x = 0; $x <= 23; $x++) {
							array_push($headers, $x);
						}
					array_push($headers, 'kassV', 'kassH', 'date_start', 'date_end');

					echo "<table style='width:80%;' id='myTable'>";
					create_table($headers);

					while($row = $result->fetch_assoc()) {
						if ($row['date_start'] != null) {
							$echo = "<tr>";
							foreach ($headers as $key) {

								if ($row[$key] != "") {
									$echo .= "<td id='test'>" . $row[$key] . "</td>";
								} else {
									$echo .= "<td id='test'>" . "-" . "</td>";
								}

							}
							$echo .= "</tr>";
							$date_start = new DateTime($row['date_start']);
							$date_end = new DateTime($row['date_end']);
							$diff = strtotime($row["date_end"]) - strtotime($row["date_start"]);

							echo $echo;
						}
					}

					echo "</tr>";
					echo "</table><br><br>";
					} else {
					echo "<p align='center'>Inga resultat</p>";
					}
			}

			function TAKOEE() {
				$max_prod = 250 / 60;

				echo "<p align='center'>Tillgänglighet = (Produktionstid - Stopptid) / Produktionstid</p>";
				echo "<p align='center'>Anläggningsutbyte = (Produktion / Produktionstid) / max produktion</p>";
				echo "<p align='center'>Kvalite = (Produktion - Kass) / Produktion</p>";
				echo "<p align='center'>OEE = Tillgänglighet * Anläggningsutbyte * Kvalite</p>";

				$sum_antal = "(select(";
				for ($x=0; $x <= 22; $x++) {
					$sum_antal .= "COALESCE(produktion." . $x . ", 0) + ";
				}
				$sum_antal .= "COALESCE(produktion.23, 0)))";

				$sql = "select produktion.datum,
						SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end)) / 60 as stopptid,
						".$sum_antal." As produktion,
						(select(produktion.kassV + produktion.kassH)) As kass,
						produktion.date_start,
						produktion.date_end,
						produktion.idle
						from produktion
						left join alarm_tid
						on produktion.datum = date(alarm_tid.message_start) AND alarm_tid.message_text NOT LIKE '%Varning%'
						AND (SELECT SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end))) / 60 < 60
						GROUP by date(alarm_tid.message_start) DESC;";

				$result = get_data($sql);

				$headers = array('Datum', 'Produktion', 'Produktionstid', 'Stopptid', 'Kass', 'Tillgänglighet', 'Anläggningsutbyte', 'Kvalite', 'OEE');
				echo "<table style='width:70%;' id='myTable'>";
				create_table($headers);
				echo "<br>";

				while($row = $result->fetch_assoc()) {
					$produktion = $row['produktion'];
					$produktionstid = ((strtotime($row["date_end"]) - strtotime($row["date_start"])) / 60) - $row["idle"];
					$stopptid = $row['stopptid'];
					$kass = $row['kass'];

					if ($produktionstid != 0 && $produktion != 0) {
						$tillgänglighet = ($produktionstid - $stopptid) / $produktionstid;
						$anläggningsutbyte = ($produktion / $produktionstid) / $max_prod;
						$kvalite = ($produktion - $kass) / $produktion;
						$OEE = $tillgänglighet * $anläggningsutbyte * $kvalite;

						echo "<tr><td>" . $row['datum'] . "</td><td>" . $produktion . "</td><td>" . bcdiv(($produktionstid / 60),1, 2)  . "</td><td>" . bcdiv(($stopptid / 60),1, 2) . "</td><td>" .
							$kass . "</td><td>" . bcdiv(($tillgänglighet) * 100, 1, 2) . "%" . "</td><td>" . bcdiv(($anläggningsutbyte) * 100, 1, 2) . "%" .
							 "</td><td>" . bcdiv(($kvalite) * 100, 1, 2) . "%" . "</td><td>" . bcdiv(($OEE) * 100, 1, 2) . "%" . "</td>";
					}
				}
				echo "</tr></table>";
			}


			if (isset($_POST['today'])) {
				echo "<p align='center'>Idag</p>";
				$interval = " = CURRENT_DATE() ";
				recent($interval);

			} else if (isset($_POST['last_week'])) {
				echo "<p align='center'>Senaste 7 dagar</p>";
				$interval = " >= current_date - INTERVAL 7 DAY";
				recent($interval);

			} else if (isset($_POST['last_month'])) {
				echo "<p align='center'>Senaste 30 dagar</p>";
				$interval = " >= current_date - INTERVAL 30 DAY";
				recent($interval);

			} else if (isset($_POST['avg_time'])) {
				echo "<p align='center'>Genomsnittlig stopptid per larm</p>";
				average();

			} else if (isset($_POST['search_date'])) {
				$from = date('Y-m-d', strtotime($_POST['dateFrom']));
				$to = date('Y-m-d', strtotime($_POST['dateTo']));
				echo "<p align='center'>Från: $from  Till:  $to </p>";
				search_date($from, $to);

			} else if (isset($_POST['utveckling'])) {
				$search_date = false;
				utveckling($search_date);

			} else if (isset($_POST['search_utveckling'])) {
				$search_date = true;
				utveckling($search_date);

			} else if (isset($_POST['search_larm'])) {
				$search_larm = ltrim(filter_input(INPUT_POST, 'search_form'));
				echo "<p align='center'>" . $search_larm . "</p>";
				search_larm($search_larm);

			} else if (isset($_POST['search_all'])) {
				$search_larm = ltrim(filter_input(INPUT_POST, 'search_form'));
				search_all($search_larm);

			} else if (isset($_POST['overview'])) {
				overview();

			} else if (isset($_POST['produktion'])) {
				produktion();

			} else if (isset($_POST['TAKOEE'])) {
				TAKOEE();
		}


		?>



	</body>
</hmtl>
