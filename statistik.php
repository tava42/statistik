<!DOCTYPE html>
<html>
	<head>
	<link rel="stylesheet" href="style.css">
	<script>
		if ( window.history.replaceState ) {
			window.history.replaceState( null, null, window.location.href );
		}
	</script>
	</head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.js"></script>
	<script src="jquery.min.js"></script>

	<body>
		<nav class="navbar" align="center">
			<br>
				<form method="post">
				<input type="submit" name="today" class="button" value="Idag">
				<input type="submit" name="last_week" class="button" value="Senaste 7 dagar">
				<input type="submit" name="last_month" class="button" value="Senaste 30 dagar">
				<input type="submit" name="TAKOEE" class="button" value="TAKOEE">
				<input type='submit' name='produktion' class='button' value='Produktion'>
				<input type="submit" name="utveckling" class="button" value="Utveckling">
				<input type="submit" name="overview" class="button" value="Översikt">
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

				<input type="text" id="search" align="right" name="search_form" size="41px" placeholder="Sök efter larm">
				<input type="submit" name="search_larm" class="button" value="Sök">
				<input type="submit" name="search_all" class="button" value="Sök allt">
				</form>
				<br>

		</nav>
		<br>
		<br>
		<script type="text/javascript">

			function sortTable(n) {
				  var table, rows, switching, i, x, y, z, shouldSwitch, dir, datum, takoee, switchcount = 0;
				  table = document.getElementById("myTable");
				  switching = true;
				  datum = false;
				  takoee = false;

				  dir = "asc";

				  z = table.rows[0].getElementsByTagName("TH")[n].innerHTML;

				  if (z == "Datum") {
					  datum = true;
				  } else if (z == "Tillgänglighet"||z == "Anläggningsutbyte"||z == "Kvalite"||z == "OEE") {
					  takoee = true;
				  }

				  while (switching) {

				    switching = false;
				    rows = table.rows;

				    for (i = 1; i < (rows.length - 1); i++) {

				      shouldSwitch = false;

				      x = rows[i].getElementsByTagName("TD")[n].innerHTML;
				      y = rows[i + 1].getElementsByTagName("TD")[n].innerHTML;

					 if (takoee) {
						 y = y.replace(/%/, "");
						 x = x.replace(/%/, "");
					 } else if (datum) {
						 y = y.replace(/-/g, "");
						 x = x.replace(/-/g, "");
					 }

				      if (dir == "asc") {
						 if (Number(x) < Number(y)) {
						   shouldSwitch = true;
						   break;
					   }
				   	} else if (dir == "desc") {
						 if (Number(x) > Number(y)) {
						   shouldSwitch = true;
						   break;
						 }
				        }
				      }

				    if (shouldSwitch) {

				      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				      switching = true;

				      switchcount ++;
				    } else {

				      if (switchcount == 0 && dir == "asc") {
				        dir = "desc";
				        switching = true;
				      }
				    }
				  }
			}

			function dashboardTAKOEE() {
				var label = [];
				var t = [];
				var a = [];
				var k = [];
				var oee = [];

				for ( var i = 1; i < takoee.rows.length; i++ ) {
				    label.push(takoee.rows[i].cells[0].innerHTML);
				    t.push(takoee.rows[i].cells[1].innerHTML.replace('%', ''));
				    a.push(takoee.rows[i].cells[2].innerHTML.replace('%', ''));
				    k.push(takoee.rows[i].cells[3].innerHTML.replace('%', ''));
				    oee.push(takoee.rows[i].cells[4].innerHTML.replace('%', ''));
				}

				var ctx = document.getElementById("dashboardTAKOEE").getContext("2d");
				ctx.canvas.width = 40;
				ctx.canvas.height = 10;

				var myChart = new Chart(ctx, {
					type: 'line',
					data: {
					    labels: label.reverse(),
					    datasets: [
						   {
							  label: "Tillgänglighet",
							  type: "line",
							  data: t.reverse(),
							  fill: false,
							  backgroundColor: "green",
							  borderColor: "green",
							  borderCapStyle: 'butt'
						   },
						   {
							  label: "Anläggningsutbyte",
							  data: a.reverse(),
							  fill: false,
							  backgroundColor: "#6496C8",
							  borderColor: "yellow",
							  borderCapStyle: 'butt'
						   },
						   {
							  label: "Kvalite",
							  data: k.reverse(),
							  fill: false,
							  backgroundColor: "purple",
							  borderColor: "purple",
							  borderCapStyle: 'butt'
						   },
						   {
							  label: "OEE",
							  data: oee.reverse(),
							  fill: false,
							  backgroundColor: "brown",
							  borderColor: "brown",
							  borderCapStyle: 'butt'
						   },
				   		]
				   	}
				});
			}

			function dashboardUtveckling(type) {
				sortTable(5);
				var incCanvas;
				var decCanvas
				if (type == "dash") {
					decCanvas = "decDashUtv";
					incCanvas = "incDashUtv";
					var rows = 5;
				} else if (type == "utveckling") {
					decCanvas = "decUtveckling";
					incCanvas = "incUtveckling";
					var rows = 20;
				}
				var maxTid = [];
				var minTid = [];
				var decAntal = [];
				var incAntal = [];
				var decLarm = [];
				var incLarm = [];

				for ( var i = 1; i <= rows; i++ ) {
					if (myTable.rows[i].cells[5].innerHTML > 0) {
						incAntal.push(myTable.rows[i].cells[5].innerHTML);
						incLarm.push(myTable.rows[i].cells[6].innerHTML);
					}

				}
				for ( var i = myTable.rows.length - 1; i >= (myTable.rows.length - rows); i-- ) {
					if (myTable.rows[i].cells[5].innerHTML < 0) {
					     decAntal.push(myTable.rows[i].cells[5].innerHTML);
						decLarm.push(myTable.rows[i].cells[6].innerHTML);
					} else {
						decLarm.push("-");
					}
				}

				var decMax = Math.min.apply(Math, decAntal);
				var incMax = Math.max.apply(Math, incAntal);

				if (decMax < 0) {
					decMax = Math.abs(decMax);
				}
				var decTick = 0;
				var incTick = 0;

				if (decMax > incMax) {
					incTick = decMax;
					decTick = -Math.abs(decMax);
				} else if (incMax > decMax) {
					incTick = incMax;
					decTick = -Math.abs(incMax);
				} else {
					incTick = incMax;
					decTick = incMax;
				}

				var ctx2 = document.getElementById(decCanvas).getContext("2d");
				var min = new Chart(ctx2, {
					type: 'bar',
					data: {
						labels: decLarm,
						datasets: [{
							label: 'Antal minskat',
							data: decAntal,
							labelLinks: decLarm,
							backgroundColor: "#44a73a",
						 	fill: false,
					  	}]
				  	},
				  	options: {
						indexAxis: 'y',
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								title: {
									// text: 'asdoksä',
									// display: true,
								}
							}
						},
						scales: {
							y: {
								position: 'right',

								stacked: true,
								labelAutoFit: true,
							},
							x: {
								reverse: true,
								// beginAtZero: true,
								suggestedMin: decTick,
								suggestedMax: 0,
							}
						}
					}
				});

				var ctx = document.getElementById(incCanvas).getContext("2d");

				var max = new Chart(ctx, {
					type: 'bar',
					data: {
	    					labels: incLarm,
						datasets: [{
							indexAxis: 'y',
							label: 'Antal ökat',
							data: incAntal,
							labelLinks: incLarm,
							backgroundColor: "#d12f2f",
							fill: false,
					  	}]
				  	},
					options: {
						indexAxis: 'y',
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								title: {
									// text: 'asdoksä',
									// display: true,
								}
							}
						},
						scales: {
							y: {
								stacked: true,
								labelAutoFit: true,
							},
							x: {
								reverse: true,
								// beginAtZero: true,
								suggestedMin: 0,
								suggestedMax: incTick,

							}
						}
					}
				});

				function clickableScales(chart, click) {

					const { ctx, canvas, scales: {x, y} } = chart;
					const top = y.top
					const left = y.left
					const right = y.right
					const bottom = y.bottom
					const height = y.height / y.ticks.length;

					//mouse coordinates
					let rect = canvas.getBoundingClientRect();
					const xCoor = click.clientX - rect.left;
					const yCoor = click.clientY - rect.top;

					for (let i = 0; i < y.ticks.length; i++) {
						if(xCoor >= left && xCoor <= right && yCoor >= top + (height * i)
						&& yCoor <= top + height + (height * i)) {
							var larm = chart.data.datasets[0].labelLinks[i];

							$.ajax({
							    url: 'statistik.php',
							    type: 'POST',
							    data: { "ajax_larm": larm},
							    success: function(response) {
								    // $("#wrap").append(response);
								    // load(response);
								    // document.write(response);
								    $("body").html(response);
							    }
							});
						}
					}
				}

				max.canvas.addEventListener('click', (e) => {
					var chart = max;
					clickableScales(chart, e)
				});
				min.canvas.addEventListener('click', (e) => {
					var chart = min;
					clickableScales(chart, e)
				});
			}

			function chartTAKOEE() {

				var label = [];
				var produktion = [];
				var produktionstid = [];
				var stopptid = [];
				var kass = [];
				var t = [];
				var a = [];
				var k = [];
				var oee = [];

				for ( var i = 1; i < myTable.rows.length; i++ ) {
				    label.push(myTable.rows[i].cells[0].innerHTML);
				    produktion.push(myTable.rows[i].cells[1].innerHTML);
				    produktionstid.push(myTable.rows[i].cells[2].innerHTML * 60);
				    stopptid.push(myTable.rows[i].cells[3].innerHTML * 60);
				    kass.push(myTable.rows[i].cells[4].innerHTML);
				    t.push(myTable.rows[i].cells[5].innerHTML.replace('%', ''));
				    a.push(myTable.rows[i].cells[6].innerHTML.replace('%', ''));
				    k.push(myTable.rows[i].cells[7].innerHTML.replace('%', ''));
				    oee.push(myTable.rows[i].cells[8].innerHTML.replace('%', ''));
				}

				var ctx = document.getElementById("prodChart").getContext("2d");

				var myChart = new Chart(ctx, {
					type: 'line',
					data: {
					    labels: label.reverse(),
					    datasets: [{
							  label: "Produktion",
							  data: produktion.reverse(),
							  fill: false,
							  hidden: true,
							  backgroundColor: "#15998E",
							  borderColor: "#15998E",
							  borderCapStyle: 'butt'
						  },
						  {
							  label: "Produktionstid",
							  data: produktionstid.reverse(),
							  fill: false,
							  hidden: true,
							  backgroundColor: "#45B08C",
							  borderColor: "#45B08C",
							  borderCapStyle: 'butt'
					   	   },
						   {
							  label: "Stopptid",
							  data: stopptid.reverse(),
							  fill: false,
							  hidden: true,
							  backgroundColor: "#48BCD1",
							  borderColor: "#48BCD1",
							  borderCapStyle: 'butt'
					    	   },
						   {
							  label: "Kass",
							  data: kass.reverse(),
							  fill: false,
							  hidden: true,
							  backgroundColor: "#C0DCEC",
							  borderColor: "#C0DCEC",
							  borderCapStyle: 'butt'
					        },
						   {
							  label: "Tillgänglighet",
							  data: t.reverse(),
							  fill: false,
							  backgroundColor: "#68BBE3",
							  borderColor: "#68BBE3",
							  borderCapStyle: 'butt'
						   },
						   {
							  label: "Anläggningsutbyte",
							  data: a.reverse(),
							  fill: false,
							  backgroundColor: "#0E86D4",
							  borderColor: "#0E86D4",
							  borderCapStyle: 'butt'
						   },
						   {
							  label: "Kvalite",
							  data: k.reverse(),
							  fill: false,
							  backgroundColor: "#055C9D",
							  borderColor: "#055C9D",
							  borderCapStyle: 'butt'
						   },
						   {
							  label: "OEE",
							  data: oee.reverse(),
							  fill: false,
							  backgroundColor: "#003060",
							  borderColor: "#003060",
							  borderCapStyle: 'butt'
						}]
					},
						options: {
							lineTension: 0.4,
		  					responsive: true,
		  					maintainAspectRatio: false,
							scales: {
								y: {
									ticks: {
										beginAtZero: true,
										min: 10,
										max: 1000,
									}
								}
							}
					   	}

				});

				function onRowClick(tableId, callback) {
					var table = document.getElementById(tableId),
					     rows = table.getElementsByTagName("tr"), i;
					for (i = 0; i < rows.length; i++) {
					     table.rows[i].onclick = function (row) {
					          return function () {
					              callback(row);
					          };
					     }(table.rows[i]);
					}
				};

				onRowClick("myTable", function (row){
					var date = row.getElementsByTagName("td")[0].innerHTML;
					$.ajax({
	    					url: 'statistik.php',
	    					type: 'POST',
	    					data: { "search_dateTAKOEE": date },
	    						success: function(response) {
	    							$("body").html(response);
								$('html,body').scrollTop(0);
							}

					});
				});


			}

			function chartSearch_larm() {
				var label = [];
				var tid = [];
				var antal = [];

				if (myTable.rows[0].cells[1].innerHTML == "Vecka") {
					for ( var i = 1; i < myTable.rows.length; i++ ) {
					    label.push(myTable.rows[i].cells[1].innerHTML);
					    tid.push(myTable.rows[i].cells[2].innerHTML);
					    antal.push(myTable.rows[i].cells[3].innerHTML);
					}
				} else if (myTable.rows[0].cells[0].innerHTML == "Datum") {
					for ( var i = 1; i < myTable.rows.length; i++ ) {
					    label.push(myTable.rows[i].cells[0].innerHTML);
					    tid.push(myTable.rows[i].cells[1].innerHTML);
					    antal.push(myTable.rows[i].cells[2].innerHTML);
					}
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
							  hidden: true,
							  lineTension: 0.4,
							  backgroundColor: "#B9CFE6",
							  borderColor: "#B9CFE6",
							  // borderCapStyle: 'butt'
						  },
						  {
							  label: "Antal",
							  type: "bar",
							  data: antal.reverse(),
							  fill: false,
							  lineTension: 0.4,
							  backgroundColor: "#6496C8",
							  borderColor: "#6496C8",

							  // borderCapStyle: 'butt'
					   }
				   ]
				   }
				});
			}

			function chartProduktion() {
				var label = [];
				for ( var i = 0; i < 24; i++ ) {
					label.push(`kl${i}`);
				}
				kl = [];
				days = 0;
				countWeekdays = 0;
				count = true;
				sum = 0;
				sumWeekdays = 0;
				avg = [];
				weekdays = [];

				for ( var i = 0; i < 24; i++ ) {
					for ( var x = 1; x < myTable.rows.length; x++ ) {

						var row = parseInt(myTable.rows[x].cells[i].innerHTML);
						var weekday = new Date(myTable.rows[x].cells[26].innerHTML).getDay();
						if (i == 0) {
							if (weekday > 0 && weekday < 5) {
								countWeekdays += 1;
							}
						}
						if (row > 0) {
							sum += row;
							days += 1;
							if (weekday > 0 && weekday < 6) {
								sumWeekdays += row;
							}
						}
					}

					avg.push(sum / days);
					kl.push(sum / myTable.rows.length - 1);
					weekdays.push(sumWeekdays / countWeekdays);
					sum = 0;
					days = 0;
					sumWeekdays = 0;
					count = false;

				}
				var ctx = document.getElementById("prodChart").getContext("2d");

				var myChart = new Chart(ctx, {
					type: 'bar',
					data: {
					    labels: label,
					    datasets: [{
							label: "Produktion / aktiva timmar",
							type: "line",
							data: avg,
							backgroundColor: '#6496C8',
							borderColor: '#6496C8',
						    },
						    {
							label: "Produktion vardagar / aktiva vardagar",
							data: weekdays,
							backgroundColor: '#B9CFE6',
							borderColor: '#B9CFE6',
						    }]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						lineTension: 0.4,
						scales: {
							y: {
								// beginAtZero: true
							}
						}
					}
				});
					    // {
						//     label: "Produktion / aktiva dagar",
						//     data: kl,
						//     fill: false,
						//     backgroundColor: 'green',
						//     borderColor: 'green',
						//     borderCapStyle: 'butt'
					    // }



			}

			function chartTAKOEEsearch() {
				var label = [];
				for ( var i = 0; i < 24; i++ ) {
					label.push(`kl${i}`);
				}

				prod = [];
				antal = [];
				stopptid = [];

				for ( var i = 0; i < 24; i++ ) {
					prod.push(prodTable.rows[1].cells[i].innerHTML);
					antal.push(antalTable.rows[1].cells[i].innerHTML);
					stopptid.push(stopptidTable.rows[1].cells[i].innerHTML);
				}
				// console.log(stopptid)
				var ctx = document.getElementById("prodChart").getContext("2d");

				var myChart = new Chart(ctx, {
					type: 'bar',
					data: {
					    labels: label,
					    datasets: [{
						     label: "Antal",
						     type: "line",
						     data: antal,
							backgroundColor: '#6496C8',
							borderColor: '#6496C8',
						    },
						    {
							label: "Produktion",
    							type: "line",
    							data: prod,
							backgroundColor: '#B9CFE6',
							borderColor: '#B9CFE6',
							},
						    {
						     label: "Stopptid",
							type: "bar",
							data: stopptid,
						     backgroundColor: '#0E86D4',
						     borderColor: '#0E86D4',
						    }]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						lineTension: 0.4,
						scales: {
							y: {
								// beginAtZero: true
							}
						}
					}
				});
					    // {
						//     label: "Produktion / aktiva dagar",
						//     data: kl,
						//     fill: false,
						//     backgroundColor: 'green',
						//     borderColor: 'green',
						//     borderCapStyle: 'butt'
					    // }



			}

		</script>

		<?php

			function create_table($headers) {
				//echo "<table id='myTable'>";
				echo "<tr>";

				$i = 0;

				foreach ($headers as $key) {
					if ($key != "Larm") {
						echo "<th onclick='sortTable(".$i.")' class=".$key.">".$key."</th>";
						$i = $i + 1;
					} else {
						echo "<th class=".$key.">".$key."</th>";
					}
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

			function dashboard() {
				$dashboard = true;
				echo "<div style='display: none'>";

				$takoee = TAKOEE($dashboard);
				$headers = array('Datum', 'Tillgänglighet', 'Anläggningsutbyte', 'Kvalite', 'OEE');
				echo "<table style='width:75%;' id='takoee'>";
				create_table($headers);

				foreach ($takoee as $row) {
					$produktion = $row['produktion'];
					$produktionstid = ((strtotime($row["date_end"]) - strtotime($row["date_start"])) / 60) - $row["idle"];
					$stopptid = $row['stopptid'];
					$kass = $row['kass'];
					if ($produktionstid != 0 && $produktion != 0) {
						$tillgänglighet = ($produktionstid - $stopptid) / $produktionstid;
						$anläggningsutbyte = ($produktion / $produktionstid) / (250 / 60);
						$kvalite = ($produktion - $kass) / $produktion;
						$OEE = $tillgänglighet * $anläggningsutbyte * $kvalite;

						echo "<tr><td>" . $row['datum'] . "</td><td>" . bcdiv(($tillgänglighet) * 100, 1, 2) . "%" . "</td><td>" . bcdiv(($anläggningsutbyte) * 100, 1, 2) . "%" .
							 "</td><td>" . bcdiv(($kvalite) * 100, 1, 2) . "%" . "</td><td>" . bcdiv(($OEE) * 100, 1, 2) . "%" . "</td>";

					}
				}


				echo "</tr></table>";
				echo "</div>";

				echo "<div id='wrap' class='wrapper'>
						<p align='center'> Utveckling antal larm </p>
						<div class='utvl'>
							<canvas id='incDashUtv'></canvas>
						</div>
						<div class='utvr'>
							<canvas id='decDashUtv'></canvas>
						</div>";

				echo "<div style='display: none'>";
					$search_date = false;
					utveckling($search_date, $dashboard);
				echo "</div></div>";



				// echo "<canvas id='dashboardTAKOEE' width='200' height='400'></canvas>";
				// echo '<script type="text/javascript">dashboardTAKOEE();</script><br><br>';

			}

			function recent($interval) {

				$exclude_varning = check_exclude();

				$sql = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm
					FROM alarm_tid
					WHERE DATE(message_start) $interval $exclude_varning
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
					ORDER BY count(message_text) DESC;";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					echo "<div id='stats' align='center'>
							<p class='inline' id='antal'></p>
							<p class='inline' id='tid'></p>
						</div>";
					$headers = array("Minuter", "Antal", "Larm");

					echo "<table width='80%' id='myTable'>";
					create_table($headers);

					$sum_antal = 0;
					$sum_tid = 0;

					while($row = $result->fetch_assoc()) {
						echo "<tr><td>" . bcdiv(($row["Tid"]), 1, 1) . "</td><td>" . $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
						$sum_antal += $row["Antal"];
						$sum_tid += $row["Tid"];
					}

					echo "</tr>";
					echo "</table><br><br>";
					$sum_antal;
					echo "<script type='text/javascript'>
							document.getElementById('antal').innerHTML = 'Sum tid: ".bcdiv(($sum_tid / 60), 1, 1)." timmar&nbsp;&nbsp|&nbsp&nbsp;';
							document.getElementById('tid').innerHTML = 'Sum antal: ".$sum_antal."';
						</script><br><br>";
					// echo "</div>";

					} else {
					echo "<p align='center'>Inga resultat</p>";
					}

			}

			function utveckling($search_date, $dashboard) {
				$exclude_varning = " AND message_text NOT LIKE '%Varning%'
					AND message_text NOT LIKE '%Lägg i sista%'
					AND message_text NOT LIKE '%BYT PALL%'
					";
				$first = 'CURRENT_DATE - INTERVAL 7 DAY AND current_date ';
				$second = 'current_date - INTERVAL 15 DAY AND current_date - INTERVAL 8 DAY ';

				if ($dashboard == false) {
					$exclude_varning = check_exclude();
					$from = date('Y-m-d', strtotime($_POST['dateFrom']));
					$to = date('Y-m-d', strtotime($_POST['dateTo']));

					//	echo "Utveckling mellan " . date('Y-m-d', strtotime($_POST['dateFrom'] . '+30 days'));

					$start = new DateTime($_POST['dateFrom'] . '-1 days');
					$end = new DateTime($_POST['dateTo']);
					$diff = $start->diff($end);

				 	if ($search_date) {
					 	$first = '"' . $from . '" AND "' . $to . '"';
					 	$second = '"'. date('Y-m-d', strtotime($_POST['dateFrom'] . '-' . $diff->format('%d') . ' days')) .
						 '" AND "' . date('Y-m-d', strtotime($_POST['dateFrom'] . '-1 days')) . '"';
						 echo "<br><div align='center'>" . date('Y-m-d', strtotime($_POST['dateFrom'] . '-' . $diff->format('%d') . ' days')) . " till " .
						 	date('Y-m-d', strtotime($_POST['dateFrom'] . '-1 days')) . " jämfört med " . $from . " till ". $to . "</div><br>";
					} else {
						echo "<p align='center'>Denna månaden jämfört med förra månaden</p>";
						$first = 'CURRENT_DATE - INTERVAL 30 DAY AND current_date ';
						$second = 'current_date - INTERVAL 61 DAY AND current_date - INTERVAL 31 DAY ';
					}

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
					if ($dashboard == false) {
						echo "<div class='wrapperUtv'>
								<div class='decUtv'>
									<canvas id='incUtveckling'></canvas>
								</div>
								<div class='decUtv'>
									<canvas id='decUtveckling'></canvas>
								</div>
							</div>";
					}
					$headers = array("NewTid", "NewAntal", "OldTid", "OldAntal", "Diff Tid", "Diff Antal", "Larm");
					echo "<table style='width:80%;' id='myTable'>";
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
					if ($dashboard == false) {
						$type = "utveckling";
					} else {
						$type = "dash";
					}
					echo "<script type='text/javascript'>dashboardUtveckling('".$type."');</script><br><br>";
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
					echo "<table style='width:80%;' id='myTable'>";
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
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm
					FROM alarm_tid WHERE DATE(message_start) BETWEEN '".$from."' AND '".$to."' $exclude_varning
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
					ORDER BY (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 DESC";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					echo "<div id='stats' align='center'>
							<p class='inline' id='antal'></p>
							<p class='inline' id='tid'></p>
						</div>";
					$headers = array("Minuter", "Antal", "Larm");
					echo "<table style='width:80%;' id='myTable'>";
					create_table($headers);

					$sum_antal = 0;
					$sum_tid = 0;

					while($row = $result->fetch_assoc()) {
						echo "<tr><td>" . bcdiv(($row["Tid"]), 1, 1) . "</td><td>" . $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
						$sum_antal += $row["Antal"];
						$sum_tid += $row["Tid"];
					}
					echo "</tr>";
					echo "</table><br><br>";
					echo "<script type='text/javascript'>
							document.getElementById('antal').innerHTML = 'Sum tid: ".bcdiv(($sum_tid / 60), 1, 1)." timmar&nbsp;&nbsp|&nbsp&nbsp;';
							document.getElementById('tid').innerHTML = 'Sum antal: ".$sum_antal."';
						</script><br><br>";
				} else {
					echo "<p align='center'>Inga resultat</p>";
				}
			}

			function TAKOEE_search($date) {

				$sql_larm = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm
					FROM alarm_tid WHERE DATE(message_start) = '".$date."' AND message_text NOT LIKE '%Varning%'
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY message_text, message_text HAVING COUNT(message_text) > 0
					ORDER BY count(message_text) DESC";

				$sql_antal = "SELECT (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm,
					hour(message_start) as Hour
					FROM alarm_tid
					WHERE DATE(message_start) = '".$date."' AND message_text NOT LIKE '%Varning%'
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 100
					GROUP BY hour(message_start)";

				$sql_stopptid = "SELECT message_text as Larm, (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid, date(message_start) As Datum, message_start, message_end, hour(message_end) As Hour
						FROM `alarm_tid`
						WHERE date(message_start) = '".$date."' and message_text NOT LIKE '%Varning%'
						AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 100
						group by message_end ORDER BY `alarm_tid`.`message_start` ASC";

				$sql_prod = "SELECT * FROM produktion WHERE date(date_start) = '".$date."' ORDER BY `produktion`.`datum` DESC";
				// echo $sql_stopptid;
				$data_larm = get_data($sql_larm);
				$data_antal = get_data($sql_antal);
				$data_stopptid = get_data($sql_stopptid);
				$data_prod = get_data($sql_prod);

				$larm = $data_larm->fetch_all(MYSQLI_ASSOC);
				$antal = $data_antal->fetch_all(MYSQLI_ASSOC);
				$stopptid = $data_stopptid->fetch_all(MYSQLI_ASSOC);
				$prod = $data_prod->fetch_all(MYSQLI_ASSOC);

				if (count($prod) > 0) {
					$headers = array();
					$headers_antal = array();
						for ($x = 0; $x <= 23; $x++) {
							array_push($headers, $x);
							array_push($headers_antal, $x);
						}
					array_push($headers, 'kassV', 'kassH', 'date_start', 'date_end');

					echo "<div class='prodChart'><canvas id='prodChart'></canvas></div>";
					echo "<div style='display: none'><table style='width:100%;' id='prodTable'>";
					create_table($headers);

					foreach ($prod as $row) {
						$echo = "<tr>";
						foreach ($headers as $key) {
							if ($row[$key] != "") {
								$echo .= "<td>" . $row[$key] . "</td>";
							} else {
								$echo .= "<td>" . "0" . "</td>";
							}

						}
						$echo .= "</tr>";
						$date_start = new DateTime($row['date_start']);
						$date_end = new DateTime($row['date_end']);
						$diff = strtotime($row["date_end"]) - strtotime($row["date_start"]);
						echo $echo;
					}

					echo "</tr>";
					echo "</table></div>";
					echo '<script type="text/javascript">chartTAKOEEsearch();</script>';
				}

				if (count($antal) > 0) {
					echo "<div style='display: none'><table style='width:100%;' id='antalTable'>";
					create_table($headers_antal);

					$echo = "<tr>";
					$found = false;
					for ($i = 0; $i < 24; $i++) {
						foreach($antal as $row) {
							if ($row['Hour'] == $i) {
								$echo .= "<td>" . $row['Antal'] . "</td>";
								$found = true;
								break;
							}
						}
						if (!$found) {
							$echo .= "<td>" . 0 . "</td>";
						}
						$found = false;
					}
					$echo .= "</tr>";
					echo $echo;
					echo "</table></div>";
				}

				if (count($stopptid) > 0) {

					$tid = array();
					$sum_diff = 0;
					$total_tid = 0;

					$d = $stopptid[0]['Hour'];
					foreach ($stopptid as $row) {
						if ($d != $row['Hour']) {
							$tid[$d] = $sum_diff;
							$sum_diff = 0;
						}

						$d = $row['Hour'];
						$start = strtotime($row['message_start']);
						$end = strtotime($row['message_end']);

						$sum_diff += abs($start - $end) / 60;

						if(end($stopptid) !== $row) {
							$tid[$d] = $sum_diff;
						}
					}

					echo "<div style='display: none'><table style='width:100%;' id='stopptidTable'>";
					create_table($headers_antal);

					$echo = "<tr>";
					$found = false;
					for ($i = 0; $i < 24; $i++) {
						foreach($tid as $key => $value) {
							if ($key == $i) {

								$echo .= "<td>" . bcdiv($value, 1, 1) . "</td>";
								$total_tid += $value;
								$found = true;
								break;
							}
						}
						if (!$found) {
							$echo .= "<td>" . 0 . "</td>";
						}
						$found = false;
					}
					$echo .= "</tr>";
					echo $echo;
					echo "</table>";

					echo "<table style='width:100%;' id='larmPerHourTable'>";
					$headers = array("Larm", "Tid", "Hour");
					create_table($headers);
					foreach ($stopptid as $row) {
						echo "<tr><td>" . $row["Larm"] . "</td><td>" . $row["Tid"] . "</td><td>" . $row["Hour"] . "</td></tr>";
					}
					echo "</table></div>";

				}

				if (count($larm) > 0) {
					echo "<div id='stats' align='center'>
							<p class='inline' id='antal'></p>
							<p class='inline' id='tid'></p>
						</div>";
					$headers = array("Minuter", "Antal", "Larm");
					echo "<table style='width:80%;' id='myTable'>";
					create_table($headers);

					$sum_antal = 0;
					$sum_tid = 0;

					foreach ($larm as $row) {
						echo "<tr><td>" . bcdiv(($row["Tid"]), 1, 1) . "</td><td>" . $row["Antal"] . "</td><td>" . $row["Larm"] . "</td></tr>";
						$sum_antal += $row["Antal"];
						$sum_tid += $row["Tid"];
					}
					echo "</tr>";
					echo "</table>";
					echo "<script type='text/javascript'>
							document.getElementById('antal').innerHTML = 'Sum tid: ".bcdiv(($total_tid / 60), 1, 1)." timmar&nbsp;&nbsp|&nbsp&nbsp;';
							document.getElementById('tid').innerHTML = 'Sum antal: ".$sum_antal."';
						</script><br><br>";
				}
			}

			function search_larm($search_larm) {
				$sql_days = "SELECT
					date(message_start) as datum,
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) As Antal,
					message_text AS Larm
					FROM alarm_tid
					WHERE message_text = '".$search_larm."'
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY DATE(message_start) DESC";

				$sql_weeks = "SELECT
					yearweek(message_start, 1) as yearweek,
					YEAR(message_start) As Year,
					WEEKOFYEAR(message_start) As Vecka,
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) As Antal,
					message_text AS Larm
					FROM alarm_tid
					WHERE message_text = '".$search_larm."'
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY YEARWEEK(message_start, 1) DESC";

				$sql_range = "SELECT yearweek(message_start, 1) as yearweek, YEAR(message_start) As Year, WEEKOFYEAR(message_start) As Week
					from alarm_tid
					group by yearweek(message_start, 1) DESC";


				$result = get_data($sql_weeks);
				$result_dates = get_data($sql_range);
				echo "<div class='wrapper'>";
				if ($result->num_rows > 0) {
					$headers = array("År", "Vecka", "Minuter", "Antal", "Larm");

					echo "<table style='width:65%;' id='myTable'>";
					create_table($headers);
					echo "<canvas id='line-chart'></canvas>";

					$data = $result->fetch_all(MYSQLI_ASSOC);
					$range = $result_dates->fetch_all(MYSQLI_ASSOC);

					$i = 0;

					foreach($range as $dates) {
						if ($dates["yearweek"] != $data[$i]["yearweek"]) {
							echo "<tr><td>" . $dates["Year"] . "</td><td>" . $dates["Week"] . "</td><td>" . 0 . "</td><td>" .
								0 . "</td><td>" . $search_larm . "</td></tr>";
						} elseif ($dates["yearweek"] = $data[$i]["yearweek"]) {
							echo "<tr><td>" . $data[$i]["Year"] . "</td><td>" . $data[$i]["Vecka"] . "</td><td>" . bcdiv(($data[$i]["Tid"]), 1, 1) . "</td><td>" .
								$data[$i]["Antal"] . "</td><td>" . $data[$i]["Larm"] . "</td></tr>";

							if ($i < count($data) - 1) {$i++;}
						}
					}

					echo "</tr>";
					echo "</table><br><br>";
					// $average = array_sum($average)/count($average);
					// echo $average;

					echo '<script type="text/javascript">chartSearch_larm();</script>';
				} else {
					echo "<p align='center'>Inga resultat</p>";
				}
				echo "</div>";
			}

			function search_larm2($search_larm) {
				$sql_days = "SELECT
					date(message_start) as Datum,
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) As Antal,
					message_text AS Larm
					FROM alarm_tid
					WHERE message_text = '".$search_larm."'
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 60
					GROUP BY DATE(message_start) DESC";

				$sql_range = "SELECT date(message_start) as Datum
					from alarm_tid
					group by date(message_start) DESC";

				$result = get_data($sql_days);
				$result_dates = get_data($sql_range);
				// $period = new DatePeriod(
				//      new DateTime('2021-07-06'),
				//      new DateInterval('P1D'),
				//      new DateTime(date("Y-m-d"))
				// );
				//
				// $range = [];
				// foreach ($period as $dates) {
    				// 	array_push($range, $dates->format('Y-m-d'));
				// }
				// $range = array_reverse($range);



				// echo $sql_days;

				if ($result->num_rows > 0) {
					$headers = array("Datum", "Minuter", "Antal", "Larm");
					echo "<div class='wrapper'>";
					echo "<table style='width:65%;' id='myTable'>";
					create_table($headers);
					echo "<canvas id='line-chart' width='200' height='400'></canvas>";

					$data = $result->fetch_all(MYSQLI_ASSOC);
					$range = $result_dates->fetch_all(MYSQLI_ASSOC);

					$i = 0;

					foreach($range as $dates) {
						if ($dates["Datum"] != $data[$i]["Datum"]) {
							echo "<tr><td>" . $dates["Datum"] . "</td><td>" . 0 . "</td><td>" .
								0 . "</td><td>" . $search_larm . "</td></tr>";
						} elseif ($dates["Datum"] == $data[$i]["Datum"]) {
							// echo $data[$i]["Datum"] . "|";
							echo "<tr><td>" . $data[$i]["Datum"] . "</td><td>" . bcdiv(($data[$i]["Tid"]), 1, 1) . "</td><td>" .
								$data[$i]["Antal"] . "</td><td>" . $data[$i]["Larm"] . "</td></tr>";

							if ($i < count($data) - 1) {$i++;}
						}
					}

					echo "</tr>";
					echo "</table><br><br>";
					// $average = array_sum($average)/count($average);
					// echo $average;
					echo "</div>";
					echo '<script type="text/javascript">chartSearch_larm();</script>';
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
					WHERE message_text LIKE '%".$search_larm."%' AND date(message_start) >= current_date - INTERVAL 30 DAY
					ORDER BY message_start DESC;";

				$result = get_data($sql);

				if ($result->num_rows > 0) {
					$headers = array('Datum', 'Tid', 'Larm');
					echo "<br><table style='width:80%;' id='myTable'>";
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
							echo "<td>" .bcdiv(($Tid), 1, 1). "</td>";
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
				echo "<br><div align='center'>Från 2021-07-06 Till " . date("Y-m-d") . "</div><br>";
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
					echo "<table style='width:80%;' id='myTable'>";
					create_table($headers);
					echo "<th style='width:10%;'>Larm</th>";

					$x = 2;
					while($row = $result->fetch_assoc()) {
						if ($x == 11) {$stn = "Övriga";} else {$stn = "Station " . $x;}
						// echo "<tr><td>" . $row["Tid"] . "</td><td>" . $row["Antal"] . "</td><td>" . $stn . "</td></tr>";
						echo "<tr><td>" . $row["Tid"] . "</td><td>" . $row["Antal"] . "</td><td>" . $stn . "</td></tr>";
						$x++;
					}

					echo "</table><br><br>";

				} else {
					echo "<p align='center'>Inga resultat</p>";
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

					// echo	"<div class='wrapperProdchart'>
					// 		<canvas id='line-chart'></canvas>
					// 	</div>";
					echo "<div class='wrapper'>";
					echo "<div class='prodChart'><canvas id='prodChart'></canvas></div>";
					echo "<table style='width:100%;' id='myTable'>";
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
					echo "</div>";
					echo '<script type="text/javascript">chartProduktion();</script>';
					} else {
					echo "<p align='center'>Inga resultat</p>";
					}
			}

			function TAKOEE($dashboard) {
				$max_prod = 250 / 60;

				$sum_prod = "(select(";
				for ($x=0; $x <= 22; $x++) {
					$sum_prod .= "COALESCE(produktion." . $x . ", 0) + ";
				}
				$sum_prod .= "COALESCE(produktion.23, 0)))";

				$sql = "select datum, ".$sum_prod." As produktion, (select(kassV + kassH)) As kass, date_start, date_end, idle
					FROM produktion where date_start IS NOT NULL
					ORDER BY produktion.datum DESC";

				$sql_stopptid = "SELECT date(message_start) As Datum, message_start, message_end, count(message_end)
						FROM `alarm_tid`
						WHERE date(message_start) > '2022-02-05' and message_text NOT LIKE '%Varning%'
						AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 100
						group by message_end ORDER BY `alarm_tid`.`message_start` DESC";

				// $sql = "select produktion.datum,
				// 		SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end)) / 60 as stopptid,
				// 		".$sum_antal." As produktion,
				// 		(select(produktion.kassV + produktion.kassH)) As kass,
				// 		produktion.date_start,
				// 		produktion.date_end,
				// 		produktion.idle
				// 		from produktion
				// 		left join alarm_tid
				// 		on produktion.datum = date(alarm_tid.message_start) AND alarm_tid.message_text NOT LIKE '%Varning%'
				// 		AND (SELECT SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end))) / 60 < 100
				// 		WHERE alarm_tid.message_start > '2022-02-05'
				// 		GROUP by date(alarm_tid.message_start) DESC;";

				// $sql2 = "SELECT date(message_id) as datum, (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid, count(message_id) as count
				// 	FROM `alarm_tid`
				// 	WHERE date(message_start) > '2022-02-05' AND message_text NOT LIKE '%Varning%' AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 100
				// 	group by message_id having count(message_id) > 1 ORDER BY `datum` DESC";

				$result = get_data($sql);
				$stopptid = get_data($sql_stopptid);

				$data = $result->fetch_all(MYSQLI_ASSOC);
				$stopptid = $stopptid->fetch_all(MYSQLI_ASSOC);

				if ($dashboard == false) {
					echo "<div style='text-align: center;'>
							<div style='display: inline-block; text-align: left;'>
								<p>Tillgänglighet = (Produktionstid - Stopptid) / Produktionstid</p>
								<p>Anläggningsutbyte = (Produktion / Produktionstid) / max produktion</p>
								<p>Kvalite = (Produktion - Kass) / Produktion</p>
								<p>OEE = Tillgänglighet * Anläggningsutbyte * Kvalite</p>
							</div>
						</div>";

					$headers = array('Datum', 'Produktion', 'Produktionstid', 'Stopptid', 'Kass', 'Tillgänglighet', 'Anläggningsutbyte', 'Kvalite', 'OEE');
					echo "<div class='prodChart'><canvas id='prodChart'></canvas></div>";
					echo "<table style='width:100%;' id='myTable'>";
					create_table($headers);
					echo "<br>";


					$tid = array();
					$sum_diff = 0;

					$d = $stopptid[0]['Datum'];
					foreach ($stopptid as $row) {
						if ($d != $row['Datum']) {
							$tid[$d] = $sum_diff;
							$sum_diff = 0;
						}

						$d = $row['Datum'];
						$start = strtotime($row['message_start']);
						$end = strtotime($row['message_end']);

						$sum_diff += abs($start - $end) / 60;

						if(end($stopptid) !== $row) {
							$tid[$d] = $sum_diff;
						}
					}

					foreach ($data as $row ) {
						$produktion = $row['produktion'];
						$produktionstid = ((strtotime($row["date_end"]) - strtotime($row["date_start"])) / 60) - $row["idle"];
						$stopptid = $tid[$row['datum']];
						$kass = $row['kass'];

						if ($produktionstid != 0 && $produktion != 0) {
							$tillgänglighet = ($produktionstid - $stopptid) / $produktionstid;
							$anläggningsutbyte = ($produktion / $produktionstid) / $max_prod;
							$kvalite = ($produktion - $kass) / $produktion;
							$OEE = $tillgänglighet * $anläggningsutbyte * $kvalite;

							echo "<tr><td class='search_date'>" . $row['datum'] . "</td><td>" . $produktion . "</td><td>" . bcdiv(($produktionstid / 60),1, 2)  . "</td><td>" . bcdiv(($stopptid / 60),1, 2) . "</td><td>" .
								$kass . "</td><td>" . bcdiv(($tillgänglighet) * 100, 1, 2) . "%" . "</td><td>" . bcdiv(($anläggningsutbyte) * 100, 1, 2) . "%" .
								 "</td><td>" . bcdiv(($kvalite) * 100, 1, 2) . "%" . "</td><td>" . bcdiv(($OEE) * 100, 1, 2) . "%" . "</td>";
						}
					}
					echo "</tr></table>";
					echo '<script type="text/javascript">chartTAKOEE();</script><br><br>';
				} else {
					return $data;
					echo "true";
				}

			}


			if (isset($_POST['today'])) {
				echo "<div class='wrapperRecent'>";
				echo "<p class='recentPeriod' align='center'>Idag</p>";
				$interval = " = CURRENT_DATE() ";
				recent($interval);
				echo "</div>";

			} else if (isset($_POST['last_week'])) {
				echo "<div class='wrapperRecent'>";
				echo "<p class='recentPeriod' align='center'>Senaste 7 dagar</p>";
				$interval = " >= current_date - INTERVAL 7 DAY";
				recent($interval);
				echo "</div>";

			} else if (isset($_POST['last_month'])) {
				echo "<div class='wrapperRecent'>";
				echo "<p class='recentPeriod' align='center'>Senaste 30 dagar</p>";
				$interval = " >= current_date - INTERVAL 30 DAY";
				recent($interval);
				echo "</div>";

			} else if (isset($_POST['avg_time'])) {
				echo "<div class='wrapperRecent'>";
				echo "<p align='center'>Genomsnittlig stopptid per larm</p>";
				average();
				echo "</div>";

			} else if (isset($_POST['search_date'])) {
				echo "<div class='wrapperRecent'>";
				$from = date('Y-m-d', strtotime($_POST['dateFrom']));
				$to = date('Y-m-d', strtotime($_POST['dateTo']));
				echo "<p align='center'>Från: $from  Till:  $to </p>";
				search_date($from, $to);
				echo "</div>";

			} else if (isset($_POST['search_dateTAKOEE'])) {
				echo "<div class='wrapperRecent'>";
				$date = $_POST['search_dateTAKOEE'];
				// $from = $_POST['takoeeDateSearch_from'];
				// $from = $_POST['takoeeDateSearch_to'];
				echo "<p align='center'>Datum: ".$date."</p>";
				TAKOEE_search($date);
				echo "</div>";

			} else if (isset($_POST['utveckling'])) {
				$search_date = false;
				$dashboard = false;
				echo "<div class='wrapper'>";
				utveckling($search_date, $dashboard);
				echo "</div>";

			} else if (isset($_POST['search_utveckling'])) {
				$search_date = true;
				$dashboard = false;
				echo "<div class='wrapper'>";
				utveckling($search_date, $dashboard);
				echo "</div>";

			} else if (isset($_POST['search_larm'])) {
				$search_larm = ltrim(filter_input(INPUT_POST, 'search_form'));
				// echo "<p align='center'>" . $search_larm . "</p>";
				search_larm($search_larm);

			} else if (isset($_POST['ajax_larm'])) {
				$search_larm = $_POST['ajax_larm'];
				// echo "<p align='center'>" . $search_larm . "</p>";
				search_larm2($search_larm);

			} else if (isset($_POST['search_all'])) {
				$search_larm = ltrim(filter_input(INPUT_POST, 'search_form'));
				echo "<div class='wrapperRecent'>";
				search_all($search_larm);
				echo "</div>";

			} else if (isset($_POST['overview'])) {
				echo "<div class='wrapperRecent'>";
					overview();

			} else if (isset($_POST['produktion'])) {
				produktion();

			} else if (isset($_POST['TAKOEE'])) {
				$dashboard = false;
				echo "<div class='wrapper'>";
				TAKOEE($dashboard);
				echo "</div>";
			} else {
				dashboard();

		}

		?>

	</body>
</hmtl>
