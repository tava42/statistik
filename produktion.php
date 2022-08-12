<?php

$a1 = 0; // fel H
$a2 = 0; // fel V
$a3 = 108; // kvar
$a4 = 1; // status
$a5 = 12450; // Artikelnummer
$a6 = 1; // idle
$a7 = 15; // pallet_id station 9


$status = preg_replace('/[^0-9]/', '', $a4);

if ($status == 1) {
     $felH = preg_replace('/[^0-9]/', '', $a1);
     $felV = preg_replace('/[^0-9]/', '', $a2);
     $kvar = preg_replace('/[^0-9]/', '', $a3);
     $modell = preg_replace('/[^0-9]/', '', $a5);
     $idling = preg_replace('/[^0-9]/', '', $a6);

     $datetime = date("Y-m-d H:i:s");
     $today = date('Y-m-d');
     $hour = intval(date('H'));

     $sql = "SELECT datum, date_start, date_end, `$hour` As antal, kassV, kassH, artikelnummer, idle_time, setup_time, old_kassV, old_kassH, old_value
          FROM produktion
          WHERE datum = '".$today."'
          ORDER BY date_start DESC LIMIT 1";

     $conn = new mysqli('localhost', 'root', '', 'steelform');

     if ($conn->connect_error) {
               die("Connection failed: " . $conn->connection_error);
     }

     $result = $conn->query($sql);
     $data = $result->fetch_all(MYSQLI_ASSOC);
     $conn->close();

     foreach ($data as $row) {
          $date_end = strtotime($row['date_end']);
          $antal = $row['antal'];
          $kassV = $row['kassV'];
          $kassH = $row['kassH'];
          $Artikelnummer = $row['artikelnummer'];
          $idle_time = $row['idle_time'];
          $setup_time = $row['setup_time'];
          $old_kassV = $row['old_kassV'];
          $old_kassH = $row['old_kassH'];
          $old_value = $row['old_value'];

          //time difference between last script update
          $time_counter = abs(($date_end - strtotime($datetime)));
     }

     if (count($data) > 0) {
          // checks if new session should be started. If machine been off for less than 30 min then same session continues.
          if ($time_counter > 30 or $modell > 0 && $modell != $Artikelnummer && $Artikelnummer != 0) {
               $sql = "INSERT INTO `produktion` (`datum`, `date_start`, `date_end`, `artikelnummer`, `old_kassV`, `old_kassH`, `old_value`)
                    VALUES ('".$today."', '".$datetime."', '".$datetime."', '".$modell."', '".$felV."', '".$felH."', '".$kvar."')";
               insert_data($sql);

          } else {

               // Logs the id of pallet's the machine is throwing away defected units from.
               $diff_kassV = $felV - $old_kassV;
               $diff_kassH = $felH - $old_kassH;

               // if ($diff_kassV > 0) {
               //      $current_pallet = "pallet_" + ".$a6.";
               //      $sql = "SELECT datum, artikelnummer, `$current_pallet`
               //           FROM pallet_fel
               //           WHERE datum = '".$today."'
               //           ORDER BY date_start DESC LIMIT 1";
               //
               //
               //      $sql = "INSERT INTO `pallet_fel`"
               // }

               if ($diff_kassV < 0 or $diff_kassH < 0) {
                    $diff_kassV = 0;
                    $diff_kassH = 0;
               }

               $new_kassV = $kassV + $diff_kassV;
               $new_kassH = $kassH + $diff_kassH;

               // Setup time is the time it takes to change model in the machine, this code tracks that time.
               if ($modell == 0) {
                    $setup_time = $setup_time + $time_counter;

                    $sql = "UPDATE `produktion`
                    SET `setup_time` = '".$setup_time."'
                    WHERE `datum` = '".$today."'
                    ORDER BY date_start DESC LIMIT 1";
                    insert_data($sql);
               }

               // Idle time is the time when the machine is not running.
               if ($idling == 1) {
                    $idle_time = $idle_time + $time_counter;

                    $sql = "UPDATE `produktion`
                    SET `idle_time` = '".$idle_time."'
                    WHERE `datum` = '".$today."'
                    ORDER BY date_start DESC LIMIT 1";
                    insert_data($sql);
               }



               if ($old_value != $kvar) {
                    $diff_value = $old_value - $kvar;
                    $new_value = $antal + ($diff_value * 2);

                    if ($diff_value < 3 && $diff_value > 0) {
                         if ($modell != 0) {
                              $sql = "UPDATE `produktion`
                                   SET `date_end` = '".$datetime."', `old_value` = '".$kvar."', `old_kassV` = '".$felV."', `old_kassH` = '".$felH."', `".$hour."` = '".$new_value."',
                                    kassV = '".$new_kassV."', kassH = '".$new_kassH."', artikelnummer = '".$modell."'
                                   WHERE `datum` = '".$today."'
                                   ORDER BY date_start DESC LIMIT 1";
                              insert_data($sql);
                         } elseif ($modell == 0) {
                              $sql = "UPDATE `produktion`
                                   SET `date_end` = '".$datetime."', `old_value` = '".$kvar."', `old_kassV` = '".$felV."', `old_kassH` = '".$felH."', `".$hour."` = '".$new_value."',
                                    kassV = '".$new_kassV."', kassH = '".$new_kassH."'
                                   WHERE `datum` = '".$today."'
                                   ORDER BY date_start DESC LIMIT 1";
                              insert_data($sql);
                         }
                    } else {
                         $sql = "UPDATE `produktion`
                         SET `old_value` = '".$kvar."'
                         WHERE `datum` = '".$today."'
                         ORDER BY date_start DESC LIMIT 1";
                         insert_data($sql);
                    }

               } else {
                    $sql = "UPDATE `produktion`
                         SET `date_end` = '".$datetime."'
                         WHERE `datum` = '".$today."'
                         ORDER BY date_start DESC LIMIT 1";
                    insert_data($sql);
                         }
          }

     } else {
          $sql = "INSERT INTO `produktion` (`datum`, `date_start`, `date_end`, `artikelnummer`, `old_kassV`, `old_kassH`, `old_value`) VALUES ('".$today."', '".$datetime."', '".$datetime."', '".$modell."', '".$felV."', '".$felH."', '".$kvar."')";
          insert_data($sql);
     }
}

function insert_data($sql) {
     $conn = new mysqli('localhost', 'root', '', 'steelform');
     echo $sql;
     $conn->query($sql);
     $conn->close();
}

 ?>
