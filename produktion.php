<?php

$datetime = date("Y-m-d H:i:s");
$today = date('Y-m-d');
$hour = intval(date('H'));

$sql = "SELECT datum, date_start, `$hour` As antal, kassV, kassH, old_kassV, old_kassH, old_value
     FROM produktion
     WHERE datum = '".$today."'";

$conn = new mysqli('localhost', 'root', '', '');

if ($conn->connect_error) {
          die("Connection failed: " . $conn->connection_error);
}

$result = $conn->query($sql);
// $conn->close();

while($row = $result->fetch_assoc()) {
     $date = $row['datum'];
     $date_start = date('Y-m-d', strtotime($row['date_start']));
     $antal = $row['antal'];
     $kassV = $row['kassV'];
     $kassH = $row['kassH'];
     $old_kassV = $row['old_kassV'];
     $old_kassH = $row['old_kassH'];
     $old_value = $row['old_value'];
}

if ($result->num_rows > 0) {
     $diff_kassV = $a2 - $old_kassV;
     $diff_kassH = $a1 - $old_kassH;

     if ($diff_kassV < 0 or $diff_kassH < 0) {
          $diff_kassV = 0;
          $diff_kassH = 0;
     }

     $new_kassV = $kassV + $diff_kassV;
     $new_kassH = $kassH + $diff_kassH;

     if ($old_value != $a3) {
          $diff_value = $old_value - $a3;
          $new_value = $antal + ($diff_value * 2);

          if ($diff_value < 3 && $diff_value > 0) {
               if ($date_start == $today) {
                    $sql = "UPDATE `produktion`
                         SET `date_end` = '".$datetime."', `old_value` = '".$a3."', `old_kassV` = '".$a2."', `old_kassH` = '".$a1."', `".$hour."` = '".$new_value."', kassV = '".$new_kassV."', kassH = '".$new_kassH."'
                         WHERE `datum` = '".$today."'";
                    insert_data($sql);
               } elseif ($date == $today) {
                    $sql = "UPDATE `produktion`
                         SET `date_start` = '".$datetime."', `date_end` = '".$datetime."', `old_value` = '".$a3."', `".$hour."` = '".$new_value."'
                         WHERE `datum` = '".$today."'";
                    insert_data($sql);
               } elseif ($date != $today) {
                    $sql = "INSERT INTO `produktion` (`datum`, `old_value`) VALUES ('".$today."', '".$a3."')";
                    insert_data($sql);
               }
          } else {
               $sql = "UPDATE `produktion` SET `old_kassH` = '".$a1."', kassV = '".$new_kassV."', `old_value` = '".$a3."' WHERE `datum` = '".$today."'";
               insert_data($sql);
          }
     } elseif ($old_kassV != $a2 or $old_kassH != $a1) {
          if ($diff_kassV > 0 or $diff_kassH > 0) {
               $sql = "UPDATE `produktion`
                    SET `old_kassV` = '".$a2."', `old_kassH` = '".$a1."', kassV = '".$new_kassV."', kassH = '".$new_kassH."'
                    WHERE `datum` = '".$today."'";
          } else {
               $sql = "UPDATE `produktion`
                    SET `old_kassV` = '".$a2."', `old_kassH` = '".$a1."'
                    WHERE `datum` = '".$today."'";
          }
          insert_data($sql);
     }
} else {
     $sql = "INSERT INTO `produktion` (`datum`, `old_kassV`, `old_kassH`, `old_value`) VALUES ('".$today."', '".$a2."', '".$a1."', '".$a3."')";
     insert_data($sql);
}


function insert_data($sql) {
     $conn = new mysqli('localhost', 'root', '', 'steelform');
     $conn->query($sql);
}

$conn->close();

 ?>
