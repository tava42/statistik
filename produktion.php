<?php

$a1 = 4; // fel H
$a2 = 9; // fel V
$a3 = 90; // kvar
$a4 = 1; // status
$a5 = 10272; // Artikelnummer left st 10
$a6 = 10273; // Artikelnummer Right st 10
$a7 = 0; // idle
$a8 = 13; // pallet_id station 9

$status = preg_replace('/[^0-9]/', '', $a4);

if ($status == 1) {

     function insert_data($sql) {
          $conn = new mysqli('localhost', 'root', '', 'steelform');
          if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connection_error);
          }
          echo $sql."<br>\n";
          $conn->query($sql);
          $conn->close();
     }

     $felH = preg_replace('/[^0-9]/', '', $a1);
     $felV = preg_replace('/[^0-9]/', '', $a2);
     $kvar = preg_replace('/[^0-9]/', '', $a3);
     $model_v = preg_replace('/[^0-9]/', '', $a5);
     $model_h = preg_replace('/[^0-9]/', '', $a6);
     $idling = preg_replace('/[^0-9]/', '', $a7);
     $pallet_id = preg_replace('/[^0-9]/', '', $a8);

     $datetime = date("Y-m-d H:i:s");
     $today = date('Y-m-d');
     $hour = intval(date('H'));

     $sql = "SELECT date_end, artikelnummer, old_kassV, old_kassH, old_kvar
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
          $artikelnummer = $row['artikelnummer'];
          $old_kassV = $row['old_kassV'];
          $old_kassH = $row['old_kassH'];
          $old_kvar = $row['old_kvar'];

          //time difference between last script update
          $time_counter = abs((strtotime($row['date_end']) - strtotime($datetime)));
     }

     if (count($data) > 0) {
          // checks if new session should be started. If machine been off for less than 30 min then same session continues.
          if ($time_counter > 3600 or $model_v == 0 && $model_v != $artikelnummer) {
               $sql = "INSERT INTO `produktion` (`datum`, `date_start`, `date_end`, `artikelnummer`, `old_kassV`, `old_kassH`, `old_kvar`)
                    VALUES ('".$today."', '".$datetime."', '".$datetime."', '".$model_v."', '".$felV."', '".$felH."', '".$kvar."')";
               insert_data($sql);
          }

          // Logs the id of pallet's the machine is throwing away defected units from.
          $kassV_check = false;
          $kassH_check = false;
          $diff_kassV = $felV - $old_kassV;
          $diff_kassH = $felH - $old_kassH;

          if ($diff_kassV > 0) {
               $kassV_check = true;
          } else if ($diff_kassV < 0 or $diff_kassV == 0) {
               $diff_kassV = 0;
          }

          if ($diff_kassH > 0) {
               $kassH_check = true;
          } else if ($diff_kassH < 0 or $diff_kassH == 0) {
               $diff_kassH = 0;
          }

          function pallet_fel($model, $diff_kass) {
               global $pallet_id, $today;
               $current_pallet = "pallet_{$pallet_id}";

               $sql = "INSERT INTO pallet_fel (datum, artikelnummer, ".$current_pallet.")
                    VALUES('".$today."', ".$model.", ".$diff_kass.")
                    ON DUPLICATE KEY UPDATE
                    ".$current_pallet." = COALESCE(".$current_pallet.", 0) + ".$diff_kass."";
               insert_data($sql);
          }

          if ($kassV_check) {
               pallet_fel($model_v, $diff_kassV);
          }

          if ($kassH_check) {
               pallet_fel($model_h, $diff_kassH);
          }

          // Setup time is the time it takes to change model in the machine.
          if ($model_v == 0) {
               $sql = "UPDATE `produktion`
               SET `setup_time` = COALESCE(`setup_time`, 0) + '".$time_counter."'
               WHERE `datum` = '".$today."'
               ORDER BY date_start DESC LIMIT 1";
               insert_data($sql);
          }

          // Idle time is the time when the machine is on but not running.
          if ($idling == 1) {
               $sql = "UPDATE `produktion`
               SET `idle_time` = COALESCE(`idle_time`, 0) + '".$time_counter."'
               WHERE `datum` = '".$today."'
               ORDER BY date_start DESC LIMIT 1";
               insert_data($sql);
          }

          if ($old_kvar != $kvar) {
               $diff_value = ($old_kvar - $kvar) * 2;

               // If remaning units decrease or increase more than 3 then its because the value was changed manually and should not be counted.
               if ($diff_value < 3 && $diff_value > 0) {
                    $sql = "UPDATE `produktion`
                         SET `date_end` = '".$datetime."', `old_kvar` = '".$kvar."', `old_kassV` = '".$felV."', `old_kassH` = '".$felH."', `".$hour."` = COALESCE(`".$hour."`, 0) + '".$diff_value."',
                         kassV = COALESCE(kassV, 0) + '".$diff_kassV."', kassH = COALESCE(kassH, 0) + '".$diff_kassH."', artikelnummer = '".$model_v."'
                         WHERE `datum` = '".$today."'
                         ORDER BY date_start DESC LIMIT 1";
                    insert_data($sql);

               } else {
                    $sql = "UPDATE `produktion`
                    SET `old_kvar` = '".$kvar."', `date_end` = '".$datetime."'
                    WHERE `datum` = '".$today."'
                    ORDER BY date_start DESC LIMIT 1";
                    insert_data($sql);
               }

          } elseif ($diff_kassV > 0 or $diff_kassH > 0) {
               $sql = "UPDATE `produktion`
                    SET `date_end` = '".$datetime."', `old_kassV` = '".$felV."', `old_kassH` = '".$felH."', kassV = COALESCE(kassV, 0) + '".$diff_kassV."', kassH = COALESCE(kassH, 0) + '".$diff_kassH."', artikelnummer = '".$model_v."'
                    WHERE `datum` = '".$today."'
                    ORDER BY date_start DESC LIMIT 1";
               insert_data($sql);

          } else {
               $sql = "UPDATE `produktion`
                    SET `date_end` = '".$datetime."'
                    WHERE `datum` = '".$today."'
                    ORDER BY date_start DESC LIMIT 1";
               insert_data($sql);
                    }

     } else {
          $sql = "INSERT INTO `produktion` (`datum`, `date_start`, `date_end`, `artikelnummer`, `old_kassV`, `old_kassH`, `old_kvar`)
               VALUES ('".$today."', '".$datetime."', '".$datetime."', '".$model_v."', '".$felV."', '".$felH."', '".$kvar."')";
          insert_data($sql);
     }
}

 ?>
