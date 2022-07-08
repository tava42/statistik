<?php

$a1 = 27;
$a2 = 204;
$a3 = 5;
$a4 = 0;
$a5 = 10256;

$status = preg_replace('/[^0-9]/', '', $a4);

if ($status == 1) {
     $felH = preg_replace('/[^0-9]/', '', $a1);
     $felV = preg_replace('/[^0-9]/', '', $a2);
     $kvar = preg_replace('/[^0-9]/', '', $a3);
     $modell = preg_replace('/[^0-9]/', '', $a5);

     $datetime = date("Y-m-d H:i:s");
     $today = date('Y-m-d');
     $hour = intval(date('H'));

     $sql = "SELECT datum, date_start, date_end, `$hour` As antal, kassV, kassH, Artikelnummer, setup_time, old_kassV, old_kassH, old_value
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
          $Artikelnummer = $row['Artikelnummer'];
          $setup_time = $row['setup_time'];
          $old_kassV = $row['old_kassV'];
          $old_kassH = $row['old_kassH'];
          $old_value = $row['old_value'];
     }

     if (count($data) > 0) {
          $idle_time = abs(($date_end - strtotime($datetime)) / 60);
          if ($idle_time > 30 or $modell > 0 && $modell != $Artikelnummer && $Artikelnummer != 0) {
               $sql = "INSERT INTO `produktion` (`datum`, `date_start`, `date_end`, `Artikelnummer`, `old_kassV`, `old_kassH`, `old_value`)
                    VALUES ('".$today."', '".$datetime."', '".$datetime."', '".$modell."', '".$felV."', '".$felH."', '".$kvar."')";
               insert_data($sql);
          } else {
               $diff_kassV = $felV - $old_kassV;
               $diff_kassH = $felH - $old_kassH;

               if ($diff_kassV < 0 or $diff_kassH < 0) {
                    $diff_kassV = 0;
                    $diff_kassH = 0;
               }

               $new_kassV = $kassV + $diff_kassV;
               $new_kassH = $kassH + $diff_kassH;

               if ($modell == 0) {
                    $setup_time = $setup_time + 5;
                    $sql = "UPDATE `produktion`
                    SET `setup_time` = '".$setup_time."'
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
                                    kassV = '".$new_kassV."', kassH = '".$new_kassH."', Artikelnummer = '".$modell."'
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
          $sql = "INSERT INTO `produktion` (`datum`, `date_start`, `date_end`, `Artikelnummer`, `old_kassV`, `old_kassH`, `old_value`) VALUES ('".$today."', '".$datetime."', '".$datetime."', '".$modell."', '".$felV."', '".$felH."', '".$kvar."')";
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
