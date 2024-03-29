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
	<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
	</head>

	<script src="alarm_old/jquery.min.js"></script>

	<body>
		<nav class="navbar" align="center">

				<form class="form_nav" method="post">
					<span class="buttons">
						<button data-animation="1" type="submit" name="today" class="link">Larm</button>
						<button type="submit" name="TAKOEE" class="link">TAKOEE</button>
						<button type='submit' name='produktion' class="link">Produktion</button>
						<button type='submit' name='produktionstid' class="link">Produktiontid</button>
						<button type="submit" name="utveckling" class="link">Utveckling</button>
						<button type="submit" name="pallet_fel" class="link">Pallet fel</button>
					 	<button type="submit" name="overview" class="link">Översikt</button>
				</form>

				<form class="search" method="post">
					<input type="text" name="search_form" placeholder="Sök efter larm" class="input">
					<button type="submit" name="search_larm" class="button">
						<i class="gg-search"></i>
					</button>

				</form>
				<!-- <input type="hidden" name="inc" value="0"/> -->
				<!-- <input type="checkbox" name="inc" value="1"> -->
				<!-- <label for="inc"> Inkludera varning</label> -->


		</nav>
		<!-- <br>
		<br> -->
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
					if (myTable.rows.length >= 5) {
						var rows = 5;
					} else {
						var rows = myTable.rows.length - 1;
					}

				} else if (type == "utveckling") {
					decCanvas = "decUtveckling";
					incCanvas = "incUtveckling";
					if (myTable.rows.length >= 20) {
						var rows = 20;
					} else {
						var rows = myTable.rows.length - 1;
					}
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
					} else {
						incLarm.push("-");
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

				var sum_produktion = 0;
				var sum_produktionstid = 0;
				var sum_stopptid = 0;
				var sum_stillestånd = 0;
				var sum_prod = 0;



				for ( var i = 1; i < myTable.rows.length; i++ ) {
				    label.push(myTable.rows[i].cells[0].innerHTML);
				    produktion.push(myTable.rows[i].cells[1].innerHTML);
				    produktionstid.push(myTable.rows[i].cells[2].innerHTML * 60);
				    stopptid.push(myTable.rows[i].cells[3].innerHTML * 60);
				    kass.push(myTable.rows[i].cells[5].innerHTML);
				    t.push(myTable.rows[i].cells[8].innerHTML.replace('%', ''));
				    a.push(myTable.rows[i].cells[9].innerHTML.replace('%', ''));
				    k.push(myTable.rows[i].cells[10].innerHTML.replace('%', ''));
				    oee.push(myTable.rows[i].cells[11].innerHTML.replace('%', ''));

				    sum_produktion += parseFloat(myTable.rows[i].cells[1].innerHTML);
				    sum_produktionstid += parseFloat(myTable.rows[i].cells[2].innerHTML);
				    sum_stopptid += parseFloat(myTable.rows[i].cells[3].innerHTML);
				    // sum_stillestånd += parseInt(myTable.rows[i].cells[4].innerHTML);


				}

				// document.getElementById("Produktion").textContent= "Produktion: " + (sum_produktion).toFixed(2);
				// document.getElementById("Produktionstid").textContent= "Produktionstid: " + (sum_produktionstid / sum_produktionstid).toFixed(2);
				document.getElementById("Prod/h").textContent= "Prod: " + (sum_produktion  / sum_produktionstid).toFixed(2);
				document.getElementById("Stopptid").textContent= "Stopptid: " + ((sum_stopptid  / sum_produktionstid) * 60).toFixed(2) + " min";

				// document.getElementById("Produktion").textContent= "Produktion: " + (sum_produktion  / (myTable.rows.length - 1)).toFixed(2);
				// document.getElementById("Produktionstid").textContent= "Produktionstid: " + (sum_produktionstid  / (myTable.rows.length - 1)).toFixed(2);
				// document.getElementById("Stopptid").textContent= "Stopptid: " + (sum_stopptid  / (myTable.rows.length - 1)).toFixed(2);
				// document.getElementById("Stopptid").textContent= "Stopptid: " + (sum_stopptid).toFixed(2);
				// // document.getElementById("Stillestånd").textContent= sum_stillestånd  / (myTable.rows.length - 1);
				// document.getElementById("Prod/h").textContent= "Prod/h: " + (sum_prod  / (myTable.rows.length - 1)).toFixed(0);
				console.log(sum_produktion, sum_produktionstid);
				// document.getElementById("Produktionstid").textContent= "Produktionstid: " + (sum_produktionstid  / (myTable.rows.length - 1)).toFixed(2);
				// document.getElementById("Stillestånd").textContent= sum_stillestånd  / (myTable.rows.length - 1);


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
							  hidden: true,
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
							  hidden: true,
							  backgroundColor: "#003060",
							  borderColor: "#003060",
							  borderCapStyle: 'butt'
						}]
					},
						options: {
							lineTension: 0.6,
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
					for (i = 1; i < rows.length; i++) {
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

			function produktionstid() {

				var label = [];
				var produktion = [];
				var produktionstid = [];
				var stopptid = [];
				var kass = [];
				var avg_stor = [];
				var avg_normal = [];
				var avg_liten = [];

				var date_stor = [];

				var sum_produktion = 0;
				var sum_produktionstid = 0;
				var sum_stopptid = 0;
				var sum_stillestånd = 0;
				var sum_prod = 0;

				var old_arti = "";
				var old_datum = "";
				var old_prod = "";


				for ( var i = 1; i < myTable.rows.length; i++ ) {
				    label.push(myTable.rows[i].cells[0].innerHTML);
				    produktion.push(myTable.rows[i].cells[2].innerHTML);
				    produktionstid.push(myTable.rows[i].cells[3].innerHTML * 60);
				    stopptid.push(myTable.rows[i].cells[4].innerHTML * 60);
				    kass.push(myTable.rows[i].cells[5].innerHTML);

				     var arti = myTable.rows[i].cells[1].innerHTML;
					var datum = myTable.rows[i].cells[0].innerHTML;
					var prod = myTable.rows[i].cells[6].innerHTML;

					const lastDigit1Str = String(arti).slice(-1);
					const artikelnummer = Number(lastDigit1Str);

					// console.log(old_arti, artikelnummer);

					function data(list, artikel, prod) {
						console.log(list)
						if (artikelnummer == artikel) {
							// if (old_arti != artikelnummer) {
							// 	list.push({x: old_datum, y: old_prod},);
							// }

							if (old_arti == artikelnummer && old_datum == datum) {
								var prod = (Number(prod) + Number(myTable.rows[i-1].cells[6].innerHTML)) / 2;
								list.pop();
								list.push({x: datum, y: prod},);
							} else {
								console.log(prod);
								list.push({x: datum, y: prod},);
							}

						} else {
						    list.push({x: NaN, y: NaN},);
						}


					}

					data(avg_stor, 1, prod);
					data(avg_normal, 3, prod);
					data(avg_liten, 5, prod);


					old_arti = artikelnummer;
					old_prod = prod;
					old_datum = datum;

					// console.log(avg_stor)

					// if (artikelnummer == 1) {
					// 	if (old_arti != artikelnummer) {
					// 		avg_stor.push({x: old_datum, y: old_prod},);
					// 	}
					//
					// 	if (old_arti == artikelnummer && old_datum == datum) {
					// 		var prod = (Number(prod) + Number(myTable.rows[i-1].cells[6].innerHTML)) / 2;
					// 		avg_stor.pop();
					// 		avg_stor.push({x: datum, y: prod},);
					// 	} else {
					// 		avg_stor.push({x: datum, y: prod},);
					// 	}
				     // } else {
					//     avg_stor.push({x: NaN, y: NaN},);
				     // }
					//
					// if (artikelnummer == 3) {
					// 	// if (old_arti != artikelnummer) {
					// 	// 	avg_normal.push({x: old_datum, y: old_prod},);
					// 	// }
					//
					// 	if (old_arti == artikelnummer && old_datum == datum) {
					// 		var y = (Number(prod) + Number(myTable.rows[i-1].cells[6].innerHTML)) / 2;
					// 		avg_normal.pop();
					// 		avg_normal.push({x: datum, y: y},);
					// 	} else {
					// 		avg_normal.push({x: datum, y: prod},);
					// 	}
					//
				     // } else {
					//     avg_normal.push({x: NaN, y: NaN},);
				     // }
					//
					// if (artikelnummer == 5) {
					// 	// if (old_arti != artikelnummer) {
					// 	// 	avg_liten.push({x: old_datum, y: old_prod},);
					// 	// }
					//
					// 	if (old_arti == artikelnummer && old_datum == datum) {
					// 		var y = (Number(prod) + Number(myTable.rows[i-1].cells[6].innerHTML)) / 2;
					// 		avg_liten.pop();
					// 		avg_liten.push({x: datum, y: y},);
					// 	} else {
					// 		avg_liten.push({x: datum, y: prod},);
					// 	}
					//
				     // } else {
					//     avg_liten.push({x: NaN, y: NaN},);
				     // }




				     // if ((arti == 10231) || (arti == 10271) || (arti == 10251)) {
					// 	if ((datum == myTable.rows[i-1].cells[6].innerHTML) && ((arti == 10231) || (arti == 10271) || (arti == 10251))) {
					// 		var y = (prod + myTable.rows[i-1].cells[6].innerHTML) / 2;
					// 		avg_stor.pop();
					// 		avg_stor.push({x: datum, y: y},);
					// 	}
					//     avg_stor.push({x: datum, y: prod},);
				     // } else {
					//     avg_stor.push({x: NaN, y: NaN},);
				     // }
					//
				     // if((arti == 10233) || (arti == 10273) || (arti == 10253)) {
					//     avg_normal.push({x: datum, y: prod},);
				     // } else {
					//     avg_normal.push({x: NaN, y: NaN},);
				     // }
					//
				     // if ((arti == 10235) || (arti == 10275) || (arti == 10255)) {
					//     avg_liten.push({x: datum, y: prod},);
				     // } else {
					//     avg_liten.push({x: NaN, y: NaN},);
				     // }



				    sum_produktion += parseFloat(myTable.rows[i].cells[1].innerHTML);
				    sum_produktionstid += parseFloat(myTable.rows[i].cells[2].innerHTML);
				    sum_stopptid += parseFloat(myTable.rows[i].cells[3].innerHTML);
				    // sum_stillestånd += parseInt(myTable.rows[i].cells[4].innerHTML);
				}

				// console.log(avg_stor[avg_stor.length - 1]);
				 // date_stor.push({x: myTable.rows[4].cells[6].innerHTML, y: myTable.rows[4].cells[0].innerHTML})
				// var list = [];
				//
				// for (i = 1; i < 10; i++ ) {
				// 	list.push({x: "2016-12-26", y: i},)
				// }
				// list.push({x: "2016-12-26", y: 2})
				// console.log(list)

				document.getElementById("Prod/h").textContent= "Prod: " + (sum_produktion  / sum_produktionstid).toFixed(2);
				document.getElementById("Stopptid").textContent= "Stopptid: " + ((sum_stopptid  / sum_produktionstid) * 60).toFixed(2) + " min";
				// console.log(date_stor);



				var ctx = document.getElementById("prodChart").getContext("2d");

				// var myChart = new Chart(ctx, {
				// 	type: 'line',
				// 	data: {
				// 	    labels: label.reverse(),
				// 	    datasets: [{
				// 			label: "Stor/h",
				// 			data: avg_stor.reverse(),
				// 			fill: false,
				// 			hidden: false,
				// 			backgroundColor: "red",
				// 			borderColor: "red",
				// 			borderCapStyle: 'butt'
				// 		},
				// 		{
				// 			label: "Normal/h",
				// 			data: avg_normal.reverse(),
				// 			fill: false,
				// 			hidden: false,
				// 			backgroundColor: "blue",
				// 			borderColor: "blue",
				// 			borderCapStyle: 'butt'
				// 		},
				// 	     {
				// 			label: "Liten/h",
				// 			data: avg_liten.reverse(),
				// 			fill: false,
				// 			hidden: false,
				// 			backgroundColor: "green",
				// 			borderColor: "green",
				// 			borderCapStyle: 'butt'
				// 		}]
				// 	},
				// 		options: {
				// 			lineTension: 0.4,
		  		// 			responsive: true,
		  		// 			maintainAspectRatio: false,
				// 			scales: {
				// 				y: {
				// 					ticks: {
				// 						beginAtZero: true,
				// 						min: 10,
				// 						max: 1000,
				// 					}
				// 				}
				// 			}
				// 	   	}
				//
				// });


				const data = {
				  datasets: [{
					 label: 'Stor',
					 borderColor: 'red',
					 data: avg_stor,
				 },  {
				     label: 'normal',
				     borderColor: 'blue',
				     data: avg_normal,
			     },	{
				    label: 'liten',
				    borderColor: 'green',
				    data: avg_liten,
			    }],
				};

				const config = {
				  	type: 'line',
				  	data: data,
				  	options: {
						lineTension: 0.6,
						spanGaps: false,
				     	responsive: true,
						maintainAspectRatio: false,

				     scales: {
						x: {
							parsing: false,
						     type: 'time',
						     time: {
						    		unit: 'day'
					   		}
					 	}
				     }
				  },
				};

				const myChart = new Chart(
				  document.getElementById('prodChart'),
				  config
				);

				function onRowClick(tableId, callback) {
					var table = document.getElementById(tableId),
					     rows = table.getElementsByTagName("tr"), i;
					for (i = 1; i < rows.length; i++) {
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
						var weekday = new Date(myTable.rows[x].cells[27].innerHTML).getDay();
						if (i == 0) {
							if (weekday > 0 && weekday < 4) {
								countWeekdays += 1;
							}
						}
						if (row > 0) {
							sum += row;
							days += 1;
							if (weekday > 0 && weekday < 5) {
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
						    // {
							// label: "Produktion / aktiva dagar",
							// data: kl,
							// fill: false,
							// type: "line",
							// backgroundColor: '#0E86D4',
							// borderColor: '#0E86D4',
							// borderCapStyle: 'butt'
							// },
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
			}

			function chartPallet() {
				var label = [];
				for ( var i = 1; i <= 15; i++ ) {
					label.push(`pallet ${i}`);
				}
				datum = [];
				artikelnummer = [];
				pallet = [];
				palletIdag = [];
				sum = 0;


				for ( var i = 0; i <= 14; i++ ) {
					for ( var x = 1; x < myTable.rows.length; x++ ) {
						var row = parseInt(myTable.rows[x].cells[i].innerHTML);
						if (row > 0) {
							sum += row;

						}
					}
					pallet.push(sum);
					sum = 0;
				}

				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1;
				var yyyy = today.getFullYear();

				if(dd<10) {
				    dd='0'+dd;
				}

				if(mm<10) {
				    mm='0'+mm;
				}

				today = yyyy+'-'+mm+'-'+dd;

				for ( var i = 0; i <= 14; i++ ) {
						for ( var x = 1; x < myTable.rows.length; x++ ) {
							if (myTable.rows[x].cells[16].innerHTML == "2022-08-18") {
								var row = parseInt(myTable.rows[x].cells[i].innerHTML);
								if (row > 0) {
									sum += row;
								}
							}
						}
					palletIdag.push(sum);
					sum = 0;
				}
				console.log(Math.max(...palletIdag) + 10);
				var ctx = document.getElementById("prodChart").getContext("2d");
				// console.log(pallet);
				var myChart = new Chart(ctx, {
					type: 'bar',
					data: {
					    labels: label,
					    datasets: [{
							label: "Pallet fel",
							type: "bar",
							data: pallet,
							hidden: false,
							backgroundColor: '#B9CFE6',
							borderColor: '#B9CFE6',
						},
						{
 							label: "Pallet fel idag",
 							type: "bar",
 							data: palletIdag,
 							backgroundColor: '#6496C8',
 							borderColor: '#6496C8',
 						    }]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						lineTension: 0.4,
						scales: {
							y: {
								//
								// Max: 100,
								// Min: 1

							}
						}
					}
				});
			}

			function chartTAKOEEsearch() {
				var label = [];
				var index;
				for ( var i = 0; i < 24; i++ ) {
					label.push(`kl${i}`);
				}

				prod = [];
				antal = [];
				stopptid = [];
				larmPerHour = [];

				for ( var i = 0; i < 24; i++ ) {
					prod.push(prodTable.rows[1].cells[i].innerHTML);
					antal.push(antalTable.rows[1].cells[i].innerHTML);
					stopptid.push(stopptidTable.rows[1].cells[i].innerHTML);
				}

				var ctx = document.getElementById("prodChart").getContext("2d");

				var myChart = new Chart(ctx, {
					type: 'bar',
					data: {
					    labels: label,
					    datasets: [{
						    label: "Produktion",
						    type: "bar",
						    data: prod,
						    backgroundColor: '#B9CFE6',
						    borderColor: '#B9CFE6',

						    },
						    {
							label: "Antal",
    						     type: "bar",
    						     data: antal,
    							backgroundColor: '#6496C8',
    							borderColor: '#6496C8',
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
						plugins: {
							tooltip: {
								callbacks: {
									afterBody: function(context) {
										if (index == context[0].dataIndex) {
												return larmPerHour;
										} else {
											index = context[0].dataIndex
											larmPerHour = ["Tid, Larm"];
											for (var i = 1; i < larmPerHourTable.rows.length; i++) {
												if (larmPerHourTable.rows[i].cells[2].innerHTML == context[0].dataIndex) {
													larmPerHour.push([
														larmPerHourTable.rows[i].cells[0].innerHTML,
														larmPerHourTable.rows[i].cells[1].innerHTML,
													]);
												}
											}
											if (larmPerHour.length > 0) {
												larmPerHour.sort(function(a, b){return b[0] - a[0]});
												console.log(larmPerHour)
												console.log(context[0].dataIndex)
												// return `Tid: ${larmPerHour[0][0]} | Larm: ${larmPerHour[0][1]}Tid: ${larmPerHour[1][0]} | Larm: ${larmPerHour[1][1]}`;
												return larmPerHour;
											}
										}
									}
								}
							}
						},
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
				// if ($_POST['inc'] == '0') {
					$exclude_varning = " AND message_text NOT LIKE '%Varning%'";
				// } else {
					// $exclude_varning = "";
				// }
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
					if (isset($row['stopptid'])) {
						$stopptid = $row['stopptid'];
					} else {
						$stopptid = 0;
					}
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

			function larm() {
				$exclude_varning = check_exclude();
				$today = date('Y-m-d');

				if(isset($_POST['modell'])) {
					$last_modell = $_POST['modell'];
				} else {
					$last_modell = "Välj modell";
				}
				echo "<div class='wrapperRecent'>";
				echo "<div class='date_header'>

					<form class='search_date' method='post'>
					<input type='date' class='date_form' name='larm_dateFrom' value='".$today."'>
					<p class='fromto'>-</p>
					<input type='date' class='date_form' name='larm_dateTo' value='".$today."'>

					<select name='modell' class='search_model'>
					    <option value='".$last_modell."'>".$last_modell."</option>
					    <option value='Allt'>Allt</option>

					    <option value='Stor'>Stor</option>
					    <option value='Normal'>Normal</option>
					    <option value='Liten'>Liten</option>

					    <option value='Stor Gul'>Stor Gul</option>
					    <option value='Stor Vit'>Stor Vit</option>
					    <option value='Stor Alu'>Stor Alu</option>

					    <option value='Normal Gul'>Normal Gul</option>
					    <option value='Normal Vit'>Normal Vit</option>
					    <option value='Normal Alu'>Normal Alu</option>

					    <option value='Liten Gul'>Liten Gul</option>
					    <option value='Liten Vit'>Liten Vit</option>
					    <option value='Liten Alu'>Liten Alu</option>
					</select>

					<button type='submit' name='search_date' class='button_date'>
						<i class='gg-search'></i>
					</button>

    					</div>";
				echo "<div class='stats'>
						<p class='inline' id='antal'></p>
						<p class='inline' id='tid'></p>
					</div>";

				$sql = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm
					FROM alarm_tid
					WHERE DATE(message_start) = CURRENT_DATE() $exclude_varning
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 120
					GROUP BY message_text
					ORDER BY count(message_text) DESC;";

				$result = get_data($sql);


				if ($result->num_rows > 0) {
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
				echo "</div>";
			}

			function search_date() {
				$exclude_varning = check_exclude();

				$today = date('Y-m-d');
				$from = date('Y-m-d', strtotime($_POST['larm_dateFrom']));
				$to = date('Y-m-d', strtotime($_POST['larm_dateTo']));

				if(isset($_POST['modell'])) {
					$last_modell = $_POST['modell'];
				} else {
					$last_modell = "Välj modell";
				}

				echo "<div class='wrapperRecent'>";
				echo "<div class='date_header'>

					<form class='search_date' method='post'>
						<input type='date' class='date_form' name='larm_dateFrom' value='".$from."'>
						<p class='fromto'>-</p>
						<input type='date' class='date_form' name='larm_dateTo' value='".$to."'>


						<select name='modell' class='search_model'>
						    <option value='".$last_modell."'>".$last_modell."</option>
						    <option value='Allt'>Allt</option>

						    <option value='Stor'>Stor</option>
						    <option value='Normal'>Normal</option>
						    <option value='Liten'>Liten</option>

						    <option value='Stor Gul'>Stor Gul</option>
						    <option value='Stor Vit'>Stor Vit</option>
						    <option value='Stor Alu'>Stor Alu</option>

						    <option value='Normal Gul'>Normal Gul</option>
						    <option value='Normal Vit'>Normal Vit</option>
						    <option value='Normal Alu'>Normal Alu</option>

						    <option value='Liten Gul'>Liten Gul</option>
						    <option value='Liten Vit'>Liten Vit</option>
						    <option value='Liten Alu'>Liten Alu</option>
						</select>
						<button type='submit' name='search_date' class='button_date'>
							<i class='gg-search'></i>
						</button>
					</form>
					</div>";

				$search_phrase = search_modell();

				$sql = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm
					FROM alarm_tid WHERE DATE(message_start) BETWEEN '".$from."' AND '".$to."' $exclude_varning
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 120 $search_phrase
					GROUP BY message_text
					ORDER BY (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 DESC";
				echo $sql;
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
				echo "</div>";
			}

			function utveckling($from, $to, $from2, $to2, $search_date, $dashboard) {
				$exclude_varning = " AND message_text NOT LIKE '%Varning%'
					AND message_text NOT LIKE '%Lägg i sista%'
					AND message_text NOT LIKE '%BYT PALL%'
					";
				$today = date('Y-m-d');

				// $first = 'CURRENT_DATE - INTERVAL 7 DAY AND current_date ';
				// $second = 'current_date - INTERVAL 15 DAY AND current_date - INTERVAL 8 DAY ';

				if ($dashboard == false) {

					$first = '"' .$from2. '' .
					 '" AND "' . $to2 . '"';
					$second = '"' . $from . '" AND "' . $to . '"';

					echo "<br><div align='center'>" . $from2 . " till " .
						$to2 . " jämfört med " . $from . " till ". $to . "</div><br>";


				}

				$sql = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV 60 As NewTid,
					COUNT(message_text) As NewAntal,
					message_text As Larm,
					message_start As Datum
					FROM alarm_tid
					WHERE DATE(message_start) BETWEEN $second
					$exclude_varning AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 120
					group by message_text
					union
					SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) DIV 60 As NewTid,
					COUNT(message_text) As NewAntal,
					message_text As Larm,
					message_start As Datum
					FROM alarm_tid
					WHERE DATE(message_start) BETWEEN $first
					$exclude_varning AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 120
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
					echo "<script type='text/javascript'>dashboardUtveckling('".$type."');</script>";
				} else {
					echo "<p align='center'>Inga resultat</p>";
				}
			}

			function pallet_fel() {
				$sql = "SELECT * FROM pallet_fel ORDER BY datum DESC"; // WHERE datum = current_date";

				$result = get_data($sql);
				$data = $result->fetch_all(MYSQLI_ASSOC);


				if (count($data) > 0) {

					$headers = array();
						for ($x = 1; $x <= 15; $x++) {
							array_push($headers, "pallet_{$x}");
						}
					array_push($headers, 'artikelnummer', 'datum');
					echo "<div class='prodChart'><canvas id='prodChart'></canvas></div>";
					echo "<table width='100%' id='myTable'>";
					create_table($headers);

					foreach ($data as $row ) {
						$echo = "<tr>";
						foreach ($headers as $key) {
							if ($row[$key] != "") {
								$echo .= "<td>" . $row[$key] . "</td>";
							} else {
								$echo .= "<td>" . "-" . "</td>";
							}

						}
						$echo .= "</tr>";
						echo $echo;
					}

					echo "</tr>";
					echo "</table><br><br>";
					echo "</div>";
					echo '<script type="text/javascript">chartPallet();</script>';
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
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 120
					GROUP BY message_text
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

			function TAKOEE_search($date) {
				// echo "<div class='larmThisHour'>
				// 		<p id='tid'></p>
				// 		<p id='larm'></p>
				// </div>";

				$sql_larm = "SELECT
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm
					FROM alarm_tid WHERE DATE(message_start) = '".$date."' AND message_text NOT LIKE '%Varning%'
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 1120
					GROUP BY message_text
					ORDER BY count(message_text) DESC";

				$sql_antal = "SELECT (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) AS Antal,
					message_text AS Larm,
					hour(message_start) as Hour
					FROM alarm_tid
					WHERE DATE(message_start) = '".$date."' AND message_text NOT LIKE '%Varning%'
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 1120
					GROUP BY hour(message_start)";

				$sql_stopptid = "SELECT message_text as Larm, (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid, date(message_start) As Datum, message_start, message_end, hour(message_end) As Hour
						FROM `alarm_tid`
						WHERE date(message_start) = '".$date."' and message_text NOT LIKE '%Varning%'
						AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 1120
						group by message_end ORDER BY `alarm_tid`.`message_start` ASC";

				$sql_prod = "SELECT * FROM produktion WHERE date(date_start) = '".$date."' ORDER BY `produktion`.`datum` DESC";

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
					// print_r($antal);
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
						$d = $row['Hour'];
						$start = strtotime($row['message_start']);
						$end = strtotime($row['message_end']);

						$hour_start = date("H", strtotime($row['message_start']));
						$hour_end = date("H", strtotime($row['message_end']));
						$date = date('Y-m-d', strtotime($row['message_start']));

						// if larm is over 2 different hours
						// if ($hour_start == $hour_end) {
							// $sum_diff += abs($start - $end) / 60;
							if (array_key_exists($d, $tid)) {
								$tid[$d] += (abs($start - $end) / 60);
							} else {
								$tid[$d] = abs($start - $end) / 60;
							}

						// } elseif ($hour_start !== $hour_end) {
						// 	$first = strtotime($date . ' ' . $hour_start . ':59:59');
						// 	$second = strtotime($date . ' ' . $hour_end . ':00:00');
						//
						// 	echo date('Y-m-d H:i:s', $start) . $row['Larm'] . "<br>";
						// 	echo date('Y-m-d H:i:s', $first) . $row['Larm'] . "<br>";
						// 	echo date('Y-m-d H:i:s', $second) . $row['Larm'] . "<br>";
						// 	echo date('Y-m-d H:i:s', $end) . $row['Larm'] . "<br>";
						//
						// 	$first_diff = abs($start - $first);
						// 	$second_diff = abs($second - $end);
						//
						// 	// $sum_diff += $first_diff / 60;
						// 	if (array_key_exists($d, $tid)) {
						// 		$tid[$d] += $first_diff / 60;
						// 	} else {
						// 		$tid[$d] = $first_diff / 60;
						// 	}
						//
						// 	if (array_key_exists($d + 1, $tid)) {
						// 		$tid[$d + 1] += $second_diff / 60;
						// 	} else {
						// 		$tid[$d + 1] = $second_diff / 60;
						// 	}
						// }
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
					$headers = array("Tid", "Larm", "Hour");
					create_table($headers);
					foreach ($stopptid as $row) {
						echo "<tr><td>" . bcdiv($row["Tid"], 1, 1) . "</td><td>" . $row["Larm"] . "</td><td>" . $row["Hour"] . "</td></tr>";
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
							document.getElementById('antal').innerHTML = 'Stopptid: ".bcdiv(($total_tid / 60), 1, 1)." timmar&nbsp;&nbsp|&nbsp&nbsp;';
							document.getElementById('tid').innerHTML = 'Antal: ".$sum_antal."';
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
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 120
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
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 120
					GROUP BY YEARWEEK(message_start, 1) DESC";

				$sql_range = "SELECT yearweek(message_start, 1) as yearweek, YEAR(message_start) As Year, WEEKOFYEAR(message_start) As Week
					from alarm_tid
					group by yearweek(message_start, 1) DESC";

				$result = get_data($sql_weeks);
				$result_dates = get_data($sql_range);
				echo $sql_days;
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
				$conn = new mysqli('localhost', 'root', '', 'steelform');

				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connection_error);
				}

				$sql_days = $conn->prepare("SELECT
					date(message_start) as Datum,
					(SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid,
					COUNT(message_text) As Antal,
					message_text AS Larm
					FROM alarm_tid
					WHERE message_text = ?
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 120
					GROUP BY DATE(message_start) DESC");

				$sql_range = "SELECT date(message_start) as Datum
					from alarm_tid
					group by date(message_start) DESC";

				$sql_days->bind_param('s', $search_larm);
				$sql_days->execute();

				$result = $sql_days->get_result();
				$conn->close();
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
					AND (SELECT SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end))) / 60 < 120
					$exclude_varning UNION ";
				}

				$sql .= "SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end)) DIV 60 AS Tid,
				COUNT(message_text) As Antal,
				message_text As Larm
				FROM alarm_tid
				WHERE message_text NOT LIKE '%Stn%' AND message_text NOT LIKE '%Station %'
				AND (SELECT SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end))) / 60 < 120
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
				$sql = "SELECT * FROM produktion ORDER BY `produktion`.`date_start`  DESC";

				$result = get_data($sql);

				if ($result->num_rows > 0) {

					$headers = array();
						for ($x = 0; $x <= 23; $x++) {
							array_push($headers, $x);
						}
					array_push($headers, 'kassV', 'kassH', 'artikelnummer', 'date_start', 'date_end');

					// echo	"<div class='wrapperProdchart'>
					// 		<canvas id='line-chart'></canvas>
					// 	</div>";
					echo "<div class='wrapperProd'>";
					echo "<div class='prodChart'><canvas id='prodChart'></canvas></div>";
					echo "<table style='width:100%;' id='myTable'>";
					create_table($headers);

					while($row = $result->fetch_assoc()) {
						if ($row['date_start'] != null) {
							$echo = "<tr>";
							foreach ($headers as $key) {
								if ($row[$key] != "") {
									if ($key == 'date_start' or $key == 'date_end') {
										if (strlen($row[$key]) == 26) {
											$row[$key] = substr($row[$key], 0, -7);
										}
									}
									$echo .= "<td>" . $row[$key] . "</td>";
								} else {
									$echo .= "<td>" . "-" . "</td>";
								}

							}
							$echo .= "</tr>";
							// $date_start = new DateTime($row['date_start']);
							// $date_end = new DateTime($row['date_end']);
							// $diff = strtotime($row["date_end"]) - strtotime($row["date_start"]);

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

			function produktionstid() {
				$max_prod = 250;

				if(isset($_POST['modell'])) {
					$last_modell = $_POST['modell'];
				} else {
					$last_modell = "Välj modell";
				}
				echo "
				<div align='center' class='date_header'>
					<form class='search_date2' method='post'>
						<select name='modell' class='search_model'>
						    <option value='".$last_modell."'>".$last_modell."</option>

						    <option value='Stor'>Stor</option>
						    <option value='Normal'>Normal</option>
						    <option value='Liten'>Liten</option>

						    <option value='Stor Gul'>Stor Gul</option>
						    <option value='Stor Vit'>Stor Vit</option>
						    <option value='Stor Alu'>Stor Alu</option>

						    <option value='Normal Gul'>Normal Gul</option>
						    <option value='Normal Vit'>Normal Vit</option>
						    <option value='Normal Alu'>Normal Alu</option>

						    <option value='Liten Gul'>Liten Gul</option>
						    <option value='Liten Vit'>Liten Vit</option>
						    <option value='Liten Alu'>Liten Alu</option>
						</select>

						<button type='submit' name='TAKOEE' class='button_date'>
							<i class='gg-search'></i>
						</button>
					</form>
				</div>";
				$search_phrase = search_modell();
				// echo $search_phrase;
				echo "<div align='left'>
				<p id='Produktionstid'>Per timme</p>
				<p id='Stopptid'>Stopptid: </p>
				<p id='Prod/h'>Prod/h: </p>
				</div>";

				$sum_prod = "(select sum((";
				for ($x=0; $x <= 22; $x++) {
					$sum_prod .= "COALESCE(produktion." . $x . ", 0) + ";
				}
				$sum_prod .= "COALESCE(produktion.23, 0))))";

				$sql = "select datum, artikelnummer, ".$sum_prod." As produktion, (SELECT SUM(TIMESTAMPDIFF(SECOND, date_start, date_end)) / 3600) as prod_tid,
					(select sum((kassV + kassH))) As kass, sum(idle_time) as idle_time, (select sum(stopptid)) as stopptid
					FROM produktion where date_start IS NOT NULL ".$search_phrase." AND datum > '2022-08-18'
					GROUP BY datum, artikelnummer
					ORDER BY produktion.date_start DESC";

				$result = get_data($sql);

				$data = $result->fetch_all(MYSQLI_ASSOC);

				$headers = array('Datum', 'Artikelnummer', 'Produktion', 'Produktionstid', 'Stopptid', 'Kass',
				'prod/h');
				echo "<div class='prodChart'><canvas id='prodChart'></canvas></div>";
				echo "<table style='width:100%;' id='myTable'>";
				create_table($headers);
				echo "<br>";


				foreach ($data as $row ) {
					$produktion = $row['produktion'];
					$produktionstid = $row['prod_tid'];
					$kass = $row['kass'];

					if ($produktionstid != 0 && $produktion != 0) {

						$old_datum = $row['datum'];
						$old_artikelnummer = $row['artikelnummer'];



						$avg_prod = $produktion / $produktionstid;

						echo "<tr><td>" . $row['datum'] .
							"</td><td>" . $row['artikelnummer'] .
							"</td><td>" . $produktion .
							"</td><td>" . bcdiv(($produktionstid), 1, 2)  .
							"</td><td>" . bcdiv(($row['stopptid']), 1, 2) .
							"</td><td>" . $kass .
							"</td><td>" . bcdiv($avg_prod, 1, 0) .
							"</td>";
					}
				}
				echo "</tr></table>";
				echo "<br>";
				echo '<script type="text/javascript">produktionstid();</script><br><br>';


			}

			function TAKOEE() {
				$max_prod = 250;

				if(isset($_POST['modell'])) {
					$last_modell = $_POST['modell'];
				} else {
					$last_modell = "Välj modell";
				}
				echo "
				<div align='center' class='date_header'>
					<form class='search_date2' method='post'>
						<select name='modell' class='search_model'>
						    <option value='".$last_modell."'>".$last_modell."</option>

						    <option value='Stor'>Stor</option>
						    <option value='Normal'>Normal</option>
						    <option value='Liten'>Liten</option>

						    <option value='Stor Gul'>Stor Gul</option>
						    <option value='Stor Vit'>Stor Vit</option>
						    <option value='Stor Alu'>Stor Alu</option>

						    <option value='Normal Gul'>Normal Gul</option>
						    <option value='Normal Vit'>Normal Vit</option>
						    <option value='Normal Alu'>Normal Alu</option>

						    <option value='Liten Gul'>Liten Gul</option>
						    <option value='Liten Vit'>Liten Vit</option>
						    <option value='Liten Alu'>Liten Alu</option>
						</select>

						<button type='submit' name='TAKOEE' class='button_date'>
							<i class='gg-search'></i>
						</button>
					</form>
				</div>";
				$search_phrase = search_modell();
				// echo $search_phrase;
				echo "<div align='left'>
				<p id='Produktionstid'>Per timme</p>
				<p id='Stopptid'>Stopptid: </p>
				<p id='Prod/h'>Prod/h: </p>
				</div>

				";


				// $search_phrase = "";
				$sum_prod = "(select sum((";
				for ($x=0; $x <= 22; $x++) {
					$sum_prod .= "COALESCE(produktion." . $x . ", 0) + ";
				}
				$sum_prod .= "COALESCE(produktion.23, 0))))";

				$sql = "select datum, ".$sum_prod." As produktion, (SELECT SUM(TIMESTAMPDIFF(SECOND, date_start, date_end)) / 3600) as prod_tid,
					(select sum((kassV + kassH))) As kass, sum(idle_time) as idle_time, (select sum(stopptid)) as stopptid, date_start, date_end, idle
					FROM produktion where date_start IS NOT NULL ".$search_phrase."
					GROUP BY datum
					ORDER BY produktion.date_start DESC";

				$sql_stopptid = "SELECT date(message_start) As Datum, message_start, message_end, count(message_end)
						FROM `alarm_tid`
						WHERE date(message_start) > '2022-02-05' and message_text NOT LIKE '%Varning%'
						AND date(message_start) = date(message_end) ".$search_phrase."
						group by message_end ORDER BY `alarm_tid`.`message_start` DESC";

				$sql3 = "select produktion.datum,
						SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end)) / 60 as stopptid,
						".$sum_prod." As produktion,
						(select(produktion.kassV + produktion.kassH)) As kass,
						produktion.date_start,
						produktion.date_end,
						produktion.idle
						from produktion
						left join alarm_tid
						on produktion.datum = date(alarm_tid.message_start) AND alarm_tid.message_text NOT LIKE '%Varning%'
						AND (SELECT SUM(TIMESTAMPDIFF(SECOND, alarm_tid.message_start, alarm_tid.message_end))) / 60 < 120
						WHERE alarm_tid.message_start > '2022-02-05' ".$search_phrase."
						GROUP by date(alarm_tid.message_start) DESC;";

				// echo $sql3;
				// $sql2 = "SELECT date(message_id) as datum, (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 AS Tid, count(message_id) as count
				// 	FROM `alarm_tid`
				// 	WHERE date(message_start) > '2022-02-05' AND message_text NOT LIKE '%Varning%' AND (SELECT SUM(TIMESTAMPDIFF(SECOND, message_start, message_end))) / 60 < 120
				// 	group by message_id having count(message_id) > 1 ORDER BY `datum` DESC";
				// echo $sql;
				// echo $sql_stopptid;
				// echo $sql3;
				$result = get_data($sql);
				$stopptid = get_data($sql_stopptid);

				$data = $result->fetch_all(MYSQLI_ASSOC);
				$stopptid = $stopptid->fetch_all(MYSQLI_ASSOC);

				// if ($dashboard == false) {
					// echo "<div style='text-align: center;'>
					// 		<div style='display: inline-block; text-align: left;'>
					// 			<p>Tillgänglighet = (Produktionstid - Stopptid) / Produktionstid</p>
					// 			<p>Anläggningsutbyte = (Produktion / Produktionstid) / max produktion</p>
					// 			<p>Kvalite = (Produktion - Kass) / Produktion</p>
					// 			<p>OEE = Tillgänglighet * Anläggningsutbyte * Kvalite</p>
					// 		</div>
					// 	</div>";

					$headers = array('Datum', 'Produktion',
					// 'Max prod', 'Avg prod',
					'Produktionstid', 'Stopptid', 'Stillestånd', 'Kass', 'prod/h', 'est. prod', 'Tillgänglighet', 'Anläggningsutbyte', 'Kvalite', 'OEE');
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

						// echo date('Y-m-d', $row['message_start']);

						// echo date('Y-m-d', strtotime($row['message_start']));
						// echo date('Y-m-d', strtotime($row['message_end']));
						if (date('Y-m-d', strtotime($row['message_start'])) != date('Y-m-d', strtotime($row['message_end']))) {
							echo "yes";
						}

						$sum_diff += abs($start - $end) / 3600;

						if(end($stopptid) !== $row) {
							$tid[$d] = $sum_diff;
						}
					}
					// print_r($tid);
					// $res = array();
					// foreach($data as $vals){
					//     if(array_key_exists($vals['datum'],$res)){
					//         $res[$vals['datum']]['idle'] += ((strtotime($vals["date_end"]) - strtotime($vals["date_start"])));
					//
					//     }
					//     else{
					//         $res[$vals['datum']]['idle'] = ((strtotime($vals["date_end"]) - strtotime($vals["date_start"])));
					//     }
			    		// }
					// print_r($res);
					// $row_counter = 0;
					//
					// $sum_avg = 0;

					// $p = 0;
					// $t = 0;

					foreach ($data as $row ) {
						$produktion = $row['produktion'];
						//
						// if(array_key_exists($row['datum'],$data)) {
						// 	$produktionstid += ((strtotime($row["date_end"]) - strtotime($row["date_start"])) / 60);
						// } else {
						// 	$produktionstid = ((strtotime($row["date_end"]) - strtotime($row["date_start"])) / 60);
						// }

						// print_r($produktionstid);
						// $produktionstid = ((strtotime($row["date_end"]) - strtotime($row["date_start"])) / 60) - $row["idle"];
						$produktionstid = $row['prod_tid'] - ($row["idle"] / 60);
						$kass = $row['kass'];
						$expected_prod = ($produktionstid) * $max_prod;

						if (isset($tid[$row['datum']])) {
							$stopptid = $tid[$row['datum']];
						} else {
							$stopptid = 0;
						}

						if ($row['idle_time'] > 0) {
							$Stillestånd = bcdiv(($row['idle_time'] / 3600) - $stopptid, 1, 2);
						} else {
							$Stillestånd = "-";
						}
						// echo bcdiv($expected_prod, 1, 0) . "<br>";
						// echo bcdiv($avg_prod, 1, 0) . "<br>";
						// <td>" . bcdiv($expected_prod, 1, 0) . "</td><td>" . bcdiv($avg_prod, 1, 0) . "</td>

						if ($produktionstid != 0 && $produktion != 0) {
							$tillgänglighet = ($produktionstid - $stopptid) / $produktionstid;
							$anläggningsutbyte = ($produktion / $produktionstid) / $max_prod;
							$kvalite = ($produktion - $kass) / $produktion;
							$OEE = $tillgänglighet * $anläggningsutbyte * $kvalite;
							$avg_prod = $produktion / $produktionstid;
							// echo $produktionstid . "<br>";
							// $p = $p + $produktion;
							// $t = $t + $produktionstid;

							// $row_counter = $row_counter + 1;
							// $sum_avg = $sum_avg + $avg_prod;


							echo "<tr><td>" . $row['datum'] . "</td><td>" . $produktion . "</td>



							<td>" . bcdiv(($produktionstid), 1, 2)  . "</td><td>" . bcdiv(($stopptid), 1, 2) . "</td><td>" . $Stillestånd . "</td><td>" .
							$kass . "</td><td>" . bcdiv($avg_prod, 1, 0) . "</td><td>" . bcdiv($expected_prod, 1, 0) . "</td><td>" . bcdiv(($tillgänglighet) * 100, 1, 2) . "%" . "</td><td>" . bcdiv(($anläggningsutbyte) * 100, 1, 2) . "%" .
								 "</td><td>" . bcdiv(($kvalite) * 100, 1, 2) . "%" . "</td><td>" . bcdiv(($OEE) * 100, 1, 2) . "%" . "</td>";
						}
					}
					echo "</tr></table>";
					// echo $sum_avg/$row_counter;
					// echo $p;
					echo "<br>";
					// echo $t;
					echo '<script type="text/javascript">chartTAKOEE();</script><br><br>';
				// } else {
				// 	return $data;
				// 	echo "true";
				// }

			}

			function search_modell() {

				if(isset($_POST['modell'])) {
					$modell = $_POST['modell'];
					if($modell == "Stor Gul") {
						$modell = 10231;
					} elseif($modell == "Stor Vit") {
						$modell = 10271;
					} elseif($modell == "Stor Alu") {
						$modell = 10251;
					} elseif($modell == "Normal Gul") {
						$modell = 10233;
					} elseif($modell == "Mormal Vit") {
						$modell = 10273;
					} elseif($modell == "Normal Alu") {
						$modell = 10253;
					} elseif($modell == "Liten Gul") {
						$modell = 10235;
					} elseif($modell == "Liten Vit") {
						$modell = 10275;
					} elseif($modell == "Liten Alu") {
						$modell = 10255;
					}

					$search_phrase = " AND artikelnummer = " . $modell;

					if($modell == "Stor") {
						$search_phrase = "AND artikelnummer in (10231, 10271, 10251)";
					} elseif($modell == "Normal") {
						$search_phrase = "AND artikelnummer in (10233, 10273, 10253)";
					} elseif($modell == "Liten") {
						$search_phrase = "AND artikelnummer in (10235, 10275, 10255)";
					}
					if($modell == "Välj modell" or $modell == "Allt") {
						$search_phrase = "";
					}
					// echo "<p align='center'>".$last_modell."</p>";

				} else {
					$search_phrase = "";
				}

				return $search_phrase;


			}


			if (isset($_POST['today'])) {
				larm();

			} else if (isset($_POST['search_date'])) {
				search_date();

			} else if (isset($_POST['TAKOEE'])) {
				// $dashboard = false;
				$modell = "";
				echo "<div class='wrapper'>";
				TAKOEE($modell);
				echo "</div>";

			} else if (isset($_POST['TAKOEE_modell'])) {
				// $dashboard = false;
				$modell = $_POST['modell'];
				echo "<div class='wrapper'>";
				TAKOEE($modell);
				echo "</div>";

			} else if (isset($_POST['avg_time'])) {
				echo "<div class='wrapperRecent'>";
				echo "<p align='center'>Genomsnittlig stopptid per larm</p>";
				average();
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
				$from = date('Y-m-d', strtotime('-1 week'));
				$to = date('Y-m-d');
				echo "<div class='wrapper'>";
				echo "<div class='date_header'>

					<form class='search_date' method='post'>
					<input type='date' class='date_form' name='dateFrom' value='".$from."'>
					<p class='fromto'>-</p>
					<input type='date' class='date_form' name='dateTo' value='".$to."'>
					<button type='submit' name='search_utveckling' class='button_date'>
						<i class='gg-search'></i>
					</button></form>
					</div>";
				$from2 = date('Y-m-d', strtotime('-1 week -1 day', strtotime($from)));
				$to2 = date('Y-m-d', strtotime('-1 day', strtotime($from)));
				utveckling($from, $to, $from2, $to2, $search_date, $dashboard);
				echo "</div>";

			} else if (isset($_POST['search_utveckling'])) {
				$search_date = true;
				$dashboard = false;
				$from = date('Y-m-d', strtotime($_POST['dateFrom']));
				$to = date('Y-m-d', strtotime($_POST['dateTo']));
				echo "<div class='wrapper'>";
				echo "<div class='date_header'>

					<form class='search_date' method='post'>
					<input type='date' class='date_form' name='dateFrom' value='".$from."'>
					<p class='fromto'>-</p>
					<input type='date' class='date_form' name='dateTo' value='".$to."'>
					<button type='submit' name='search_utveckling' class='button_date'>
						<i class='gg-search'></i>
					</button></form>
					</div>";
				$exclude_varning = check_exclude();
				$start = new DateTime(date('Y-m-d', strtotime('-1 day', strtotime($from))));
				$end = new DateTime($to);
				$diff = $start->diff($end);
				$from2 = date('Y-m-d', strtotime('-'.$diff->format('%a').' day', strtotime($_POST['dateFrom'])));
				$to2 = date('Y-m-d', strtotime('-1 day', strtotime($_POST['dateFrom'])));
				utveckling($from, $to, $from2, $to2, $search_date, $dashboard);
				echo "</div>";

			} else if (isset($_POST['pallet_fel'])) {
				echo "<div class='wrapperProd'>";
				pallet_fel();
				echo "</div>";

			} else if (isset($_POST['search_larm'])) {
				// $search_larm = $_POST['search_form'];
				$search_larm = ltrim(filter_input(INPUT_POST, 'search_form'));
				$search_larm = trim($search_larm);
				$search_larm = stripslashes($search_larm);
				$search_larm = htmlspecialchars($search_larm);
				$search_larm = str_replace(';', '', $search_larm);
				// echo "<p align='center'>" . $search_larm . "</p>";

				search_larm2($search_larm);

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
			} else if (isset($_POST['produktionstid'])) {
				echo "<div class='wrapper'>";
				produktionstid();
				echo "</div>";

			} else {
				// dashboard();
				// $search_date = false;
				// $dashboard = true;
				// echo "<div class='wrapper'>";
				// utveckling($search_date, $dashboard);
				// larm();
				larm();
				// echo "</div>";
		}

		?>

	</body>
</hmtl>
