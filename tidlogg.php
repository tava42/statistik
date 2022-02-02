<?php

function get_data($a1) {
     $datetime = date("Y-m-d H:i:s");
     $today = date('Y-m-d');
     // $datetime = date("2022-02-03 04:48:16");
     // $today = '2022-02-03';
     $hour = intval(date('H'));


     $sql = "SELECT datum, date_start, `$hour` As antal, kassV, kassH, old_value FROM tillv WHERE datum = '".$today."'";

     $conn = new mysqli('localhost', 'root', '', 'steelform');

     if ($conn->connect_error) {
          die("Connection failed: " . $conn->connection_error);
     }

     $result = $conn->query($sql);
     $conn->close();

     while($row = $result->fetch_assoc()) {
          $date = $row['datum'];
          $date_start = date('Y-m-d', strtotime($row['date_start']));
          $antal = $row['antal'];
          $kassV = $row['kassV'];
          $kassH = $row['kassH'];
          $old_value = $row['old_value'];

     }

     $a1 = $old_value - 1;

     if ($result->num_rows > 0) {
          if ($old_value != $a1) {
               $diff = $old_value - $a1;
               $new_value = $antal + $diff;
               echo $diff;
               if ($diff < 3 && $diff > 0) {
                    if ($date_start == $today) {
                         $sql = "UPDATE `tillv`
                              SET `date_end` = '".$datetime."', `old_value` = '".$a1."', `".$hour."` = '".$new_value."'
                              WHERE `datum` = '".$today."'";
                         insert_data($sql);
                    } elseif ($date == $today) {
                         $sql = "UPDATE `tillv`
                              SET `date_start` = '".$datetime."', `date_end` = '".$datetime."', `old_value` = '".$a1."', `".$hour."` = '".$new_value."'
                              WHERE `datum` = '".$today."'";
                         insert_data($sql);
                    } elseif ($date != $today) {
                         $sql = "INSERT INTO `tillv` (`datum`, `old_value`) VALUES ('".$today."', '".$a1."')";
                         insert_data($sql);
                    }
               } else {
                    $sql = "UPDATE `tillv` SET `old_value` = '".$a1."' WHERE `datum` = '".$today."'";
                    insert_data($sql);
               }
          }
     } else {
          $sql = "INSERT INTO `tillv` (`datum`, `old_value`) VALUES ('".$today."', '".$a1."')";
          insert_data($sql);
     }
}


get_data($a1);

function insert_data($sql) {
     $conn = new mysqli('localhost', 'root', '', 'steelform');

     if ($conn->connect_error) {
          die("Connection failed: " . $conn->connection_error);
     }
     echo $sql;

     $conn->query($sql);
     $conn->close();
}

echo "<br>";
$a1 = "";

 ?>
