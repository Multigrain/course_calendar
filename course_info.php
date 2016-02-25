<?php
  //DB read only credentials
  $host = 'mycampus.ctlmvn6rw3p8.us-east-1.rds.amazonaws.com:3306';
  $user = 'appserver';
  $password = 'Publicuser';
  $dbname = 'mycampus';

  //Determines if valid query (includes query_type field)
  if(isset($_GET['query_type'])) {
    $query_type = $_GET['query_type'];

    if($query_type == 'semesters') {
      //Year selection - Finds all available terms
      $connection = mysqli_connect($host, $user, $password, $dbname) or die("Error " . mysqli_connect_error());

      $sql = "SELECT year, term FROM Semesters;";
      $result = $connection->query($sql);

      $semesters = array();
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          array_push($semesters, array($row['year'], $row['semester']));
        }
      }

      echo json_encode($semesters);
      mysqli_close($connection);
    } elseif($query_type == 'course_code') {
      //Course codes - Finds all course codes for specified semester



    } elseif($query_type == 'course_info') {
      //Course selection - Finds section/session info for specified course

    }

  }


?>
