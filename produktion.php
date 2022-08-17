<?php

$a1 = 11; // fel H
$a2 = 12; // fel V
$a3 = 6; // kvar
$a4 = 1; // status I18.2
$a5 = 114; // Artikelnummer left st 1 DB30.DBW8
$a6 = 113; // Artikelnummer Right st 1 DB30.DBW10
$a7 = 1; // idle DB10.DBX8.0
$a8 = 10; // pallet_id station 10 DB30.DBW722
$a9 = 9; // drifttid DB26.DBW14
$a10 = 4; // stopptid DB26.DBW16

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

     function select_data($sql) {
          $conn = new mysqli('localhost', 'root', '', 'steelform');
          if ($conn->connect_error) {
               die("Connection failed: " . $conn->connection_error);
          }
          $result = $conn->query($sql);
          $data = $result->fetch_all(MYSQLI_ASSOC);
          $conn->close();
          return $data;
     }

     $felH = preg_replace('/[^0-9]/', '', $a1);
     $felV = preg_replace('/[^0-9]/', '', $a2);
     $kvar = preg_replace('/[^0-9]/', '', $a3);
     $model_v = preg_replace('/[^0-9]/', '', $a5);
     $model_h = preg_replace('/[^0-9]/', '', $a6);
     $idling = preg_replace('/[^0-9]/', '', $a7);
     $pallet_id = preg_replace('/[^0-9]/', '', $a8);
     $drifttid = preg_replace('/[^0-9]/', '', $a9);
     $stopptid = preg_replace('/[^0-9]/', '', $a10);

     $now = new DateTime;
     $datetime = $now->format("Y-m-d H:i:s.u");
     $today = date('Y-m-d');
     $hour = intval(date('H'));

     $sql = "SELECT artikelnummer, old_drifttid, old_stopptid, old_kassV, old_kassH, old_kvar, (UNIX_TIMESTAMP(now(3)) - UNIX_TIMESTAMP(date_end)) As time_counter
          FROM produktion
          WHERE datum = '".$today."'
          ORDER BY date_start DESC LIMIT 1";

     $data = select_data($sql);

     foreach ($data as $row) {
          $artikelnummer = $row['artikelnummer'];
          $old_drifttid = $row['old_drifttid'];
          $old_stopptid = $row['old_stopptid'];
          $old_kassV = $row['old_kassV'];
          $old_kassH = $row['old_kassH'];
          $old_kvar = $row['old_kvar'];
          $time_counter = $row['time_counter'];

     }

     if (count($data) > 0) {
          // checks if new session should be started. If machine been off for less than 30 min then same session continues.
          if ($time_counter > 1800 or $model_v != 0 && $model_v != $artikelnummer) {
               $sql = "INSERT INTO `produktion` (`datum`, `date_start`, `date_end`, `artikelnummer`, `old_drifttid`, `old_stopptid`, `old_kassV`, `old_kassH`, `old_kvar`)
                    VALUES ('".$today."', '".$datetime."', '".$datetime."', '".$model_v."', '".$drifttid."', '".$stopptid."', '".$felV."', '".$felH."', '".$kvar."')";
               insert_data($sql);
          } else {
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

               if ($model_v == 0) {
                    $model_v = $artikelnummer;
                    $model_h = $artikelnummer - 1;
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

               $diff_drifttid = $drifttid - $old_drifttid;
               $diff_stopptid = $stopptid - $old_stopptid;

               if ($diff_drifttid < 0) {
                    $diff_drifttid = 0;
               }

               if ($diff_stopptid < 0) {
                    $diff_stopptid = 0;
               }

               if ($old_kvar != $kvar or $diff_kassV > 0 or $diff_kassH > 0) {
                    // If remaning units decrease or increase more than 5 then its because the value was changed manually and should not be counted.
                    $diff_kvar = ($old_kvar - $kvar) * 2;
                    if ($diff_kvar < 0 or $diff_kvar > 5) {
                         $diff_kvar = 0;
                    }

                    $sql = "UPDATE `produktion`
                         SET `date_end` = '".$datetime."', `old_kvar` = '".$kvar."', `old_drifttid` = '".$drifttid."', `old_stopptid` = '".$stopptid."', `old_kassV` = '".$felV."', `old_kassH` = '".$felH."',
                         `".$hour."` = COALESCE(`".$hour."`, 0) + '".$diff_kvar."', drifttid = COALESCE(drifttid, 0) + '".$diff_drifttid."', stopptid = COALESCE(stopptid, 0) + '".$diff_stopptid."',
                         kassV = COALESCE(kassV, 0) + '".$diff_kassV."', kassH = COALESCE(kassH, 0) + '".$diff_kassH."', artikelnummer = '".$model_v."'
                         WHERE `datum` = '".$today."'
                         ORDER BY date_start DESC LIMIT 1";
                    insert_data($sql);

               } else {
                    $sql = "UPDATE `produktion`
                         SET `date_end` = '".$datetime."', `old_drifttid` = '".$drifttid."', `old_stopptid` = '".$stopptid."',
                         drifttid = COALESCE(drifttid, 0) + '".$diff_drifttid."', stopptid = COALESCE(stopptid, 0) + '".$diff_stopptid."'
                         WHERE `datum` = '".$today."'
                         ORDER BY date_start DESC LIMIT 1";
                    insert_data($sql);
               }
          }

     } else {
          // New day, start new session.
          if ($model_v == 0) {
               $sql = "SELECT artikelnummer FROM produktion ORDER BY date_start DESC LIMIT 1";
               $data = select_data($sql);
               foreach ($data as $row) {
                    $model_v = $row['artikelnummer'];
               }
          }

          $sql = "INSERT INTO `produktion` (`datum`, `date_start`, `date_end`, `artikelnummer`, `old_drifttid`, `old_stopptid`, `old_kassV`, `old_kassH`, `old_kvar`)
               VALUES ('".$today."', '".$datetime."', '".$datetime."', '".$model_v."', '".$drifttid."', '".$stopptid."', '".$felV."', '".$felH."', '".$kvar."')";
          insert_data($sql);
     }
}

?>
