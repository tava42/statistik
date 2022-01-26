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
			td {

				text-overflow: ellipsis;
				overflow: hidden;
				white-space:nowrap;
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

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<div align="center">
		<form method="post">
			<input type="text" id="search" text-align="left" name="search_form" placeholder="Sök efter larm">
			<input type="submit" name="Search" class="button" value="Sök">
		</form>
	</div>
	<body>

	<?php
	

	if (isset($_POST['Search'])) {
		$search_larm = ltrim(filter_input(INPUT_POST, 'search_form'));
		$sql = "SELECT
				((TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
				message_text AS Larm,
				message_start as Start
				FROM alarm_tid
				WHERE message_text LIKE '%".$search_larm."%'
				ORDER BY message_start DESC;";

		Fetch_data($sql);
	}

	function Fetch_data($sql) {

				$conn = new mysqli('localhost', 'root', '', 'steelform');

					if ($conn->connect_error) {
						die("Connection failed: " . $conn->connection_error);
					}

					$result = $conn->query($sql);
					$conn->close();

					if ($result->num_rows > 0) {
						echo "<table>";
						echo "<tr>";

						echo "<col span='1' style='width: 5%;'>";
						echo "<col span='1' style='width: 5%;'>";
						echo "<col span='1' style='width: 5%;'>";
						echo "<th>Tid</th>";
						echo "<th>Datum</th>";
						echo "<th>Larm</th>";

							$rows = [];
							while($row = mysqli_fetch_assoc($result))
								{
									$rows[] = $row;
								}
							$change = 0;
							#$reverse=array_reverse($rows);
							$temp = 0;

							foreach($rows as $value) {

							if ($temp != 0) {

								echo "<tr><td>" .$Tid. "</td>";
								echo "<td>" .$Start. "</td>";
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

	?>

	</body>
</hmtl>
