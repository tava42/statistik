<?php

$a1 = 37; // fel H
$a2 = 48; // fel V
$a3 = 292; // kvar
$a4 = 10254; // Artikelnummer left st 1 DB30.DBW8
$a5 = 10255; // Artikelnummer Right st 1 DB30.DBW10
$a6 = 1; // idle DB10.DBX8.0
$a7 = 9; // pallet_id station 10 DB30.DBW722
$a8 = 33; // drifttid DB26.DBW14
$a9 = 24; // stopptid DB26.DBW16
$a10 = 1; // status I18.2

$status = preg_replace('/[^0-9]/', '', $a10);

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

     function clean_var($input) {
          return preg_replace('/[^0-9]/', '', $input);
     }

     $input_var = array($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9);
     $rename_var = array('felH', 'felV', 'kvar', 'model_v', 'model_h', 'idling', 'pallet_id', 'drifttid', 'stopptid');

     for ($i = 0; $i < count($input_var); $i++) {
          ${$rename_var[$i]} = clean_var($input_var[$i]);
     }

     $t = microtime(true);
     $micro = sprintf("%06d",($t - floor($t)) * 1000000);
     $time = new DateTime(date('Y-m-d H:i:s.'.$micro, $t));
     $datetime = $time->format("Y-m-d H:i:s.u");
     $timestamp = $time->format("U.u");

     $today = date('Y-m-d');
     $hour = intval(date('H'));

     $sql = "SELECT artikelnummer, old_drifttid, old_stopptid, old_kassV, old_kassH, old_kvar, date_end
          FROM produktion
          WHERE datum = '".$today."'
          ORDER BY date_start DESC LIMIT 1";

     $data = select_data($sql);

     if (count($data) > 0) {
          foreach ($data as $row) {
               $artikelnummer = $row['artikelnummer'];
               $old_drifttid = $row['old_drifttid'];
               $old_stopptid = $row['old_stopptid'];
               $old_kassV = $row['old_kassV'];
               $old_kassH = $row['old_kassH'];
               $old_kvar = $row['old_kvar'];

               $time = new DateTime(date($row['date_end']));
               $date_end = $time->format("U.u");
               $time_counter = round($timestamp - $date_end, 3);

          }

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

               // The machine changes artikelnummer to 0 between when one orders, this code is to avoid a 0 in the database.
               if ($model_v == 0) {
                    $model_v = $artikelnummer;
                    $model_h = $artikelnummer + 1;
               }
               // Logs the id of pallet's the machine is throwing away defected units from.
               function pallet_fel($model, $diff_kass) {
                    global $pallet_id, $today;
                    $current_pallet = "pallet_{$pallet_id}";

                    $sql = "INSERT INTO pallet_fel (datum, artikelnummer, ".$current_pallet.")
                         VALUES('".$today."', ".$model.", ".$diff_kass.")
                         ON DUPLICATE KEY UPDATE
                         ".$current_pallet." = COALESCE(".$current_pallet.", 0) + ".$diff_kass."";
                    insert_data($sql);
               }

               // Calculates the change of kass, defected units.
               function kass_diff($fel, $old_kass, $kass_check, $model) {
                    $diff_kass = $fel - $old_kass;
                    if ($diff_kass > 0) {
                         $kass_check = true;
                    } else if ($diff_kass < 0 or $diff_kass == 0) {
                         $diff_kass = 0;
                    }

                    if ($kass_check) {
                         pallet_fel($model, $diff_kass);
                    }

                    return $diff_kass;
               }

               // Calculates the change of drift and stopptid, runtime and stoppage.
               function tid_diff($tid, $old_tid) {
                    $diff_tid = $tid - $old_tid;

                    if ($diff_tid < 0) {
                         $diff_tid = 0;
                    }
                    return $diff_tid;
               }

               $kassV_check = $kassH_check = false;
               $diff_kassV = kass_diff($felV, $old_kassV, $kassV_check, $model_v);
               $diff_kassH = kass_diff($felH, $old_kassH, $kassH_check, $model_h);

               $diff_drifttid = tid_diff($drifttid, $old_drifttid);
               $diff_stopptid = tid_diff($stopptid, $old_stopptid);

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

               } elseif ($diff_drifttid > 0 or $diff_stopptid > 0) {
                    $sql = "UPDATE `produktion`
                         SET `date_end` = '".$datetime."', `old_drifttid` = '".$drifttid."', `old_stopptid` = '".$stopptid."',
                         drifttid = COALESCE(drifttid, 0) + '".$diff_drifttid."', stopptid = COALESCE(stopptid, 0) + '".$diff_stopptid."'
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
